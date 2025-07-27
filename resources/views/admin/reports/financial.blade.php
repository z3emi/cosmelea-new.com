@extends('admin.layout')

@section('title', 'لوحة التقارير المالية')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 0;
        border-radius: .75rem !important;
        position: relative; /* Required for the stretched-link to work */
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .text-purple { color: #6f42c1 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- فلتر الشهر والسنة في الأعلى --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">التقارير المالية</h1>
        <form method="GET" action="{{ route('admin.reports.financial') }}" class="d-flex gap-2 align-items-center bg-white p-2 rounded-3 shadow-sm">
            <select name="month" class="form-select form-select-sm">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select form-select-sm">
                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
            <button type="submit" class="btn btn-sm btn-primary">تطبيق</button>
        </form>
    </div>

    {{-- بطاقات الملخص العام --}}
    <div class="row g-4 mb-4">
        @php
            $stats = [
                ['label' => 'إجمالي المبيعات', 'value' => number_format($summary->total_sales, 0) . ' د.ع', 'icon' => 'cash-stack', 'bg' => '#eadbcd', 'color' => 'text-dark', 'route' => route('admin.orders.index')],
                ['label' => 'إجمالي المشتريات', 'value' => number_format($totalPurchases, 0) . ' د.ع', 'icon' => 'truck', 'bg' => '#cfe2ff', 'color' => 'text-primary', 'route' => route('admin.purchases.index')],
                ['label' => 'إجمالي المصاريف', 'value' => number_format($totalExpenses, 0) . ' د.ع', 'icon' => 'receipt-cutoff', 'bg' => '#f8d7da', 'color' => 'text-danger', 'route' => route('admin.expenses.index')],
                ['label' => 'الربح الصافي', 'value' => number_format($netProfit, 0) . ' د.ع', 'icon' => 'wallet2', 'bg' => '#d1e7dd', 'color' => 'text-success', 'route' => null],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100" style="background-color: {{ $stat['bg'] }};">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 fs-2 {{ $stat['color'] }}">
                            <i class="bi bi-{{ $stat['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="fw-semibold fs-6">{{ $stat['label'] }}</div>
                            @php
                                $value = $stat['label'] === 'الربح الصافي'
                                    ? '<span class="' . ($netProfit < 0 ? 'text-danger' : $stat['color']) . '">' . number_format($netProfit, 0) . ' د.ع</span>'
                                    : $stat['value'];
                            @endphp
                            <div class="fs-4 fw-bold">{!! $value !!}</div>
                        </div>
                    </div>
                    @if($stat['route'])
                        <a href="{{ $stat['route'] }}" class="stretched-link"></a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- الرسم البياني --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h6 class="mb-3">الأداء المالي خلال الفترة المحددة</h6>
            <canvas id="financialChart" height="100"></canvas>
        </div>
    </div>

    <div class="row g-4">
        {{-- المنتجات الأكثر ربحية --}}
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4 border-0 h-100">
                <div class="card-body">
                    <h6 class="mb-3">المنتجات الأكثر ربحية</h6>
                    <ul class="list-group list-group-flush">
                        @forelse ($topProfitableProducts as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
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
        {{-- المنتجات الأكثر مبيعاً --}}
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4 border-0 h-100">
                <div class="card-body">
                    <h6 class="mb-3">المنتجات الأكثر مبيعاً</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($topSellingProducts as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                {{ $item->product->name_ar ?? 'منتج محذوف' }}
                                <span class="badge bg-primary rounded-pill fs-6">{{ $item->total_quantity_sold }} قطعة</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center">لا توجد بيانات كافية.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('financialChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'إجمالي المبيعات',
                    data: @json($salesData),
                    borderColor: '#be6661',
                    backgroundColor: 'rgba(205, 137, 133, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'الربح الإجمالي',
                    data: @json($profitData),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('ar-IQ').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('ar-IQ').format(context.parsed.y) + ' د.ع';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
