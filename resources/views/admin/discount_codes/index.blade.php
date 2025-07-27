@extends('admin.layout')

@section('title', 'إدارة أكواد الخصم')
@push('styles')
<style>
/* Custom pagination styles */
.pagination {
  justify-content: center !important;
  gap: 0.4rem;
  margin-top: 1rem;
}
.pagination .page-item .page-link {
  background-color: #f9f5f1 !important;
  color: #be6661 !important;
  border-color: #be6661 !important;
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
  background-color: #be6661 !important;
  border-color: #be6661 !important;
  color: #fff !important;
  font-weight: 700;
  pointer-events: none;
}
</style>
@endpush

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">أكواد الخصم</h4>
        {{-- **التحقق من صلاحية الإنشاء** --}}
        @can('create-discount-codes')
            <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>
                إنشاء كود جديد
            </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>الكود</th>
                        <th>النوع</th>
                        <th>القيمة</th>
                        <th>مرات الاستخدام</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($discountCodes as $code)
                        <tr>
                            <td><code>{{ $code->code }}</code></td>
                            <td>{{ $code->type == 'fixed' ? 'مبلغ ثابت' : 'نسبة مئوية' }}</td>
                            <td>
                                @if($code->type == 'fixed')
                                    {{ number_format($code->value, 0) }} د.ع
                                @else
                                    {{ $code->value }}%
                                    @if($code->max_discount_amount)
                                        <small class="d-block text-muted">(بحد أقصى {{ number_format($code->max_discount_amount, 0) }} د.ع)</small>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $code->usages_count }} / {{ $code->max_uses ?? '∞' }}</td>
                            <td>
                                @if(!$code->is_active)
                                    <span class="badge bg-secondary">غير فعال</span>
                                @elseif($code->expires_at && $code->expires_at->isPast())
                                    <span class="badge bg-danger">منتهي الصلاحية</span>
                                @else
                                    <span class="badge bg-success">فعال</span>
                                @endif
                            </td>
                            <td>
                                {{-- **التحقق من صلاحية التعديل** --}}
                                @can('edit-discount-codes')
                                    <a href="{{ route('admin.discount-codes.edit', $code->id) }}" class="btn btn-sm btn-outline-info m-1 px-2" title="تعديل"><i class="bi bi-pencil"></i></a>
                                @endcan

                                {{-- **التحقق من صلاحية الحذف** --}}
                                @can('delete-discount-codes')
                                    <form action="{{ route('admin.discount-codes.destroy', $code->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حذف"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endcan

                                {{-- **التحقق من صلاحية التعديل (لتغيير الحالة)** --}}
                                @can('edit-discount-codes')
                                    <form action="{{ route('admin.discount-codes.toggleStatus', $code->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $code->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} m-1 px-2" title="{{ $code->is_active ? 'إيقاف' : 'تفعيل' }}">
                                            <i class="bi {{ $code->is_active ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-4">لا توجد أكواد خصم لعرضها.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.discount_codes.index') }}" class="d-flex align-items-center">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="per_page" class="me-2">عدد العناصر:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>

            <div>
                {{ $discountCodes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
