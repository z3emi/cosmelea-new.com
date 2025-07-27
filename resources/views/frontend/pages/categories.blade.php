@extends('layouts.app')
@section('title', 'تصفح الأقسام')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        
        {{-- عنوان الصفحة --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-[#4a3f3f]">تصفح جميع الأقسام</h1>
            <p class="text-[#7a6e6e] mt-3 max-w-2xl mx-auto">اكتشف عالم الجمال من خلال أقسامنا المتنوعة</p>
        </div>
        
        {{-- قائمة الأقسام كشجرة --}}
        <div class="max-w-4xl mx-auto">
            <ul class="space-y-6">
                @forelse ($categories as $category)
                    <li class="relative pl-6">
                        {{-- القسم الرئيسي --}}
                        <a href="{{ route('shop', ['category' => $category->slug]) }}"
                           class="group flex items-center justify-between p-5 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-[#eadbcd]">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-[#cd8985] shadow-sm">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name_ar }}"
                                         class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-[#4a3f3f] group-hover:text-[#cd8985] transition-colors">
                                        {{ $category->name_ar }}
                                    </h3>
                                    <p class="text-[#7a6e6e] text-sm mt-1">{{ $category->total_products_count }} منتج</p>
                                </div>
                            </div>
                            <i class="bi bi-chevron-left text-[#cd8985] text-xl"></i>
                        </a>

                        {{-- الأقسام الفرعية --}}
                        @if ($category->children->isNotEmpty())
                            <ul class="mt-4 pl-6 space-y-4">
                                @foreach($category->children as $child)
                                    <li class="relative pl-6">
                                        <a href="{{ route('shop', ['category' => $child->slug]) }}"
                                           class="group flex items-center justify-between p-4 bg-[#f9f5f1] rounded-lg hover:bg-[#f0e6dd] transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="w-3 h-3 rounded-full bg-[#cd8985]"></div>
                                                <span class="font-medium text-[#4a3f3f] group-hover:text-[#be6661]">
                                                    {{ $child->name_ar }}
                                                </span>
                                            </div>
                                            <span class="text-[#7a6e6e] text-sm">{{ $child->total_products_count }} منتج</span>
                                        </a>

                                        {{-- الأقسام الفرعية من المستوى الثالث --}}
                                        @if ($child->children->isNotEmpty())
                                            <ul class="mt-2 pl-6 space-y-2">
                                                @foreach($child->children as $grandchild)
                                                    <li class="relative pl-6">
                                                        <a href="{{ route('shop', ['category' => $grandchild->slug]) }}"
                                                           class="group flex items-center justify-between py-2 px-3 bg-white rounded-md hover:bg-[#f9f5f1] transition-colors">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-2 h-2 rounded-full bg-[#dcaca9]"></div>
                                                                <span class="text-sm text-[#4a3f3f] group-hover:text-[#be6661]">
                                                                    {{ $grandchild->name_ar }}
                                                                </span>
                                                            </div>
                                                            <span class="text-[#9ca3af] text-xs">{{ $grandchild->total_products_count }} منتج</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @empty
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-[#eadbcd]">
                        <i class="bi bi-folder-x text-4xl text-[#cd8985] mb-3"></i>
                        <p class="text-[#7a6e6e]">لا يوجد أقسام لعرضها حالياً</p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* خطوط التوصيل بين الأقسام */
    ul > li:not(:last-child)::before {
        content: "";
        position: absolute;
        top: 0;
        left: 24px;
        bottom: -24px;
        width: 2px;
        background-color: #eadbcd;
    }

    /* تحسينات للجوال */
    @media (max-width: 640px) {
        ul > li {
            padding-left: 1rem;
        }
        
        ul > li::before {
            left: 12px;
        }
        
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush