@extends('admin.layout')

@section('title', 'سلة المحذوفات - أكواد الخصم')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">سلة المحذوفات - أكواد الخصم</h4>
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
                    @forelse ($trashedCodes as $code)
                        <tr>
                            <td><code>{{ $code->code }}</code></td>
                            <td>{{ $code->type == 'fixed' ? 'مبلغ ثابت' : 'نسبة مئوية' }}</td>
                            <td>{{ $code->type == 'fixed' ? number_format($code->value, 0) . ' د.ع' : $code->value . '%' }}</td>
                            <td>{{ $code->usages_count }} / {{ $code->max_uses ?? '∞' }}</td>
                            <td>
                                <span class="badge bg-secondary">محذوف</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.discount_codes.restore', $code->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm btn-outline-success m-1 px-2" title="استرجاع"><i class="bi bi-arrow-clockwise"></i></button>
                                </form>
                                <form action="{{ route('admin.discount_codes.forceDelete', $code->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حذف نهائي"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">لا توجد أكواد خصم في سلة المحذوفات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-3">
    {{ $trashedCodes->links() }}
</div>
@endsection
