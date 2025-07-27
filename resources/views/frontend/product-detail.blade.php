@extends('layouts.app')

@section('title', $product->name_ar)

@push('styles')
<style>
    .prose p { margin-bottom: 1rem; }
    .prose ul { list-style-type: disc; padding-right: 1.5rem; }
    .thumbnail-active {
        border-color: #cd8985; /* brand-primary color */
        box-shadow: 0 0 0 2px #cd8985;
    }
    .zoom-modal-overlay {
        background-color: rgba(0, 0, 0, 0.75);
    }
    /* Styles copied from homepage for consistency */
    .product-card { transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border-radius: 12px; background-color: #f9f5f1; overflow: hidden; border: 2px solid transparent; }
    .product-card:hover { box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); transform: translateY(-8px); }
    .product-image-container {aspect-ratio: 1 / 1; transition: transform 0.5s ease;}
    .product-card:hover .product-image-container { transform: scale(1.03); }
    .sale-product-highlight {
        border-color: #FFD700; /* Gold color */
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
    }
    .btn-primary { background-color: #cd8985; color: white; border: none; transition: all 0.3s ease; }
    .btn-primary:hover { background-color: #be6661; transform: translateY(-2px); }
</style>
@endpush

@section('content')
<div class="bg-gray-50/50">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-10 lg:gap-16 items-start"
             x-data="{
                @if($product->images->isNotEmpty())
                    mainImage: '{{ asset('storage/' . $product->images->first()->image_path) }}',
                @else
                    mainImage: 'https://placehold.co/600x600?text=No+Image',
                @endif
                quantity: 1,
                added: false,
                loadingAdd: false,
                isFavorite: {{ $isFavorited ? 'true' : 'false' }},
                loadingFav: false,
                imageZoomOpen: false
             }">

            {{-- Product Image Gallery --}}
            <div class="md:col-span-2 flex flex-col gap-4">
                <div class="bg-white p-2 rounded-lg shadow-md border border-gray-200">
                    <button @click="imageZoomOpen = true" class="w-full cursor-zoom-in">
                        <img :src="mainImage" alt="{{ $product->name_ar }}"
                             class="w-full h-auto aspect-square object-contain rounded-lg">
                    </button>
                </div>
                @if($product->images->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-2">
                    @foreach($product->images as $image)
                        <button @click="mainImage = '{{ asset('storage/' . $image->image_path) }}'" 
                                class="w-20 h-20 flex-shrink-0 rounded-md border-2 p-1 transition"
                                :class="mainImage === '{{ asset('storage/' . $image->image_path) }}' ? 'thumbnail-active' : 'border-gray-200 hover:border-brand-primary'">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail" class="w-full h-full object-cover rounded-sm">
                        </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="md:col-span-3 flex flex-col">
                <div class="text-sm text-gray-500 mb-2">
                    <a href="{{ route('shop') }}" class="hover:text-brand-primary">المتجر</a> / 
                    <a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="hover:text-brand-primary">{{ $product->category->name_ar }}</a>
                </div>
                <h1 class="text-3xl lg:text-4xl font-bold text-brand-text mb-3">{{ $product->name_ar }}</h1>
                <span class="text-sm text-gray-500 mb-4">SKU: {{ $product->sku ?? 'N/A' }}</span>

                <div class="mb-6">
                    @if($product->sale_price && $product->sale_price > 0)
                        <span class="text-brand-primary font-bold text-3xl">{{ number_format($product->sale_price, 0) }} د.ع</span>
                        <span class="text-gray-400 line-through text-xl ml-3">{{ number_format($product->price, 0) }} د.ع</span>
                    @else
                        <span class="text-brand-primary font-bold text-3xl">{{ number_format($product->price, 0) }} د.ع</span>
                    @endif
                </div>
                
                <div class="border-t border-b border-gray-200 py-4 mb-8 space-y-3">
                    <div class="flex items-center text-sm text-gray-700"><i class="bi bi-truck text-brand-primary text-xl w-8 text-center"></i><span>توصيل لكل محافظات العراق</span></div>
                    <div class="flex items-center text-sm text-gray-700"><i class="bi bi-gift text-brand-primary text-xl w-8 text-center"></i><span>اشتري بقيمة 75 ألف و احصلي على هديتين وتوصيل مجاني</span></div>
                    <div class="flex items-center text-sm text-gray-700"><i class="bi bi-box-seam text-brand-primary text-xl w-8 text-center"></i><span>توصيل مجاني عند الطلب بقيمة 50 ألف د.ع</span></div>
                    <div class="flex items-center text-sm text-gray-700"><i class="bi bi-patch-check-fill text-brand-primary text-xl w-8 text-center"></i><span>منتجات أصلية و مضمونة</span></div>
                </div>

                <div class="space-y-4 mb-8">
                    {{-- Quantity Selector --}}
                    <div class="flex items-center gap-4">
                        <label for="quantity" class="text-lg font-semibold text-gray-700">الكمية:</label>
                        <div class="inline-flex items-center bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                            <button type="button" @click="quantity > 1 ? quantity-- : 1" class="px-4 py-2 text-xl font-bold text-gray-700 hover:bg-gray-100 transition">−</button>
                            <input type="number" x-model.number="quantity" min="1" class="w-16 text-center text-lg font-semibold focus:outline-none border-x border-gray-200 h-11" />
                            <button type="button" @click="quantity++" class="px-4 py-2 text-xl font-bold text-gray-700 hover:bg-gray-100 transition">+</button>
                        </div>
                    </div>
                    {{-- Add to Cart Button --}}
                    <button 
                        @click.prevent="
                            loadingAdd = true;
                            addToCart({{ $product->id }}, quantity).then(data => {
                                if(data.success) {
                                    added = true;
                                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { cartCount: data.cartCount } }));
                                    setTimeout(() => added = false, 2000);
                                } else {
                                    alert(data.message || 'حدث خطأ ما.');
                                }
                                loadingAdd = false;
                            }).catch(() => {
                                alert('حدث خطأ في الاتصال بالخادم.');
                                loadingAdd = false;
                            });
                        "
                        class="w-full flex justify-center items-center gap-2 py-3 px-6 rounded-lg bg-brand-primary text-white font-bold text-lg shadow hover:bg-brand-accent transition duration-300"
                        :disabled="loadingAdd || added"
                    >
                        <span x-show="!added && !loadingAdd"><i class="bi bi-cart-plus-fill text-xl"></i> أضف إلى السلة</span>
                        <span x-show="loadingAdd"><i class="bi bi-arrow-repeat animate-spin text-xl"></i></span>
                        <span x-show="added"><i class="bi bi-check-lg text-xl"></i> تمت الإضافة</span>
                    </button>
                    {{-- Add to Wishlist Button --}}
                    @auth
                    <button 
                        @click.prevent="
                            loadingFav = true;
                            toggleWishlist({{ $product->id }}).then(data => {
                                if(data.success) {
                                    isFavorite = !isFavorite;
                                    window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                } else {
                                    alert(data.message || 'حدث خطأ في العملية.');
                                }
                                loadingFav = false;
                            }).catch(() => {
                                alert('حدث خطأ في الاتصال بالخادم.');
                                loadingFav = false;
                            });
                        "
                        class="w-full flex justify-center items-center gap-2 py-3 px-6 rounded-lg border border-gray-300 bg-white text-brand-dark font-semibold text-lg hover:bg-gray-100 transition"
                        :disabled="loadingFav"
                    >
                        <i class="bi text-xl" :class="isFavorite ? 'bi-heart-fill text-red-500' : 'bi-heart'"></i>
                        <span x-text="isFavorite ? 'إزالة من المفضلة' : 'أضف إلى المفضلة'"></span>
                    </button>
                    @endauth
                </div>

                <div class="prose max-w-none text-gray-700 leading-relaxed border-t border-gray-200 pt-8">
                    <h3 class="font-bold text-xl mb-4">الوصف</h3>
                    {!! $product->description_ar !!}
                </div>
            </div>

            {{-- Image Zoom Modal --}}
            <div x-show="imageZoomOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 zoom-modal-overlay"
                 style="display: none;">
                
                <div class="relative max-w-4xl max-h-full" @click.away="imageZoomOpen = false">
                    <img :src="mainImage" alt="Zoomed Image" class="w-full h-auto object-contain rounded-lg shadow-2xl">
                    <button @click="imageZoomOpen = false" class="absolute -top-3 -right-3 bg-white text-black rounded-full h-8 w-8 flex items-center justify-center text-xl">&times;</button>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
    <div class="container mx-auto px-4 py-12 border-t border-gray-200 mt-12">
        <h2 class="text-3xl font-bold text-brand-dark mb-8 text-center">ربما يعجبك أيضاً</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                <div class="product-card @if($relatedProduct->sale_price && $relatedProduct->sale_price > 0) sale-product-highlight @endif" x-data="{
                    added: false,
                    loadingAdd: false,
                    isFavorite: {{ in_array($relatedProduct->id, $favoriteProductIds ?? []) ? 'true' : 'false' }},
                    loadingFav: false
                }">
                    <a href="{{ route('product.detail', $relatedProduct) }}" class="relative block">
                        <div class="product-image-container">
                            @if ($relatedProduct->firstImage)
                                <img src="{{ asset('storage/' . $relatedProduct->firstImage->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://placehold.co/400x400?text=No+Image" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if($relatedProduct->sale_price && $relatedProduct->sale_price > 0)
                            @php
                                $discountPercentage = round((($relatedProduct->price - $relatedProduct->sale_price) / $relatedProduct->price) * 100);
                            @endphp
                            <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                -{{ $discountPercentage }}%
                            </div>
                        @endif
                    </a>
                    <div class="p-4 text-right">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-lg text-brand-dark">{{ $relatedProduct->name_ar }}</h3>
                            @auth
                            <button 
                                @click.prevent="
                                    loadingFav = true;
                                    toggleWishlist({{ $relatedProduct->id }}).then(data => {
                                        if(data.success) {
                                            isFavorite = !isFavorite;
                                            window.dispatchEvent(new CustomEvent('wishlist-updated', { detail: { count: data.wishlistCount } }));
                                        }
                                        loadingFav = false;
                                    });
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
                            @if($relatedProduct->sale_price && $relatedProduct->sale_price > 0)
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($relatedProduct->sale_price, 0) }} د.ع</span>
                                <span class="text-gray-400 line-through text-sm ml-2">{{ number_format($relatedProduct->price, 0) }} د.ع</span>
                            @else
                                <span class="text-brand-primary font-bold text-lg">{{ number_format($relatedProduct->price, 0) }} د.ع</span>
                            @endif
                        </div>
                        <button 
                            @click.prevent="
                                loadingAdd = true;
                                addToCart({{ $relatedProduct->id }}, 1).then(data => {
                                    if(data.success) {
                                        added = true;
                                        setTimeout(() => added = false, 2000);
                                    }
                                    loadingAdd = false;
                                });
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
    @endif
</div>
@endsection

@push('scripts')
{{-- ===== START: الكود المضاف (دوال الجافاسكريبت) ===== --}}
<script>
    function addToCart(productId, quantity) {
        return fetch("{{ route('cart.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        })
        .then(response => response.json());
    }

    function toggleWishlist(productId) {
        return fetch(`{{ url('/wishlist/toggle-async') }}/${productId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(response => response.json());
    }
</script>
{{-- ===== END: الكود المضاف ===== --}}
@endpush
