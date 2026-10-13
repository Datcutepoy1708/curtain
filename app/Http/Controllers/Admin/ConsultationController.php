<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with('staff')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_mode')) {
            $query->where('service_mode', $request->service_mode);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%")
                  ->orWhere('province_name', 'like', "%{$s}%");
            });
        }

        $consultations = $query->paginate(15)->withQueryString();
        $staffMembers = User::where('role', '!=', 'customer')->orderBy('name')->get();

        return view('admin.consultations.index', compact('consultations', 'staffMembers'));
    }

    public function show($id)
    {
        $consultation = Consultation::with([
            'staff',
            'fitGuaranteeConfirmedBy',
            'windows.product',
            'quotations.items',
            'currentQuotation.items',
            'currentQuotation.order'
        ])->findOrFail($id);

        $staffMembers = User::where('role', '!=', 'customer')
            ->orderByRaw("CASE 
                WHEN role = 'technician' THEN 1 
                WHEN role = 'staff' THEN 2 
                WHEN role = 'sales' THEN 3 
                WHEN role = 'manager' THEN 4 
                ELSE 5 END")
            ->orderBy('name')
            ->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.consultations.show', compact('consultation', 'staffMembers', 'products'));
    }

    public function updateStatus(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,assigned,surveying,quoted,completed,cancelled',
            'staff_id' => 'nullable|exists:users,id',
            'quotation_amount' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $oldValues = [
            'status' => $consultation->status,
            'staff_id' => $consultation->staff_id,
        ];

        $consultation->update($validated);

        $actionType = ($oldValues['staff_id'] != $consultation->staff_id) ? 'assign' : 'update_status';
        $desc = "Cập nhật điều phối lịch hẹn #{$consultation->code}: Trạng thái '{$consultation->status}'";
        if ($consultation->staff) {
            $desc .= " — Phân công: {$consultation->staff->name}";
        }

        AuditLog::record(
            $actionType,
            'consultations',
            $desc,
            $consultation->code,
            $oldValues,
            $validated
        );

        return redirect()->back()->with('success', "Cập nhật trạng thái lịch hẹn [{$consultation->code}] thành công!");
    }

    /**
     * Cập nhật thông tin và tiến trình giao Hộp mẫu vải (Dành cho tư vấn từ xa)
     */
    public function updateSampleBox(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        abort_unless($consultation->isRemote(), 422, 'Cập nhật hộp mẫu vải chỉ áp dụng cho hình thức tư vấn từ xa.');
        abort_unless($consultation->sample_box_requested, 422, 'Khách hàng không yêu cầu gửi hộp mẫu vải cho lịch hẹn này.');

        if ($consultation->sample_box_status === Consultation::SAMPLE_BOX_DELIVERED) {
            return redirect()->back()->with('error', 'Hộp mẫu vải đã được giao thành công (delivered), không thể thay đổi thông tin nữa.');
        }

        $validated = $request->validate([
            'sample_box_status' => [
                'required',
                Rule::in([
                    Consultation::SAMPLE_BOX_NONE,
                    Consultation::SAMPLE_BOX_PENDING,
                    Consultation::SAMPLE_BOX_SHIPPED,
                    Consultation::SAMPLE_BOX_DELIVERED,
                    Consultation::SAMPLE_BOX_CANCELLED,
                ]),
            ],
            'shipping_tracking_code' => [
                'nullable',
                'string',
                'max:100',
                'required_if:sample_box_status,' . Consultation::SAMPLE_BOX_SHIPPED,
            ],
        ], [
            'shipping_tracking_code.required_if' => 'Vui lòng nhập mã vận đơn vận chuyển khi chuyển trạng thái sang "Đang vận chuyển (shipped)".',
        ]);

        if (!Consultation::isValidSampleBoxTransition($consultation->sample_box_status, $validated['sample_box_status'])) {
            return redirect()->back()->with('error', "Không thể chuyển trạng thái hộp mẫu từ '{$consultation->sample_box_status}' sang '{$validated['sample_box_status']}' theo quy trình nghiệp vụ.");
        }

        $oldValues = [
            'sample_box_status' => $consultation->sample_box_status,
            'shipping_tracking_code' => $consultation->shipping_tracking_code,
        ];

        $consultation->update([
            'sample_box_status' => $validated['sample_box_status'],
            'shipping_tracking_code' => $validated['shipping_tracking_code'] ?? $consultation->shipping_tracking_code,
        ]);

        AuditLog::record(
            'update_sample_box',
            'consultations',
            "Cập nhật hộp mẫu vải lịch hẹn #{$consultation->code}: Trạng thái '{$consultation->sample_box_status}' (Vận đơn: " . ($consultation->shipping_tracking_code ?: 'chưa có') . ")",
            $consultation->code,
            $oldValues,
            $validated
        );

        return redirect()->back()->with('success', "Cập nhật trạng thái hộp mẫu vải cho lịch hẹn [{$consultation->code}] thành công!");
    }

    /**
     * Cập nhật tiến độ đo đạc từ xa và thẩm định Bảo hiểm vừa vặn 100%
     */
    public function updateMeasurement(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        abort_unless($consultation->isRemote(), 422, 'Quy trình thẩm định số đo từ xa chỉ áp dụng cho hình thức tư vấn từ xa.');

        if ($consultation->quotations()->exists()) {
            return redirect()->back()->with('error', 'Lịch hẹn đã có bảng báo giá chính thức! Toàn bộ số đo và điều kiện bảo hiểm đã bị khóa để đảm bảo tính pháp lý.');
        }

        $validated = $request->validate([
            'measurement_status' => [
                'required',
                Rule::in([
                    Consultation::MEASUREMENT_PENDING,
                    Consultation::MEASUREMENT_GUIDED,
                    Consultation::MEASUREMENT_CONFIRMED,
                    Consultation::MEASUREMENT_SELF,
                ]),
            ],
            'fit_guarantee_eligible' => 'nullable|boolean',
        ]);

        $hasQuotations = $consultation->quotations()->exists();

        if (!Consultation::isValidMeasurementTransition($consultation->measurement_status, $validated['measurement_status'], $hasQuotations)) {
            return redirect()->back()->with('error', "Không thể chuyển trạng thái đo đạc từ '{$consultation->measurement_status}' sang '{$validated['measurement_status']}' (đã có báo giá hoặc sai quy trình nghiệp vụ).");
        }

        $newMeasurementStatus = $validated['measurement_status'];
        $wantsFitGuarantee = $request->boolean('fit_guarantee_eligible');

        // Quy tắc nghiệp vụ: Chỉ khi measurement_status = confirmed mới được bật Bảo hiểm vừa vặn
        if ($newMeasurementStatus === Consultation::MEASUREMENT_CONFIRMED) {
            $fitGuarantee = $wantsFitGuarantee;
            $fitGuaranteeAt = $fitGuarantee ? now() : null;
            $fitGuaranteeBy = $fitGuarantee ? auth()->id() : null;
        } else {
            $fitGuarantee = false;
            $fitGuaranteeAt = null;
            $fitGuaranteeBy = null;
        }

        $oldValues = [
            'measurement_status' => $consultation->measurement_status,
            'fit_guarantee_eligible' => $consultation->fit_guarantee_eligible,
            'fit_guarantee_confirmed_at' => $consultation->fit_guarantee_confirmed_at,
            'fit_guarantee_confirmed_by' => $consultation->fit_guarantee_confirmed_by,
        ];

        $consultation->update([
            'measurement_status' => $newMeasurementStatus,
            'fit_guarantee_eligible' => $fitGuarantee,
            'fit_guarantee_confirmed_at' => $fitGuaranteeAt,
            'fit_guarantee_confirmed_by' => $fitGuaranteeBy,
        ]);

        AuditLog::record(
            'update_measurement',
            'consultations',
            "Cập nhật thẩm định đo đạc #{$consultation->code}: Trạng thái '{$newMeasurementStatus}' | Bảo hiểm vừa vặn: " . ($fitGuarantee ? 'Đủ điều kiện' : 'Không áp dụng'),
            $consultation->code,
            $oldValues,
            [
                'measurement_status' => $newMeasurementStatus,
                'fit_guarantee_eligible' => $fitGuarantee,
            ]
        );

        return redirect()->back()->with('success', "Cập nhật tiến trình đo đạc và bảo hiểm cho lịch hẹn [{$consultation->code}] thành công!");
    }
}
