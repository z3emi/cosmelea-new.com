@php
// Array of Arab countries with their codes and flags
$countries = [
    ['name' => 'العراق', 'code' => '+964', 'flag' => '🇮🇶'],
    ['name' => 'مصر', 'code' => '+20', 'flag' => '🇪🇬'],
    ['name' => 'السعودية', 'code' => '+966', 'flag' => '🇸🇦'],
    ['name' => 'الإمارات', 'code' => '+971', 'flag' => '🇦🇪'],
    ['name' => 'الأردن', 'code' => '+962', 'flag' => '🇯🇴'],
    ['name' => 'سوريا', 'code' => '+963', 'flag' => '🇸🇾'],
    ['name' => 'لبنان', 'code' => '+961', 'flag' => '🇱🇧'],
    ['name' => 'فلسطين', 'code' => '+970', 'flag' => '🇵🇸'],
    ['name' => 'قطر', 'code' => '+974', 'flag' => '🇶🇦'],
    ['name' => 'البحرين', 'code' => '+973', 'flag' => '🇧🇭'],
    ['name' => 'الكويت', 'code' => '+965', 'flag' => '🇰🇼'],
    ['name' => 'عُمان', 'code' => '+968', 'flag' => '🇴🇲'],
    ['name' => 'اليمن', 'code' => '+967', 'flag' => '🇾🇪'],
    ['name' => 'الجزائر', 'code' => '+213', 'flag' => '🇩🇿'],
    ['name' => 'تونس', 'code' => '+216', 'flag' => '🇹🇳'],
    ['name' => 'المغرب', 'code' => '+212', 'flag' => '🇲🇦'],
    ['name' => 'ليبيا', 'code' => '+218', 'flag' => '🇱🇾'],
    ['name' => 'السودان', 'code' => '+249', 'flag' => '🇸🇩'],
    ['name' => 'موريتانيا', 'code' => '+222', 'flag' => '🇲🇷'],
    ['name' => 'الصومال', 'code' => '+252', 'flag' => '🇸🇴'],
    ['name' => 'جيبوتي', 'code' => '+253', 'flag' => '🇩🇯'],
    ['name' => 'جزر القمر', 'code' => '+269', 'flag' => '🇰🇲'],
];
@endphp

