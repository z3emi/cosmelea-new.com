@extends('admin.layout')

@section('title', 'إدارة المستخدمين')

@push('styles')
<style>
    /* تمييز المستخدم المحظور باللون الأحمر */
    .table-danger, .table-danger > th, .table-danger > td {
        background-color: #fbe9e7 !important;
        text-decoration: line-through;
        opacity: 0.7;
    }
    /* === START: التعديل المطلوب === */
    /* تمييز المستخدم غير المفعل باللون الرمادي */
    .table-inactive, .table-inactive > th, .table-inactive > td {
        background-color: #f1f3f5 !important; /* لون رمادي فاتح */
        opacity: 0.8;
    }
    /* === END: التعديل المطلوب === */

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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">جميع المستخدمين</h4>
        <div>
            <a href="{{ route('admin.users.inactive') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-clock-history me-1"></i>
                المستخدمون قيد التفعيل
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm" style="background-color: #cd8985; border-color: #cd8985;">
                <i class="bi bi-plus-circle me-1"></i>
                إضافة مستخدم جديد
            </a>
            <form action="{{ route('admin.users.forceLogoutAll') }}" method="POST" class="d-inline" onsubmit="return confirm('تسجيل خروج جميع المستخدمين؟');">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    تسجيل خروج الكل
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        {{-- فورم البحث --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو رقم الهاتف..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary" style="background-color: #cd8985; border-color: #cd8985;">
                    <i class="bi bi-search me-1"></i> بحث
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>رقم الهاتف</th>
                        <th>الحالة</th>
                        <th>النوع</th>
                        <th>تاريخ التسجيل</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        {{-- === START: التعديل المطلوب === --}}
                        <tr @class([
                            'table-danger' => $user->banned_at,
                            'table-inactive' => is_null($user->phone_verified_at) && is_null($user->banned_at)
                        ])>
                        {{-- === END: التعديل المطلوب === --}}
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>
                                {{-- === START: التعديل المطلوب === --}}
                                @if($user->banned_at)
                                    <span class="badge bg-danger">محظور</span>
                                @elseif(is_null($user->phone_verified_at))
                                    <span class="badge bg-secondary">غير مفعل</span>
                                @else
                                    <span class="badge bg-success">نشط</span>
                                @endif
                                {{-- === END: التعديل المطلوب === --}}
                            </td>
                            <td>
                                @if ($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        @if($role->name == 'Super-Admin')
                                            <span class="badge bg-danger">{{ $role->name }}</span>
                                        @else
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endif
                                    @endforeach
                                @elseif ($user->permissions->isNotEmpty())
                                    <span class="badge bg-info text-dark">خاص</span>
                                @else
                                    <span class="badge bg-secondary">مستخدم</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-info m-1 px-2" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->banned_at)
                                    <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success m-1 px-2" title="إلغاء الحظر">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حظر">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.users.forceLogout', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('تسجيل خروج هذا المستخدم؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning m-1 px-2" title="تسجيل خروج">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary m-1 px-2" title="تسجيل الدخول كمستخدم">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">لا يوجد مستخدمين لعرضهم.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- إضافة اختيار عدد المستخدمين بالصفحة + عرض التصفح --}}
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex align-items-center">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="per_page" class="me-2">عدد المستخدمين:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 5) == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div>
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
