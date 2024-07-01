<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // $request->authenticate();

        // $request->session()->regenerate();

        // return redirect()->intended(RouteServiceProvider::HOME);
        $request->authenticate();

        // Tạo mã xác thực ngẫu nhiên
        $code = rand(100000, 999999);

        // Lưu mã xác thực vào session
        $request->session()->put('two_factor_code', $code);
        $request->session()->put('user_id', Auth::id());

        // Gửi yêu cầu đến Webhook của Make
        $webhookUrl = 'https://hook.eu2.make.com/bi4k7vr4qefvm3rti9728ylaailu79ou'; // Thay bằng URL Webhook của bạn
        $data = [
            'email' => Auth::user()->email,
            'code' => $code,
        ];

        $client = new Client();
        $response = $client->post($webhookUrl, [
            'json' => $data
        ]);

        // Đăng xuất người dùng tạm thời để yêu cầu xác thực 2FA
        Auth::logout();

        // Chuyển hướng đến trang nhập mã xác thực
        return redirect()->route('verify-2fa.show');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
