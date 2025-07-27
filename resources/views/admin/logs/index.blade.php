@extends('admin.layout')
@section('title', 'سجل الأنشطة')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">سجل الأنشطة</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>المستخدم</th>
                        <th>الإجراء</th>
                        <th>النوع</th>
                        <th>العنصر</th>
                        <th>التاريخ والوقت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->user->name ?? 'نظام' }}</td>
                            <td>
                                @if($log->action === 'created') <span class="badge bg-success">إنشاء</span>
                                @elseif($log->action === 'updated') <span class="badge bg-warning text-dark">تحديث</span>
                                @elseif($log->action === 'deleted') <span class="badge bg-danger">حذف</span>
                                @endif
                            </td>
                            <td>{{ class_basename($log->loggable_type) }}</td>
                            <td>
                                {{-- محاولة عرض اسم العنصر إذا كان متاحاً --}}
                                @if($log->loggable)
                                    {{ $log->loggable->name ?? $log->loggable->name_ar ?? '#' . $log->loggable_id }}
                                @else
                                    #{{ $log->loggable_id }} (محذوف)
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-4">لا توجد سجلات لعرضها.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
