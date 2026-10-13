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
     * Chuyển hướng người dùng sang hệ thống xác thực OAuth2 thực tế của Google, Facebook, hoặc Zalo
     */
    public function redirect(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login')->withErrors([
                'social' => "Phương thức đăng nhập '$provider' không được hỗ trợ."
            ]);
        }

        [$clientId, $clientSecret] = $this->getCredentials($provider);

        // Kiểm tra xem đã cấu hình Client ID / Secret thực tế chưa
        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'social' => "Chưa cấu hình API Key thực tế cho {$provider}. Vui lòng kiểm tra biến môi trường trong file .env."
            ]);
        }

        $redirectUri = $this->getRedirectUri($provider);
        $state = Str::random(40);

        // Lưu trữ state và redirectUri vào session để đối soát callback
        session()->put("oauth_state_{$provider}", $state);
        session()->put("oauth_redirect_uri_{$provider}", $redirectUri);

        return match ($provider) {
            'google' => redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'access_type' => 'offline',
                'prompt' => 'select_account',
                'state' => $state,
            ])),

            'facebook' => redirect()->away('https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'public_profile',
                'state' => $state,
            ])),

            'zalo' => (function () use ($clientId, $redirectUri, $state) {
                // Giao thức Zalo OAuth v4 bắt buộc cơ chế PKCE (code_challenge & code_verifier)
                $codeVerifier = Str::random(64);
                session()->put('zalo_code_verifier', $codeVerifier);
                $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

                return redirect()->away('https://oauth.zaloapp.com/v4/permission?' . http_build_query([
                    'app_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'code_challenge' => $codeChallenge,
                    'state' => $state,
                ]));
            })(),
        };
    }

    /**
     * Nhận phản hồi Callback thực tế từ nhà cung cấp OAuth2
     */
    public function callback(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login')->withErrors([
                'social' => "Phương thức đăng nhập '$provider' không được hỗ trợ."
            ]);
        }

        // Bắt lỗi khi người dùng bấm Hủy (Cancel) hoặc lỗi từ bên thứ ba
        if ($request->has('error') || $request->has('error_description') || $request->has('error_message')) {
            $err = $request->get('error_description')
                ?: ($request->get('error_message') ?: ($request->get('error') ?: 'Bạn đã từ chối cấp quyền đăng nhập.'));
            return redirect()->route('login')->withErrors(['social' => "Xác thực {$provider} không thành công: {$err}"]);
        }

        $code = $request->input('code');
        if (!$code) {
            return redirect()->route('login')->withErrors(['social' => "Không tìm thấy mã xác thực OAuth (code) trả về từ {$provider}."]);
        }

        // Kiểm tra tính hợp lệ của State (chống CSRF)
        $expectedState = session()->pull("oauth_state_{$provider}");
        if ($expectedState && $request->filled('state') && !hash_equals($expectedState, (string) $request->input('state'))) {
            return redirect()->route('login')->withErrors(['social' => 'Mã phiên bảo mật OAuth không trùng khớp. Vui lòng thử lại.']);
        }

        [$clientId, $clientSecret] = $this->getCredentials($provider);
        $redirectUri = session()->pull("oauth_redirect_uri_{$provider}") ?: $this->getRedirectUri($provider);

        try {
            $profileData = $this->fetchProfileFromProvider($provider, $code, $clientId, $clientSecret, $redirectUri);
            return $this->authenticateSocialUser($provider, $profileData);
        } catch (\Throwable $e) {
            Log::error("Social callback error [{$provider}]: " . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'social' => "Lỗi xác thực tài khoản {$provider} thực tế: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Tra cứu profile thực tế từ Token qua API chính thức của Provider
     */
    protected function fetchProfileFromProvider(string $provider, string $code, string $clientId, string $clientSecret, string $redirectUri): array
    {
        if ($provider === 'google') {
            $tokenRes = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if (!$tokenRes->successful()) {
                $err = $tokenRes->json('error_description') ?: ($tokenRes->json('error') ?: $tokenRes->body());
                throw new \Exception("Google Token Error: {$err}");
            }

            $tokenData = $tokenRes->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                throw new \Exception("Không nhận được Access Token từ Google.");
            }

            $userRes = Http::withoutVerifying()->withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (!$userRes->successful()) {
                throw new \Exception("Không thể lấy thông tin người dùng từ Google API.");
            }

            $userData = $userRes->json();

            return [
                'id' => $userData['sub'],
                'name' => $userData['name'] ?? ($userData['email'] ? explode('@', $userData['email'])[0] : 'Google User'),
                'email' => $userData['email'] ?? null,
                'avatar' => $userData['picture'] ?? null,
            ];
        }

        if ($provider === 'facebook') {
            $tokenRes = Http::withoutVerifying()->get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            if (!$tokenRes->successful()) {
                $err = $tokenRes->json('error.message') ?: $tokenRes->body();
                throw new \Exception("Facebook Token Error: {$err}");
            }

            $tokenData = $tokenRes->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                throw new \Exception("Không nhận được Access Token từ Facebook.");
            }

            $userRes = Http::withoutVerifying()->get('https://graph.facebook.com/me', [
                'access_token' => $accessToken,
                'fields' => 'id,name,email,picture.type(large)',
            ]);

            if (!$userRes->successful()) {
                throw new \Exception("Không thể lấy thông tin người dùng từ Facebook Graph API.");
            }

            $userData = $userRes->json();

            return [
                'id' => $userData['id'],
                'name' => $userData['name'] ?? 'Facebook User',
                'email' => $userData['email'] ?? null,
                'avatar' => $userData['picture']['data']['url'] ?? null,
            ];
        }

        if ($provider === 'zalo') {
            $codeVerifier = session()->pull('zalo_code_verifier');
            if (!$codeVerifier) {
                throw new \Exception("Không tìm thấy Zalo PKCE code_verifier trong phiên. Vui lòng thử đăng nhập lại.");
            }

            $tokenRes = Http::withoutVerifying()->asForm()->withHeaders([
                'secret_key' => $clientSecret,
            ])->post('https://oauth.zaloapp.com/v4/access_token', [
                'code' => $code,
                'app_id' => $clientId,
                'grant_type' => 'authorization_code',
                'code_verifier' => $codeVerifier,
            ]);

            $tokenData = $tokenRes->json();

            if (isset($tokenData['error']) && (int) $tokenData['error'] !== 0) {
                $err = $tokenData['message'] ?? ($tokenData['error_description'] ?? "Mã lỗi: {$tokenData['error']}");
                throw new \Exception("Zalo Token Error: {$err}");
            }

            $accessToken = $tokenData['access_token'] ?? null;
            if (!$accessToken) {
                throw new \Exception("Không nhận được Access Token từ Zalo.");
            }

            $userRes = Http::withoutVerifying()->withHeaders([
                'access_token' => $accessToken,
            ])->get('https://graph.zalo.me/v2.0/me', [
                'fields' => 'id,name,picture',
            ]);

            $userData = $userRes->json();

            if (isset($userData['error']) && (int) $userData['error'] !== 0) {
                $err = $userData['message'] ?? "Mã lỗi: {$userData['error']}";
                throw new \Exception("Zalo Profile Error: {$err}");
            }

            return [
                'id' => $userData['id'],
                'name' => $userData['name'] ?? 'Zalo User',
                'email' => "zalo_{$userData['id']}@curtainlux.vn",
                'avatar' => $userData['picture']['data']['url'] ?? null,
            ];
        }

        throw new \Exception("Nhà cung cấp mạng xã hội không hợp lệ.");
    }

    /**
     * Lưu trữ tài khoản và kích hoạt đăng nhập
     */
    protected function authenticateSocialUser(string $provider, array $profile)
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

        // 2. Nếu chưa có theo provider_id, tìm theo email thực tế
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

        // 3. Nếu vẫn chưa có tài khoản, tạo mới người dùng Khách hàng
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

        // 5. Đăng nhập người dùng vào hệ thống
        Auth::login($user, true);
        session()->regenerate();

        $providerName = match ($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            'zalo' => 'Zalo',
            default => ucfirst($provider),
        };

        return redirect()->intended(route('shop.index'))->with('success', "Đăng nhập thành công qua {$providerName}! Chào mừng quý khách {$user->name}.");
    }

    /**
     * Xác định Redirect URI linh hoạt: ưu tiên cấu hình cụ thể hoặc tự động sinh theo URL hiện tại
     */
    protected function getRedirectUri(string $provider): string
    {
        $custom = config("services.{$provider}.redirect_uri")
            ?: (config("services.{$provider}.redirect") ?: env(strtoupper($provider) . '_REDIRECT_URI'));

        if (!empty($custom)) {
            return $custom;
        }

        return route('auth.social.callback', ['provider' => $provider]);
    }

    /**
     * Lấy App ID / Client ID và Secret Key từ config hoặc env
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

    /**
     * Chế độ sandbox dự phòng cho môi trường offline test
     */
    public function sandboxLogin(Request $request, string $provider)
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login');
        }

        $mockProfiles = [
            'google' => [
                'id' => 'gg_' . substr(md5('google_demo_user'), 0, 12),
                'name' => 'Nguyễn Tuấn Anh (Google Demo)',
                'email' => 'tuananh.curtainlux@gmail.com',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80',
                'phone' => '0912345678',
            ],
            'facebook' => [
                'id' => 'fb_' . substr(md5('facebook_demo_user'), 0, 12),
                'name' => 'Lê Thảo My (Facebook Demo)',
                'email' => 'thaomy.fb@curtainlux.vn',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
                'phone' => '0933445566',
            ],
            'zalo' => [
                'id' => 'zl_' . substr(md5('zalo_demo_user'), 0, 12),
                'name' => 'Trần Quốc Bảo (Zalo Demo)',
                'email' => 'zalo_quocbao@curtainlux.vn',
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=150&q=80',
                'phone' => '0988776655',
            ],
        ];

        return $this->authenticateSocialUser($provider, $mockProfiles[$provider]);
    }
}
