@extends('layouts.app')
@section('title', 'ملفي الشخصي')
@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- عنوان الصفحة -->
    <h1 class="text-2xl font-bold text-[#4a3f3f] mb-6 text-center md:text-right">حسابي الشخصي</h1>
    
    <div class="flex flex-col gap-6">
        <!-- شريط التنقل للجوال - أفقي -->
        <nav class="md:hidden bg-white rounded-lg shadow-sm border border-[#eadbcd] sticky top-0 z-10">
            <div class="flex overflow-x-auto scrollbar-hide px-2 py-2">
                <a href="{{ route('profile.show') }}" 
                   class="flex flex-col items-center min-w-[80px] px-3 py-2 rounded-md transition-colors {{ request()->routeIs('profile.show') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1]' }}">
                    <i class="bi bi-person-fill text-xl mb-1"></i>
                    <span class="text-xs">الملف</span>
                </a>
                <a href="{{ route('profile.orders') }}" 
                   class="flex flex-col items-center min-w-[80px] px-3 py-2 rounded-md transition-colors {{ request()->routeIs('profile.orders*') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1]' }}">
                    <i class="bi bi-box-seam text-xl mb-1"></i>
                    <span class="text-xs">الطلبات</span>
                </a>
                <a href="{{ route('profile.addresses.index') }}" 
                   class="flex flex-col items-center min-w-[80px] px-3 py-2 rounded-md transition-colors {{ request()->routeIs('profile.addresses*') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1]' }}">
                    <i class="bi bi-geo-alt-fill text-xl mb-1"></i>
                    <span class="text-xs">العناوين</span>
                </a>
                <a href="{{ route('wishlist') }}" 
                   class="flex flex-col items-center min-w-[80px] px-3 py-2 rounded-md transition-colors {{ request()->routeIs('wishlist') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1]' }}">
                    <i class="bi bi-heart-fill text-xl mb-1 {{ request()->routeIs('wishlist') ? 'text-white' : 'text-[#cd8985]' }}"></i>
                    <span class="text-xs">المفضلة</span>
                </a>
                <a href="{{ route('logout') }}" 
                   class="flex flex-col items-center min-w-[80px] px-3 py-2 rounded-md transition-colors text-red-600 hover:bg-red-50"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right text-xl mb-1"></i>
                    <span class="text-xs">خروج</span>
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </nav>

        <!-- محتوى الصفحة الرئيسي -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- الشريط الجانبي للكمبيوتر -->
            <aside class="hidden md:block w-full md:w-1/4">
                <nav class="flex flex-col justify-between h-full bg-white p-4 rounded-lg shadow-sm border border-[#eadbcd] sticky top-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('profile.show') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-md font-medium transition-colors {{ request()->routeIs('profile.show') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1] hover:text-[#cd8985]' }}">
                                <i class="bi bi-person-fill"></i>
                                <span>ملفي الشخصي</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.orders') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-md font-medium transition-colors {{ request()->routeIs('profile.orders*') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1] hover:text-[#cd8985]' }}">
                                <i class="bi bi-box-seam"></i>
                                <span>طلباتي</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.addresses.index') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-md font-medium transition-colors {{ request()->routeIs('profile.addresses*') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1] hover:text-[#cd8985]' }}">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>عناوين الشحن</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('wishlist') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-md font-medium transition-colors {{ request()->routeIs('wishlist') ? 'bg-[#cd8985] text-white' : 'text-[#4a3f3f] hover:bg-[#f9f5f1] hover:text-[#cd8985]' }}">
                                <i class="bi bi-heart-fill {{ request()->routeIs('wishlist') ? 'text-white' : 'text-[#cd8985]' }}"></i>
                                <span>المفضلة</span>
                            </a>
                        </li>
                    </ul>
                    <div class="mt-6 pt-4 border-t border-[#eadbcd]">
                        <a href="{{ route('logout') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-md font-medium text-red-600 border border-red-500 hover:bg-red-50 transition-colors"
                           onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>تسجيل الخروج</span>
                        </a>
                        <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </nav>
            </aside>

            <!-- المحتوى الرئيسي -->
            <main class="w-full md:w-3/4 bg-white rounded-lg shadow-sm border border-[#eadbcd] p-4 md:p-6">
                @yield('profile-content')
            </main>
        </div>
    </div>
</div>

<style>
    /* تحسينات للعرض على الجوال */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        /* إخفاء شريط التمرير الأفقي */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    }
</style>
@endsection