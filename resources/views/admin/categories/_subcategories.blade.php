@foreach($children as $subcategory)
    <li>
        <div class="category-item">
            <div class="category-info">
                <img src="{{ asset('storage/' . $subcategory->image) }}" alt="{{ $subcategory->name_ar }}" width="50" class="img-thumbnail rounded-circle">
                <span>{{ $subcategory->name_ar }}</span>
                {{-- ===== START: التعديل المطلوب ===== --}}
                <span class="badge bg-dark rounded-pill">{{ $subcategory->total_products_count }} منتج</span>
                {{-- ===== END: التعديل المطلوب ===== --}}
            </div>
            <div class="category-actions">
                <a href="{{ route('admin.categories.edit', $subcategory->id) }}" class="btn btn-sm btn-outline-info m-1 px-2" title="تعديل">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('admin.categories.destroy', $subcategory->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger m-1 px-2" title="حذف">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @if($subcategory->children->isNotEmpty())
            <ul>
                @include('admin.categories._subcategories', ['children' => $subcategory->children])
            </ul>
        @endif
    </li>
@endforeach
