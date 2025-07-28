@extends('admin.layout')

@section('title', 'إنشاء كود خصم جديد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">إنشاء كود خصم جديد</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.discount-codes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">الكود <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="value" class="form-label">قيمة الخصم <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="value" name="value" value="{{ old('value') }}" required>
                </div>
                {{-- حقل الحد الأقصى للخصم --}}
                <div class="col-md-6 mb-3" id="max_discount_amount_wrapper" style="display: none;">
                    <label for="max_discount_amount" class="form-label">الحد الأقصى لمبلغ الخصم (د.ع)</label>
                    <input type="number" step="0.01" class="form-control" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="max_uses" class="form-label">أقصى عدد للاستخدام (اتركه فارغاً للاستخدام غير المحدود)</label>
                    <input type="number" class="form-control" id="max_uses" name="max_uses" value="{{ old('max_uses') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="max_uses_per_user" class="form-label">أقصى عدد للاستخدام لكل مستخدم (اتركه فارغاً للاستخدام غير المحدود)</label>
                    <input type="number" class="form-control" id="max_uses_per_user" name="max_uses_per_user" value="{{ old('max_uses_per_user') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="expires_at" class="form-label">تاريخ الانتهاء (اتركه فارغاً ليبقى صالحاً دائماً)</label>
                    <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="categories" class="form-label">الأقسام المسموح لها</label>
                    <select multiple class="form-select" id="categories" name="categories[]">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(in_array($category->id, old('categories', [])))>{{ $category->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="products" class="form-label">المنتجات المسموح لها</label>
                    <select multiple class="form-select" id="products" name="products[]">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected(in_array($product->id, old('products', [])))>{{ $product->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">حفظ الكود</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const maxDiscountWrapper = document.getElementById('max_discount_amount_wrapper');

        function toggleMaxDiscountField() {
            if (typeSelect.value === 'percentage') {
                maxDiscountWrapper.style.display = 'block';
            } else {
                maxDiscountWrapper.style.display = 'none';
                document.getElementById('max_discount_amount').value = ''; // تفريغ الحقل عند الإخفاء
            }
        }

        // إظهار/إخفاء الحقل عند تغيير النوع
        typeSelect.addEventListener('change', toggleMaxDiscountField);

        // إظهار/إخفاء الحقل عند تحميل الصفحة
        toggleMaxDiscountField();
    });
</script>
@endpush
