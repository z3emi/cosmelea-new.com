@php
    // This defines the variable as an empty array if it doesn't exist, preventing errors.
    $favoriteProductIds = $favoriteProductIds ?? [];
@endphp

@extends('layouts.app')

@section('title', 'كوزميليا | وجهتك الأولى للجمال')

@push('styles')
<style>
    :root {
        --primary-color: #cd8985;
        --primary-hover: #be6661;
        --secondary-color: #eadbcd;
        --dark-color: #3a3a3a;
        --light-color: #f8f5f2;
    }
    .hero-slider { height: 600px; border-radius: 0 !important; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    @media (max-width: 768px) { .hero-slider { height: 400px; } }
    .hero-overlay { background: linear-gradient(135deg, rgba(205, 137, 133, 0.5), rgba(190, 102, 97, 0.5)); }
    .hero-content { backdrop-filter: blur(1px); }
    .product-card { transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border-radius: 12px; background-color: #f9f5f1; overflow: hidden; border: 2px solid transparent; }
    .product-card:hover { box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); transform: translateY(-8px); }
    .product-image-container {aspect-ratio: 1 / 1; transition: transform 0.5s ease;}
    .product-card:hover .product-image-container { transform: scale(1.03); }
    .btn-primary { background-color: var(--primary-color); color: white; border: none; transition: all 0.3s ease; }
    .btn-primary:hover { background-color: var(--primary-hover); transform: translateY(-2px); }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    /* Grid for 7 items per row */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 1280px) { /* xl screens */
        .products-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }
    @media (max-width: 1024px) { /* lg screens */
        .products-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) { /* sm screens */
        .products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    .sale-product-highlight {
        border-color: #FFD700; /* Gold color */
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
    }
</style>
@endpush

@section('content')

{{-- Hero Section --}}
<section class="relative w-full h-[300px] sm:h-[600px] md:h-screen overflow-hidden shadow-xl my-8 hero-slider">
    <div class="absolute inset-0 overflow-hidden">
        <iframe src="https://player.vimeo.com/video/1101778381?autoplay=1&muted=1&loop=1&background=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" class="absolute top-1/2 left-1/2 w-[160%] h-[160%] -translate-x-1/2 -translate-y-1/2" style="pointer-events:none; border:none;"></iframe>
    </div>
    <div class="absolute inset-0 hero-overlay flex items-center justify-center px-4 z-10">
        <div class="text-white text-center max-w-xl px-4 hero-content">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight drop-shadow-lg">لمسة جمالك تبدأ هنا</h1>
            <p class="text-lg sm:text-xl mb-8 drop-shadow-md">ألوان ناعمة، أناقة خالدة، وتسوق سهل</p>
            <a href="{{ route('shop') }}" class="bg-white text-[#be6661] px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-md">تسوقي الآن</a>
        </div>
    </div>
</section>

{{-- Categories Section --}}
<section class="py-16 bg-white" x-data="{ scrollContainer: null }" x-init="scrollContainer = $refs.catScroll">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-brand-dark mb-12">تصفحي حسب الفئة</h2>
        <div class="relative">
            <button @click="scrollContainer.scrollBy({ left: -300, behavior: 'smooth' })" class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white/80 text-brand-primary w-10 h-10 rounded-full flex items-center justify-center shadow-md hover:bg-brand-primary hover:text-white transition">
                <i class="bi bi-chevron-left text-lg"></i>
            </button>
            <div class="overflow-x-auto no-scrollbar px-12" x-ref="catScroll" style="height: 150px;">
                <div class="flex flex-row gap-8 justify-start items-center w-max">
                    <a href="{{ route('shop', ['on_sale' => 'true']) }}" class="flex flex-col items-center min-w-[100px] group text-center">
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-red-500 group-hover:border-brand-dark shadow-lg bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center transition-all duration-300">
                            <i class="bi bi-percent text-4xl text-white transition-transform duration-300 group-hover:scale-110"></i>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-brand-dark whitespace-nowrap">عروض مميزة</h3>
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('shop', ['category' => $category->slug]) }}" class="flex flex-col items-center min-w-[100px] group text-center">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-brand-light group-hover:border-brand-primary shadow-md bg-white relative transition-all duration-300">
                                <img src="{{ asset('storage/' . $category->image) }}" class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <h3 class="mt-3 text-sm font-semibold text-brand-dark whitespace-nowrap">{{ $category->name_ar }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
            <button @click="scrollContainer.scrollBy({ left: 300, behavior: 'smooth' })" class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white/80 text-brand-primary w-10 h-10 rounded-full flex items-center justify-center shadow-md hover:bg-brand-primary hover:text-white transition">
                <i class="bi bi-chevron-right text-lg"></i>
            </button>
        </div>
    </div>
</section>

{{-- New Products Section --}}
@if($newProducts->isNotEmpty())
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-brand-dark">جديد في متجرنا</h2>
            <a href="{{ route('shop') }}" class="text-brand-primary font-semibold hover:underline">عرض الكل <i class="bi bi-arrow-left-short"></i></a>
        </div>
        <div class="products-grid">
            @foreach($newProducts->take(14) as $product)
                <div class="product-card @if($product->sale_price && $product->sale_price > 0) sale-product-highlight @endif" x-data="{
                    added: false,
                    loadingAdd: false,
                    isFavorite: {{ in_array($product->id, $favoriteProductIds ?? []) ? 'true' : 'false' }},
                    loadingFav: false
                }">
                    <a href="{{ route('product.detail', $product) }}" class="relative block">
                        <div class="product-image-container">
                            @if ($product->firstImage)
                                <img src="{{ asset('storage/' . $product->firstImage->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://placehold.co/400x400?text=No+Image" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if($product->sale_price && $product->sale_price > 0)
                            @php
                                $discountPercentage = round((($product->price - $product->sale_price) / $product->price) * 100);
                            @endphp
                            <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                -{{ $discountPercentage }}%
                            </div>
                        @endif
                    </a>
                    <div class="p-4 text-right">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-lg text-brand-dark">{{ $product->name_ar }}</h3>
                            @auth
                            <button 
                                @click.prevent="
                                    loadingFav = true;
                                    fetch('{{ url('/wishlist/toggle-async') }}/{{ $product->id }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            isFavorite = !isFavorite;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                        }
                                    })
                                    .finally(() => loadingFav = false);
                                "
                                class="text-gray-400 hover:text-red-500 transition-colors"
                                :class="{ 'text-red-500': isFavorite }"
                                :disabled="loadingFav"
                            >
                                <i class="bi text-2xl" :class="isFavorite ? 'bi-heart-fill' : 'bi-heart'"></i>
                            </button>
                            @endauth
                        </div>
                        <div class="mb-3">
                            @if($product->sale_price && $product->sale_price > 0)
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($product->sale_price, 0) }} د.ع</span>
                                <span class="text-gray-400 line-through text-sm ml-2">{{ number_format($product->price, 0) }} د.ع</span>
                            @else
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($product->price, 0) }} د.ع</span>
                            @endif
                        </div>
                        <button 
                            @click.prevent="
                                loadingAdd = true;
                                fetch('{{ route('cart.store') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.success) {
                                        added = true;
                                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { cartCount: data.cartCount } }));
                                        setTimeout(() => added = false, 2000);
                                    } else {
                                        alert(data.message || 'حدث خطأ ما.');
                                    }
                                })
                                .finally(() => loadingAdd = false);
                            "
                            class="btn-primary w-full py-2 rounded-lg flex justify-center items-center gap-2 transition-all"
                            :disabled="loadingAdd || added"
                        >
                            <span x-show="!added && !loadingAdd"><i class="bi bi-cart-plus"></i> أضف للسلة</span>
                            <span x-show="loadingAdd"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                            <span x-show="added"><i class="bi bi-check-lg"></i> تمت الإضافة</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Sale Products Section --}}
