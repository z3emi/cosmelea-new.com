@extends('layouts.app')

@section('title', 'عربة التسوق')

@section('content')
<div class="bg-gray-50 min-h-screen"
    x-data='{
        cartItems: @json($cartItems),
        subtotal: {{ $total }},
        discount: {{ $discountValue }},
        shippingCost: {{ $shippingCost }},
        discountCode: "{{ session('discount_code', '') }}",
        feedbackMessage: "{{ session('discount_error') ?: (session('discount_code') ? 'تم تطبيق كود الخصم: ' . session('discount_code') : '') }}",
        feedbackType: "{{ session('discount_error') ? 'error' : (session('discount_code') ? 'success' : '') }}",
        
        updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) {
                this.cartItems[productId].quantity = 1;
                return;
            }
            this.cartItems[productId].quantity = newQuantity;
            this.updateCartOnServer(productId, newQuantity);
        },

        removeItem(productId) {
            if (!confirm("هل أنت متأكد من إزالة هذا المنتج؟")) return;
            
            fetch("{{ route('cart.destroy') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    delete this.cartItems[productId];
                    window.dispatchEvent(new CustomEvent("cart-updated", { detail: { cartCount: data.cartCount } }));
                    this.recalculateTotal();
                }
            });
        },

        updateCartOnServer(productId, quantity) {
            fetch("{{ route('cart.update') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.dispatchEvent(new CustomEvent("cart-updated", { detail: { cartCount: data.cartCount } }));
                    this.recalculateTotal();
                }
            });
        },

        recalculateTotal() {
            let newTotal = 0;
            for (const id in this.cartItems) {
                newTotal += this.cartItems[id].product.price * this.cartItems[id].quantity;
            }
            this.subtotal = newTotal;
            this.shippingCost = (newTotal >= 50000) ? 0 : 4000;
        },

        applyDiscount() {
            fetch("{{ route('cart.applyDiscount') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
                body: JSON.stringify({ discount_code: this.discountCode })
            })
            .then(res => res.json().then(data => ({status: res.status, body: data})))
            .then(({status, body}) => {
                this.feedbackMessage = body.message;
                if (status === 200) {
                    this.feedbackType = "success";
                    this.discount = body.discount_value;
                    this.discountCode = body.discount_code;
                } else {
                    this.feedbackType = "error";
                    this.discount = 0;
                }
            })
            .catch(() => {
                this.feedbackMessage = "حدث خطأ في الاتصال.";
                this.feedbackType = "error";
            });
        },

        removeDiscount() {
            fetch("{{ route('cart.removeDiscount') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.discount = 0;
                    this.discountCode = "";
                    this.feedbackMessage = data.message;
                    this.feedbackType = "success";
                }
            });
        },

        formatPrice(price) {
            // Use en-US locale to display English numerals (0-9)
            return new Intl.NumberFormat("en-US").format(price);
        }
    }'
