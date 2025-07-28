<!DOCTYPE html>
@php
    $locale = app()->getLocale();
    $dir = in_array($locale, ['ar', 'ku']) ? 'rtl' : 'ltr';
@endphp
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('layout.cosmelea') . ' | Cosmelea')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "brand-bg": "#FFFFFF",
                        "brand-primary": "#D1A3A4",
                        "brand-secondary": "#F3E5E3",
                        "brand-dark": "#34282C",
                        "brand-text": "#34282C",
                        "brand-accent": "#BE6661",
                        "brand-gray": "#6B7280",
                    },
                    animation: {
                        'heartbeat': 'heartbeat 1.5s ease-in-out infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'ping-once': 'ping 1s cubic-bezier(0, 0, 0.2, 1)',
                    },
                    keyframes: {
                        heartbeat: {
                            '0%, 100%': { transform: 'scale(1)' },
                            '50%': { transform: 'scale(1.2)' },
                        }
                    }
                },
            },
        };
    </script>

    <style>
        body {
            font-family: "Cairo", sans-serif;
            background-color: #F9F5F1;
            scroll-behavior: smooth;
            line-height: 1;
            margin: 0;
            padding: 0;
        }
        header {
            margin-bottom: 0 !important;
            padding-bottom: 5 !important;
        }
        main, section {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .footer-mobile {
            background-color: #FFFFFF;
            border-top: 1px solid #E5E7EB;
        }
        .footer-mobile a {
            transition: all 0.3s ease;
        }
        .footer-mobile a.active {
            color: #BE6661;
            font-weight: bold;
        }
        .footer-mobile .icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }
        .footer-mobile .label {
            font-size: 0.75rem;
        }
        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #BE6661;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
    @stack("styles")
</head>

<body
    class="relative flex flex-col min-h-screen pb-20 md:pb-0"
    x-data="{
        wishlistCount: {{ auth()->check() ? auth()->user()->favorites()->count() : 0 }},
        cartCount: {{ count(session('cart', [])) }},
        isWishlistUpdated: false,
        isCartUpdated: false,
        showWelcome: !sessionStorage.getItem('welcomeScreenShown'),
        
        closeWelcomeModal() {
            this.showWelcome = false;
            sessionStorage.setItem('welcomeScreenShown', 'true');
        }
    }"
    @wishlist-updated.window="wishlistCount = $event.detail.count; isWishlistUpdated = true; setTimeout(() => isWishlistUpdated = false, 500)"
    @cart-updated.window="cartCount = $event.detail.cartCount; isCartUpdated = true; setTimeout(() => isCartUpdated = false, 500)"
    x-init="
        fetch('{{ route('cart.count') }}')
            .then(res => res.json())
            .then(data => { cartCount = data.count; })
            .catch(() => {});
        @auth
        fetch('{{ route('wishlist.count') }}')
            .then(res => res.json())
            .then(data => { wishlistCount = data.count; })
            .catch(() => {});
        @endauth
    "
