<?php

namespace App\Services;

use App\Models\DiscountCode;
use Exception;

class DiscountService
{
    /**
     * التحقق من صلاحية كود الخصم وحساب قيمة الخصم الفعلية.
     *
     * @param string $code الكود المدخل من قبل المستخدم.
     * @param float $totalAmount المبلغ الإجمالي للطلب قبل الخصم.
     * @return array تحتوي على قيمة الخصم ومعرّف الكود.
     * @throws \Exception في حال كان الكود غير صالح.
     */
    public function apply(string $code, float $totalAmount): array
    {
        $discountCode = DiscountCode::where('code', $code)->first();

        // 1. التحقق من وجود الكود
        if (!$discountCode) {
            throw new Exception('كود الخصم غير صحيح.');
        }

        // 2. التحقق من أن الكود فعال
        if (!$discountCode->is_active) {
            throw new Exception('كود الخصم غير فعال حالياً.');
        }

        // 3. التحقق من تاريخ الصلاحية
        if ($discountCode->expires_at && $discountCode->expires_at->isPast()) {
            throw new Exception('كود الخصم منتهي الصلاحية.');
        }

        // 4. التحقق من عدد مرات الاستخدام الإجمالي
        if ($discountCode->max_uses !== null && $discountCode->usages()->count() >= $discountCode->max_uses) {
            throw new Exception('تم الوصول للحد الأقصى لاستخدام هذا الكود.');
        }

        // (يمكن إضافة التحقق من استخدام المستخدم الواحد هنا إذا لزم الأمر)

        // 5. حساب قيمة الخصم
        $discountValue = 0;
        if ($discountCode->type === 'percentage') {
            // إذا كان النوع نسبة مئوية
            $discountValue = ($discountCode->value / 100) * $totalAmount;

            // *** هذا هو الجزء الأهم: تطبيق الحد الأقصى للخصم ***
            if ($discountCode->max_discount_amount !== null && $discountValue > $discountCode->max_discount_amount) {
                $discountValue = $discountCode->max_discount_amount;
            }

        } elseif ($discountCode->type === 'fixed') {
            // إذا كان النوع مبلغاً ثابتاً
            $discountValue = $discountCode->value;
        }

        // تأكد من أن الخصم لا يتجاوز المبلغ الإجمالي
        $discountValue = min($discountValue, $totalAmount);

        return [
            'discount_amount' => round($discountValue, 2),
            'discount_code_id' => $discountCode->id,
        ];
    }
}
