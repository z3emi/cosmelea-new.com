@extends('layouts.app')

@section('title', $product->name_ar)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- صورة المنتج -->
        <div>
            <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name_ar }}" class="w-full h-64 object-cover rounded mb-4">
        </div>

        <!-- تفاصيل المنتج -->
        <div>
            <h1 class="text-3xl font-bold text-brand-text mb-4">{{ $product->name_ar }}</h1>
            <p class="text-brand-primary text-2xl font-semibold mb-6">
                {{ number_format($product->price, 0, ',', '.') }} د.ع
            </p>
            <p class="text-gray-700 leading-relaxed mb-6">
                {{ $product->description_ar }}
            </p>

            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        class="bg-brand-dark text-white px-6 py-2 rounded-lg hover:bg-brand-primary transition">
                    أضف إلى السلة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection