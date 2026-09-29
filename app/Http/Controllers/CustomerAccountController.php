<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class CustomerAccountController extends Controller
{
    /**
     * Màn hình Hồ sơ & Cài đặt tài khoản khách hàng
     */
    public function profile()
    {
        $user = Auth::user();
        $ordersCount = Order::where('user_id', $user->id)->count();
        $consultationsCount = Consultation::where('user_id', $user->id)->count();

        return view('shop.account.profile', compact('user', 'ordersCount', 'consultationsCount'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin hồ sơ của bạn thành công!');
    }

    /**
     * Đổi mật khẩu đăng nhập
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Đã đổi mật khẩu tài khoản thành công!');
    }

    /**
     * Danh sách lịch sử đơn hàng của khách hàng
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)->with('items.product')->latest();

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('order_status', $status);
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('shop.account.orders', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng và theo dõi tiến độ may rèm
     */
    public function orderDetail($orderCode)
    {
        $user = Auth::user();

        $order = Order::where('order_code', strtoupper(trim($orderCode)))
            ->where('user_id', $user->id)
            ->with(['items.product'])
            ->firstOrFail();

        return view('shop.account.order-detail', compact('order'));
    }

    /**
     * Hủy đơn hàng (dành cho khách khi đơn còn ở trạng thái pending)
     */
    public function cancelOrder(Request $request, $orderCode)
    {
        $user = Auth::user();

        $order = Order::where('order_code', strtoupper(trim($orderCode)))
            ->where('user_id', $user->id)
            ->with('items')
            ->firstOrFail();

        if (!$order->canBeCancelledByCustomer()) {
            return redirect()->back()->with('error', 'Đơn hàng này đã được xác nhận hoặc đang tiến hành gia công tại xưởng, không thể hủy trực tuyến. Quý khách vui lòng liên hệ hotline 0912.345.678.');
        }

        // Hoàn lại tồn kho cho các sản phẩm trong đơn
        $order->restoreStock();

        $reason = $request->input('cancel_reason', 'Khách hàng yêu cầu hủy đơn trên tài khoản');
        $order->notes = ($order->notes ? $order->notes . " | " : "") . "Lý do hủy: " . $reason;
        $order->order_status = 'cancelled';
        $order->save();

        return redirect()->route('customer.orders')->with('success', "Đã hủy đơn hàng {$order->order_code} thành công. Lượng phôi rèm đã được hoàn trả lại kho.");
    }

    /**
     * Danh sách lịch hẹn khảo sát đo rèm của khách
     */
    public function consultations()
    {
        $user = Auth::user();

        $consultations = Consultation::where('user_id', $user->id)
            ->with(['windows', 'currentQuotation', 'quotations'])
            ->latest()
            ->paginate(10);

        return view('shop.account.consultations', compact('consultations'));
    }

    private function canAccessQuotation(Quotation $quotation, Request $request): bool
    {
        $user = Auth::user();

        return ($user && ($quotation->consultation->user_id === $user->id || $user->role === 'admin'))
            || ($request->routeIs('customer.quotation.guest.*') && $request->hasValidSignature());
    }

    /**
     * Chi tiết Bảng Báo Giá Chính Thức (Từng ô cửa, phiên bản v1, v2...)
     */
    public function quotationDetail(Request $request, $code)
    {
        $quotation = Quotation::where('quotation_code', strtoupper(trim($code)))
            ->with(['consultation.staff', 'items.consultationWindow', 'consultation.windows', 'order'])
            ->firstOrFail();

        // Kiểm tra quyền sở hữu
        if (!$this->canAccessQuotation($quotation, $request)) {
            abort(403, 'Bạn không có quyền xem bảng báo giá này.');
        }

        if ($quotation->status === 'draft') {
            abort(404);
        }

        $guestAccess = $request->routeIs('customer.quotation.guest.*');
        $acceptUrl = $guestAccess
            ? URL::temporarySignedRoute('customer.quotation.guest.accept', now()->addDays(30), ['code' => $quotation->quotation_code])
            : route('customer.quotation.accept', $quotation->quotation_code);
        $revisionUrl = $guestAccess
            ? URL::temporarySignedRoute('customer.quotation.guest.request-revision', now()->addDays(30), ['code' => $quotation->quotation_code])
            : route('customer.quotation.request-revision', $quotation->quotation_code);

        return view('shop.account.quotation-detail', compact('quotation', 'guestAccess', 'acceptUrl', 'revisionUrl'));
    }

    /**
     * Khách hàng chấp thuận duyệt bản báo giá
     */
    public function acceptQuotation(Request $request, $code)
    {
        $quotation = Quotation::where('quotation_code', strtoupper(trim($code)))
            ->with('consultation')
            ->firstOrFail();

        if (!$this->canAccessQuotation($quotation, $request)) {
            abort(403, 'Bạn không có quyền thao tác với bảng báo giá này.');
        }

        if (!$quotation->canBeAccepted()) {
            return redirect()->back()->with('error', 'Báo giá này không ở trạng thái có thể duyệt.');
        }

        $note = $request->input('customer_notes', 'Khách hàng duyệt báo giá trực tuyến');
        $quotation->update([
            'status' => 'accepted',
            'customer_notes' => ($quotation->customer_notes ? $quotation->customer_notes . "\n" : '') . "Duyệt ngày " . now()->format('d/m/Y H:i') . ": " . $note,
        ]);
        $quotation->consultation->update([
            'current_quotation_id' => $quotation->id,
            'quotation_amount' => $quotation->total_amount,
        ]);

        return redirect()->back()->with('success', "🎉 Quý khách đã duyệt thành công Bảng Báo Giá [{$quotation->quotation_code}]! Chuyên viên CurtainLux sẽ tiến hành kích hoạt lệnh may đo tại xưởng.");
    }

    /**
     * Khách hàng gửi yêu cầu điều chỉnh số đo / ô cửa / cấu hình
     */
    public function requestQuotationRevision(Request $request, $code)
    {
        $request->validate([
            'revision_notes' => 'required|string|min:5|max:1000',
            'customer_offer_amount' => 'nullable|integer|min:1|max:99999999999999',
            'revision_item_id' => 'nullable|integer|min:1',
        ]);

        $quotation = Quotation::where('quotation_code', strtoupper(trim($code)))
            ->with('consultation')
            ->firstOrFail();

        if (!$this->canAccessQuotation($quotation, $request)) {
            abort(403, 'Bạn không có quyền thao tác với bảng báo giá này.');
        }

        if (!$quotation->canBeAccepted()) {
            return redirect()->back()->with('error', 'Báo giá không còn mở để thương lượng.');
        }

        $item = $request->filled('revision_item_id')
            ? $quotation->items()->whereKey($request->integer('revision_item_id'))->firstOrFail()
            : null;
        $reason = ($item ? '[' . $item->room_name . '] ' : '') . $request->input('revision_notes');
        $quotation->update([
            'status' => 'revision_requested',
            'customer_offer_amount' => $request->input('customer_offer_amount'),
            'customer_notes' => ($quotation->customer_notes ? $quotation->customer_notes . "\n" : '') . "Yêu cầu chỉnh sửa ngày " . now()->format('d/m/Y H:i') . ": " . $reason,
        ]);

        return redirect()->back()->with('success', "Đã gửi yêu cầu chỉnh sửa báo giá đến chuyên viên kỹ thuật CurtainLux! Chúng tôi sẽ điều chỉnh cấu hình và cập nhật phiên bản báo giá mới sớm nhất.");
    }
}
