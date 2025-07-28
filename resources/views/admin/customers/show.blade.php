@php
// مصفوفة الحالات بالعربية لتوحيد التصميم
$statusLabels = [
    'pending' => 'قيد الانتظار',
    'processing' => 'قيد المعالجة',
    'shipped' => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي',
    'returned' => 'مرتجع',
];
@endphp

@extends('admin.layout')
@section('title', 'تفاصيل العميل: ' . $customer->name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .map-container {
        height: 250px;
        border-radius: 0.375rem;
        margin-top: 1rem;
        z-index: 1;
    }
    .pagination {
        justify-content: center !important;
        gap: 0.4rem;
        margin-top: 1rem;
    }
    .pagination .page-item .page-link {
        background-color: #f9f5f1 !important;
        color: #cd8985 !important;
        border-color: #cd8985 !important;
        font-weight: 600;
        border-radius: 0.375rem;
        transition: background-color 0.3s, color 0.3s;
        box-shadow: none;
    }
    .pagination .page-item .page-link:hover {
        background-color: #dcaca9 !important;
        color: #fff !important;
        border-color: #dcaca9 !important;
    }
    .pagination .page-item.active .page-link {
        background-color: #cd8985 !important;
        border-color: #cd8985 !important;
        color: #fff !important;
    }
</style>
@endpush

@section('content')
<div class="row">
    {{-- Customer Info Column --}}
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-person-circle me-2"></i>ملف العميل</h4>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-right"></i> العودة
                </a>
            </div>
            <div class="card-body text-center">
                {{-- ===== START: التعديل المطلوب ===== --}}
                <div class="mb-3">
                    @if($customer->user && $customer->user->avatar)
                        <img src="{{ asset('storage/' . $customer->user->avatar) }}" alt="{{ $customer->name }}" class="rounded-circle mx-auto" width="100" height="100" style="object-fit: cover;">
                    @else
                        <img src="https://i.pravatar.cc/100?u={{ $customer->id }}" alt="Avatar" class="rounded-circle mx-auto">
                    @endif
                </div>
                <h5 class="card-title">{{ $customer->name }}</h5>
                <p class="card-text"><strong>رقم الهاتف:</strong> {{ $customer->phone_number }}</p>
                <p class="card-text mb-0"><strong>البريد الإلكتروني:</strong> {{ $customer->email ?? 'لا يوجد' }}</p>
                {{-- ===== END: التعديل المطلوب ===== --}}
            </div>
        </div>

        <div class="card shadow-sm mb-4" x-data="{ showAddresses: false }">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>العناوين المحفوظة</h4>
                <button @click="showAddresses = !showAddresses" class="btn btn-sm btn-outline-secondary">
                    <span x-show="!showAddresses">عرض <i class="bi bi-chevron-down"></i></span>
                    <span x-show="showAddresses" style="display: none;">إخفاء <i class="bi bi-chevron-up"></i></span>
                </button>
            </div>
            <div class="card-body" x-show="showAddresses" x-collapse style="display: none;">
                @if($customer->user && $customer->user->addresses->isNotEmpty())
                    @foreach($customer->user->addresses as $address)
                        <div class="mb-3 @if(!$loop->last) border-bottom pb-3 @endif">
                            <p class="mb-1"><strong>{{ $address->governorate }}, {{ $address->city }}</strong></p>
                            <p class="text-muted mb-1">{{ $address->address_details }}</p>
                            @if($address->nearest_landmark)
                                <p class="text-muted small mb-0">نقطة دالة: {{ $address->nearest_landmark }}</p>
                            @endif

                            @if($address->latitude && $address->longitude)
                                <div id="map-{{ $address->id }}" class="map-container"></div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">لا توجد عناوين محفوظة لهذا العميل.</p>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>إحصائيات الطلبات</h4>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>إجمالي الطلبات:</strong> {{ $totalOrders }}</p>
                <ul class="list-unstyled small mb-2">
                    @foreach($orderCounts as $status => $count)
                        <li>{{ $statusLabels[$status] ?? $status }}: {{ $count }}</li>
                    @endforeach
                </ul>
                <p class="mb-0"><strong>مجموع المبالغ للطلبات المُوصلة:</strong> {{ number_format($deliveredAmount, 0) }} د.ع</p>
            </div>
        </div>
    </div>

    {{-- Customer Orders Column --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-receipt me-2"></i>سجل طلبات العميل</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.customers.show', $customer->id) }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ابحث برقم الطلب أو حالته..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary" style="background-color: #cd8985; border-color: #cd8985;">
                            <i class="bi bi-search"></i> بحث
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>المبلغ الإجمالي</th>
                                <th>الخصم</th>
                                <th>الحالة</th>
                                <th>تاريخ الطلب</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr @class([
                                    'table-warning' => $order->status == 'pending',
                                    'table-info' => $order->status == 'processing',
                                    'table-primary' => $order->status == 'shipped',
                                    'table-success' => $order->status == 'delivered',
                                    'table-secondary' => $order->status == 'cancelled',
                                    'table-danger' => $order->status == 'returned',
                                ])>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ number_format($order->total_amount, 0) }} د.ع</td>
                                    <td>{{ number_format($order->discount_amount ?? 0, 0) }} د.ع</td>
                                    <td>
                                        <span class="badge @if($order->status == 'pending') bg-warning text-dark @elseif($order->status == 'processing') bg-info text-dark @elseif($order->status == 'shipped') bg-primary @elseif($order->status == 'delivered') bg-success @elseif($order->status == 'cancelled') bg-secondary @elseif($order->status == 'returned') bg-danger @endif">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary px-2" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-4">لا توجد طلبات لهذا العميل.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <form method="GET" action="{{ route('admin.customers.show', $customer->id) }}" class="d-flex align-items-center">
                        @foreach(request()->except(['per_page', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="per_page" class="me-2">عدد الطلبات:</label>
                        <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 80px;">
                            @foreach([5, 15, 25, 50] as $size)
                                <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </form>
                
                    <div>
                        {{ $orders->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($customer->user?->addresses->isNotEmpty())
        @foreach($customer->user->addresses as $address)
            @if($address->latitude && $address->longitude)
                const map_{{ $address->id }} = L.map('map-{{ $address->id }}').setView([{{ $address->latitude }}, {{ $address->longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map_{{ $address->id }});
                L.marker([{{ $address->latitude }}, {{ $address->longitude }}]).addTo(map_{{ $address->id }});
            @endif
        @endforeach
    @endif
});
</script>
@endpush
