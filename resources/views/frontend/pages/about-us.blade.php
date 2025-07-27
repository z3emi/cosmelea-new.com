@extends('layouts.app')

@section('title', 'طريقة الطلب - كوزميليا')

@push('styles')
<style>
    .order-section {
        background-color: #f9f5f1;
        min-height: 100vh;
        padding: 4rem 1rem;
    }

    .order-card {
        background-color: #fff;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        max-width: 900px;
        margin: 0 auto;
    }

    .order-step {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .order-step-icon {
        flex-shrink: 0;
        background-color: #cd8985;
        color: #fff;
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        font-weight: bold;
    }

    .order-step-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #be6661;
        margin-bottom: 0.25rem;
    }

    .order-step-content p {
        font-size: 1rem;
        color: #333;
        margin: 0;
    }

    .order-heading {
        font-size: 2rem;
        color: #be6661;
        font-weight: bold;
        text-align: center;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="order-section">
    <div class="order-card">
        <h1 class="order-heading">طريقة الطلب من كوزميليا</h1>

        <div class="order-step">
            <div class="order-step-icon">1</div>
            <div class="order-step-content">
                <h3>اختاري المنتجات</h3>
                <p>تصفحي الموقع أو استخدمي خاصية البحث لاختيار المنتجات اللي تحبيها وأضيفيها إلى السلة.</p>
            </div>
        </div>

        <div class="order-step">
            <div class="order-step-icon">2</div>
            <div class="order-step-content">
                <h3>ادخلي معلومات التوصيل</h3>
                <p>بعد ما تكملي اختيار المنتجات، توجهي للسلة وسجلي معلوماتچ (الاسم، العنوان، ورقم الهاتف).</p>
            </div>
        </div>

        <div class="order-step">
            <div class="order-step-icon">3</div>
            <div class="order-step-content">
                <h3>اختاري وسيلة الدفع</h3>
                <p>تقدرين تختارين بين الدفع عند الاستلام أو التحويل البنكي حسب راحتچ.</p>
            </div>
        </div>

        <div class="order-step">
            <div class="order-step-icon">4</div>
            <div class="order-step-content">
                <h3>تأكيد الطلب</h3>
                <p>بمجرد تأكيد الطلب، راح توصلك رسالة تأكيد، وراح نجهز الطرد مباشرة.</p>
            </div>
        </div>

        <div class="order-step">
            <div class="order-step-icon">5</div>
            <div class="order-step-content">
                <h3>التوصيل خلال 24 ساعة</h3>
                <p>راح يوصلج الطلب بأسرع وقت داخل كل محافظات العراق، مع ضمان سلامة المنتج وهدية حسب قيمة الطلب.</p>
            </div>
        </div>

        <p class="text-center mt-8 font-semibold text-brand-dark">
            لأي استفسار أو مساعدة، فريق كوزميليا حاضر دومًا 💬
        </p>
    </div>
</div>
@endsection
