@extends('layouts.app')

@section('title', 'تتبع طلبك')

@push('styles')
<style>
    .timeline {
        position: relative;
        padding: 2rem 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        right: 1.25rem; /* For RTL */
        transform: translateX(50%);
        height: 100%;
        width: 4px;
        background-color: #e9ecef;
        border-radius: 2px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2.5rem;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    .timeline-icon {
        position: absolute;
        top: 0;
        right: 1.25rem; /* For RTL */
        transform: translate(50%, -25%);
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        z-index: 1;
        border: 4px solid #f9f5f1; /* Match page background */
    }
    .timeline-content {
        margin-right: 5rem; /* For RTL */
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
    }
    /* Active/Completed State */
    .timeline-item.active .timeline-icon {
        background-color: #be6661; /* Primary Dark */
        color: white;
    }
    .timeline-item.active .timeline-content {
        border-color: #be6661;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50/50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-brand-text">تتبع طلبك</h1>
            <p class="text-gray-500 mt-2">أدخل رقم الطلب ورقم الهاتف المرتبط به لعرض حالته.</p>
        </div>

        <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('tracking.track') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">رقم الطلب</label>
                        <input type="text" name="order_id" id="order_id" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="مثال: 123" value="{{ old('order_id', $order->id ?? '') }}" required>
                    </div>
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="phone_number" id="phone_number" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="مثال: 07..." value="{{ old('phone_number', $order->customer->phone_number ?? '') }}" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-4 rounded-md hover:bg-brand-primary transition duration-300">
                            <i class="bi bi-search"></i> تتبع
                        </button>
                    </div>
                </div>
            </form>

            @if(session('error'))
                <div class="mt-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
        </div>

        {{-- Display Order Status Timeline if order is found --}}
        @if(isset($order))
            <div class="max-w-2xl mx-auto mt-12">
                <h3 class="text-2xl font-bold text-center mb-6 text-brand-text">حالة الطلب #{{ $order->id }}</h3>
                @php
                    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                    $statusLabels = ['قيد الانتظار', 'قيد المعالجة', 'تم الشحن', 'تم التوصيل'];
                    $statusIcons = ['hourglass-split', 'gear-fill', 'truck', 'check2-circle'];
                    $currentStatusIndex = array_search($order->status, $statuses);
                @endphp

                <div class="timeline">
                    @foreach($statuses as $index => $status)
                        @if($order->status != 'cancelled' && $order->status != 'returned')
                            <div class="timeline-item {{ $currentStatusIndex >= $index ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="bi bi-{{ $statusIcons[$index] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4 class="font-bold">{{ $statusLabels[$index] }}</h4>
                                    @if($currentStatusIndex == $index)
                                        <p class="text-sm text-gray-600">هذه هي الحالة الحالية لطلبك.</p>
                                    @elseif($currentStatusIndex > $index)
                                        <p class="text-sm text-gray-500">تم إكمال هذه المرحلة.</p>
                                    @else
                                        <p class="text-sm text-gray-400">لم يصل طلبك إلى هذه المرحلة بعد.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Special case for cancelled or returned orders --}}
                    @if($order->status == 'cancelled')
                        <div class="timeline-item active">
                            <div class="timeline-icon bg-gray-500 text-white"><i class="bi bi-x-circle-fill"></i></div>
                            <div class="timeline-content border-gray-500">
                                <h4 class="font-bold">تم إلغاء الطلب</h4>
                                <p class="text-sm text-gray-600">تم إلغاء هذا الطلب.</p>
                            </div>
                        </div>
                    @elseif($order->status == 'returned')
                        <div class="timeline-item active">
                            <div class="timeline-icon bg-red-500 text-white"><i class="bi bi-arrow-return-left"></i></div>
                            <div class="timeline-content border-red-500">
                                <h4 class="font-bold">تم إرجاع الطلب</h4>
                                <p class="text-sm text-gray-600">تم إرجاع هذا الطلب.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
