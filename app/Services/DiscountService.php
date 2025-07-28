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
     * @param array $items بيانات المنتجات في الطلب (product_id, category_id, price, quantity).
     * @return array تحتوي على قيمة الخصم ومعرّف الكود.
     * @throws \Exception في حال كان الكود غير صالح.
     */
    public function apply(string $code, array $items): array
    {
        $discountCode = DiscountCode::with(['products', 'categories'])->where('code', $code)->first();

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

        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $applicableTotal = $totalAmount;

        if ($discountCode->products->isNotEmpty() || $discountCode->categories->isNotEmpty()) {
            $applicableTotal = 0;
            foreach ($items as $item) {
                $pid = $item['product_id'];
                $cid = $item['category_id'] ?? null;
                if ($discountCode->products->contains('id', $pid) || ($cid && $discountCode->categories->contains('id', $cid))) {
                    $applicableTotal += $item['price'] * $item['quantity'];
                }
            }
            if ($applicableTotal == 0) {
                throw new Exception('كود الخصم غير صالح لهذه المنتجات.');
            }
        }

        $discountValue = 0;
        if ($discountCode->type === 'percentage') {
            $discountValue = ($discountCode->value / 100) * $applicableTotal;
            if ($discountCode->max_discount_amount !== null && $discountValue > $discountCode->max_discount_amount) {
                $discountValue = $discountCode->max_discount_amount;
            }
        } elseif ($discountCode->type === 'fixed') {
            $discountValue = $discountCode->value;
        }

        $discountValue = min($discountValue, $applicableTotal);

        return [
            'discount_amount' => round($discountValue, 2),
            'discount_code_id' => $discountCode->id,
        ];
    }
}
