<form method="GET" action="{{ route('shop') }}" id="filter-form">
    <div class="space-y-6">
        {{-- Categories --}}
        <div x-data="{ open: true }">
            <button type="button" @click="open = !open" class="w-full flex justify-between items-center text-lg font-bold text-brand-dark">
                <span>🗂️ الفئات</span>
                <i class="bi transition-transform" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </button>
            <div x-show="open" x-collapse class="mt-3 space-y-2 pr-2">
                <a href="{{ route('shop') }}" class="block text-sm hover:text-brand-primary {{ !request('category') ? 'text-brand-primary font-semibold' : 'text-brand-text' }}">
                    عرض الكل
                </a>
                @foreach ($categories as $parentCategory)
                    <div x-data="{ open: {{ $parentCategory->children->pluck('slug')->contains(request('category')) || request('category') === $parentCategory->slug ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('shop', ['category' => $parentCategory->slug]) }}" class="flex-grow text-sm font-semibold hover:text-brand-primary {{ request('category') === $parentCategory->slug ? 'text-brand-primary' : 'text-brand-text' }}">
                                {{ $parentCategory->name_ar }}
                            </a>
                            @if($parentCategory->children->isNotEmpty())
                            <button type="button" @click="open = !open" class="text-xs p-1">
                                <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </button>
                            @endif
                        </div>
                        @if($parentCategory->children->isNotEmpty())
                        <div x-show="open" x-collapse class="pr-4 mt-2 space-y-2 border-r-2 border-brand-light">
                            @foreach($parentCategory->children as $child)
                                <a href="{{ route('shop', ['category' => $child->slug]) }}" class="block text-sm hover:text-brand-primary {{ request('category') === $child->slug ? 'text-brand-primary font-semibold' : 'text-brand-text' }}">
                                    {{ $child->name_ar }}
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Price Input --}}
        <div x-data="{ open: true }" class="border-t pt-4">
            <button type="button" @click="open = !open" class="w-full flex justify-between items-center text-lg font-bold text-brand-dark">
                <span>💸 السعر</span>
                <i class="bi transition-transform" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </button>
            <div x-show="open" x-collapse class="mt-4">
                <div class="flex items-center gap-3">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="من" class="w-full p-2 rounded-md border border-gray-300 text-sm text-center">
                    <span>-</span>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="إلى" class="w-full p-2 rounded-md border border-gray-300 text-sm text-center">
                </div>
            </div>
        </div>

        {{-- **THE CHANGE**: Added Featured Offers Filter --}}
        <div x-data="{ open: true }" class="border-t pt-4">
            <button type="button" @click="open = !open" class="w-full flex justify-between items-center text-lg font-bold text-brand-dark">
                <span>⭐ العروض المميزة</span>
                <i class="bi transition-transform" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </button>
            <div x-show="open" x-collapse class="mt-3">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="on_sale" value="true" class="accent-brand-dark" {{ request('on_sale') ? 'checked' : '' }}>
                    <span class="text-sm text-brand-text">عرض المنتجات المخفضة فقط</span>
                </label>
            </div>
        </div>

        {{-- Filter Buttons --}}
        <div class="space-y-2 pt-4 border-t">
            <button type="submit"
                    class="w-full bg-brand-dark text-brand-white py-2 rounded-full hover:bg-brand-primary transition text-sm">
                تطبيق الفلتر
            </button>
            {{-- Updated to include the new 'on_sale' parameter --}}
            @if(request()->hasAny(['q', 'category', 'min_price', 'max_price', 'on_sale']))
                <a href="{{ route('shop') }}"
                   class="block w-full text-center text-sm text-gray-500 hover:text-brand-dark">
                    🧹 مسح الفلتر
                </a>
            @endif
        </div>
    </div>
</form>
