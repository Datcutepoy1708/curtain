<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('setting_value', 'setting_key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bank_id' => 'nullable|string|regex:/^[A-Za-z0-9]{2,12}$/',
            'bank_account_number' => 'nullable|string|regex:/^[0-9]{6,20}$/',
            'bank_account_name' => 'nullable|string|max:120',
            'home_survey_province_codes' => 'nullable|array',
            'home_survey_province_codes.*' => [
                'string',
                \Illuminate\Validation\Rule::in(array_keys(config('provinces', []))),
            ],
        ]);
        $data = $request->except('_token');

        // Bật/Tắt thanh toán ngân hàng (checkbox HTML không gửi nếu unchecked)
        $data['enable_bank_transfer'] = $request->has('enable_bank_transfer') ? '1' : '0';

        // Xử lý riêng danh sách mã tỉnh hỗ trợ khảo sát tận nhà
        if ($request->has('has_home_survey_config') || $request->has('home_survey_province_codes')) {
            $provinceCodes = $validated['home_survey_province_codes'] ?? [];
            Setting::set(
                'home_survey_province_codes',
                json_encode(array_values($provinceCodes), JSON_UNESCAPED_UNICODE),
                'booking',
                'Danh sách mã tỉnh hỗ trợ khảo sát tận nhà'
            );
            unset($data['home_survey_province_codes'], $data['has_home_survey_config']);
        }

        foreach ($data as $key => $value) {
            $storeValue = is_array($value) ? json_encode(array_values($value), JSON_UNESCAPED_UNICODE) : (string)$value;
            Setting::set($key, $storeValue);
        }

        return redirect()->back()->with('success', 'Đã lưu cấu hình hệ thống CurtainLux thành công.');
    }
}
