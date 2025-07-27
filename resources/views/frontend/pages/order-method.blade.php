@extends('layouts.app')

@section('title', 'طريقة الطلب - كوزميليا')

@push('styles')

<style> .cosmelea-bg { position: relative; overflow: hidden; background-color: #f9f5f1; } .cosmelea-bg::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 15% 50%, #a8e6cf 2px, transparent 0), radial-gradient(circle at 85% 30%, #dcedc1 2px, transparent 0), radial-gradient(circle at 25% 90%, #ffd3b6 2px, transparent 0), radial-gradient(circle at 75% 70%, #ffaaa5 2px, transparent 0); background-size: 50px 50px; opacity: 0.3; z-index: 0; } .content-wrapper { position: relative; z-index: 1; } </style>
@endpush

@section('content')

<div class="min-h-screen cosmelea-bg flex items-center justify-center px-4 py-16"> <div class="content-wrapper max-w-4xl w-full bg-white p-10 rounded-lg shadow-md text-brand-text leading-relaxed">

    <h1 class="text-4xl font-bold text-brand-primary mb-8 text-center">طريقة الطلب من كوزميليا</h1>

    <p class="mb-6 text-lg">
        الطلب من <strong>كوزميليا</strong> سهل وسريع! اختاري الطريقة اللي تعجبچ:
    </p>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-brand-dark mb-4">① من خلال الموقع أو التطبيق:</h2>
        <ul class="list-disc list-inside rtl:pr-6 space-y-2 text-lg">
            <li>📱 تصفّحي الأقسام واختاري المنتجات اللي تحتاجينها.</li>
            <li>🛒 ضيفيها إلى سلة التسوق.</li>
            <li>📝 اضغطي "إتمام الطلب" واملئي معلوماتچ (الاسم، العنوان، رقم الهاتف).</li>
            <li>💳 اختاري وسيلة الدفع المناسبة (كاش – زين كاش – ماستر كارد).</li>
            <li>🚚 توصلچ الطلبيّة خلال 24 ساعة لكل المحافظات!</li>
        </ul>
    </div>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-brand-dark mb-4">② من خلال التواصل المباشر:</h2>
        <ul class="list-disc list-inside rtl:pr-6 space-y-2 text-lg">
            <li>📲 واتساب: <a href="https://wa.me/9647700000000" class="text-blue-600 hover:underline">رابط مباشر للطلب</a></li>
            <li>💬 إنستغرام: عبر الرسائل الخاصة</li>
            <li>📩 فقط ارسلي رسالة، وراح نهتم بالباقي!</li>
        </ul>
    </div>

    <div class="bg-pink-50 border border-pink-200 rounded-lg p-6 mt-8">
        <p class="text-lg font-semibold mb-2">💡 تذكّري:</p>
        <ul class="list-disc list-inside rtl:pr-6 space-y-2 text-lg">
            <li>📘 كل طلب توصلچ ويّاه: شرح مفصّل لاستخدام كل منتج.</li>
            <li>🩺 استشارة مجانية من فريق مختص.</li>
            <li>🎁 هدية حسب قيمة الطلب.</li>
        </ul>
    </div>

    <p class="mt-10 text-center text-lg font-semibold">
        اختاري الطريقة الأنسب، وعيشي تجربة جمال ولا أروع مع <span class="text-brand-primary font-bold">Cosmelea</span> ✨
    </p>

</div>
</div> 
@endsection