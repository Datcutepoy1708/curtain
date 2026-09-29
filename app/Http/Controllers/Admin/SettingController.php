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
        $request->validate([
            'bank_id' => 'nullable|string|regex:/^[A-Za-z0-9]{2,12}$/',
            'bank_account_number' => 'nullable|string|regex:/^[0-9]{6,20}$/',
            'bank_account_name' => 'nullable|string|max:120',
        ]);
        $data = $request->except('_token');

        // Bật/Tắt thanh toán ngân hàng (checkbox HTML không gửi nếu unchecked)
        $data['enable_bank_transfer'] = $request->has('enable_bank_transfer') ? '1' : '0';

        foreach ($data as $key => $value) {
            Setting::set($key, (string)$value);
        }

        return redirect()->back()->with('success', 'Đã lưu cấu hình hệ thống CurtainLux thành công.');
    }
}
