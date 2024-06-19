<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\OtpLogin;

class AuthOtpController extends Controller
{
    // Return View of OTP Login Page
    public function login()
    {
        return view('auth.otp-login');
    }

    // Generate OTP
    public function generate(Request $request)
    {
        
        # Generate An OTP
        $verificationCode = $this->generateOtp($request->email);

        $message = "Your OTP To Login is - ".$verificationCode->otp;
        # Return With OTP 
        
        return redirect()->route('otp.verification', ['user_id' => $verificationCode->user_id])->with('success',  $message); 
    }

    public function generateOtp($email)
    {
        $user = User::where('email', $email)->first();
        # User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();

        $now = Carbon::now();

        if($verificationCode && $now->isBefore($verificationCode->expire_at)){
            return $verificationCode;
        }

        // Create a New OTP
        
       
        $verify =  VerificationCode::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
        $mailData = [
            'title' =>  'Mail from IBM Internship',
            'body' => 'This is for testing purpose',
            'otp' => $verify->otp,
        ];
        // dd($verify);
        Mail::to($user->email)->send(new OtpLogin($mailData));

        return $verify;
    }

    public function verification($user_id)
    {
        return view('auth.otp-verification')->with([
            'user_id' => $user_id
        ]);
    }

    public function loginWithOtp(Request $request)
    {
        #Validation
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ]);

        #Validation Logic
        $verificationCode   = VerificationCode::where('user_id', $request->user_id)->where('otp', $request->otp)->first();

        $now = Carbon::now();
        if (!$verificationCode) {
            return redirect()->back()->with('error', 'Your OTP is not correct');
        }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){
            return redirect()->route('otp.login')->with('error', 'Your OTP has been expired');
        }

        $user = User::whereId($request->user_id)->first();

        if($user){
            // Expire The OTP
            $verificationCode->update([
                'expire_at' => Carbon::now()
            ]);

            Auth::login($user);
            
            return redirect()->route('dashboard');
        }

        return redirect()->route('otp.login')->with('error', 'Your Otp is not correct');
    }
}