@extends('frontend.profile.layout')

@section('title', 'عناويني')

@section('profile-content')
<div class="bg-white rounded-lg shadow-sm border border-[#eadbcd] p-4 md:p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-4 md:mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-[#4a3f3f]">عناوين الشحن</h2>
        <a href="{{ route('profile.addresses.create') }}" 
           class="bg-[#cd8985] text-white font-bold py-2 px-4 rounded-md hover:bg-[#be6661] transition-colors text-sm w-full md:w-auto text-center">
            <i class="bi bi-plus-circle"></i> إضافة عنوان جديد
        </a>
    </div>

    @if ($addresses->isEmpty())
        <div class="text-center py-8 md:py-12">
            <i class="bi bi-geo-alt text-4xl md:text-6xl text-[#eadbcd]"></i>
            <p class="mt-3 md:mt-4 text-[#7a6e6e]">لم تقم بإضافة أي عناوين بعد</p>
            <a href="{{ route('profile.addresses.create') }}" 
               class="mt-3 inline-block bg-[#cd8985] text-white font-bold py-2 px-4 rounded-md hover:bg-[#be6661] transition-colors text-sm">
                إضافة عنوان جديد
            </a>
        </div>
    @else
        <div class="space-y-3 md:space-y-4">
            @foreach ($addresses as $address)
                <div class="border border-[#eadbcd] rounded-lg p-3 md:p-4 flex flex-col md:flex-row justify-between gap-3 hover:shadow-sm transition">
                    <div class="flex-grow">
                        <p class="font-semibold text-[#4a3f3f] text-sm md:text-base">
                            {{ $address->governorate }}، {{ $address->city }}
                        </p>
                        <p class="text-[#7a6e6e] text-xs md:text-sm mt-1">{{ $address->address_details }}</p>
                        @if($address->nearest_landmark)
                        <p class="text-[#9ca3af] text-xxs md:text-xs mt-1">
                            أقرب نقطة دالة: {{ $address->nearest_landmark }}
                        </p>
                        @endif
                    </div>
                        <form action="{{ route('profile.addresses.destroy', $address->id) }}" method="POST" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs md:text-sm">
                                <i class="bi bi-trash3"></i> حذف
                            </button>
                        </form>
                </div>                    
            @endforeach
        </div>
    @endif
</div>
@endsection