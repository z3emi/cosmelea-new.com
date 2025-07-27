@extends('admin.layout')

@section('title', 'تفاصيل الطلب #' . $order->id)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --primary-dark: #be6661;
        --primary-medium: #cd8985;
        --text-dark: #3a3a3a;
    }
    .panel {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .panel-header {
        font-weight: bold;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        justify-content: space-between; /* This will push the button to the end */
    }
    .panel-header .header-text {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    #orderLocationMap { height: 300px; z-index: 1; border-radius: 8px; }
</style>
@endpush

@section('content')
@php
    $statusTexts = [
        'pending' => ['text' => 'قيد الانتظار', 'color' => 'warning'],
        'processing' => ['text' => 'قيد المعالجة', 'color' => 'info'],
        'shipped' => ['text' => 'تم الشحن', 'color' => 'primary'],
        'delivered' => ['text' => 'تم التوصيل', 'color' => 'success'],
        'returned' => ['text' => 'مرتجع', 'color' => 'danger'],
        'cancelled' => ['text' => 'ملغى', 'color' => 'secondary']
    ];
    $statusInfo = $statusTexts[$order->status] ?? ['text' => $order->status, 'color' => 'dark'];
@endphp

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>تفاصيل الطلب <span class="text-primary">#{{ $order->id }}</span></h3>
        <div>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-dark"><i class="bi bi-printer-fill me-1"></i> طباعة</a>
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-info"><i class="bi bi-pencil-fill me-1"></i> تعديل</a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Right Column (swapped for better layout) --}}
        <div class="col-lg-4">
            <div class="panel">
                {{-- ===== START: التعديل المطلوب ===== --}}
                <div class="panel-header">
                    <div class="header-text">
                        <i class="bi bi-person-circle"></i>
                        <span>معلومات العميل</span>
                    </div>
                    @if($order->customer)
                    <a href="{{ route('admin.customers.show', $order->customer->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-person-lines-fill"></i> عرض الملف
                    </a>
                    @endif
                </div>
                {{-- ===== END: التعديل المطلوب ===== --}}
                <p class="mb-1"><strong>الاسم:</strong> {{ $order->customer->name ?? 'عميل محذوف' }}</p>
                <p><strong>الهاتف:</strong> <a href="tel:{{ $order->customer->phone_number ?? '' }}">{{ $order->customer->phone_number ?? 'N/A' }}</a></p>
                <hr class="my-2">
                <p class="mb-1"><strong>العنوان:</strong> {{ $order->governorate }}, {{ $order->city }}</p>
                <p class="text-muted small">{{ $order->address_details }}</p>
            </div>
            
            <div class="panel">
                <div class="panel-header"><div class="header-text"><i class="bi bi-file-earmark-text"></i><span>ملخص الطلب</span></div></div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>الحالة:</span> <span class="badge bg-{{ $statusInfo['color'] }}">{{ $statusInfo['text'] }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>المجموع الفرعي:</span> <span>{{ number_format($order->subtotal, 0) }} د.ع</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>الخصم:</span> <span class="text-success">- {{ number_format($order->discount_amount, 0) }} د.ع</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>الشحن:</span> <span>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 0) . ' د.ع' : 'مجاني' }}</span></li>
                    <li class="list-group-item d-flex justify-content-between fw-bold fs-5"><span>الإجمالي:</span> <span class="text-primary">{{ number_format($order->total_amount, 0) }} د.ع</span></li>
                </ul>
            </div>
            <div class="panel">
                <div class="panel-header"><div class="header-text"><i class="bi bi-arrow-repeat"></i><span>تغيير حالة الطلب</span></div></div>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <select name="status" class="form-select">
                            @foreach($statusTexts as $key => $info)
                                <option value="{{ $key }}" @selected($order->status == $key)>{{ $info['text'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Left Column --}}
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-header"><div class="header-text"><i class="bi bi-cart-check"></i><span>المنتجات المطلوبة</span></div></div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>المنتج</th><th class="text-center">السعر</th><th class="text-center">الكمية</th><th class="text-end">الإجمالي</th></tr></thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->product?->firstImage ? asset('storage/' . $item->product->firstImage->image_path) : 'https://placehold.co/50x50?text=Img' }}" width="50" height="50" class="rounded me-2" style="object-fit:cover;">
                                        <span>{{ $item->product->name_ar ?? 'منتج محذوف' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">{{ number_format($item->price, 0) }} د.ع</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price * $item->quantity, 0) }} د.ع</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header"><div class="header-text"><i class="bi bi-geo-alt-fill"></i><span>موقع التوصيل</span></div></div>
                @if($order->customer?->user?->addresses->isNotEmpty() && $order->customer->user->addresses->first()->latitude)
                    <div id="orderLocationMap"></div>
                @else
                    <p class="text-muted text-center my-4">لم يحدد العميل الموقع على الخريطة.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($order->customer?->user?->addresses->isNotEmpty() && $order->customer->user->addresses->first()->latitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lat = {{ $order->customer->user->addresses->first()->latitude }};
            const lng = {{ $order->customer->user->addresses->first()->longitude }};
            const map = L.map('orderLocationMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup('موقع توصيل الطلب.').openPopup();
        });
    </script>
@endif
@endpush
