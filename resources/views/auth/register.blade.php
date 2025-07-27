@php
// قائمة الدول كما في صفحة تسجيل الدخول
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

@section('title', 'إنشاء حساب جديد')

@push('styles')
<style>
    .phone-input-group { display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 0.5rem; transition: all 0.2s ease-in-out; direction: rtl; position: relative; }
    .phone-input-group:focus-within { border-color: #cd8985; box-shadow: 0 0 0 2px rgba(205, 137, 133, 0.25); }
    .country-code-btn { padding: 0.5rem 0.75rem; background-color: #f9fafb; border-left: 1px solid #d1d5db; border-radius: 0.5rem 0 0 0.5rem; display: flex; align-items: center; gap: 0.3rem; font-size: 0.9rem; cursor: pointer; min-width: 90px; justify-content: flex-start; }
    .phone-input { border: none; outline: none; box-shadow: none; flex-grow: 1; padding: 0.75rem; font-size: 1rem; direction: rtl; }
    .country-list { position: absolute; right: 0; margin-top: 0.5rem; width: 100%; max-height: 240px; overflow-y: auto; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; box-shadow: 0 4px 8px rgb(0 0 0 / 0.1); z-index: 9999; direction: ltr; }
    .country-list a { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.9rem; cursor: pointer; color: #333; transition: background-color 0.2s; user-select: none; }
    .country-list a:hover { background-color: #f3e5e3; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden"
        x-data="{
            countryMenuOpen: false,
            countries: {{ json_encode($countries) }},
            selectedCountry: {{ json_encode($countries[0]) }},
            localPhone: '{{ old('local_phone_number', '') }}',
            
            init() {
                // مراقبة أي تغيير في حقل رقم الهاتف
                this.$watch('localPhone', (value) => {
                    // إذا كانت الدولة المختارة هي العراق
                    if (this.selectedCountry.code === '+964') {
                        // إذا بدأ الرقم بـ '0'، قم بحذفه
                        if (value.startsWith('0')) {
                            this.$nextTick(() => { this.localPhone = value.substring(1); });
                        }
                    }
                });
            },

            toggleCountryMenu() {
                this.countryMenuOpen = !this.countryMenuOpen;
            },
            selectCountry(country) {
                this.selectedCountry = country;
                this.countryMenuOpen = false;
            }
        }"
        x-init="init()"
        @click.away="countryMenuOpen = false"
    >
        <div class="py-8 px-6 md:px-8">
            <h2 class="text-2xl font-bold text-center text-brand-text mb-2">إنشاء حساب جديد</h2>
            <p class="text-center text-sm text-gray-600 mb-6">انضمي إلى عالم كوزميليا</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- الاسم الكامل -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">الاسم الكامل</label>
                    <input id="name" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary @error('name') border-red-500 @enderror"
                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رقم الهاتف مع اختيار الدولة -->
                <div class="mb-4 relative">
                    <label for="local_phone_number" class="block text-gray-700 text-sm font-bold mb-2">رقم الهاتف</label>
                    <div class="phone-input-group">
                        <button type="button" class="country-code-btn" @click="toggleCountryMenu()">
                            <span x-text="selectedCountry.flag"></span>
                            <span class="font-semibold text-gray-600" x-text="selectedCountry.code"></span>
                            <i class="bi bi-chevron-down text-xs"></i>
                        </button>
                        <input id="local_phone_number" type="tel" class="phone-input"
                            name="local_phone_number"
                            x-model="localPhone"
                            required
                            autocomplete="tel"
                            :placeholder="selectedCountry.code === '+964' ? 'مثال: 7712345678' : 'أدخل رقم الهاتف'"
                            :maxlength="selectedCountry.code === '+964' ? 10 : 15"
                            :pattern="selectedCountry.code === '+964' ? '7[0-9]{9}' : null"
                            title="للرقم العراقي، أدخل 10 أرقام تبدأ بالرقم 7."
                        >
                    </div>
                    <!-- حقل مخفي يحمل الرقم الكامل -->
                    <input type="hidden" name="phone_number" :value="selectedCountry.code.replace('+', '') + localPhone">
                    
                    <p class="text-xs text-gray-500 mt-2">سيتم إرسال رمز تحقق عبر واتساب إلى هذا الرقم لتفعيل حسابك.</p>

                    <!-- ملاحظة تظهر فقط عند اختيار العراق -->
                    <p x-show="selectedCountry.code === '+964'" class="text-xs text-blue-600 mt-1" style="display: none;">
                        ملاحظة: أدخل الرقم المكون من 10 أرقام بدون الصفر في البداية (مثال: 7712345678).
                    </p>

                    <div x-show="countryMenuOpen" class="country-list" x-transition style="display: none;">
                        <template x-for="country in countries" :key="country.code">
                            <a href="#" role="option" tabindex="0" @click.prevent="selectCountry(country)">
                                <span class="text-xl" x-text="country.flag"></span>
                                <span class="flex-grow text-right" x-text="country.name"></span>
                                <span class="text-gray-500" x-text="country.code"></span>
                            </a>
                        </template>
                    </div>

                    @error('phone_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">كلمة المرور</label>
                    <input id="password" type="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary @error('password') border-red-500 @enderror"
                        name="password" required autocomplete="new-password">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تأكيد كلمة المرور -->
                <div class="mb-6">
                    <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">تأكيد كلمة المرور</label>
                    <input id="password-confirm" type="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        name="password_confirmation" required autocomplete="new-password">
                </div>

                <!-- زر إنشاء الحساب -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-brand-primary text-white font-bold py-3 px-4 rounded-md hover:bg-brand-dark transition duration-300">
                        إنشاء الحساب
                    </button>
                </div>

                <div class="text-center text-sm">
                    <p class="text-gray-600">
                        لديك حساب بالفعل؟
                        <a class="font-bold text-brand-primary hover:underline" href="{{ route('login') }}">
                            تسجيل الدخول
                        </a>
                    </p>
                </div>

                {{-- زر التواصل عبر واتساب --}}
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
@endsection
