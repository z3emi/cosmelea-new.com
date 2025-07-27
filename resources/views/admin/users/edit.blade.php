@extends('admin.layout')

@section('title', 'تعديل المستخدم: ' . $user->name)

@php
    // =========================================================
    // نفس منطق تجميع الصلاحيات من صفحة تعديل الأدوار
    // =========================================================
    $groupDisplayNames = [
        'المنتجات' => 'المنتجات',
        'التصنيفات' => 'التصنيفات',
        'الطلبات' => 'الطلبات',
        'المستخدمون والأدوار' => 'المستخدمون والأدوار',
        'العملاء' => 'العملاء',
        'الموردون والمشتريات' => 'الموردون والمشتريات',
        'الشؤون المالية والمخزون' => 'الشؤون المالية والمخزون',
        'كود الخصم' => 'كود الخصم',
        'لوحة التحكم' => 'لوحة التحكم',
        'أخرى' => 'أخرى',
    ];

    function friendlyPermissionName($perm) {
        $map = [
            'view-products' => 'عرض المنتجات', 'create-products' => 'إنشاء منتجات', 'edit-products' => 'تعديل المنتجات', 'delete-products' => 'حذف المنتجات',
            'view-categories' => 'عرض التصنيفات', 'create-categories' => 'إنشاء تصنيفات', 'edit-categories' => 'تعديل التصنيفات', 'delete-categories' => 'حذف التصنيفات',
            'view-orders' => 'عرض الطلبات', 'create-orders' => 'إنشاء طلبات', 'edit-orders' => 'تعديل الطلبات', 'delete-orders' => 'حذف الطلبات', 'view-trashed-orders' => 'عرض المحذوفة', 'restore-orders' => 'استعادة الطلبات', 'force-delete-orders' => 'حذف نهائي',
            'view-users' => 'عرض المستخدمين', 'create-users' => 'إنشاء مستخدمين', 'edit-users' => 'تعديل المستخدمين', 'delete-users' => 'حذف المستخدمين', 'ban-users' => 'حظر المستخدمين',
            'view-roles' => 'عرض الأدوار', 'create-roles' => 'إنشاء أدوار', 'edit-roles' => 'تعديل الأدوار', 'delete-roles' => 'حذف الأدوار',
            'view-customers' => 'عرض العملاء', 'create-customers' => 'إنشاء عملاء', 'edit-customers' => 'تعديل العملاء', 'delete-customers' => 'حذف العملاء', 'ban-customers' => 'حظر العملاء',
            'view-suppliers' => 'عرض الموردين', 'create-suppliers' => 'إنشاء موردين', 'edit-suppliers' => 'تعديل الموردين', 'delete-suppliers' => 'حذف الموردين',
            'view-purchases' => 'عرض المشتريات', 'create-purchases' => 'إنشاء مشتريات', 'edit-purchases' => 'تعديل المشتريات', 'delete-purchases' => 'حذف المشتريات',
            'view-expenses' => 'عرض المصاريف', 'create-expenses' => 'إنشاء مصاريف', 'edit-expenses' => 'تعديل المصاريف', 'delete-expenses' => 'حذف المصاريف',
            'view-inventory' => 'عرض المخزون', 'view-reports' => 'عرض التقارير',
            'view-discount-codes' => 'عرض أكواد الخصم', 'create-discount-codes' => 'إنشاء أكواد خصم', 'edit-discount-codes' => 'تعديل أكواد خصم', 'delete-discount-codes' => 'حذف أكواد الخصم',
            'view-admin-panel' => 'الدخول إلى لوحة التحكم',
        ];
        return $map[$perm] ?? '';
    }

    function permissionGroupName($permissionName) {
        if (str_contains($permissionName, 'product')) return 'المنتجات';
        if (str_contains($permissionName, 'category')) return 'التصنيفات';
        if (str_contains($permissionName, 'order')) return 'الطلبات';
        if (str_contains($permissionName, 'user') || str_contains($permissionName, 'role') || str_contains($permissionName, 'ban')) return 'المستخدمون والأدوار';
        if (str_contains($permissionName, 'customer')) return 'العملاء';
        if (str_contains($permissionName, 'supplier') || str_contains($permissionName, 'purchase')) return 'الموردون والمشتريات';
        if (str_contains($permissionName, 'expense') || str_contains($permissionName, 'inventory') || str_contains($permissionName, 'report')) return 'الشؤون المالية والمخزون';
        if (str_contains($permissionName, 'discount')) return 'كود الخصم';
        if ($permissionName === 'view-admin-panel') return 'لوحة التحكم';
        return 'أخرى';
    }

    $groupedPermissions = [];
    foreach ($permissions as $permission) {
        $group = permissionGroupName($permission->name);
        $groupedPermissions[$group][] = $permission;
    }
@endphp

