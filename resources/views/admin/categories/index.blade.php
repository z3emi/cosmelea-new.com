@extends('admin.layout')
@section('title', 'إدارة الأقسام')

@push('styles')
<style>
    /* --- تنسيقات شجرة الأقسام --- */
    .category-tree ul { padding-right: 2rem; border-right: 2px solid #e9ecef; margin-top: 0.5rem; }
    .category-tree li { list-style-type: none; position: relative; padding: 0.5rem 0 0.5rem 1.5rem; }
    .category-tree li::before { content: ''; position: absolute; top: 0; right: -2px; height: 100%; width: 2px; background-color: #e9ecef; }
    .category-tree li::after { content: ''; position: absolute; top: 1.5rem; right: -2px; width: 1.5rem; height: 2px; background-color: #e9ecef; }
    .category-tree li:last-child::before { height: 1.5rem; }
    .category-item { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background-color: #fff; border: 1px solid #dee2e6; border-radius: 0.375rem; transition: box-shadow 0.2s; }
    .category-item:hover { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); }
    .category-info { display: flex; align-items: center; gap: 1rem; }
    .category-actions { display: flex; gap: 0.5rem; }
    .parent-category { background-color: #f8f9fa; border-color: #ced4da; }
</style>
@endpush

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">شجرة الأقسام</h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>
            إضافة قسم جديد
        </a>
    </div>
    <div class="card-body">
        <div class="category-tree">
            @forelse ($categories as $category)
                <div class="mb-3">
                    <div class="category-item parent-category">
                        <div class="category-info">
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name_ar }}" width="50" class="img-thumbnail rounded-circle">
                            <strong>{{ $category->name_ar }}</strong>
                            {{-- ===== START: التعديل المطلوب ===== --}}
                            <span class="badge bg-dark rounded-pill">{{ $category->total_products_count }} منتج</span>
                            {{-- ===== END: التعديل المطلوب ===== --}}
                            <span class="badge bg-secondary">قسم رئيسي</span>
                        </div>
                        <div class="category-actions">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-info m-1 px-2" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم وكل الأقسام الفرعية التابعة له؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حذف"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    @if($category->children->isNotEmpty())
                        <ul>
                            @include('admin.categories._subcategories', ['children' => $category->children])
                        </ul>
                    @endif
                </div>
            @empty
                <div class="text-center py-4"><p>لا يوجد أقسام رئيسية لعرضها.</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
