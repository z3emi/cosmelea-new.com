@extends('frontend.profile.layout')

@section('title', 'طلباتي')

@push('styles')
<style>
    /* تحسينات الباجينيشن للجوال */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.3rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination .page-item .page-link {
        background-color: #f9f5f1 !important;
        color: #be6661 !important;
        border: 1px solid #eadbcd !important;
        font-weight: 600;
        border-radius: 0.25rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
        min-width: 2.5rem;
        text-align: center;
        transition: all 0.2s;
    }

    .pagination .page-item .page-link:hover {
        background-color: #dcaca9 !important;
        color: #ffffff !important;
        border-color: #dcaca9 !important;
    }

    .pagination .page-item.active .page-link {
        background-color: #be6661 !important;
        border-color: #be6661 !important;
        color: #ffffff !important;
    }

    .pagination .page-item.disabled .page-link {
        color: #d1d5db !important;
        background-color: #f9f5f1 !important;
        border-color: #f3f3f3 !important;
    }

    /* تحسينات عامة للجوال */
    @media (max-width: 640px) {
        .order-card {
            padding: 1rem;
        }
        
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .order-status {
            align-self: flex-start;
        }
        
        .order-footer {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .order-details-link {
            align-self: flex-end;
        }
    }
</style>
@endpush

@section('profile-content')
<div class="bg-white rounded-lg shadow-sm border border-[#eadbcd] p-4 md:p-6">
    <div class="flex flex-col justify-between items-start mb-4 md:mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-[#4a3f3f]">سجل طلباتي</h2>
            <p class="text-[#7a6e6e] text-sm md:text-base mt-1">هنا يمكنك تتبع جميع طلباتك الحالية والسابقة</p>
        </div>
    </div>
    
    @if($orders->isEmpty())
        <div class="text-center py-8 md:py-12">
            <i class="bi bi-receipt text-4xl md:text-6xl text-[#eadbcd]"></i>
            <p class="mt-3 md:mt-4 text-[#7a6e6e]">لم تقومي بأي طلبات بعد</p>
            <a href="{{ route('shop') }}" 
               class="mt-3 md:mt-4 inline-block bg-[#cd8985] text-white font-bold py-2 px-4 md:px-5 rounded-md hover:bg-[#be6661] transition-colors text-sm md:text-base">
                ابدئي التسوق الآن
            </a>
        </div>
    @else
        <div class="space-y-4 md:space-y-6">
            @foreach($orders as $order)
                @php
                    $statusClass = match($order->status) {
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                        'processing' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                        'delivered' => 'bg-green-100 text-green-800 border-green-300',
                        'cancelled' => 'bg-gray-100 text-gray-800 border-gray-300',
                        'returned' => 'bg-red-100 text-red-800 border-red-300',
                        default => 'bg-gray-100 text-gray-800 border-gray-300',
                    };
                    $statusText = match($order->status) {
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد المعالجة',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التوصيل',
                        'cancelled' => 'ملغي',
                        'returned' => 'مرتجع',
                        default => $order->status,
                    };
                @endphp
                
                <div class="border border-[#eadbcd] rounded-lg p-3 md:p-4 transition hover:shadow-md hover:border-[#cd8985] order-card">
                    {{-- رأس البطاقة --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-2 md:pb-3 border-b border-[#eadbcd] order-header">
                        <div>
                            <p class="font-bold text-[#4a3f3f] text-sm md:text-base">طلب رقم #{{ $order->id }}</p>
                            <p class="text-[#7a6e6e] text-xs md:text-sm">تاريخ الطلب: {{ $order->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="mt-1 sm:mt-0 order-status">
                            <span class="text-xs font-medium px-2 py-1 rounded-full border {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- صور المنتجات --}}
                    <div class="py-3 md:py-4">
                        <div class="flex items-center gap-2 md:gap-3 overflow-x-auto pb-2 scrollbar-hide">
                            @foreach($order->items->take(5) as $item)
                                @if($item->product)
                                <img src="{{ $item->product->firstImage ? asset('storage/' . $item->product->firstImage->image_path) : 'https://placehold.co/80x80/f9f5f1/cd8985?text=Img' }}" 
                                     alt="{{ $item->product->name_ar }}" 
                                     class="w-10 h-10 md:w-12 md:h-12 rounded-md object-cover border border-[#eadbcd] flex-shrink-0">
                                @endif
                            @endforeach
                            @if($order->items->count() > 5)
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-md bg-[#f9f5f1] flex items-center justify-center text-xs font-bold text-[#cd8985] flex-shrink-0">
                                    +{{ $order->items->count() - 5 }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- تذييل البطاقة --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pt-2 md:pt-3 border-t border-[#eadbcd] gap-2 order-footer">
                        <p class="text-[#4a3f3f] text-sm md:text-base">
                            الإجمالي: <span class="font-bold">{{ number_format($order->total_amount, 0) }} د.ع</span>
                        </p>
                        <a href="{{ route('profile.orders.show', $order->id) }}" 
                           class="text-xs md:text-sm font-semibold text-[#cd8985] hover:underline hover:text-[#be6661] order-details-link">
                            عرض التفاصيل <i class="bi bi-chevron-left"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination Links --}}
        <div class="mt-6 md:mt-8">
            {{ $orders->onEachSide(1)->links() }}
        </div>
    @endif
</div>
@endsection