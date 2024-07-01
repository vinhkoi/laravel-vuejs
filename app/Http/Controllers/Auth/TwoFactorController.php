<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function show()
    {
        return inertia('Auth/Verify2FA');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $code = $request->input('code');
        if ($code == session('two_factor_code')) {
            Auth::loginUsingId(session('user_id'));
            session()->forget('two_factor_code');
            session()->forget('user_id');

            return redirect()->intended('/dashboard'); // Điều chỉnh đường dẫn theo nhu cầu của bạn
        }

        return back()->withErrors(['code' => 'Mã xác thực không chính xác']);
    }
}
