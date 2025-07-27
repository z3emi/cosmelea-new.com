@extends('layouts.app')

@section('title', 'سياسة الخصوصية - كوزميليا')

@push('styles')
<style>
    /* استخدمنا نفس الخلفية كوزميليا */
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
<div class="min-h-screen cosmelea-bg flex items-center justify-center px-4 py-12">
    <div class="content-wrapper max-w-4xl w-full bg-white p-8 rounded-lg shadow-md text-brand-text">

        {{-- العنوان الرئيسي --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-brand-primary">سياسة الخصوصية</h1>
            <p class="mt-2 text-lg text-gray-600">آخر تحديث: {{ date('j F, Y') }}</p>
        </div>

        {{-- مقدمة --}}
        <div class="prose lg:prose-lg max-w-none bg-brand-bg p-6 rounded-lg shadow-sm">
            <p class="lead">
                في <strong>كوزميليا</strong>، خصوصيتكِ وثقتكِ شيء نعتز به جدًا. نتعامل مع بياناتكِ وكأنها بياناتنا، ونلتزم بأقصى درجات الأمان والشفافية.
            </p>
        </div>

        {{-- محتوى السياسات --}}
        <div class="mt-8 space-y-12">
            {{-- 1. المعلومات التي نجمعها --}}
            <section>
                <h2 class="text-2xl font-semibold text-brand-dark mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="bi bi-collection text-brand-primary"></i>
                    ما هي المعلومات التي نجمعها؟
                </h2>
                <ul class="list-disc list-inside space-y-2 rtl:pr-5">
                    <li>الاسم الكامل</li>
                    <li>رقم الهاتف</li>
                    <li>عنوان التوصيل</li>
                    <li>البريد الإلكتروني (اختياري)</li>
                    <li>نوع البشرة/الشعر (اختياري، إذا طلبتِ استشارة)</li>
                    <li>معلومات الدفع (لا نقوم بتخزينها، تتم معالجتها عبر بوابات دفع آمنة فقط)</li>
                </ul>
            </section>
</br>
            {{-- 2. لماذا نجمع المعلومات --}}
            <section>
                <h2 class="text-2xl font-semibold text-brand-dark mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="bi bi-shield-check text-brand-primary"></i>
                    لماذا نجمع هذه المعلومات؟
                </h2>
                <ul class="list-disc list-inside space-y-2 rtl:pr-5">
                    <li>لإتمام الطلب وتوصيل المنتجات بدقة.</li>
                    <li>لتقديم استشارة مخصصة حسب نوع بشرتكِ/شعركِ.</li>
                    <li>لإرسال العروض والتحديثات إذا وافقتِ عليها.</li>
                    <li>لتحسين تجربتكِ داخل الموقع والتطبيق.</li>
                </ul>
            </section>
</br>
            {{-- 3. ماذا لا نفعل --}}
            <section>
                <h2 class="text-2xl font-semibold text-brand-dark mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="bi bi-x-circle text-brand-primary"></i>
                    ماذا لا نفعله أبدًا؟
                </h2>
                <ul class="list-disc list-inside space-y-2 rtl:pr-5">
                    <li>لا نبيع، نشارك، أو ننشر بياناتكِ لأي طرف ثالث.</li>
                    <li>لا نرسل رسائل تسويقية بدون موافقتكِ الصريحة.</li>
                    <li>لا نحتفظ بمعلومات الدفع بعد إتمام عملية الشراء.</li>
                </ul>
            </section>
</br>
            {{-- 4. حماية البيانات --}}
            <section>
                <h2 class="text-2xl font-semibold text-brand-dark mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="bi bi-lock text-brand-primary"></i>
                    كيف نحمي بياناتكِ؟
                </h2>
                <ul class="list-disc list-inside space-y-2 rtl:pr-5">
                    <li>نقوم بتخزين المعلومات على سيرفرات آمنة ومشفّرة.</li>
                    <li>نستخدم أحدث بروتوكولات الحماية (مثل SSL وتشفير البيانات).</li>
                    <li>نطبق أنظمة دخول مؤمّنة للوصول إلى البيانات.</li>
                    <li>نراجع بشكل دوري إجراءات الأمان ونتخذ خطوات استباقية لأي تحديثات.</li>
                </ul>
            </section>
</br>
            {{-- 5. حقوق المستخدم --}}
            <section>
                <h2 class="text-2xl font-semibold text-brand-dark mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="bi bi-lightbulb text-brand-primary"></i>
                    حقوقكِ الكاملة
                </h2>
                <p class="mb-3">يمكنكِ بأي وقت:</p>
                <ul class="list-disc list-inside space-y-2 rtl:pr-5">
                    <li>طلب عرض أو تعديل أو حذف بياناتكِ الشخصية.</li>
                    <li>طلب تحديث معلوماتكِ لضمان دقتها.</li>
                    <li>إلغاء الاشتراك من أي إشعارات تسويقية.</li>
                </ul>
            </section>

        </div>
</br>
        {{-- الخاتمة --}}
        <div class="mt-12 text-center text-gray-700 border-t pt-6 space-y-2">
            <p>كل شيء تحت سيطرتكِ… لأنكِ أهم من أي سياسة.</p>
            <p>إذا كان لديكِ أي استفسار، لا تترددي في 
                <a href="#" class="text-brand-primary underline hover:text-brand-dark transition">التواصل معنا</a>.
            </p>
        </div>

    </div>
</div>
@endsection