@if($saleProducts->isNotEmpty())
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-brand-dark">عروض مميزة</h2>
            <a href="{{ route('shop', ['on_sale' => 'true']) }}" class="text-brand-primary text-sm font-semibold hover:underline">عرض الكل <i class="bi bi-arrow-left-short"></i></a>
        </div>
        <div class="products-grid">
            @foreach($saleProducts->take(14) as $product)
                <div class="product-card sale-product-highlight" x-data="{
                    added: false,
                    loadingAdd: false,
                    isFavorite: {{ in_array($product->id, $favoriteProductIds ?? []) ? 'true' : 'false' }},
                    loadingFav: false
                }">
                    <a href="{{ route('product.detail', $product) }}" class="relative block">
                        <div class="product-image-container">
                           @if ($product->firstImage)
                                <img src="{{ asset('storage/' . $product->firstImage->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://placehold.co/400x400?text=No+Image" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if($product->sale_price && $product->sale_price > 0)
                            @php
                                $discountPercentage = round((($product->price - $product->sale_price) / $product->price) * 100);
                            @endphp
                            <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                -{{ $discountPercentage }}%
                            </div>
                        @endif
                    </a>
                    <div class="p-4 text-right">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-lg text-brand-dark">{{ $product->name_ar }}</h3>
                            @auth
                            <button @click.prevent="
                                    loadingFav = true;
                                    fetch('{{ url('/wishlist/toggle-async') }}/{{ $product->id }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            isFavorite = !isFavorite;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                        }
                                    })
                                    .finally(() => loadingFav = false);
                                " class="text-gray-400 hover:text-red-500 transition-colors" :class="{ 'text-red-500': isFavorite }" :disabled="loadingFav">
                                <i class="bi text-2xl" :class="isFavorite ? 'bi-heart-fill' : 'bi-heart'"></i>
                            </button>
                            @endauth
                        </div>
                        <div class="mb-3">
                            @if($product->sale_price && $product->sale_price > 0)
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($product->sale_price, 0) }} د.ع</span>
                                <span class="text-gray-400 line-through text-sm ml-2">{{ number_format($product->price, 0) }} د.ع</span>
                            @else
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($product->price, 0) }} د.ع</span>
                            @endif
                        </div>
                        <button @click.prevent="
                                loadingAdd = true;
                                fetch('{{ route('cart.store') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.success) {
                                        added = true;
                                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { cartCount: data.cartCount } }));
                                        setTimeout(() => added = false, 2000);
                                    } else {
                                        alert(data.message || 'حدث خطأ ما.');
                                    }
                                })
                                .finally(() => loadingAdd = false);
                            " class="btn-primary w-full py-2 rounded-lg flex justify-center items-center gap-2 transition-all" :disabled="loadingAdd || added">
                            <span x-show="!added && !loadingAdd"><i class="bi bi-cart-plus"></i> أضف للسلة</span>
                            <span x-show="loadingAdd"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                            <span x-show="added"><i class="bi bi-check-lg"></i> تمت الإضافة</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Best Selling Products Section --}}
