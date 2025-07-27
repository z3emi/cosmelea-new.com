@extends('layouts.app')

@section('title', 'تم استلام طلبك بنجاح')

@push('styles')
<style>
    /* Simple confetti-like background effect */
    .confetti-bg {
        position: relative;
        overflow: hidden;
    }
    .confetti-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image:
            radial-gradient(circle at 15% 50%, #a8e6cf 2px, transparent 0),
            radial-gradient(circle at 85% 30%, #dcedc1 2px, transparent 0),
            radial-gradient(circle at 25% 90%, #ffd3b6 2px, transparent 0),
            radial-gradient(circle at 75% 70%, #ffaaa5 2px, transparent 0);
        background-size: 50px 50px;
        opacity: 0.3;
        z-index: 0;
    }
    .content-wrapper {
        position: relative;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12 confetti-bg">
    <div class="content-wrapper max-w-2xl w-full">
        <div class="bg-white p-8 rounded-xl shadow-lg text-center border border-gray-200">

            {{-- Success Icon --}}
            <div class="w-20 h-20 bg-green-100 rounded-full p-4 flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-patch-check-fill text-5xl text-green-500"></i>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-brand-text mb-2">شكراً لكِ، طلبكِ قيد التنفيذ!</h1>

            {{-- Display the success message from the controller --}}
            @if(session('success'))
                <p class="text-md text-gray-600 mb-4">{{ session('success') }}</p>
            @else
                <p class="text-md text-gray-600 mb-4">تم استلام طلبك بنجاح. سنتصل بكِ قريباً لتأكيد التفاصيل.</p>
            @endif

            {{-- Order ID Section --}}
            {{-- ملاحظة: يجب تمرير متغير $order من الكونترولر لعرض رقم الطلب --}}
            @if(isset($order))
            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-lg py-3 px-4 my-6">
                <p class="text-sm text-gray-500">رقم الطلب الخاص بكِ هو:</p>
                <p class="text-xl font-mono font-bold text-brand-dark tracking-wider">{{ $order->id }}</p>
            </div>
            @endif

            {{-- What's next section --}}
            <div class="text-left my-8 p-4 bg-blue-50/50 border-l-4 border-blue-400 rounded-r-lg">
                <h3 class="font-bold text-blue-800">ماذا سيحدث الآن؟</h3>
                <ul class="list-disc list-inside text-sm text-blue-700 mt-2 space-y-1">
                    <li>ستصلكِ مكالمة هاتفية من فريقنا خلال 24 ساعة لتأكيد الطلب.</li>
                    <li>سيتم تجهيز وشحن طلبكِ بعد التأكيد.</li>
                    <li>يمكنكِ تتبع حالة الطلب من صفحة "طلباتي".</li>
                </ul>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('shop') }}" class="w-full sm:w-auto bg-brand-primary text-white font-bold py-3 px-6 rounded-md hover:bg-brand-dark transition-colors duration-300">
                    <i class="bi bi-arrow-left"></i> متابعة التسوق
                </a>
                {{-- تأكد من وجود مسار (route) باسم 'orders.index' أو ما شابه لعرض طلبات المستخدم --}}
                <a href="#" class="w-full sm:w-auto bg-gray-200 text-gray-800 font-bold py-3 px-6 rounded-md hover:bg-gray-300 transition-colors duration-300">
                    عرض طلباتي
                </a>
            </div>

        </div>
    </div>
</div>
@endsection