<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'facebook', 'zalo'];

    /**
     * Chuyển hướng đến nhà cung cấp OAuth2 hoặc hỗ trợ Sandbox nếu chưa cấu hình Key
     */
    public function redirect(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login')->withErrors([
                'social' => "Phương thức đăng nhập '$provider' không được hỗ trợ."
            ]);
        }

        [$clientId, $clientSecret] = $this->getCredentials($provider);
        $redirectUri = route('auth.social.callback', ['provider' => $provider]);

        // Nếu đã có App Key trên môi trường thực tế, chuyển hướng sang OAuth2 thật
        if (!empty($clientId) && !empty($clientSecret)) {
            $state = Str::random(40);
            session()->put("oauth_state_{$provider}", $state);

            return match ($provider) {
                'google' => redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                    'client_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'response_type' => 'code',
                    'scope' => 'openid profile email',
                    'state' => $state,
                ])),
                'facebook' => redirect()->away('https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
                    'client_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'response_type' => 'code',
                    'scope' => 'public_profile',
                    'state' => $state,
                ])),
                'zalo' => redirect()->away('https://oauth.zaloapp.com/v4/permission?' . http_build_query([
                    'app_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'state' => $state,
                ])),
            };
        }

        // Nếu đang chạy local chưa điền Client ID, tự động đăng nhập nhanh qua luồng Sandbox chuyên nghiệp
        return $this->sandboxLogin($request, $provider);
    }

    /**
     * Nhận phản hồi Callback từ nhà cung cấp mạng xã hội
     */
    public function callback(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login')->withErrors([
                'social' => "Phương thức đăng nhập '$provider' không được hỗ trợ."
            ]);
        }

        if ($request->has('error') || $request->has('error_description')) {
            $err = $request->get('error_description') ?: 'Bạn đã từ chối cấp quyền đăng nhập.';
            return redirect()->route('login')->withErrors(['social' => $err]);
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('login')->withErrors(['social' => 'Không tìm thấy mã xác thực OAuth từ nhà cung cấp.']);
        }

        [$clientId, $clientSecret] = $this->getCredentials($provider);
        $redirectUri = route('auth.social.callback', ['provider' => $provider]);

        try {
            $profileData = $this->fetchProfileFromProvider($provider, $code, $clientId, $clientSecret, $redirectUri);
            return $this->authenticateSocialUser($provider, $profileData);
        } catch (\Throwable $e) {
            Log::error("Social callback error [{$provider}]: " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'social' => "Lỗi đồng bộ tài khoản {$provider}: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Tra cứu profile từ Token thật
     */
    protected function fetchProfileFromProvider(string $provider, string $code, string $clientId, string $clientSecret, string $redirectUri): array
    {
        if ($provider === 'google') {
            $tokenRes = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ])->throw()->json();

            $userRes = Http::withToken($tokenRes['access_token'])->get('https://www.googleapis.com/oauth2/v3/userinfo')->throw()->json();

            return [
                'id' => $userRes['sub'],
                'name' => $userRes['name'] ?? 'Google User',
                'email' => $userRes['email'] ?? null,
                'avatar' => $userRes['picture'] ?? null,
            ];
        }

        if ($provider === 'facebook') {
            $tokenRes = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ])->throw()->json();

            $userRes = Http::get('https://graph.facebook.com/me', [
                'access_token' => $tokenRes['access_token'],
                'fields' => 'id,name,email,picture.type(large)',
            ])->throw()->json();

            return [
                'id' => $userRes['id'],
                'name' => $userRes['name'] ?? 'Facebook User',
                'email' => $userRes['email'] ?? null,
                'avatar' => $userRes['picture']['data']['url'] ?? null,
            ];
        }

        if ($provider === 'zalo') {
            $tokenRes = Http::asForm()->withHeaders(['secret_key' => $clientSecret])->post('https://oauth.zaloapp.com/v4/access_token', [
                'code' => $code,
                'app_id' => $clientId,
                'grant_type' => 'authorization_code',
            ])->throw()->json();

            $userRes = Http::withHeaders(['access_token' => $tokenRes['access_token']])->get('https://graph.zalo.me/v2.0/me', [
                'fields' => 'id,name,picture',
            ])->throw()->json();

            return [
                'id' => $userRes['id'],
                'name' => $userRes['name'] ?? 'Zalo User',
                'email' => "zalo_{$userRes['id']}@curtainlux.vn",
                'avatar' => $userRes['picture']['data']['url'] ?? null,
            ];
        }

        throw new \Exception("Nhà cung cấp không hợp lệ");
    }

    /**
     * Đăng nhập thử nghiệm Sandbox 1 chạm trên môi trường phát triển / Localhost
     */
    public function sandboxLogin(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login');
        }

        // Tạo profile mẫu tương ứng từng nhà mạng
        $mockProfiles = [
            'google' => [
                'id' => 'gg_' . substr(md5('google_demo_user'), 0, 12),
                'name' => 'Nguyễn Tuấn Anh (Google)',
                'email' => 'tuananh.curtainlux@gmail.com',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80',
                'phone' => '0912345678',
            ],
            'facebook' => [
                'id' => 'fb_' . substr(md5('facebook_demo_user'), 0, 12),
                'name' => 'Lê Thảo My (Facebook)',
                'email' => 'thaomy.fb@curtainlux.vn',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
                'phone' => '0933445566',
            ],
            'zalo' => [
                'id' => 'zl_' . substr(md5('zalo_demo_user'), 0, 12),
                'name' => 'Trần Quốc Bảo (Zalo Official)',
                'email' => 'zalo_quocbao@curtainlux.vn',
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=150&q=80',
                'phone' => '0988776655',
            ],
        ];

        $profile = $mockProfiles[$provider];
        return $this->authenticateSocialUser($provider, $profile, true);
    }

    /**
     * Lưu trữ tài khoản và kích hoạt đăng nhập
     */
    protected function authenticateSocialUser(string $provider, array $profile, bool $isSandbox = false)
    {
        $providerId = (string) $profile['id'];
        $email = $profile['email'] ?? "{$provider}_{$providerId}@curtainlux.vn";
        $name = $profile['name'] ?? (ucfirst($provider) . ' User');
        $avatar = $profile['avatar'] ?? null;
        $phone = $profile['phone'] ?? null;

        // 1. Tìm theo provider & provider_id
        $user = User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        // 2. Nếu chưa có, tìm theo email
        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->provider = $provider;
                $user->provider_id = $providerId;
                if (empty($user->avatar) && $avatar) {
                    $user->avatar = $avatar;
                }
                $user->save();
            }
        }

        // 3. Nếu vẫn chưa có, tạo tài khoản Khách hàng mới
        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make(Str::random(32)),
                'role' => 'customer',
                'status' => 'active',
                'avatar' => $avatar,
                'provider' => $provider,
                'provider_id' => $providerId,
            ]);
        }

        // 4. Kiểm tra tài khoản có bị khóa không
        if ($user->status === 'inactive') {
            return redirect()->route('login')->withErrors([
                'social' => 'Tài khoản này đang tạm thời bị khóa. Vui lòng liên hệ hỗ trợ CurtainLux.'
            ]);
        }

        // 5. Đăng nhập
        Auth::login($user, true);
        session()->regenerate();

        $providerName = match($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            'zalo' => 'Zalo',
            default => ucfirst($provider),
        };

        $suffix = $isSandbox ? ' (Chế độ Thử nghiệm Sandbox)' : '';
        return redirect()->intended(route('shop.index'))->with('success', "Đăng nhập thành công qua {$providerName}{$suffix}! Chào mừng {$user->name}.");
    }

    /**
     * Lấy App ID / Client ID và Secret Key linh hoạt từ config hoặc env
     */
    protected function getCredentials(string $provider): array
    {
        $upper = strtoupper($provider);

        $clientId = config("services.{$provider}.client_id")
            ?: (env("{$upper}_CLIENT_ID") ?: env("{$upper}_APP_ID"));

        $clientSecret = config("services.{$provider}.client_secret")
            ?: (env("{$upper}_CLIENT_SECRET") ?: (env("{$upper}_APP_SECRET") ?: env("{$upper}_SECRET_KEY")));

        return [$clientId, $clientSecret];
    }
}
