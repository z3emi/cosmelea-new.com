@extends('admin.layout')

@section('title', 'إدارة المنتجات')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0">جميع المنتجات</h4>

        @can('create-products')
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>
            إضافة منتج جديد
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" style="min-width: 950px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th style="width: 35%;">المنتج</th>
                        <th>سعر البيع</th>
                        <th>سعر الخصم</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr @class(['opacity-50' => !$product->is_active])>
                            <td>{{ $product->id }}</td>
                            
                            <td class="text-start">
                                <div class="d-flex align-items-center">
                                    {{-- ===== START: التعديل المطلوب ===== --}}
                                    @if ($product->firstImage)
                                        <img src="{{ asset('storage/' . $product->firstImage->image_path) }}" alt="{{ $product->name_ar }}" width="60" class="img-thumbnail me-3">
                                    @else
                                        <img src="https://placehold.co/60x60?text=No+Image" alt="No Image" class="img-thumbnail me-3">
                                    @endif
                                    {{-- ===== END: التعديل المطلوب ===== --}}
                                    <div>
                                        <h6 class="mb-0">{{ $product->name_ar }}</h6>
                                        <small class="text-muted">SKU: <strong>{{ $product->sku ?? 'N/A' }}</strong></small>
                                    </div>
                                </div>
                            </td>

                            <td>{{ number_format($product->price, 0) }} د.ع</td>
                            <td>
                                @if($product->sale_price)
                                    <span class="text-success fw-bold">{{ number_format($product->sale_price, 0) }} د.ع</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-secondary">غير فعال</span>
                                @endif
                            </td>
                            <td>
                                @can('edit-products')
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary m-1 px-2" title="تعديل"><i class="bi bi-pencil"></i></a>

                                <form action="{{ route('admin.products.toggleStatus', $product->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} m-1 px-2" title="{{ $product->is_active ? 'إيقاف' : 'تفعيل' }}">
                                        <i class="bi {{ $product->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                    </button>
                                </form>
                                @endcan

                                @can('delete-products')
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">لا توجد منتجات لعرضها.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- إضافة اختيار عدد المنتجات بالصفحة + عرض التصفح --}}
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex align-items-center">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="per_page" class="me-2">عدد المنتجات:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 5) == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div>
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
