@extends('frontend.profile.layout')
@section('title', 'تفاصيل الطلب #' . $order->id)

@push('styles')
<style>
    /* تتبع حالة الطلب - متجاوب */
    .order-tracker {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 1.5rem 0;
        padding: 0 0.5rem;
    }

    .order-tracker::before {
        content: '';
        position: absolute;
        top: 1.5rem;
        left: 1rem;
        right: 1rem;
        height: 2px;
        background-color: #f3f3f3;
        z-index: 0;
    }

    .step {
        position: relative;
        z-index: 1;
        text-align: center;
        width: 25%;
    }

    .step-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        position: relative;
        transition: all 0.3s ease;
        border: 2px solid #fff;
    }

    .step.completed .step-circle {
        background-color: #16a34a;
        color: white;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);
    }

    .step.active .step-circle {
        background-color: #be6661;
        color: white;
        box-shadow: 0 0 0 3px rgba(190, 102, 97, 0.2);
    }

    .step.pending .step-circle {
        background-color: #f3f3f3;
        color: #9ca3af;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        margin-top: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .step.completed .step-label,
    .step.active .step-label {
        color: #4a3f3f;
        font-weight: 600;
    }

    .step-icon {
        font-size: 1rem;
    }

    /* تحسينات عامة للجوال */
    @media (max-width: 640px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .order-summary-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .product-item {
            padding: 0.75rem;
            gap: 0.75rem;
        }
        
        .product-image {
            width: 16vw;
            height: 16vw;
            min-width: 60px;
            min-height: 60px;
        }
    }
</style>
@endpush

@section('profile-content')
@php
    // حساب المبالغ
    $recalculatedSubtotal = 0;
    foreach ($order->items as $item) {
        $recalculatedSubtotal += $item->price * $item->quantity;
    }
    
    $shippingCost = $order->shipping_cost;
    $discountAmount = $order->discount_amount;
    $finalTotal = ($recalculatedSubtotal - $discountAmount) + $shippingCost;
    
    // تحديد حالة الطلب
    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
    $currentStatusIndex = array_search($order->status, $statuses);
@endphp

<div class="bg-white rounded-lg shadow-sm border border-[#eadbcd] p-4 md:p-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-col justify-between items-start mb-4 md:mb-6 order-header">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-[#4a3f3f]">تفاصيل الطلب</h2>
            <p class="text-[#7a6e6e] text-xs md:text-sm">رقم الطلب: <span class="font-mono">#{{ $order->id }}</span></p>
        </div>
        <a href="{{ route('profile.orders') }}" 
           class="text-xs md:text-sm font-semibold text-[#cd8985] hover:underline mt-2 flex items-center gap-1">
            <i class="bi bi-arrow-right"></i> العودة إلى الطلبات
        </a>
    </div>

    {{-- حالة الطلب --}}
    @if($order->status == 'cancelled')
        <div class="bg-gray-100 text-gray-700 p-3 md:p-4 rounded-lg text-center mb-6">
            <i class="bi bi-x-circle-fill text-2xl md:text-3xl mb-1 md:mb-2"></i>
            <h3 class="font-bold text-lg md:text-xl">هذا الطلب تم إلغاؤه</h3>
        </div>
    @elseif($order->status == 'returned')
        <div class="bg-red-100 text-red-700 p-3 md:p-4 rounded-lg text-center mb-6">
            <i class="bi bi-arrow-return-left text-2xl md:text-3xl mb-1 md:mb-2"></i>
            <h3 class="font-bold text-lg md:text-xl">هذا الطلب مرتجع</h3>
        </div>
    @else
        {{-- تتبع حالة الطلب --}}
        <div class="order-tracker">
            @foreach(['قيد الانتظار', 'قيد التجهيز', 'تم الشحن', 'تم التوصيل'] as $index => $label)
            <div class="step 
                @if($currentStatusIndex !== false && $currentStatusIndex > $index) completed 
                @elseif($currentStatusIndex !== false && $currentStatusIndex == $index) active 
                @else pending @endif">
                <div class="step-circle">
                    <i class="bi step-icon {{ ['bi-hourglass-split', 'bi-box-seam', 'bi-truck', 'bi-house-check-fill'][$index] }}"></i>
                </div>
                <p class="step-label">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    @endif

    {{-- المنتجات --}}
    <h3 class="font-bold text-lg mb-2 md:mb-3 text-[#4a3f3f]">المنتجات</h3>
    <div class="space-y-2 md:space-y-3">
        @foreach($order->items as $item)
        <div class="flex items-center gap-3 p-2 md:p-3 border border-[#eadbcd] rounded-lg product-item">
            <img src="{{ $item->product?->firstImage ? asset('storage/' . $item->product->firstImage->image_path) : 'https://placehold.co/80x80/f9f5f1/cd8985?text=Img' }}"
                 alt="{{ $item->product->name_translated ?? 'منتج' }}"
                 class="w-16 h-16 md:w-20 md:h-20 rounded-md object-cover product-image">
            <div class="flex-grow">
                <p class="font-semibold text-[#4a3f3f] text-sm md:text-base">{{ $item->product->name_translated ?? 'منتج محذوف' }}</p>
                <p class="text-[#7a6e6e] text-xs md:text-sm">الكمية: {{ $item->quantity }}</p>
            </div>
            <div class="text-left">
                <p class="font-bold text-[#be6661] text-sm md:text-base">{{ number_format($item->price * $item->quantity, 0) }} د.ع</p>
                <p class="text-[#9ca3af] text-xxs md:text-xs">({{ number_format($item->price, 0) }} د.ع للقطعة)</p>
            </div>
        </div>
        @endforeach
    </div>

    <hr class="my-4 md:my-6 border-[#eadbcd]">

    {{-- ملخص الطلب --}}
    <div class="grid grid-cols-1 gap-4 md:gap-6 order-summary-grid">
        <div>
            <h3 class="font-bold text-lg mb-2 md:mb-3 text-[#4a3f3f]">ملخص الدفع</h3>
            <div class="space-y-1 md:space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-[#7a6e6e]">المجموع الفرعي:</span>
                    <span class="font-medium">{{ number_format($recalculatedSubtotal, 0) }} د.ع</span>
                </div>
                @if($discountAmount > 0)
                <div class="flex justify-between text-green-600">
                    <span>الخصم:</span>
                    <span class="font-medium">-{{ number_format($discountAmount, 0) }} د.ع</span>
                </div>
                @endif
                <div class="flex justify-between text-[#7a6e6e]">
                    <span>الشحن:</span>
                    <span class="font-medium">{{ $shippingCost > 0 ? number_format($shippingCost, 0) . ' د.ع' : 'مجاني' }}</span>
                </div>
                <div class="flex justify-between font-bold text-base md:text-lg text-[#4a3f3f] border-t border-[#eadbcd] pt-2 mt-2">
                    <span>الإجمالي:</span>
                    <span>{{ number_format($finalTotal, 0) }} د.ع</span>
                </div>
            </div>
        </div>
        <div>
            <h3 class="font-bold text-lg mb-2 md:mb-3 text-[#4a3f3f]">عنوان الشحن</h3>
            <div class="text-sm text-[#7a6e6e] space-y-1">
                <p><strong class="text-[#4a3f3f]">المحافظة:</strong> {{ $order->governorate }}</p>
                <p><strong class="text-[#4a3f3f]">المدينة:</strong> {{ $order->city }}</p>
                <p><strong class="text-[#4a3f3f]">تفاصيل العنوان:</strong> {{ $order->address_details }}</p>
                @if($order->nearest_landmark)
                <p><strong class="text-[#4a3f3f]">أقرب نقطة دالة:</strong> {{ $order->nearest_landmark }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection