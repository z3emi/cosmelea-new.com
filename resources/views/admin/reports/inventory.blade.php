@extends('admin.layout')

@section('title', 'تقارير المخزون')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 0;
        border-radius: .75rem !important;
        position: relative;
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
    {{-- فلتر الشهر والسنة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">تقارير المخزون</h1>
        <form method="GET" action="{{ route('admin.reports.stock') }}" class="d-flex gap-2 align-items-center bg-white p-2 rounded-3 shadow-sm">
            <select name="month" class="form-select form-select-sm">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ ($month ?? now()->month) == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select form-select-sm">
                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
            <button type="submit" class="btn btn-sm btn-primary">تطبيق</button>
        </form>
    </div>

    <div class="row g-4">
        {{-- المنتجات على وشك النفاد --}}
        <div class="col-lg-6">
            <div class="card stat-card shadow-sm h-100" style="background-color: #fff3cd;">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>منتجات على وشك النفاد</h5>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->isEmpty())
                        <p class="text-center text-muted mt-3">لا توجد منتجات على وشك النفاد حاليًا.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($lowStockProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.products.edit', $product->id) }}">{{ $product->name_ar }}</a>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $product->stock_quantity }} قطعة متبقية</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- المنتجات النافدة --}}
        <div class="col-lg-6">
            <div class="card stat-card shadow-sm h-100" style="background-color: #f8d7da;">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-x-octagon-fill me-2"></i>منتجات نفدت من المخزون</h5>
                </div>
                <div class="card-body">
                    @if($outOfStockProducts->isEmpty())
                        <p class="text-center text-muted mt-3">لا توجد منتجات نافدة حاليًا.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($outOfStockProducts as $product)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.products.edit', $product->id) }}">{{ $product->name_ar }}</a>
                                    <span class="badge bg-danger rounded-pill">نفدت الكمية</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- المنتجات الأكثر مبيعًا --}}
        <div class="col-12">
            <div class="card stat-card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-trophy-fill me-2"></i>المنتجات الأكثر مبيعًا (آخر 30 يوم)</h5>
                </div>
                <div class="card-body">
                    @if($topSellingProducts->isEmpty())
                        <p class="text-center text-muted mt-3">لا توجد بيانات مبيعات كافية.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th class="text-center">عدد مرات البيع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSellingProducts as $product)
                                        <tr>
                                            <td>{{ $product->name_ar }}</td>
                                            <td class="text-center fw-bold">{{ $product->order_items_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
