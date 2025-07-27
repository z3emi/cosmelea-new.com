@php
// قائمة الدول
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

@section('title', 'إعادة تعيين كلمة السر')

@push('styles')
{{-- يمكنك نسخ نفس الأنماط (styles) من صفحة التسجيل هنا --}}
<style>
    .phone-input-group { display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s ease-in-out; direction: rtl; position: relative; }
    .phone-input-group:focus-within { border-color: #cd8985; box-shadow: 0 0 0 2px rgba(205, 137, 133, 0.25); }
    .country-code-btn { padding: 0.5rem 0.75rem; background-color: #f9fafb; border-left: 1px solid #d1d5db; border-radius: 0.5rem 0 0 0.5rem; display: flex; align-items: center; gap: 0.3rem; font-size: 0.9rem; cursor: pointer; min-width: 90px; justify-content: flex-start; }
    .phone-input { border: none; outline: none; box-shadow: none; flex-grow: 1; padding: 0.75rem; font-size: 1rem; direction: rtl; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden"
        x-data="{
            countryMenuOpen: false,
            countries: {{ json_encode($countries) }},
            selectedCountry: {{ json_encode($countries[0]) }},
            localPhone: '{{ old('local_phone_number', '') }}'
        }"
    >
        <div class="py-8 px-6 md:px-8">
            <h2 class="text-2xl font-bold text-center text-brand-text mb-2">إعادة تعيين كلمة السر</h2>
            <p class="text-center text-sm text-gray-600 mb-6">أدخل رقم هاتفك المسجل لإرسال رمز التحقق.</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.send.otp') }}">
                @csrf
                <div class="mb-4 relative">
                    <label for="local_phone_number" class="block text-gray-700 text-sm font-bold mb-2">رقم الهاتف</label>
                    <div class="phone-input-group">
                        <button type="button" class="country-code-btn" @click="countryMenuOpen = !countryMenuOpen">
                            <span x-text="selectedCountry.flag"></span>
                            <span class="font-semibold text-gray-600" x-text="selectedCountry.code"></span>
                        </button>
                        <input id="local_phone_number" type="tel" class="phone-input" name="local_phone_number" x-model="localPhone" required autocomplete="tel" autofocus>
                    </div>
                    <input type="hidden" name="phone_number" :value="selectedCountry.code.replace('+', '') + localPhone">
                </div>

                <div class="mb-4">
                    <button type="submit" class="w-full bg-brand-primary text-white font-bold py-3 px-4 rounded-md hover:bg-brand-dark transition duration-300">
                        إرسال رمز التحقق
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