@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@push('styles')
<style>
    .phone-input-group { display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s ease-in-out; direction: rtl; }
    .phone-input-group:focus-within { border-color: #cd8985; box-shadow: 0 0 0 2px rgba(205, 137, 133, 0.25); }
    .country-code-btn { padding: 0.5rem 0.75rem; background-color: #f9fafb; border-left: 1px solid #d1d5db; border-radius: 0.5rem 0 0 0.5rem; display: flex; align-items: center; gap: 0.3rem; font-size: 0.9rem; cursor: pointer; min-width: 90px; justify-content: flex-start; }
    .phone-input { border: none; outline: none; box-shadow: none; flex-grow: 1; padding: 0.75rem; font-size: 1rem; direction: rtl; }
    .country-list { position: absolute; right: 0; margin-top: 0.5rem; width: 100%; max-height: 240px; overflow-y: auto; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; box-shadow: 0 4px 8px rgb(0 0 0 / 0.1); z-index: 9999; direction: ltr; }
    .country-list a { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.9rem; cursor: pointer; color: #333; transition: background-color 0.2s; user-select: none; }
    .country-list a:hover { background-color: #f3e5e3; }
    .password-wrapper { position: relative; }
    .password-toggle { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280; }
</style>
<style>
    /* هيكل مجموعة إدخال الهاتف */
    .phone-input-group { 
        display: flex; 
        align-items: center; 
        border: 1px solid #d1d5db; 
        border-radius: 0.5rem; 
        transition: all 0.2s ease-in-out;
        direction: ltr; /* المحتوى من اليسار لليمين */
    }
    .phone-input-group:focus-within { 
        border-color: #cd8985; 
        box-shadow: 0 0 0 2px rgba(205, 137, 133, 0.25); 
    }
    
    /* زر كود الدولة (الآن على اليسار) */
    .country-code-btn { 
        padding: 0.5rem 0.75rem; 
        background-color: #f9fafb; 
        border-right: 1px solid #d1d5db; /* الخط على اليمين الآن */
        border-radius: 0.5rem 0 0 0.5rem; /* زوايا دائرية على اليسار */
        display: flex; 
        align-items: center; 
        gap: 0.3rem; 
        font-size: 0.9rem; 
        cursor: pointer; 
        min-width: 90px; 
        justify-content: center;
    }
    
    /* حقل إدخال الرقم (الآن على اليمين) */
    .phone-input { 
        border: none; 
        outline: none; 
        box-shadow: none; 
        flex-grow: 1; 
        padding: 0.75rem; 
        font-size: 1rem; 
        direction: ltr; /* اتجاه الأرقام LTR */
        text-align: left; /* محاذاة النص لليسار */
    }
    
    /* قائمة الدول */
    .country-list { 
        position: absolute; 
        left: 0; /* الآن تفتح من اليسار */
        margin-top: 0.5rem; 
        width: 100%; 
        max-height: 240px; 
        overflow-y: auto; 
        background: white; 
        border: 1px solid #d1d5db; 
        border-radius: 0.5rem; 
        box-shadow: 0 4px 8px rgb(0 0 0 / 0.1); 
        z-index: 9999; 
        direction: rtl; /* القائمة بالعربية */
    }
    .country-list a { 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
        padding: 0.5rem 0.75rem; 
        font-size: 0.9rem; 
        cursor: pointer; 
        color: #333; 
        transition: background-color 0.2s; 
        user-select: none; 
    }
    .country-list a:hover { 
        background-color: #f3e5e3; 
    }
    
    /* بقية الأنماط */
    .password-wrapper { 
        position: relative; 
    }
    .password-toggle { 
        position: absolute; 
        left: 0.75rem; 
        top: 50%; 
        transform: translateY(-50%); 
        cursor: pointer; 
        color: #6b7280; 
    }
    .max-w-md {
        width: 100%;
    }
    .bg-brand-dark {
        background-color: #4a2c2a;
    }
    .bg-brand-primary {
        background-color: #cd8985;
    }
    .text-brand-primary {
        color: #cd8985;
    }
    .text-brand-text {
        color: #4a2c2a;
    }
    .focus\:ring-brand-primary:focus {
        --tw-ring-color: #cd8985;
    }
    .border-brand-primary {
        border-color: #cd8985;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50/50 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <div class="py-8 px-6 md:px-8">
                <h2 class="text-2xl font-bold text-center text-brand-text mb-2">تسجيل الدخول</h2>
                <p class="text-center text-sm text-gray-600 mb-8">مرحبًا بعودتكِ إلى عالم كوزميليا</p>

                <form method="POST" action="{{ route('login') }}" x-data="{ 
                    showPassword: false, 
                    countryMenuOpen: false,
                    selectedCountry: {{ json_encode($countries[0]) }},
                    localNumber: '{{ old('local_phone_number') ?? '' }}'
                }" @click.away="countryMenuOpen = false">
                    @csrf

                    {{-- Phone Number --}}
                    <div class="mb-4 relative">
                        <label for="local_phone_number" class="block text-gray-700 text-sm font-bold mb-2">رقم الهاتف</label>
                        <div class="phone-input-group">
                            <button type="button" @click="countryMenuOpen = !countryMenuOpen" class="country-code-btn">
                                <span x-text="selectedCountry.flag"></span>
                                <span class="font-semibold text-gray-600" x-text="selectedCountry.code"></span>
                                <i class="bi bi-chevron-down text-xs"></i>
                            </button>
                            <input id="local_phone_number" type="tel" class="phone-input" name="local_phone_number" 
                                x-model="localNumber"
                                required autocomplete="tel" autofocus placeholder="مثال: 7701234567">
                        </div>
                        <input type="hidden" name="phone_number" :value="selectedCountry.code.replace('+', '') + localNumber">
                        <div x-show="countryMenuOpen" class="country-list" x-transition style="display: none;">
                            @foreach($countries as $country)
                            <a href="#" @click.prevent="selectedCountry = {{ json_encode($country) }}; countryMenuOpen = false">
                                <span class="text-xl">{{ $country['flag'] }}</span>
                                <span class="flex-grow text-right">{{ $country['name'] }}</span>
                                <span class="text-gray-500">{{ $country['code'] }}</span>
                            </a>
                            @endforeach
                        </div>
                        @error('phone_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">كلمة المرور</label>
                        <div class="password-wrapper">
                            <input :type="showPassword ? 'text' : 'password'" id="password" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary @error('password') border-red-500 @enderror" name="password" required>
                            <span class="password-toggle" @click="showPassword = !showPassword">
                                <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </span>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between mb-6">
                        <label for="remember" class="flex items-center cursor-pointer">
                            <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-brand-primary focus:ring-brand-primary border-gray-300 rounded">
                            <span class="mr-2 block text-sm text-gray-900">تذكرني</span>
                        </label>
                        
                        {{-- ===== START: الرابط المضاف ===== --}}
                        <a href="{{ route('password.reset.phone.form') }}" class="text-sm text-brand-primary hover:underline">
                            هل نسيت كلمة السر؟
                        </a>
                        {{-- ===== END: الرابط المضاف ===== --}}
                    </div>

                    {{-- Login Button --}}
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-4 rounded-full hover:bg-brand-primary transition duration-300">
                            تسجيل الدخول
                        </button>
                    </div>

                    <div class="text-center text-sm mt-6">
                        <p class="text-gray-600 mb-3">
                            ليس لديك حساب؟
                        </p>
                        <a class="w-full block border border-brand-primary text-brand-primary font-bold py-3 px-4 rounded-full hover:bg-brand-primary hover:text-white transition duration-300" href="{{ route('register') }}">
                            إنشاء حساب جديد
                        </a>
                    </div>
                    
                    {{-- WhatsApp Contact Button --}}
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 mb-2">بحاجة لمساعدة؟ تواصل معنا على الواتساب:</p>
                        <a href="https://wa.me/9647701234567" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md shadow">
                            <i class="bi bi-whatsapp text-lg ml-2"></i>
                            تواصل عبر واتساب
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection