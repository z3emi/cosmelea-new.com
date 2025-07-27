@extends('admin.layout')
@section('title', 'تقارير العملاء')
@push('styles')
<style>
    /* إعدادات عامة للبطاقات الإحصائية */
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 10px !important;
        overflow: hidden;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    /* ألوان مخصصة للبطاقات - نفس الألوان المستخدمة في الصفحة المالية */
    .bg-success-custom {
        background-color: #d1e7dd !important;
    }
    
    .bg-primary-custom {
        background-color: #cfe2ff !important;
    }
    
    .bg-danger-custom {
        background-color: #f8d7da !important;
    }
    
    .bg-warning-custom {
        background-color: #fff3cd !important;
    }
    
    .text-success-custom {
        color: #198754 !important;
    }
    
    .text-primary-custom {
        color: #0d6efd !important;
    }
    
    .text-danger-custom {
        color: #dc3545 !important;
    }
    
    .text-warning-custom {
        color: #ffc107 !important;
    }
    
    /* تصميم رأس البطاقة */
    .card-header {
        border-bottom: none;
        padding: 1rem 1.5rem;
    }
    
    /* تصميم القائمة */
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    /* تصميم الوسوم */
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 600;
        border-radius: 6px;
    }
    
    /* تصميم الفلتر */
    .filter-form {
        background-color: var(--white);
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .filter-form select, .filter-form button {
        border-radius: 6px;
        border: 1px solid var(--secondary-light);
    }
    
    /* تصميم جدول العملاء غير النشطين */
    .table th {
        font-weight: 600;
        color: var(--text-dark);
        border-bottom-width: 2px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(222, 172, 169, 0.05);
    }
    
    /* أيقونات محسنة */
    .card-header i {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }
    
    /* تأثير التحوصل على الصور */
    .img-thumbnail {
        border: 1px solid var(--secondary-light);
        transition: transform 0.2s;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.05);
    }
    
    /* تصميم خاص للبطاقات في صفحة التقارير المالية */
    .stat-card-financial {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
    }
    
    .stat-card-financial:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }
    
    /* تصميم خاص للبطاقات في صفحة تقارير العملاء */
    .stat-card-customers {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
    }
    
    .stat-card-customers:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }
    
    /* تصميم خاص للبطاقات في صفحة التقارير المالية */
    .stat-card-financial .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }
    
    /* تصميم خاص للبطاقات في صفحة تقارير العملاء */
    .stat-card-customers .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }
    
    /* تصميم خاص للبطاقات في صفحة التقارير المالية */
    .stat-card-financial .card-body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    /* تصميم خاص للبطاقات في صفحة تقارير العملاء */
    .stat-card-customers .card-body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    /* تصميم خاص للبطاقات في صفحة التقارير المالية */
    .stat-card-financial .card-body .fw-bold {
        font-size: 1.5rem;
    }
    
    /* تصميم خاص للبطاقات في صفحة تقارير العملاء */
    .stat-card-customers .card-body .fw-bold {
        font-size: 1.5rem;
    }
    
    /* تصميم خاص للبطاقات في صفحة التقارير المالية */
    .stat-card-financial .card-body .badge {
        font-size: 0.9rem;
    }
    
    /* تصميم خاص للبطاقات في صفحة تقارير العملاء */
    .stat-card-customers .card-body .badge {
        font-size: 0.9rem;
    }
</style>
@endpush
@section('content')
<div class="container-fluid">
    {{-- فلتر الشهر والسنة في الأعلى --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">تقارير العملاء</h1>
        <form method="GET" action="{{ route('admin.reports.customers') }}" class="d-flex gap-2 align-items-center filter-form p-2">
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
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search me-1"></i> تطبيق
            </button>
        </form>
    </div>
    
    <div class="row g-4">
        {{-- أفضل العملاء (قيمة المشتريات) --}}
        <div class="col-lg-6">
            <div class="card stat-card stat-card-customers shadow-sm h-100">
                <div class="card-header text-primary-custom">
                    <h5 class="mb-0"><i class="bi bi-gem"></i> أفضل العملاء (قيمة المشتريات)</h5>
                </div>
                <div class="card-body">
                    @if($topSpenders->isEmpty())
                        <div class="text-center text-muted mt-4">
                            <i class="bi bi-exclamation-circle fs-3 mb-2"></i>
                            <p>لا توجد بيانات كافية.</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($topSpenders as $customer)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="https://picsum.photos/seed/{{ $customer->id }}/40/40.jpg" class="rounded-circle me-2" alt="Customer">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-decoration-none text-dark">{{ $customer->name }}</a>
                                    </div>
                                    <span class="badge bg-success-custom text-success-custom rounded-pill">{{ number_format($customer->orders_sum_total_amount, 0) }} د.ع</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- أفضل العملاء (عدد الطلبات) --}}
        <div class="col-lg-6">
            <div class="card stat-card stat-card-customers shadow-sm h-100">
                <div class="card-header text-primary-custom">
                    <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> أفضل العملاء (عدد الطلبات)</h5>
                </div>
                <div class="card-body">
                    @if($mostFrequentBuyers->isEmpty())
                        <div class="text-center text-muted mt-4">
                            <i class="bi bi-exclamation-circle fs-3 mb-2"></i>
                            <p>لا توجد بيانات كافية.</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($mostFrequentBuyers as $customer)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="https://picsum.photos/seed/{{ $customer->id }}/40/40.jpg" class="rounded-circle me-2" alt="Customer">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-decoration-none text-dark">{{ $customer->name }}</a>
                                    </div>
                                    <span class="badge bg-primary-custom text-primary-custom rounded-pill">{{ $customer->orders_count }} طلب</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- العملاء غير النشطين --}}
        <div class="col-12">
            <div class="card stat-card stat-card-customers shadow-sm">
                <div class="card-header text-primary-custom">
                    <h5 class="mb-0"><i class="bi bi-moon-stars-fill"></i> عملاء غير نشطين (آخر 90 يومًا)</h5>
                </div>
                <div class="card-body">
                    @if($inactiveCustomers->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-1 mb-2"></i>
                            <p class="fs-5">لا يوجد عملاء غير نشطين حاليًا.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>العميل</th>
                                        <th>رقم الهاتف</th>
                                        <th class="text-center">تاريخ آخر طلب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inactiveCustomers as $customer)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://picsum.photos/seed/{{ $customer->id }}/40/40.jpg" class="rounded-circle me-2" alt="Customer">
                                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-decoration-none text-dark">{{ $customer->name }}</a>
                                                </div>
                                            </td>
                                            <td>{{ $customer->phone_number }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ optional($customer->orders->max('created_at'))->format('Y-m-d') ?? 'لا يوجد' }}</span>
                                            </td>
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