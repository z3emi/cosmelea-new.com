@extends('frontend.profile.layout')

@section('title', 'قائمة المفضلة')

@push('styles')
<style>
    /* تحسينات للجوال مع الحفاظ على الهوية البصرية */
    .product-card {
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        background-color: #f9f5f1;
        overflow: hidden;
        border: 1px solid #eadbcd;
    }
    
    .product-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }
    
    .product-image-container {
        aspect-ratio: 1 / 1;
        transition: transform 0.3s ease;
    }
    
    .sale-product-highlight {
        border-color: #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }
    
    /* تحسينات للعرض على الجوال */
    @media (max-width: 640px) {
        .product-card {
            margin-bottom: 1rem;
        }
        
        .product-info {
            padding: 0.75rem;
        }
        
        .product-title {
            font-size: 0.95rem;
        }
        
        .product-price {
            font-size: 0.9rem;
        }
        
        .add-to-cart-btn {
            padding: 0.5rem;
            font-size: 0.85rem;
        }
        
        .discount-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
        }
    }
</style>
@endpush

@section('profile-content')
<div class="bg-white rounded-lg shadow-sm border border-[#eadbcd] p-4 md:p-6">
    <div class="text-center mb-6 md:mb-10">
        <h1 class="text-2xl md:text-3xl font-bold text-[#4a3f3f]">
            <i class="bi bi-heart-fill mr-2 text-[#cd8985]"></i>
            قائمة مفضلتك
        </h1>
        <p class="text-[#7a6e6e] mt-1 md:mt-2 text-sm md:text-base">كل المنتجات التي أحببتها في مكان واحد</p>
    </div>

    @if ($favorites->isEmpty())
        <div class="text-center py-8 md:py-12">
            <div class="icon-wrapper mb-4 md:mb-6">
                <i class="bi bi-heart text-4xl md:text-6xl" style="color: #dcaca9;"></i>
            </div>
            <h3 class="text-lg md:text-xl font-medium mb-2 md:mb-3" style="color: #be6661;">قائمتك فارغة</h3>
            <p class="text-[#7a6e6e] mb-4 md:mb-6 text-sm md:text-base">يمكنك إضافة منتجات إلى المفضلة بالنقر على أيقونة القلب</p>
            <a href="{{ route('shop') }}" class="inline-block px-6 md:px-8 py-2 md:py-3 rounded-full font-medium transition-colors bg-[#cd8985] text-white hover:bg-[#be6661] text-sm md:text-base">
                تصفح المتجر
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach ($favorites as $favorite)
                @if ($product = $favorite->product)
                <div class="product-card @if($product->isOnSale()) sale-product-highlight @endif"
                     x-data="{
                        added: false,
                        loadingAdd: false,
                        isFavorite: true,
                        loadingFav: false
                     }" 
                     x-show="isFavorite" 
                     x-transition:leave="transition ease-in duration-300">
                    <a href="{{ route('product.detail', $product) }}" class="relative block">
                        <div class="product-image-container">
                            @if ($product->firstImage)
                                <img src="{{ asset('storage/' . $product->firstImage->image_path) }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <img src="https://placehold.co/400x400/f9f5f1/cd8985?text=No+Image" class="w-full h-full object-cover" loading="lazy">
                            @endif
                        </div>
                        @if($product->isOnSale())
                            @php
                                $discountPercentage = round((($product->price - $product->sale_price) / $product->price) * 100);
                            @endphp
                            <div class="absolute top-2 md:top-3 right-2 md:right-3 bg-red-500 text-white text-xs font-bold px-1.5 md:px-2 py-0.5 md:py-1 rounded-full discount-badge">
                                -{{ $discountPercentage }}%
                            </div>
                        @endif
                    </a>
                    <div class="p-3 md:p-4 text-right product-info">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-base md:text-lg text-[#4a3f3f] product-title">{{ $product->name_translated }}</h3>
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
                                            isFavorite = false;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                        }
                                    })
                                    .finally(() => loadingFav = false);
                                "
                                class="text-[#cd8985] hover:text-[#be6661] transition-colors"
                                :disabled="loadingFav"
                            >
                                <i class="bi bi-heart-fill text-xl md:text-2xl"></i>
                            </button>
                        </div>
                        <div class="mb-2 md:mb-3 product-price">
                            @if($product->isOnSale())
                                <span class="text-[#cd8985] font-bold text-base md:text-lg">{{ number_format($product->sale_price, 0) }} د.ع</span>
                                <span class="text-[#9ca3af] line-through text-xs md:text-sm ml-2">{{ number_format($product->price, 0) }} د.ع</span>
                            @else
                                <span class="text-[#cd8985] font-bold text-base md:text-lg">{{ number_format($product->price, 0) }} د.ع</span>
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
                            class="w-full py-1.5 md:py-2 rounded-lg flex justify-center items-center gap-2 transition-all bg-[#cd8985] text-white hover:bg-[#be6661] add-to-cart-btn"
                            :disabled="loadingAdd || added"
                        >
                            <span x-show="!added && !loadingAdd"><i class="bi bi-cart-plus"></i> أضف للسلة</span>
                            <span x-show="loadingAdd"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                            <span x-show="added"><i class="bi bi-check-lg"></i> تمت الإضافة</span>
                        </button>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection