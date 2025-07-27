@extends('admin.layout')

@section('title', 'لوحة التحكم الرئيسية')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease-in-out;
    }
    .stat-card:hover {
        transform: scale(1.03);
    }
    .text-purple {
        color: #6f42c1;
    }
</style>
@endpush

@section('content')
<h1 class="h3 mb-4 fw-bold">لوحة التحكم</h1>

{{-- كروت الإحصائيات الرئيسية --}}
<div class="row g-4 mb-4">
    {{-- **THE CHANGE**: Each card is now wrapped in a @can directive --}}
    @can('view-orders')
    @php
        $stats = [
            ['label' => 'إجمالي الطلبات', 'value' => $totalOrders, 'icon' => 'receipt', 'bg' => '#cccccc', 'color' => 'text-purple', 'route' => route('admin.orders.index')],
            ['label' => 'طلبات قيد الانتظار', 'value' => $pendingOrders, 'icon' => 'clock-history', 'bg' => '#fff3cd', 'color' => 'text-warning', 'route' => route('admin.orders.index', ['status' => 'pending'])],
            ['label' => 'طلبات مكتملة', 'value' => $completedOrders, 'icon' => 'check-circle', 'bg' => '#d1e7dd', 'color' => 'text-success', 'route' => route('admin.orders.index', ['status' => 'delivered'])],
            ['label' => 'طلبات راجعة', 'value' => $returnedOrders, 'icon' => 'arrow-return-left', 'bg' => '#f8d7da', 'color' => 'text-danger', 'route' => route('admin.orders.index', ['status' => 'returned'])],
        ];
    @endphp
    @foreach ($stats as $stat)
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card text-dark border-0 shadow-sm rounded-3 h-100" style="background-color: {{ $stat['bg'] }};">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 fs-2 {{ $stat['color'] }}">
                        <i class="bi bi-{{ $stat['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-semibold fs-6">{{ $stat['label'] }}</div>
                        <div class="fs-4 fw-bold">{{ $stat['value'] }}</div>
                    </div>
                </div>
                <a href="{{ $stat['route'] }}" class="stretched-link"></a>
            </div>
        </div>
    @endforeach
    @endcan
</div>

{{-- كروت إضافية --}}
<div class="row g-4 mb-4">
    @can('view-customers')
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm rounded-3 h-100 text-white" style="background-color: #cd8985;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 fs-2"><i class="bi bi-people"></i></div>
                <div>
                    <div class="fw-semibold fs-6">العملاء النشطين</div>
                    <div class="fs-4 fw-bold">{{ $activeCustomers }}</div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('view-orders')
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm rounded-3 h-100 text-white" style="background-color: #be6661;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 fs-2"><i class="bi bi-calendar-day"></i></div>
                <div>
                    <div class="fw-semibold fs-6">الطلبات اليوم</div>
                    <div class="fs-4 fw-bold">{{ $todayOrders }}</div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('view-reports')
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm rounded-3 h-100 text-dark" style="background-color: #eadbcd;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 fs-2"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <div class="fw-semibold fs-6">إجمالي المبيعات</div>
                    <div class="fs-4 fw-bold">{{ number_format($totalSales, 0) }} د.ع</div>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>

{{-- الرسم البياني --}}
@can('view-reports')
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="mb-3">عدد الطلبات خلال آخر 30 يوم حسب الحالة</h6>
        <canvas id="ordersChart" height="100"></canvas>
    </div>
</div>
@endcan

{{-- آخر الطلبات --}}
@can('view-orders')
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="mb-3">آخر الطلبات</h6>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>العميل</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer->name ?? '—' }}</td>
                        <td>{{ number_format($order->total_amount, 0) }} د.ع</td>
                        <td>{{ $statusLabels[$order->status] ?? $order->status }}</td>
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">لا توجد طلبات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endcan

<div class="row g-4">
    {{-- المنتجات الأكثر مبيعاً --}}
    @can('view-products')
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h6 class="mb-3">المنتجات الأكثر مبيعاً</h6>
                <ul class="list-group">
                    @forelse ($topProducts as $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $product->name_ar ?? $product->name }}
                            <span class="badge bg-primary rounded-pill">{{ $product->orders_count }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center">لا توجد بيانات</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    @endcan

    {{-- المنتجات الأكثر ربحية --}}
    @can('view-reports')
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <h6 class="mb-3">المنتجات الأكثر ربحية</h6>
                <ul class="list-group">
                    @forelse ($topProfitableProducts as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->product->name_ar ?? 'منتج محذوف' }}
                            <span class="badge bg-success rounded-pill fs-6">{{ number_format($item->profit, 0) }} د.ع</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center">لا توجد بيانات كافية.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    @endcan
</div>

{{-- العملاء الأكثر نشاطاً --}}
@can('view-customers')
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="mb-3">العملاء الأكثر نشاطاً</h6>
        <ul class="list-group">
            @forelse ($topCustomers as $customer)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $customer->name }}
                    <span class="badge bg-success rounded-pill">{{ $customer->orders_count }} طلب</span>
                </li>
            @empty
                <li class="list-group-item text-center">لا توجد بيانات</li>
            @endforelse
        </ul>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ordersChart');
    if (ctx) {
        const ordersChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'إجمالي الطلبات',
                        data: {!! json_encode($chartTotalOrdersData) !!},
                        borderColor: '#6f42c1',
                        backgroundColor: 'rgba(111, 66, 193, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'طلبات مكتملة',
                        data: {!! json_encode($chartCompletedOrdersData) !!},
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'طلبات قيد الانتظار',
                        data: {!! json_encode($chartPendingOrdersData) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'طلبات راجعة',
                        data: {!! json_encode($chartReturnedOrdersData) !!},
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
@endpush
