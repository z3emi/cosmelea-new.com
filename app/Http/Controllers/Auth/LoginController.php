<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\SendsWhatsAppOtp;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers, SendsWhatsAppOtp;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * تم تعديل هذه الدالة للتحقق من تفعيل الحساب قبل تسجيل الدخول
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // --- START: الكود الذي يتحقق من حالة التفعيل ---
        $user = User::where($this->username(), $request->input($this->username()))->first();

        // تحقق من وجود المستخدم وصحة كلمة المرور
        if ($user && Hash::check($request->password, $user->password)) {
            // تحقق مما إذا كان الحساب غير مفعل
            if (is_null($user->phone_verified_at)) {
                // المستخدم موجود ولكنه غير مفعل
                // إعادة إرسال الرمز وتوجيهه لصفحة التحقق
                $otp = random_int(100000, 999999);
                $user->update([
                    'whatsapp_otp' => $otp,
                    'whatsapp_otp_expires_at' => Carbon::now()->addMinutes(10),
                ]);
                
                $this->sendOtpViaWhatsApp($user->phone_number, $otp);

                $request->session()->put('phone_for_verification', $user->phone_number);
                
                return redirect()->route('otp.verification.show')
                    ->with('status', 'حسابك غير مفعل. لقد أرسلنا رمز تحقق جديد إلى واتساب.');
            }
        }
        // --- END: الكود الذي يتحقق من حالة التفعيل ---

        // هذا الكود هو الكود الافتراضي لتسجيل الدخول في Laravel
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    
    /**
     * تحديد حقل تسجيل الدخول (باستخدام رقم الهاتف بدلاً من الإيميل)
     */
    public function username()
    {
        return 'phone_number';
    }
}
