<header class="bg-[#be6661] py-3 shadow-md sticky top-0 z-50" 
        x-data="{ 
            userMenuOpen: false, 
            langMenuOpen: false, 
            wishlistCount: {{ Auth::check() ? Auth::user()->favorites()->count() : 0 }},
            cartCount: {{ \App\Http\Controllers\CartController::getCartCount() }},
            isWishlistUpdated: false,
            isCartUpdated: false
        }"
        @wishlist-updated.window="wishlistCount = $event.detail.count; isWishlistUpdated = true; setTimeout(() => isWishlistUpdated = false, 1000)"
        @cart-updated.window="cartCount = $event.detail.cartCount; isCartUpdated = true; setTimeout(() => isCartUpdated = false, 1000)">
    <div class="container mx-auto flex items-center justify-between px-4 md:px-8 text-white font-semibold">
        {{-- الشعار --}}
        <a href="{{ route('home') }}" class="text-2xl flex items-center gap-2 hover:opacity-90 transition">
            <img src="{{ asset('logo.svg') }}" alt="logo" class="w-8 h-8">
            <span class="text-white text-2xl font-bold">الريان</span>
        </a>
        {{-- شريط البحث --}}
        <form action="{{ route('shop') }}" method="GET" class="flex-1 mx-6 hidden md:flex max-w-2xl">
            <div class="flex w-full bg-white rounded-full overflow-hidden shadow-sm">
                <input type="text" name="q" placeholder="ابحث عن منتجات أو علامات تجارية"
                       class="flex-1 px-4 py-2 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#be6661]">
                <button type="submit" class="px-4 bg-white text-[#be6661] hover:text-[#cd8985] transition">
                    <i class="bi bi-search text-lg"></i>
                </button>
            </div>
        </form>
        {{-- القائمة اليمنى --}}
        <div class="flex items-center gap-4 text-white">
            {{-- اللغة --}}
            <div class="relative">
                <button @click="langMenuOpen = !langMenuOpen" class="hover:opacity-80 transition flex items-center gap-1">
                    @php
                        $currentLocale = app()->getLocale();
                        $flagMap = ['en' => 'us', 'ar' => 'iq', 'ku' => 'iq'];
                    @endphp
                    <img src="/images/flags/{{ $flagMap[$currentLocale] ?? 'us' }}.svg" class="w-6 h-6 inline-block" alt="{{ strtoupper($currentLocale) }}">
                    <span class="text-sm hidden sm:inline">{{ strtoupper($currentLocale) }}</span>
                </button>
                <div x-show="langMenuOpen" @click.away="langMenuOpen = false"
                     class="absolute right-0 mt-2 w-40 bg-white text-[#4a3f3f] border border-[#eadbcd] rounded-md shadow-lg py-2 text-sm z-50"
                     x-transition>
                    <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 hover:bg-[#f9f5f1]">العربية</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 hover:bg-[#f9f5f1]">English</a>
                    <a href="{{ route('lang.switch', 'ku') }}" class="block px-4 py-2 hover:bg-[#f9f5f1]">کوردی</a>
                </div>
            </div>
            {{-- تسجيل الدخول أو الحساب --}}
            @auth
            <div class="relative">
                <button @click="userMenuOpen = !userMenuOpen" class="hover:opacity-80 transition flex items-center gap-1">
                    <i class="bi bi-person-circle text-xl"></i>
                    <span class="text-sm hidden sm:inline">حسابي</span>
                </button>
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                     class="absolute right-0 mt-2 w-48 bg-white text-[#4a3f3f] border border-[#eadbcd] rounded-md shadow-lg py-2 text-sm z-50"
                     x-transition>
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:bg-[#f9f5f1]">الملف الشخصي</a>
                    @if(auth()->user()?->hasRole('Super-Admin'))
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-[#f9f5f1]">لوحة التحكم</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-right px-4 py-2 hover:bg-[#f9f5f1]">تسجيل الخروج</button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="hover:underline hover:text-[#f9f5f1] transition text-sm">
                تسجيل الدخول أو الاشتراك
            </a>
            @endauth
            {{-- السلة --}}
            <div class="relative">
                <a href="{{ route('cart.index') }}" class="hover:opacity-80 transition relative">
                    <i class="bi bi-bag text-xl"></i>
                    <span x-text="cartCount"
                          x-show="cartCount > 0"
                          class="absolute -top-1 -right-2 bg-white text-[#be6661] text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full"
                          :class="{'animate-ping': isCartUpdated}">
                    </span>
                </a>
            </div>
            {{-- الاقسام --}}
            <div class="relative">
                <a href="https://cosmele.com/categories" class="hover:opacity-80 transition flex items-center gap-1">
                    <i class="bi bi-grid text-xl"></i>
                    <span class="text-sm hidden sm:inline">الاقسام</span>
                </a>
            </div>
        </div>
    </div>
</header>