@section('content')
<form action="{{ route('admin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- بداية الأكورديون للأقسام المنسدلة --}}
    <div class="accordion" id="userEditAccordion">

        {{-- ========================================================= --}}
        {{-- القسم الأول: تعديل المعلومات الأساسية --}}
        {{-- ========================================================= --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    تعديل معلومات المستخدم
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#userEditAccordion">
                <div class="accordion-body">
                    {{-- عرض الأخطاء --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة (اتركه فارغاً لعدم التغيير)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- القسم الثاني: الأدوار والصلاحيات --}}
        {{-- ========================================================= --}}
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    الأدوار والصلاحيات
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#userEditAccordion">
                <div class="accordion-body">
                    <div class="mb-3">
                        <h6>الأدوار (Roles)</h6>
                        <p class="text-muted small">حدد الأدوار التي سينتمي إليها المستخدم. سيرث المستخدم جميع صلاحيات الدور المحدد.</p>
                        @forelse($roles as $role)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                            </div>
                        @empty
                            <p class="text-muted">لا توجد أدوار معرفة.</p>
                        @endforelse
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h5 class="mt-4">الصلاحيات المباشرة (Direct Permissions)</h5>
                        <p class="text-muted small">يمكنك منح صلاحيات إضافية للمستخدم بشكل مباشر. (يفضل استخدام الأدوار)</p>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="select_all_permissions">
                            <label class="form-check-label fw-bold" for="select_all_permissions">تحديد كل الصلاحيات</label>
                        </div>

                        <div class="row gy-4">
                            @foreach ($groupedPermissions as $groupName => $groupPermissions)
                                <div class="col-md-6 col-lg-4">
                                    <div class="p-3 border rounded shadow-sm h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-primary fw-bold m-0">{{ $groupDisplayNames[$groupName] ?? $groupName }}</h6>
                                            <div class="form-check m-0">
                                                <input type="checkbox" class="form-check-input select-group" id="select_group_{{ $loop->index }}" data-group="{{ $loop->index }}">
                                                <label class="form-check-label" for="select_group_{{ $loop->index }}">تحديد الكل</label>
                                            </div>
                                        </div>

                                        @foreach ($groupPermissions as $permission)
                                            <div class="form-check mb-2 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <input
                                                        class="form-check-input permission-checkbox group-{{ $loop->parent->index }}"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        id="permission_{{ $permission->id }}"
                                                        {{-- ملاحظة: نستخدم hasPermissionTo للتحقق من الصلاحيات المباشرة --}}
                                                        {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label ms-2" for="permission_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                                @if(friendlyPermissionName($permission->name))
                                                    <small class="text-muted" style="font-size: 0.85em;">{{ friendlyPermissionName($permission->name) }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- نهاية الأكورديون --}}

    {{-- ========================================================= --}}
    {{-- القسم الثالث: سجل طلبات المستخدم --}}
    {{-- ========================================================= --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">سجل طلبات المستخدم (آخر 5 طلبات)</h5>
            {{-- <a href="{{ route('admin.users.orders', $user->id) }}" class="btn btn-sm btn-outline-secondary">عرض كل الطلبات</a> --}}
        </div>
        <div class="card-body">
            @if(isset($orders) && $orders->isEmpty())
                <p class="text-muted text-center">لا توجد طلبات سابقة لهذا المستخدم.</p>
            @elseif(isset($orders))
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الطلب</th>
                                <th>الحالة</th>
                                <th>المبلغ</th>
                                <th>تاريخ الطلب</th>
                                <th>عرض</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td><span class="badge bg-info text-dark">{{ $order->status }}</span></td>
                                <td>{{ number_format($order->total_amount, 0) }} د.ع</td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-xs btn-outline-secondary">التفاصيل</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">العودة لقائمة المستخدمين</a>
    </div>
</form>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // تحديد/إلغاء تحديد الكل العام
        const selectAllCheckbox = document.getElementById('select_all_permissions');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('click', function(event) {
                const checked = event.target.checked;
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = checked;
                });
                document.querySelectorAll('.select-group').forEach(g => g.checked = checked);
            });
        }

        // تحديد/إلغاء تحديد الكل داخل كل مجموعة
        document.querySelectorAll('.select-group').forEach(groupCheckbox => {
            groupCheckbox.addEventListener('change', function() {
                const groupIndex = this.dataset.group;
                const checked = this.checked;
                document.querySelectorAll('.group-' + groupIndex).forEach(cb => cb.checked = checked);
                updateSelectAllCheckbox();
            });
        });

        // تحديث حالة مربع "تحديد الكل" العام والمجموعات
        function updateSelectAllCheckboxes() {
            const allPermissions = document.querySelectorAll('.permission-checkbox');
            if (allPermissions.length === 0) return;

            const allGroups = document.querySelectorAll('.select-group');
            
            // تحديث مربع "تحديد الكل" العام
            const allChecked = Array.from(allPermissions).every(cb => cb.checked);
            document.getElementById('select_all_permissions').checked = allChecked;

            // تحديث مربعات المجموعات
            allGroups.forEach(groupCheckbox => {
                const groupIndex = groupCheckbox.dataset.group;
                const groupPermissions = document.querySelectorAll('.group-' + groupIndex);
                if (groupPermissions.length > 0) {
                    const groupAllChecked = Array.from(groupPermissions).every(cb => cb.checked);
                    groupCheckbox.checked = groupAllChecked;
                }
            });
        }

        // عند تغيير أي صلاحية، يتم تحديث كل شيء
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAllCheckboxes);
        });

        // نحدث الحالة فور تحميل الصفحة
        updateSelectAllCheckboxes();
    });
</script>
@endpush