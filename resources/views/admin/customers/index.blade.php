@extends('admin.layout')
@section('title', 'إدارة العملاء')

@push('styles')
<style>
    /* تمييز العملاء المميزين */
    .table-row-gold {
        background-color: #fff8e1 !important;
    }
    .table-row-bronze {
        background-color: #fcece0 !important;
    }
    /* تمييز العميل المحظور باللون الأحمر */
    .table-danger {
        background-color: #fbe9e7 !important;
    }
    .table-danger td {
        text-decoration: line-through;
        opacity: 0.8;
    }
    /* تخصيص شكل روابط التنقل ليتناسب مع التصميم */
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
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0">جميع العملاء</h4>

        @can('create-customers')
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm" style="background-color: #cd8985; border-color: #cd8985;">
            <i class="bi bi-plus-circle me-1"></i> إضافة عميل يدوي
        </a>
        @endcan
    </div>
    <div class="card-body">

        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 mb-4">
            <div class="col">
                <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو رقم الهاتف..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>محظور</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" style="background-color: #cd8985; border-color: #cd8985;">
                    <i class="bi bi-search me-1"></i> بحث
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" style="min-width: 900px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الصورة</th> {{-- ===== تم إضافة هذا العمود ===== --}}
                        <th>اسم العميل</th>
                        <th>رقم الهاتف</th>
                        <th>الحالة</th>
                        <th>الطلبات المكتملة</th>
                        <th>الملاحظات</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        <tr @class([
                            'table-danger' => $customer->user?->banned_at,
                            'table-row-gold' => !$customer->user?->banned_at && $customer->orders_count >= 10,
                            'table-row-bronze' => !$customer->user?->banned_at && $customer->orders_count >= 5,
                        ])>
                            <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $index + 1 }}</td>
                            {{-- ===== START: تم إضافة هذا الحقل ===== --}}
                            <td>
                                @if($customer->user && $customer->user->avatar)
                                    <img src="{{ asset('storage/' . $customer->user->avatar) }}" alt="{{ $customer->name }}" class="rounded-circle mx-auto" width="40" height="40" style="object-fit: cover;">
                                @else
                                    <img src="https://i.pravatar.cc/40?u={{ $customer->id }}" alt="Avatar" class="rounded-circle mx-auto">
                                @endif
                            </td>
                            {{-- ===== END: تم إضافة هذا الحقل ===== --}}                            
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone_number }}</td>
                            <td>
                                @if($customer->user?->banned_at)
                                    <span class="badge bg-danger">محظور</span>
                                @else
                                    <span class="badge bg-success">نشط</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $customer->orders_count }}</span>
                            </td>
                            <td>{{ $customer->notes ?? '-' }}</td>
                            <td>
                                @can('view-customers')
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary m-1 px-2" title="عرض الطلبات">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endcan

                                @can('edit-customers')
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-info m-1 px-2" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan

                                @can('ban-customers')
                                    @if($customer->user?->banned_at)
                                        <form action="{{ route('admin.customers.unban', $customer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success m-1 px-2" title="إلغاء الحظر">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.customers.ban', $customer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حظر">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">لا يوجد عملاء لعرضهم.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex align-items-center">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="per_page" class="me-2">عدد العملاء:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 20) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>

            <div>
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
