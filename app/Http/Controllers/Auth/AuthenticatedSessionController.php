<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\AuthOtpController;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $obj = new AuthOtpController();

        $verificationCode = $obj->generateOtp($request->email);

        $message = "Your OTP To Login is - ".$verificationCode->otp;
        
        
        return redirect()->route('otp.verification', ['user_id' => $verificationCode->user_id])->with('success',  $message);
       
        

        return redirect()->intended(route('dashboard', absolute: false));
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
