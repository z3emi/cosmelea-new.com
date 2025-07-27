@extends('admin.layout')

@section('title', 'تعديل الدور: ' . $role->name)

@section('content')

@php
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
            'view-products' => 'عرض المنتجات',
            'create-products' => 'إنشاء منتجات',
            'edit-products' => 'تعديل المنتجات',
            'delete-products' => 'حذف المنتجات',
            'view-categories' => 'عرض التصنيفات',
            'create-categories' => 'إنشاء تصنيفات',
            'edit-categories' => 'تعديل التصنيفات',
            'delete-categories' => 'حذف التصنيفات',
            'view-orders' => 'عرض الطلبات',
            'create-orders' => 'إنشاء طلبات',
            'edit-orders' => 'تعديل الطلبات',
            'delete-orders' => 'حذف الطلبات',
            'view-trashed-orders' => 'عرض الطلبات المحذوفة',
            'restore-orders' => 'استعادة الطلبات',
            'force-delete-orders' => 'حذف نهائي للطلبات',
            'view-users' => 'عرض المستخدمين',
            'create-users' => 'إنشاء مستخدمين',
            'edit-users' => 'تعديل المستخدمين',
            'delete-users' => 'حذف المستخدمين',
            'ban-users' => 'حظر المستخدمين',
            'view-roles' => 'عرض الأدوار',
            'create-roles' => 'إنشاء أدوار',
            'edit-roles' => 'تعديل الأدوار',
            'delete-roles' => 'حذف الأدوار',
            'view-customers' => 'عرض العملاء',
            'create-customers' => 'إنشاء عملاء',
            'edit-customers' => 'تعديل العملاء',
            'delete-customers' => 'حذف العملاء',
            'ban-customers' => 'حظر العملاء',
            'view-suppliers' => 'عرض الموردين',
            'create-suppliers' => 'إنشاء موردين',
            'edit-suppliers' => 'تعديل الموردين',
            'delete-suppliers' => 'حذف الموردين',
            'view-purchases' => 'عرض المشتريات',
            'create-purchases' => 'إنشاء مشتريات',
            'edit-purchases' => 'تعديل المشتريات',
            'delete-purchases' => 'حذف المشتريات',
            'view-expenses' => 'عرض المصاريف',
            'create-expenses' => 'إنشاء مصاريف',
            'edit-expenses' => 'تعديل المصاريف',
            'delete-expenses' => 'حذف المصاريف',
            'view-inventory' => 'عرض المخزون',
            'view-reports' => 'عرض التقارير',
            'view-discount-codes' => 'عرض أكواد الخصم',
            'create-discount-codes' => 'إنشاء أكواد خصم',
            'edit-discount-codes' => 'تعديل أكواد الخصم',
            'delete-discount-codes' => 'حذف أكواد الخصم',
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

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">تعديل الدور: {{ $role->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">اسم الدور</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <h5 class="mt-4">الصلاحيات المتاحة</h5>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="select_all_permissions">
                    <label class="form-check-label fw-bold" for="select_all_permissions">تحديد الكل</label>
                </div>

                <div class="row gy-4">
                    @foreach ($groupedPermissions as $groupName => $groupPermissions)
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded shadow-sm h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-be6661 fw-bold m-0">{{ $groupDisplayNames[$groupName] ?? $groupName }}</h6>
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
                                                value="{{ $permission->id }}"
                                                id="permission_{{ $permission->id }}"
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
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

            <button type="submit" class="btn btn-primary mt-4">تحديث</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary mt-4">إلغاء</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // تحديد/إلغاء تحديد الكل العام
    document.getElementById('select_all_permissions').addEventListener('click', function(event) {
        const checked = event.target.checked;
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = checked;
        });
        // مزامنة كل المجموعات
        document.querySelectorAll('.select-group').forEach(g => g.checked = checked);
    });

    // تحديد/إلغاء تحديد الكل داخل كل مجموعة فقط
    document.querySelectorAll('.select-group').forEach(groupCheckbox => {
        groupCheckbox.addEventListener('change', function() {
            const groupIndex = this.dataset.group;
            const checked = this.checked;
            document.querySelectorAll('.group-' + groupIndex).forEach(cb => cb.checked = checked);

            // تحديث مربع تحديد الكل العام
            updateSelectAllCheckbox();
        });
    });

    // تحديث حالة مربع "تحديد الكل" العام بناءً على المجموعات
    function updateSelectAllCheckbox() {
        const allPermissions = document.querySelectorAll('.permission-checkbox');
        const allGroups = document.querySelectorAll('.select-group');

        const allChecked = Array.from(allPermissions).every(cb => cb.checked);
        document.getElementById('select_all_permissions').checked = allChecked;

        // تحديث كل مربعات المجموعات (في حال تم تحديدها جزئياً)
        allGroups.forEach(groupCheckbox => {
            const groupIndex = groupCheckbox.dataset.group;
            const groupPermissions = document.querySelectorAll('.group-' + groupIndex);
            const groupAllChecked = Array.from(groupPermissions).every(cb => cb.checked);
            groupCheckbox.checked = groupAllChecked;
        });
    }

    // عند تغيير أي صلاحية، تحديث المجموعات وتحديد الكل العام
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllCheckbox);
    });

    // نحدث الحالة فور تحميل الصفحة (مثلاً لو الصلاحيات محددة مسبقاً)
    document.addEventListener('DOMContentLoaded', updateSelectAllCheckbox);
</script>
@endpush

@endsection
