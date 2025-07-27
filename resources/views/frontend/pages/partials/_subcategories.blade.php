@foreach($children as $subcategory)
<li class="my-2">
    <a href="{{ route('shop', ['category' => $subcategory->slug]) }}" class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition-all duration-200">
        <div class="flex items-center gap-4">
            <img src="{{ asset('storage/' . $subcategory->image) }}" alt="{{ $subcategory->name_ar }}" class="w-12 h-12 rounded-full object-cover">
            <span class="font-semibold text-brand-text">{{ $subcategory->name_ar }}</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span>{{ $subcategory->total_products_count }} منتج</span>
            <i class="bi bi-chevron-left"></i>
        </div>
    </a>
    
    {{-- استدعاء ذاتي لعرض المستويات الأعمق --}}
    @if($subcategory->children->isNotEmpty())
        <ul class="mt-2 mr-6 border-r-2 border-brand-secondary">
            @include('frontend.pages.partials._subcategories', ['children' => $subcategory->children])
        </ul>
    @endif
</li>
@endforeach
