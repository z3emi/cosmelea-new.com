@extends('layouts.app')

@section('title', 'الأسئلة الشائعة - كوزميليا')

@push('styles')
<style>
    .cosmelea-bg {
        position: relative;
        overflow: hidden;
        background-color: #f9f5f1;
    }
    .cosmelea-bg::before {
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
<div class="cosmelea-bg py-16 px-4 min-h-screen">
    <div class="content-wrapper max-w-3xl mx-auto bg-white rounded-xl shadow-md p-8 text-gray-800 leading-relaxed">
        <h1 class="text-3xl font-bold text-center text-brand-dark mb-8">الأسئلة الشائعة</h1>

        <p class="text-lg mb-6 text-center">هنا جمعنالج أكثر الأسئلة اللي توصلنا، مع إجابات سريعة وواضحة:</p>

        <div class="space-y-6 text-base">
            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">🧴 هل المنتجات أصلية؟</h2>
                <p>أكيد! كل منتجات كوزميليا أصلية 100% ومأخوذة من مصادرها الرسمية، وبضمان كامل ضد التقليد.</p>
            </div>

            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">🧑‍⚕️ أكو استشارة قبل الشراء؟</h2>
                <p>نعم طبعًا، فريقنا يضم صيادلة وأطباء اختصاص يجاوبونچ ويوجهوج حسب نوع بشرتچ أو مشكلتچ.</p>
            </div>

            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">📦 توصلون الطلبات لكل المحافظات؟</h2>
                <p>إي نعم، نوصل لكل محافظات العراق خلال 24 ساعة فقط.</p>
            </div>

            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">🎁 شنو الهدايا والخصومات؟</h2>
                <p>نهتم نكافئچ دائمًا:</p>
                <ul class="list-disc list-inside rtl:pr-6 mt-2">
                    <li>كل طلب يوصلچ ويّاه هدية.</li>
                    <li>الخصم يعتمد على قيمة الطلب.</li>
                    <li>شوفي <a href="#" class="text-brand-dark underline">صفحة العروض والهدايا</a> للتفاصيل الكاملة.</li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">📋 توصل ويّا الطلب طريقة الاستخدام؟</h2>
                <p>أكيد. كل منتج نرسله ويّاه ورقة شرح للأستخدام مفصلة خطوة بخطوة، حسب نوع البشرة أو الشعر.</p>
            </div>

            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">💳 شلون أقدر أدفع؟</h2>
                <p>نوفر إلچ 3 طرق:</p>
                <ul class="list-disc list-inside rtl:pr-6 mt-2">
                    <li>كاش عند الاستلام</li>
                    <li>زين كاش</li>
                    <li>ماستر كارد</li>
                </ul>
            </div>

            <div>
                <h2 class="font-semibold text-brand-primary text-lg mb-1">🛍 شلون أطلب؟</h2>
                <p>من التطبيق، أو الموقع، أو راسلينا على إنستغرام أو واتساب مباشرة.</p>
            </div>
        </div>
    </div>
</div>
@endsection
