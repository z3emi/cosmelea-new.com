@extends('layouts.app')

@section('title', 'سياسة الاستبدال والإرجاع - كوزميليا')

@push('styles')
<style>
    .cosmelea-bg {
        position: relative;
        background-color: #f9f5f1;
        overflow: hidden;
    }
    .cosmelea-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 40%, #ffaaa5 2px, transparent 0),
            radial-gradient(circle at 80% 60%, #ffd3b6 2px, transparent 0),
            radial-gradient(circle at 30% 90%, #dcedc1 2px, transparent 0),
            radial-gradient(circle at 70% 20%, #a8e6cf 2px, transparent 0);
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
<div class="cosmelea-bg py-16 px-4 min-h-screen">
    <div class="content-wrapper max-w-3xl mx-auto bg-white rounded-xl shadow-md p-8 text-gray-800">
        <h1 class="text-3xl font-bold text-center text-brand-dark mb-8">سياسة الاستبدال / الإرجاع</h1>

        <div class="space-y-8 text-base leading-relaxed">

            <p>في كوزميليا، نحرص أن تكون تجربتچ آمنة ومريحة، ولهذا عدنا سياسة استبدال واضحة وسهلة لضمان رضاچ التام.</p>

            <div>
                <h2 class="font-semibold text-lg text-brand-primary">✅ يحق لچ ترجعين أو تبدلين المنتج إذا:</h2>
                <ul class="list-disc list-inside space-y-2 mt-2">
                    <li>وصلچ المنتج تالف أو مكسور أثناء الشحن.</li>
                    <li>استلمتي منتج مختلف عن الي طلبتي.</li>
                    <li>كان المنتج منتهي الصلاحية.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-lg text-brand-primary">⏱ مهلة تقديم الطلب:</h2>
                <p>خلال ٤٨ ساعة من استلام الطلب.</p>
            </div>

            <div>
                <h2 class="font-semibold text-lg text-brand-primary">📦 شروط قبول الإرجاع:</h2>
                <ul class="list-disc list-inside space-y-2 mt-2">
                    <li>المنتج غير مستخدم وبنفس حالته الأصلية.</li>
                    <li>يكون بالتغليف الأصلي والكرتون مع الملصقات.</li>
                    <li>إرفاق صورة أو فيديو يوضح الخلل أو المشكلة.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-lg text-brand-primary">💡 بعد قبول الطلب:</h2>
                <ul class="list-disc list-inside space-y-2 mt-2">
                    <li>نعوضچ بمنتج بديل أو نرجع المبلغ حسب الحالة.</li>
                    <li>التوصيل مجاني إذا الخطأ من عدنا.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-lg text-brand-primary">🚫 لا تشمل سياسة الإرجاع:</h2>
                <ul class="list-disc list-inside space-y-2 mt-2">
                    <li>المنتجات المفتوحة أو المجربة.</li>
                    <li>المنتجات المستخدمة جزئيًا.</li>
                    <li>تغيير الرأي أو عدم ملاءمة المنتج للذوق الشخصي.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-lg text-brand-primary">📌 ملاحظات مهمة:</h2>
                <p>نتعامل مع كل حالة بشفافية وعدالة لضمان راحتچ ورضاچ الكامل 💖</p>
            </div>

        </div>
    </div>
</div>
@endsection
