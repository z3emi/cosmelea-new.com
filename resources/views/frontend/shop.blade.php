@php
    // This defines the variable as an empty array if it doesn't exist, preventing errors.
    $favoriteProductIds = $favoriteProductIds ?? [];
@endphp

@extends('layouts.app')

@section('title', $pageTitle ?? 'المتجر')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" rel="stylesheet" />
<style>
    :root {
        --primary-color: #cd8985;
        --primary-hover: #be6661;
        --dark-color: #3a3a3a;
    }
    body { background-color: #FFFFFF; font-family: 'Cairo', sans-serif; }
    
    .product-card { transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border-radius: 12px; background-color: #f9f5f1; overflow: hidden; border: 2px solid transparent; }
    .product-card:hover { box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); transform: translateY(-8px); }
    .product-image-container {aspect-ratio: 1 / 1; transition: transform 0.5s ease;}
    .product-card:hover .product-image-container { transform: scale(1.03); }
    .btn-primary { background-color: var(--primary-color); color: white; border: none; transition: all 0.3s ease; }
    .btn-primary:hover { background-color: var(--primary-hover); transform: translateY(-2px); }
    .sale-product-highlight {
        border-color: #FFD700; /* Gold color */
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
    }

    /* ===== START: التعديل المطلوب ===== */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr)); /* 5 منتجات في السطر للشاشات الكبيرة */
        gap: 1.5rem;
    }
    @media (max-width: 640px) { /* sm screens and smaller */
        .products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)); /* منتجان في السطر للهاتف */
            gap: 0.75rem;
        }
    }
    /* ===== END: التعديل المطلوب ===== */
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ mobileFiltersOpen: false }">
    {{-- Hero Section --}}
    <div class="bg-brand-light p-10 rounded-2xl text-center shadow mb-12">
        <h1 class="text-4xl font-bold text-brand-dark">{!! $pageTitle ?? 'تسوقي أحدث المنتجات' !!}</h1>
        <p class="text-brand-text mt-3 text-lg">اكتشفي تشكيلتنا الواسعة من منتجات الجمال والعناية</p>
    </div>

    {{-- Mobile Filter Button --}}
    <div class="lg:hidden mb-6">
        <button @click="mobileFiltersOpen = true" class="w-full bg-white p-3 rounded-lg shadow-md flex justify-between items-center">
            <span class="font-semibold text-brand-dark"><i class="bi bi-funnel-fill mr-2"></i> عرض الفلاتر</span>
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>

    {{-- Mobile Filter Off-canvas --}}
    <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" @click="mobileFiltersOpen = false" style="display: none;"></div>
    <div x-show="mobileFiltersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 h-full w-4/5 max-w-sm bg-white z-50 overflow-y-auto p-6" style="display: none;">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">الفلاتر</h2>
            <button @click="mobileFiltersOpen = false" class="text-2xl">&times;</button>
        </div>
        @include('frontend.partials._filters', ['categories' => $categories])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        {{-- Desktop Sidebar --}}
        <aside class="lg:col-span-1 hidden lg:block">
            <div class="bg-white rounded-2xl p-6 border border-gray-200 sticky top-28 shadow-md">
                @include('frontend.partials._filters')
            </div>
        </aside>

        {{-- Product Grid --}}
        <div class="lg:col-span-4">
            <div class="products-grid">
                @forelse ($products as $product)
                    <div class="product-card @if($product->sale_price && $product->sale_price > 0) sale-product-highlight @endif" x-data="{
                        added: false,
                        loadingAdd: false,
                        isFavorite: {{ in_array($product->id, $favoriteProductIds) ? 'true' : 'false' }},
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
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-2xl">
                        <p>لم يتم العثور على منتجات تطابق بحثك.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const priceSlider = document.getElementById('price-slider');
        if (priceSlider) {
            const minPrice = {{ request('min_price', 0) }};
            const maxPrice = {{ request('max_price', 500000) }};
            noUiSlider.create(priceSlider, {
                start: [minPrice, maxPrice],
                connect: true,
                direction: 'rtl',
                step: 1000,
                range: {
                    'min': 0,
                    'max': 500000
                },
                format: {
                    to: value => Math.round(value),
                    from: value => parseInt(value)
                }
            });

            const minInput = document.getElementById('min_price');
            const maxInput = document.getElementById('max_price');
            const minDisplay = document.getElementById('min-price-display');
            const maxDisplay = document.getElementById('max-price-display');

            priceSlider.noUiSlider.on('update', function (values) {
                minInput.value = values[0];
                maxInput.value = values[1];
                minDisplay.textContent = `${values[0]} د.ع`;
                maxDisplay.textContent = `${values[1]} د.ع`;
            });
        }
    });
</script>
@endpush