>

    <div class="sticky top-0 z-50">
        @if(isset($show_dashboard_notification) && $show_dashboard_notification == 'on' && !empty($dashboard_notification_content))
        <div x-data="{ show: true }" x-show="show" x-transition class="bg-brand-dark text-white text-center p-2 text-sm relative">
            <div class="container mx-auto">
                {!! $dashboard_notification_content !!}
            </div>
            <button @click="show = false" class="absolute top-1/2 left-4 transform -translate-y-1/2 text-xl">&times;</button>
        </div>
        @endif

        <header class="bg-[#be6661] py-3 shadow-md">
            <div class="container mx-auto hidden md:flex items-center justify-between px-4 md:px-8 text-white font-semibold">
                <a href="{{ route('homepage') }}" class="text-xl sm:text-2xl flex items-center gap-2 hover:opacity-90 transition">
                    <img src="https://cosmelea.com/storage/logo-white.png" alt="logo" class="w-10 h-10">
                    <span class="text-white font-bold">{{ __('layout.cosmelea') }}</span>
                </a>
                <form action="{{ route('products.search') }}" method="GET" class="flex flex-1 mx-6 max-w-2xl">
                    <div class="flex w-full bg-white rounded-full overflow-hidden">
                        <input type="text" name="query" placeholder="{{ __('layout.search_placeholder') }}"
                               class="flex-1 px-4 py-2 text-sm text-gray-700 placeholder-gray-500 focus:outline-none">
                        <button type="submit" class="px-4 bg-white text-[#be6661] hover:text-[#cd8985]">
                            <i class="bi bi-search text-lg"></i>
                        </button>
                    </div>
                </form>
                <div class="hidden md:flex items-center gap-4 text-white">
                    <div class="flex gap-2">
                        <a href="{{ route('lang.switch', 'ar') }}" class="hover:underline {{ app()->getLocale() == 'ar' ? 'font-bold' : '' }}">AR</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="hover:underline {{ app()->getLocale() == 'en' ? 'font-bold' : '' }}">EN</a>
                        <a href="{{ route('lang.switch', 'ku') }}" class="hover:underline {{ app()->getLocale() == 'ku' ? 'font-bold' : '' }}">KU</a>
                    </div>
                    @auth
                    <a href="{{ route('profile.show') }}" class="hover:opacity-80 transition relative group">
                        <i class="bi bi-person text-xl"></i>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="hover:underline hover:text-[#f9f5f1] transition text-sm">
                        {{ __('layout.login_register') }}
                    </a>
                    @endauth
                    <a href="{{ route('cart.index') }}" class="hover:opacity-80 transition relative">
                        <i class="bi bi-bag text-xl"></i>
                        <span x-show="cartCount > 0" x-text="cartCount"
                              class="badge"
                              :class="{'animate-ping-once': isCartUpdated}" style="display: none;"></span>
                    </a>
                    <a href="{{ route('wishlist') }}" class="hover:opacity-80 transition relative" x-ref="wishlistCounter">
                        <i class="bi bi-heart text-xl"></i>
                        <span x-show="wishlistCount > 0" x-text="wishlistCount"
                              class="badge"
                              :class="{'animate-ping-once': isWishlistUpdated}" style="display: none;"></span>
                    </a>
                </div>
            </div>
            <div class="container mx-auto md:hidden flex flex-col items-center gap-3 px-4">
                 <a href="{{ route('homepage') }}" class="text-xl sm:text-2xl flex items-center gap-2 hover:opacity-90 transition text-white font-bold">
                    <img src="https://cosmelea.com/storage/logo-white.png" alt="logo" class="w-8 h-8">
                    <span>{{ __('layout.cosmelea') }}</span>
                </a>
                 <form action="{{ route('products.search') }}" method="GET" class="w-full">
                    <div class="flex w-full bg-white rounded-full overflow-hidden">
                        <input type="text" name="query" placeholder="{{ __('layout.search_placeholder') }}"
                               class="flex-1 px-4 py-2 text-sm text-gray-700 placeholder-gray-500 focus:outline-none">
                        <button type="submit" class="px-4 bg-white text-[#be6661] hover:text-[#cd8985]">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </header>
    </div>

    <main class="flex-grow">
        <section class="bg-white pt-4 pb-0 mt-0 overflow-hidden">
            @yield('content')
        </section>
    </main>

    <footer class="bg-[#f9f5f1] text-[#34282C] py-12 border-t border-[#eadbcd]">
        <div class="container mx-auto px-4 grid grid-cols-1 gap-10 text-center md:text-right md:grid-cols-4">
            <div class="md:col-span-2 flex flex-col items-center md:items-start">
                <a href="{{ route('homepage') }}" class="flex items-center justify-center md:justify-start gap-2 text-2xl font-bold text-[#be6661] mb-4">
                    <img src="https://cosmelea.com/storage/logo-black.png" alt="Cosmelea Logo" class="w-12 h-12">
                    {{ __('layout.cosmelea') }}
                </a>
                <p class="leading-relaxed text-sm text-[#6B7280] max-w-md">
                    وجهتكِ الأولى لمنتجات التجميل الأصلية والعناية الفاخرة. نؤمن أن جمالكِ يستحق الأفضل دائمًا.
                </p>
                <div class="flex gap-4 text-[#be6661] text-2xl mt-4 justify-center md:justify-start">
                    <a href="#" class="hover:text-[#cd8985] transition-colors"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="hover:text-[#cd8985] transition-colors"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="hover:text-[#cd8985] transition-colors"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-[#be6661] mb-4">روابط سريعة</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('homepage') }}" class="hover:text-[#cd8985]">{{ __('layout.home') }}</a></li>
                    <li><a href="{{ route('shop') }}" class="hover:text-[#cd8985]">{{ __('layout.shop') }}</a></li>
                    <li><a href="{{ route('wishlist') }}" class="hover:text-[#cd8985]">{{ __('layout.wishlist') }}</a></li>
                    <li><a href="{{ route('profile.show') }}" class="hover:text-[#cd8985]">{{ __('layout.my_account') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-[#be6661] mb-4">{{ __('layout.information') }}</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('about.us') }}" class="hover:text-[#cd8985]">{{ __('layout.about_us') }}</a></li>
                    <li><a href="{{ route('privacy.policy') }}" class="hover:text-[#cd8985]">{{ __('layout.privacy_policy') }}</a></li>
                    <li><a href="{{ route('order.method') }}" class="hover:text-[#cd8985]">{{ __('layout.how_to_order') }}</a></li>
                    <li><a href="#" class="hover:text-[#cd8985]">{{ __('layout.contact_us') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-12 text-center text-xs text-[#6B7280] border-t border-[#eadbcd] pt-6">
            &copy; {{ date("Y") }} جميع الحقوق محفوظة لـ <a href="{{ route('homepage') }}" class="font-bold text-[#be6661] hover:text-[#cd8985]">Cosmelea</a>
        </div>
    </footer>

    <footer class="fixed bottom-0 left-0 right-0 footer-mobile z-50 shadow-inner md:hidden" x-data>
        <div class="grid grid-cols-5 text-center text-sm text-[#be6661] font-semibold">
            <!-- الرئيسية -->
            <a href="{{ route('homepage') }}" 
               class="py-3 flex flex-col items-center hover:bg-[#f9f5f1] {{ request()->routeIs('homepage') ? 'bg-[#ad574e] text-white font-bold hover:bg-[#be6661]' : '' }}">
                <i class="bi bi-house-door text-xl"></i>
                <span class="text-xs mt-1">{{ __('layout.home') }}</span>
            </a>
            <!-- المتجر -->
            <a href="{{ route('shop') }}" 
               class="py-3 flex flex-col items-center hover:bg-[#f9f5f1] {{ request()->routeIs('shop') ? 'bg-[#ad574e] text-white font-bold hover:bg-[#be6661]' : '' }}">
                <i class="bi bi-grid text-xl"></i>
                <span class="text-xs mt-1">{{ __('layout.shop') }}</span>
            </a>
            <!-- السلة -->
            <a href="{{ route('cart.index') }}" 
               class="py-3 flex flex-col items-center hover:bg-[#f9f5f1] {{ request()->routeIs('cart.index') ? 'bg-[#ad574e] text-white font-bold hover:bg-[#be6661]' : '' }}">
                <div class="relative">
                    <i class="bi bi-bag text-xl"></i>
                    <span x-show="cartCount > 0" x-text="cartCount" class="badge" :class="{'animate-ping-once': isCartUpdated}" style="display: none;"></span>
                </div>
                <span class="text-xs mt-1">{{ __('layout.cart') }}</span>
            </a>
            <!-- الاقسام -->
            <a href="{{ route('categories.index') }}" 
               class="py-3 flex flex-col items-center hover:bg-[#f9f5f1] {{ request()->routeIs('categories.index') ? 'bg-[#ad574e] text-white font-bold hover:bg-[#be6661]' : '' }}">
                <i class="bi bi-grid-3x3-gap text-xl"></i>
                <span class="text-xs mt-1">{{ __('layout.categories') }}</span>
            </a>
            <!-- حسابي -->
            <a href="{{ route('profile.show') }}" 
               class="py-3 flex flex-col items-center hover:bg-[#f9f5f1] {{ request()->routeIs('profile.show') ? 'bg-[#ad574e] text-white font-bold hover:bg-[#be6661]' : '' }}">
                <i class="bi bi-person text-xl"></i>
                <span class="text-xs mt-1">{{ __('layout.my_account') }}</span>
            </a>
        </div>
    </footer>

    @if(isset($show_welcome_screen) && $show_welcome_screen == 'on' && !empty($welcome_screen_content))
    <div x-show="showWelcome" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" 
         style="background-color: rgba(0,0,0,0.5); display: none;"
         @keydown.escape.window="closeWelcomeModal()">
        
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col relative" @click.away="closeWelcomeModal()">
            <button @click="closeWelcomeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-3xl leading-none z-10">&times;</button>
            
            <div class="p-6 overflow-y-auto prose max-w-none">
                {!! $welcome_screen_content !!}
            </div>
        </div>
    </div>
    @endif

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    @stack('scripts')
</body>
</html>