@if($bestSellingProducts->isNotEmpty())
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-baseline gap-4 mb-6">
            <h2 class="text-3xl font-bold text-brand-dark">الأكثر مبيعاً</h2>
            <a href="{{ route('shop') }}" class="text-brand-primary text-sm font-semibold hover:underline">عرض الكل <i class="bi bi-arrow-left-short"></i></a>
        </div>
        <div class="products-grid">
            @foreach($bestSellingProducts->take(14) as $product)
                <div class="product-card @if($product->sale_price && $product->sale_price > 0) sale-product-highlight @endif" x-data="{
                    added: false,
                    loadingAdd: false,
                    isFavorite: {{ in_array($product->id, $favoriteProductIds ?? []) ? 'true' : 'false' }},
                    loadingFav: false
                }">
                    <a href="{{ route('product.detail', $product) }}" class="relative block">
                        <div class="product-image-container">
                            @if ($product->firstImage)
                                <img src="{{ asset('storage/' . $product->firstImage->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://placehold.co/400x400?text=No+Image" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if($product->sale_price && $product->sale_price > 0)
                            @php
                                $discountPercentage = round((($product->price - $product->sale_price) / $product->price) * 100);
                            @endphp
                            <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                -{{ $discountPercentage }}%
                            </div>
                        @endif
                    </a>
                    <div class="p-4 text-right">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-lg text-brand-dark">{{ $product->name_ar }}</h3>
                            @auth
                            <button @click.prevent="
                                    loadingFav = true;
                                    fetch('{{ url('/wishlist/toggle-async') }}/{{ $product->id }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            isFavorite = !isFavorite;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                        }
                                    })
                                    .finally(() => loadingFav = false);
                                " class="text-gray-400 hover:text-red-500 transition-colors" :class="{ 'text-red-500': isFavorite }" :disabled="loadingFav">
                                <i class="bi text-2xl" :class="isFavorite ? 'bi-heart-fill' : 'bi-heart'"></i>
                            </button>
                            @endauth
                        </div>
                        <div class="mb-3">
                            @if($product->sale_price && $product->sale_price > 0)
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($product->sale_price, 0) }} د.ع</span>
                                <span class="text-gray-400 line-through text-sm ml-2">{{ number_format($product->price, 0) }} د.ع</span>
                            @else
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($product->price, 0) }} د.ع</span>
                            @endif
                        </div>
                        <button @click.prevent="
                                loadingAdd = true;
                                fetch('{{ route('cart.store') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.success) {
                                        added = true;
                                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { cartCount: data.cartCount } }));
                                        setTimeout(() => added = false, 2000);
                                    } else {
                                        alert(data.message || 'حدث خطأ ما.');
                                    }
                                })
                                .finally(() => loadingAdd = false);
                            " class="btn-primary w-full py-2 rounded-lg flex justify-center items-center gap-2 transition-all" :disabled="loadingAdd || added">
                           <span x-show="!added && !loadingAdd"><i class="bi bi-cart-plus"></i> أضف للسلة</span>
                           <span x-show="loadingAdd"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                           <span x-show="added"><i class="bi bi-check-lg"></i> تمت الإضافة</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
// This AlpineJS function was for a slideshow that is no longer in the Hero section.
// It can be safely removed if you are not using it elsewhere.
function slideshow() {
    return {
        slides: [
            '{{ asset("storage/slideshow1.jpg") }}',
            '{{ asset("storage/slideshow2.jpg") }}',
            '{{ asset("storage/slideshow3.jpg") }}',
        ],
        currentIndex: 0,
        init() {
            this.startAutoSlide();
        },
        startAutoSlide() {
            setInterval(() => {
                this.next();
            }, 5000);
        },
        next() {
            this.currentIndex = (this.currentIndex + 1) % this.slides.length;
        },
        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
        }
    }
}
</script>
@endpush