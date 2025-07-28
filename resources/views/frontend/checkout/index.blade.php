@extends('layouts.app')

@section('title', 'إتمام عملية الشراء')

@push('styles')
    <style>
        .step-badge {
            width: 2.5rem; height: 2.5rem; border-radius: 9999px;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: bold; color: white;
        }
        .address-card.selected {
            border-color: #be6661; /* brand-primary */
            box-shadow: 0 0 0 2px #be6661;
        }
    </style>
@endpush

@section('content')
<div class="bg-gray-50/50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-brand-text">إتمام الشراء</h1>
            <p class="text-gray-500 mt-2">أكمل معلوماتك لتأكيد الطلب.</p>
        </div>

        {{-- ===== START: قسم عرض الأخطاء المضاف ===== --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-8 max-w-4xl mx-auto" role="alert">
                <strong class="font-bold">يرجى تصحيح الأخطاء التالية:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- ===== END: قسم عرض الأخطاء المضاف ===== --}}

<form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
    @csrf
    <input type="hidden" name="address_option" value="saved">

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
        {{-- Left Column: Shipping and Payment --}}
        <div class="lg:w-7/12 xl:w-2/3 space-y-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center gap-4 mb-5">
                    <span class="step-badge bg-brand-primary">1</span>
                    <h2 class="text-xl font-bold text-brand-text">معلومات الشحن</h2>
                </div>
                
                <div class="space-y-4" x-data="{ selectedAddressId: {{ $addresses->first()->id ?? 'null' }} }">
                    @if($addresses->isNotEmpty())
                    <div class="mb-4">
                        <h3 class="text-md font-semibold text-gray-800 mb-2">اختر من عناوينك المحفوظة:</h3>
                        <div class="space-y-3" id="saved_addresses_list">
                            @foreach($addresses as $address)
                            <label class="address-card block border rounded-lg p-4 cursor-pointer transition hover:border-brand-primary" :class="selectedAddressId == {{ $address->id }} ? 'selected' : ''">
                                <div class="flex items-center">
                                    <input type="radio" name="saved_address_id" value="{{ $address->id }}"
                                        class="saved-address-radio h-4 w-4 text-brand-primary focus:ring-brand-primary"
                                        @click="selectedAddressId = {{ $address->id }}"
                                        {{ $loop->first ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <p class="font-semibold">{{ $address->governorate }}, {{ $address->city }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->address_details }}</p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <p class="text-yellow-800">لم تقم بإضافة أي عنوان بعد. يرجى إضافة عنوان للمتابعة.</p>
                    </div>
                    @endif

                    @if($addresses->count() < 5)
                    <button type="button" id="addAddressBtn" class="w-full text-left border rounded-lg p-4 flex items-center gap-3 text-brand-primary font-semibold hover:bg-gray-50 transition" data-bs-toggle="modal" data-bs-target="#newAddressModal">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>إضافة عنوان شحن جديد</span>
                    </button>
                    @endif
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center gap-4 mb-5">
                    <span class="step-badge bg-brand-primary">2</span>
                    <h2 class="text-xl font-bold text-brand-text">طريقة الدفع</h2>
                </div>
                <div class="bg-gray-50 p-4 rounded-md border hover:border-brand-primary transition">
                    <label for="cash" class="flex items-center cursor-pointer">
                        <input id="cash" name="payment_method" type="radio"
                               class="h-4 w-4 text-brand-primary focus:ring-brand-primary"
                               value="cash_on_delivery" checked required>
                        <span class="ml-3 font-medium">الدفع عند الاستلام</span>
                        <i class="bi bi-cash-coin text-xl text-green-600 ml-auto"></i>
                    </label>
                </div>
            </div>
        </div>

        {{-- Right Column: Order Summary --}}
        <div class="lg:w-5/12 xl:w-1/3">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-24">
                <h2 class="text-xl font-bold text-brand-text mb-4 border-b pb-4">ملخص الطلب</h2>
                
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto pr-2">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item['product']->firstImage ? asset('storage/' . $item['product']->firstImage->image_path) : 'https://placehold.co/60x60' }}" alt="{{ $item['product']->name_translated }}" class="w-12 h-12 rounded-md object-cover">
                                <div>
                                    <p class="text-gray-800 font-semibold">{{ $item['product']->name_translated }}</p>
                                    <p class="text-gray-500">الكمية: {{ $item['quantity'] }}</p>
                                </div>
                            </div>
                            <span class="font-medium">{{ number_format($item['price'] * $item['quantity']) }} د.ع</span>
                        </div>
                    @endforeach
                </div>
                
                <div class="space-y-2 border-t pt-4">
                    <div class="flex justify-between font-semibold"><span>المجموع الفرعي</span><span>{{ number_format($subtotal) }} د.ع</span></div>
                    <div class="flex justify-between text-green-600"><span>الخصم</span><span>- {{ number_format($discountValue) }} د.ع</span></div>
                    <div class="flex justify-between text-gray-500"><span>الشحن</span><span>{{ $shippingCost > 0 ? number_format($shippingCost) . ' د.ع' : 'مجاني' }}</span></div>
                    <div class="flex justify-between font-bold text-xl text-brand-dark border-t pt-2 mt-2"><span>الإجمالي</span><span>{{ number_format($finalTotal) }} د.ع</span></div>
                </div>

                <div class="mt-6">
                    <button class="w-full bg-brand-dark text-white font-bold py-3 px-4 rounded-md hover:bg-brand-primary transition duration-300 text-lg" type="submit" @if($addresses->isEmpty()) disabled @endif>
                        <i class="bi bi-shield-check"></i>
                        تأكيد الطلب الآن
                    </button>
                    @if($addresses->isEmpty())
                        <p class="text-red-500 text-xs text-center mt-2">يجب إضافة عنوان أولاً لتتمكن من تأكيد الطلب.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="newAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#cd8985;color:white;">
                <h5 class="modal-title">إضافة عنوان جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="new-address-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">المحافظة</label>
                        <input type="text" name="governorate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المدينة / القضاء</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تفاصيل العنوان</label>
                        <input type="text" name="address_details" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">أقرب نقطة دالة</label>
                        <input type="text" name="nearest_landmark" class="form-control">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
                <div class="alert alert-danger mt-2 d-none" id="address-error"></div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const savedAddressesRadios = document.querySelectorAll('.saved-address-radio');
            
            function selectAddress(radio) {
                document.querySelectorAll('.address-card').forEach(card => card.classList.remove('selected'));
                radio.closest('.address-card').classList.add('selected');
            }

            savedAddressesRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if(this.checked) {
                        selectAddress(this);
                    }
                });
            });

            // Set initial state
            const checkedRadio = document.querySelector('input[name="saved_address_id"]:checked');
            if(checkedRadio) {
                selectAddress(checkedRadio);
            }

            const newAddressForm = document.getElementById('new-address-form');
            if(newAddressForm) {
                newAddressForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(newAddressForm);
                    fetch('{{ route('checkout.address.store.ajax') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token') || document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            const list = document.getElementById('saved_addresses_list');
                            const id = data.address.id;
                            const label = document.createElement('label');
                            label.className = 'address-card block border rounded-lg p-4 cursor-pointer transition hover:border-brand-primary selected';
                            label.innerHTML = `<div class="flex items-center"><input type="radio" name="saved_address_id" value="${id}" class="saved-address-radio h-4 w-4 text-brand-primary focus:ring-brand-primary" checked><div class="ml-3"><p class="font-semibold">${data.address.governorate}, ${data.address.city}</p><p class="text-sm text-gray-600">${data.address.address_details}</p></div></div>`;
                            list.appendChild(label);
                            document.querySelectorAll('.address-card').forEach(c => c.classList.remove('selected'));
                            label.querySelector('input').addEventListener('change', function(){selectAddress(this);});
                            new bootstrap.Modal(document.getElementById('newAddressModal')).hide();
                        } else {
                            const err = document.getElementById('address-error');
                            err.textContent = data.message || 'خطأ في الحفظ';
                            err.classList.remove('d-none');
                        }
                    });
                });
            }
        });
    </script>
@endpush