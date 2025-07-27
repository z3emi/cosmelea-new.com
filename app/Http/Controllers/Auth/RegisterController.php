<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        
        $otp = random_int(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'whatsapp_otp' => $otp,
            'whatsapp_otp_expires_at' => Carbon::now()->addMinutes(10),
            // ===== START: هذا هو السطر الجديد =====
            'avatar' => 'avatars/default.jpg', // تعيين الصورة الافتراضية
            // ===== END: هذا هو السطر الجديد =====
        ]);
        
        // تعيين دور "user" الافتراضي للمستخدم الجديد
        $user->assignRole('user'); 

        // إرسال رمز التحقق عبر واتساب
        $this->sendOtpViaWhatsApp($user->phone_number, $otp);

        // ربط العميل بالمستخدم
        $this->linkCustomerToUser($user);

        // تخزين رقم الهاتف في الجلسة لاستخدامه في صفحة التحقق
        $request->session()->put('phone_for_verification', $user->phone_number);

        // توجيه المستخدم لصفحة إدخال الرمز
        return redirect()->route('otp.verification.show')
                         ->with('status', 'تم إرسال رمز التحقق إلى رقم هاتفك عبر واتساب.');
    }

    /**
     * دالة لإرسال رسالة واتساب مع متغيرات النص والزر
     */
    protected function sendOtpViaWhatsApp($recipientPhoneNumber, $otp)
    {
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $version = env('WHATSAPP_VERSION', 'v20.0');
        $templateName = 'registration_otp'; 

        $response = Http::withToken($accessToken)->post(
            "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages",
            [
                'messaging_product' => 'whatsapp',
                'to' => $recipientPhoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'ar'],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => (string)$otp],
                            ],
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => '0',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => 'verify' 
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        );

        if ($response->failed()) {
            Log::error('WhatsApp API Error: ' . $response->body());
        }
    }

    /**
     * دالة مساعدة لربط المستخدم بالعميل
     */
    protected function linkCustomerToUser(User $user)
    {
        $customer = Customer::where('phone_number', $user->phone_number)->first();

        if ($customer && is_null($customer->user_id)) {
            $customer->update(['user_id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
        } elseif (!$customer) {
            Customer::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
            ]);
        }
    }
}
