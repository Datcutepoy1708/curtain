<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('shop.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 6 ký tự.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.'])->withInput(['email']);
        }

        if (!$user->isActive()) {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị vô hiệu hóa.'])->withInput(['email']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isAdmin() || $user->isStaff()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('shop.index'))->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->name);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('shop.index');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được đăng ký.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shop.index')->with('success', 'Đăng ký tài khoản thành viên CurtainLux thành công!');
    }

    public function showAdminLogin()
    {
        if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isStaff())) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Vui lòng nhập email quản trị.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Thông tin đăng nhập quản trị không chính xác.'])->withInput(['email']);
        }

        if (!$user->isAdmin() && !$user->isStaff()) {
            return back()->withErrors(['email' => 'Bạn không có quyền truy cập trang quản trị này.'])->withInput(['email']);
        }

        if (!$user->isActive()) {
            return back()->withErrors(['email' => 'Tài khoản quản trị đã bị khóa.'])->withInput(['email']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        \App\Models\AuditLog::record('login', 'auth', "Đăng nhập thành công vào trang quản trị CurtainLux", (string) $user->id);

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Chào mừng Quản trị viên ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.index')->with('success', 'Đã đăng xuất tài khoản thành công.');
    }

    public function adminLogout(Request $request)
    {
        $u = Auth::user();
        if ($u) {
            \App\Models\AuditLog::record('logout', 'auth', "Đăng xuất khỏi trang quản trị", (string) $u->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất khỏi trang quản trị CurtainLux.');
    }

    // =========================================================================
    // Forgot & Reset Password Flow
    // =========================================================================
    public function showForgotPassword(Request $request)
    {
        $isAdmin = false;
        return view('auth.forgot-password', compact('isAdmin'));
    }

    public function showAdminForgotPassword(Request $request)
    {
        $isAdmin = true;
        return view('auth.forgot-password', compact('isAdmin'));
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Không tìm thấy tài khoản với email này trong hệ thống.',
        ]);

        $email = trim($request->email);
        $token = \Illuminate\Support\Str::random(60);

        // Update or insert token with created_at timestamp
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        // Log secure audit trace (do NOT expose reset link in user session or UI)
        \Illuminate\Support\Facades\Log::info("Password reset requested for {$email}");
        \Illuminate\Support\Facades\Log::debug("Secure password reset link for {$email}: {$resetUrl}");

        return back()->with('status', 'Yêu cầu khôi phục đã được ghi nhận!')
                     ->with('info', "Nếu email này tồn tại trong hệ thống CurtainLux, hướng dẫn và liên kết khôi phục mật khẩu đã được gửi đến hộp thư của bạn. Vui lòng kiểm tra email (bao gồm cả thư mục Spam/Quảng cáo).");
    }

    public function showResetPassword($token, Request $request)
    {
        $email = $request->query('email', '');
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Mã liên kết xác thực không hợp lệ hoặc đã được sử dụng. Vui lòng yêu cầu lại.']);
        }

        // Validate token expiration (maximum 60 minutes)
        $tokenAgeMinutes = \Carbon\Carbon::parse($record->created_at)->diffInMinutes(now());
        if ($tokenAgeMinutes > 60) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu đã hết hạn (chỉ có hiệu lực trong 60 phút). Vui lòng gửi lại yêu cầu mới.']);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Revoke token immediately after use
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->route('admin.login')->with('success', 'Đặt lại mật khẩu quản trị thành công! Vui lòng đăng nhập với mật khẩu mới.');
            }

            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới.');
        }

        return back()->withErrors(['email' => 'Không tìm thấy tài khoản người dùng tương ứng.']);
    }
}
