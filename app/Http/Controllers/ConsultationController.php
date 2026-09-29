<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:50',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string|max:50',
            'curtain_types_interested' => 'nullable|array',
            'estimated_windows' => 'nullable|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $consultation = Consultation::create([
            'user_id' => auth()->id(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'] ?? 'Hồ Chí Minh',
            'district' => $validated['district'] ?? null,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'] ?? 'Sáng (08:30 - 11:30)',
            'curtain_types_interested' => $validated['curtain_types_interested'] ?? [],
            'estimated_windows' => $validated['estimated_windows'] ?? 1,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch khảo sát thành công! Chuyên viên CurtainLux sẽ liên hệ quý khách trong vòng 30 phút.',
                'code' => $consultation->code,
            ]);
        }

        return redirect()->back()->with('success', "Đặt lịch thành công! Mã cuộc hẹn của bạn là: {$consultation->code}. Chuyên viên kỹ thuật sẽ liên hệ mang mẫu tận nhà cho bạn.");
    }
}