>
    <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-brand-text">سلة التسوق (<span x-text="Object.keys(cartItems).length"></span>)</h1>
            <a href="{{ route('shop') }}" class="text-sm text-gray-500 hover:text-brand-primary">
                <i class="bi bi-arrow-left-short"></i> متابعة التسوق
            </a>
        </div>

        <template x-if="Object.keys(cartItems).length === 0">
            <div class="text-center bg-white p-10 rounded-lg shadow-md">
                <i class="bi bi-cart-x text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-2">عربة التسوق الخاصة بك فارغة</h2>
                <p class="text-gray-500 mb-6">يبدو أنك لم تقم بإضافة أي منتجات بعد.</p>
                <a href="{{ route('shop') }}" class="inline-block bg-brand-primary text-white font-bold py-3 px-6 rounded-md hover:bg-brand-dark transition duration-300">
                    العودة إلى المتجر
                </a>
            </div>
        </template>

        <template x-if="Object.keys(cartItems).length > 0">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                {{-- المنتجات --}}
                <div class="lg:w-7/12 xl:w-2/3">
                    <div class="space-y-4">
                        <template x-for="item in Object.values(cartItems)" :key="item.product.id">
                            <div class="bg-white rounded-lg shadow-sm p-4 flex gap-4">
                                <a :href="`/product/${item.product.id}`" class="w-24 h-24 flex-shrink-0">
                                    <img :src="item.product.first_image ? `/storage/${item.product.first_image.image_path}` : 'https://placehold.co/150x150?text=No+Image'" :alt="item.product.name_ar" class="w-full h-full object-cover rounded-md">
                                </a>
                                <div class="flex flex-col flex-grow w-full">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <a :href="`/product/${item.product.id}`" class="font-bold text-lg text-brand-text hover:text-brand-primary" x-text="item.product.name_ar"></a>
                                            <p class="text-sm text-gray-500">SKU: <span x-text="item.product.sku || 'N/A'"></span></p>
                                        </div>
                                        <button @click="removeItem(item.product.id)" class="text-gray-400 hover:text-red-500 transition" title="إزالة المنتج">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-end justify-between mt-auto">
                                        <div class="flex items-center border rounded-md overflow-hidden">
                                            <button @click="updateQuantity(item.product.id, item.quantity + 1)" class="px-3 py-1 text-lg hover:bg-gray-100">+</button>
                                            <input type="number" x-model.number="item.quantity" @change="updateQuantity(item.product.id, item.quantity)" class="w-12 text-center border-x focus:outline-none">
                                            <button @click="updateQuantity(item.product.id, item.quantity - 1)" class="px-3 py-1 text-lg hover:bg-gray-100">-</button>
                                        </div>
                                        <p class="font-bold text-lg text-brand-dark" x-text="`${formatPrice(item.product.price * item.quantity)} د.ع`"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ملخص --}}
                <div class="lg:w-5/12 xl:w-1/3">
                    <div class="bg-white p-6 rounded-lg shadow-sm sticky top-24">
                        <h2 class="text-xl font-bold mb-4">ملخص الطلب</h2>
                        
                        <div class="space-y-3 text-gray-700">
                            <div class="flex justify-between"><span>الإجمالي</span><span class="font-semibold" x-text="`${formatPrice(subtotal)} د.ع`"></span></div>
                            <div class="flex justify-between"><span>الخصم</span><span class="font-semibold text-green-600" x-text="`- ${formatPrice(discount)} د.ع`"></span></div>
                            <div class="flex justify-between"><span>الشحن</span><span class="font-semibold" x-text="shippingCost > 0 ? `${formatPrice(shippingCost)} د.ع` : 'مجاني'"></span></div>
                        </div>

                        <div class="flex justify-between font-bold text-xl border-t mt-4 pt-4"><span>المجموع</span><span x-text="`${formatPrice(subtotal - discount + shippingCost)} د.ع`"></span></div>

                        <div class="mt-4 text-center">
                            <div x-show="subtotal < 50000" style="display: none;">
                                <p class="text-sm text-gray-600 mb-2">
                                    باقي <strong class="text-brand-primary" x-text="formatPrice(50000 - subtotal)"></strong> د.ع للحصول على شحن مجاني
                                </p>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-500 h-2.5 rounded-full" :style="`width: ${(subtotal / 50000) * 100}%`"></div>
                                </div>
                            </div>
                            <div x-show="subtotal >= 50000" class="text-green-600 font-semibold p-2 bg-green-50 rounded-md" style="display: none;">
                                <p>🎉 لقد حصلت على شحن مجاني!</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('checkout.index') }}" class="block text-center w-full bg-brand-dark text-white font-bold py-3 px-4 rounded-md hover:bg-brand-primary transition duration-300">إتمام عملية الشراء</a>
                        </div>
                        
                        <div class="mt-4">
                            <template x-if="discount <= 0">
                                <form @submit.prevent="applyDiscount" class="flex gap-2">
                                    <input type="text" x-model="discountCode" placeholder="إضافة كوبون" class="flex-1 border rounded-md px-3 py-2 text-right focus:ring-2 focus:ring-brand-primary">
                                    <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">تطبيق</button>
                                </form>
                            </template>
                            <template x-if="discount > 0">
                                <div class="bg-green-100 text-green-800 p-2 rounded text-sm flex justify-between items-center">
                                    <span>تم تطبيق كود: <strong x-text="discountCode"></strong></span>
                                    <button @click="removeDiscount" class="text-red-600 hover:text-red-800 font-bold" title="إزالة الكوبون">&times;</button>
                                </div>
                            </template>
                            <p x-show="feedbackMessage" :class="{ 'text-green-600': feedbackType === 'success', 'text-red-600': feedbackType === 'error' }" class="text-sm mt-2" x-text="feedbackMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
