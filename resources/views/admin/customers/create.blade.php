@extends('admin.layout')

@section('title', 'إضافة عميل جديد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">إنشاء حساب عميل جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
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
                    <label for="name" class="form-label">اسم العميل الكامل</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone_number" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني (اختياري)</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                </div>
                
                <hr class="my-3">
                <h6 class="mb-3">تفاصيل العنوان (اختياري)</h6>

                <div class="col-md-6 mb-3">
                    <label for="governorate" class="form-label">المحافظة</label>
                    <input type="text" class="form-control" id="governorate" name="governorate" value="{{ old('governorate') }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">المدينة</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="address_details" class="form-label">تفاصيل إضافية للعنوان</label>
                    <textarea class="form-control" id="address_details" name="address_details" rows="3">{{ old('address_details') }}</textarea>
                </div>
                <div class="mb-3">
    <label for="notes" class="form-label">ملاحظات</label>
    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
</div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">إنشاء العميل</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection