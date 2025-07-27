@extends('admin.layout')
@section('title', 'تعديل العميل')
@section('content')
<div class="card shadow-sm">
    <div class="card-header"><h5 class="mb-0">تعديل بيانات العميل: {{ $customer->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم العميل الكامل</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number', $customer->phone_number) }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">البريد الإلكتروني (اختياري)</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $customer->email) }}">
                </div>
                <div class="mb-3">
    <label for="notes" class="form-label">ملاحظات</label>
    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
</div>
            </div>
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>
@endsection