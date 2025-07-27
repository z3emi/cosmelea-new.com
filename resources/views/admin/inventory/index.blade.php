@extends('admin.layout')

@section('title', 'إدارة المخزون')

@push('styles')
<style>
    /*
     * تم تحديث هذا القسم بالكامل ليتناسب مع هوية التصميم
     * وإضافة الألوان المريحة للعين
    */
    .summary-card {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-left: 5px solid #cd8985;
        border-radius: .5rem;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
    }
    .summary-card .icon {
        font-size: 2rem;
        color: #cd8985;
    }
    .summary-card h6 {
        color: #6c757d;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    .summary-card .value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #343a40;
    }
    /* تعديل صف ملخص المنتج */
    .product-summary-row {
        color: #333 !important; /* لون نص داكن ليتناسب مع الخلفيات الفاتحة */
        font-weight: bold;
        border-top: 2px solid #fff !important; /* فاصل بين المنتجات */
    }
    .product-summary-row td {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    /* صف الوجبة مع سهم التفرع */
    .batch-row td:first-child::before {
        content: '↳';
        margin-left: 1.5rem;
        margin-right: 0.5rem;
        color: #6c757d;
        font-weight: bold;
    }
    .batch-row:hover {
        background-color: #f5f5f5; /* لون hover محايد */
    }
</style>
@endpush

@section('content')

@php
    // حساب الإجماليات لعرضها في بطاقات الملخص
    $grandTotalValue = 0;
    $grandTotalQuantity = 0;
    $uniqueProductsCount = $stockItems->count();

    foreach ($stockItems as $items) {
        foreach($items as $item) {
            $grandTotalValue += $item->quantity_remaining * $item->purchase_price;
            $grandTotalQuantity += $item->quantity_remaining;
        }
    }
    
    // مصفوفة من الألوان الناعمة والمريحة للعين
    $productColors = ['#fdf0f0', '#e6f3f8', '#e8f5e9', '#fffde7', '#f3e5f5', '#e0f7fa'];
@endphp

<div class="card shadow-sm">
    <div class="card-header" style="background-color: #f9f5f1; border-bottom: 2px solid #cd8985;">
        <h4 class="mb-0" style="color: #cd8985;">نظرة عامة على المخزون</h4>
    </div>
    <div class="card-body">
        {{-- قسم الملخص العام --}}
        <div class="row">
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="icon"><i class="bi bi-wallet2"></i></div>
                    <h6>القيمة الإجمالية للمخزون</h6>
                    <div class="value">{{ number_format($grandTotalValue, 0) }} د.ع</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="icon"><i class="bi bi-boxes"></i></div>
                    <h6>إجمالي عدد القطع</h6>
                    <div class="value">{{ $grandTotalQuantity }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div class="icon"><i class="bi bi-tags"></i></div>
                    <h6>عدد المنتجات الفريدة</h6>
                    <div class="value">{{ $uniqueProductsCount }}</div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">تفاصيل وجبات المخزون</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>المنتج</th>
                        <th>تاريخ الشراء</th>
                        <th>المورّد</th>
                        <th>سعر الكلفة (للوجبة)</th>
                        <th>سعر البيع (الحالي)</th>
                        <th>الكمية المتبقية</th>
                        <th>قيمة الوجبة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stockItems as $productId => $items)
                        @php
                            $product = $items->first()->product;
                            $totalProductQuantity = $items->sum('quantity_remaining');
                            $totalProductValue = 0;
                            foreach($items as $item) {
                                $totalProductValue += $item->quantity_remaining * $item->purchase_price;
                            }
                            // اختيار لون للمنتج الحالي من مصفوفة الألوان
                            $color = $productColors[$loop->index % count($productColors)];
                        @endphp
                        {{-- صف خاص بالمنتج لعرض الإجماليات مع تطبيق اللون --}}
                        <tr class="product-summary-row" style="background-color: {{ $color }};">
                            <td colspan="5" class="text-start ps-3">{{ $product->name_ar }}</td>
                            <td class="text-center">{{ $totalProductQuantity }}</td>
                            <td class="text-center">{{ number_format($totalProductValue, 0) }} د.ع</td>
                        </tr>
                        {{-- عرض كل وجبة متوفرة من هذا المنتج --}}
                        @foreach ($items as $item)
                            <tr class="batch-row">
                                <td></td> {{-- هذا العمود يعرض سهم التفرع من خلال CSS --}}
                                <td class="text-center">{{ $item->purchaseInvoice->invoice_date->format('Y-m-d') }}</td>
                                <td class="text-center">{{ $item->purchaseInvoice->supplier->name ?? '-' }}</td>
                                <td class="text-center">{{ number_format($item->purchase_price, 0) }} د.ع</td>
                                <td class="text-center">{{ number_format($product->price, 0) }} د.ع</td>
                                <td class="text-center">{{ $item->quantity_remaining }}</td>
                                <td class="text-center">{{ number_format($item->quantity_remaining * $item->purchase_price, 0) }} د.ع</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-4">لا توجد بضاعة في المخزون حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
