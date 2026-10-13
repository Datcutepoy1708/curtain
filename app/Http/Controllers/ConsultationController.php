<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
{
    public function store(Request $request)
    {
        $provinceKeys = array_keys(config('provinces', []));

        $validated = $request->validateWithBag('consultation', [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'province_code' => ['required', 'string', Rule::in($provinceKeys)],
            'address' => 'required|string|max:255',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string|max:50',
            'curtain_types_interested' => 'nullable|array',
            'estimated_windows' => 'nullable|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $supportedCodes = json_decode(
            Setting::get('home_survey_province_codes', '[]'),
            true
        ) ?: [];

        $serviceMode = in_array(
            (string) $validated['province_code'],
            $supportedCodes,
            true
        ) ? Consultation::SERVICE_MODE_HOME_SURVEY : Consultation::SERVICE_MODE_REMOTE;

        $provinceName = config("provinces.{$validated['province_code']}") ?? $validated['province_code'];

        // Conditional validation & sanitization
        if ($serviceMode === Consultation::SERVICE_MODE_REMOTE) {
            $remoteData = $request->validateWithBag('consultation', [
                'contact_method' => ['required', 'string', Rule::in(['zalo_video', 'phone_call', 'google_meet'])],
                'sample_box_requested' => 'nullable|boolean',
                'installation_method' => ['required', 'string', Rule::in([
                    Consultation::INSTALL_SELF,
                    Consultation::INSTALL_CUSTOMER_ARRANGED,
                    Consultation::INSTALL_NEED_ADVICE,
                ])],
            ]);

            $contactMethod = $remoteData['contact_method'];
            $sampleBoxRequested = $request->boolean('sample_box_requested');
            $sampleBoxStatus = $sampleBoxRequested ? Consultation::SAMPLE_BOX_PENDING : Consultation::SAMPLE_BOX_NONE;
            $installationMethod = $remoteData['installation_method'];
        } else {
            // Home survey: ignore and do not accept remote-specific parameters
            $contactMethod = null;
            $sampleBoxRequested = false;
            $sampleBoxStatus = Consultation::SAMPLE_BOX_NONE;
            $installationMethod = null;
        }

        $consultation = Consultation::create([
            'user_id' => auth()->id(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'address' => $validated['address'],
            'province_code' => $validated['province_code'],
            'province_name' => $provinceName,
            'city' => $provinceName,
            'service_mode' => $serviceMode,
            'contact_method' => $contactMethod,
            'sample_box_requested' => $sampleBoxRequested,
            'sample_box_status' => $sampleBoxStatus,
            'shipping_tracking_code' => null,
            'measurement_status' => Consultation::MEASUREMENT_PENDING,
            'fit_guarantee_eligible' => false,
            'fit_guarantee_confirmed_at' => null,
            'fit_guarantee_confirmed_by' => null,
            'installation_method' => $installationMethod,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'] ?? 'Sáng (08:30 - 11:30)',
            'curtain_types_interested' => $validated['curtain_types_interested'] ?? [],
            'estimated_windows' => $validated['estimated_windows'] ?? 1,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        if ($serviceMode === Consultation::SERVICE_MODE_HOME_SURVEY) {
            $successMsg = "Đặt lịch khảo sát thành công! Mã cuộc hẹn [{$consultation->code}]. Chuyên viên kỹ thuật sẽ liên hệ mang catalogue mẫu vải thực tế đến tận nơi cho bạn theo lịch hẹn.";
        } else {
            $contactLabel = match($contactMethod) {
                'zalo_video' => 'Zalo Video Call 1-1',
                'google_meet' => 'Google Meet',
                default => 'cuộc gọi điện thoại',
            };

            $sampleMsg = $sampleBoxRequested ? ' và gửi hộp mẫu vải dệt thực tế về tận nhà cho bạn' : '';

            $successMsg = "Đặt lịch tư vấn từ xa thành công! Mã cuộc hẹn [{$consultation->code}]. Chuyên viên CurtainLux sẽ liên hệ qua {$contactLabel} để hướng dẫn đo đạc{$sampleMsg}.";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'code' => $consultation->code,
                'service_mode' => $serviceMode,
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }
}
