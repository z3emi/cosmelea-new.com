@extends('frontend.profile.layout')

@section('title', 'تعديل الملف الشخصي')

@section('profile-content')
<div x-data="{ openPasswordModal: false }" class="space-y-6 bg-[#ffffff] p-4 md:p-8 rounded-lg shadow-sm border border-[#eadbcd]">

    {{-- عرض رسائل النجاح والأخطاء --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md text-sm md:text-base" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md text-sm md:text-base" role="alert">
            <p class="font-bold mb-1 md:mb-2">الرجاء إصلاح الأخطاء التالية:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- فورم تعديل البيانات --}}
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        <h2 class="text-xl md:text-2xl font-bold text-[#4a3f3f] mb-4">معلومات الملف الشخصي</h2>

        {{-- تحميل الصورة --}}
        <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
            <div class="relative">
                <img
                    id="avatarPreview"
                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://i.pravatar.cc/150?u=' . $user->id }}"
                    alt="الصورة الشخصية"
                    class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover border-2 border-[#cd8985]"
                >
            </div>
            <div class="w-full md:w-auto">
                <label for="avatar" class="block w-full md:w-auto cursor-pointer bg-[#cd8985] text-white px-4 py-2 rounded-md text-sm hover:bg-[#be6661] transition-colors text-center">
                    تغيير الصورة الشخصية
                </label>
                <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" onchange="previewAvatar(event)">
                <p class="text-xs text-gray-500 mt-1 text-center md:text-right">يفضل صورة مربعة 200x200 بكسل</p>
            </div>
        </div>

        @php
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
        @endphp

        {{-- حقول المعلومات --}}
        <div class="grid grid-cols-1 gap-4 text-[#4a3f3f]">
            <div>
                <label for="first_name" class="block mb-1 font-medium text-sm md:text-base">الاسم الأول</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $firstName) }}" 
                       class="w-full border border-[#eadbcd] rounded-md shadow-sm px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#cd8985]">
            </div>
            <div>
                <label for="last_name" class="block mb-1 font-medium text-sm md:text-base">اسم العائلة</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $lastName) }}" 
                       class="w-full border border-[#eadbcd] rounded-md shadow-sm px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#cd8985]">
            </div>
            <div>
                <label for="email" class="block mb-1 font-medium text-sm md:text-base">البريد الإلكتروني (اختياري)</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                       class="w-full border border-[#eadbcd] rounded-md shadow-sm px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#cd8985]">
            </div>
            <div>
                <label for="phone_number" class="block mb-1 font-medium text-sm md:text-base">رقم الهاتف</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                       class="w-full border border-[#eadbcd] rounded-md shadow-sm px-3 py-2 bg-gray-100 cursor-not-allowed text-sm md:text-base" readonly>
            </div>
        </div>

        {{-- أزرار الحفظ --}}
        <div class="flex flex-col sm:flex-row justify-start gap-3 mt-6">
            <button type="submit" class="bg-[#be6661] text-white font-bold px-6 py-2 rounded-md hover:bg-[#cd8985] transition-colors text-sm md:text-base">
                حفظ التغييرات
            </button>

            <button type="button" @click="openPasswordModal = true" 
                    class="bg-[#cd8985] text-white font-bold px-6 py-2 rounded-md hover:bg-[#be6661] transition-colors text-sm md:text-base">
                تغيير كلمة المرور
            </button>
        </div>
    </form>

    {{-- نافذة تغيير كلمة المرور --}}
    <div x-show="openPasswordModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         style="display: none;">
        <div @click.away="openPasswordModal = false" 
             class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg relative mx-2">
            <button @click="openPasswordModal = false" 
                    class="absolute left-4 top-4 text-gray-500 hover:text-gray-700">
                <i class="bi bi-x-lg"></i>
            </button>
            
            <h3 class="text-lg md:text-xl font-bold mb-4 text-[#4a3f3f] text-center">تحديث كلمة المرور</h3>
            
            <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_password" class="block mb-1 font-medium text-sm md:text-base">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full border border-[#eadbcd] rounded-md px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#cd8985]">
                </div>
                <div>
                    <label for="new_password" class="block mb-1 font-medium text-sm md:text-base">كلمة المرور الجديدة</label>
                    <input type="password" name="new_password" id="new_password" required
                        class="w-full border border-[#eadbcd] rounded-md px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#cd8985]">
                </div>
                <div>
                    <label for="new_password_confirmation" class="block mb-1 font-medium text-sm md:text-base">تأكيد كلمة المرور</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                        class="w-full border border-[#eadbcd] rounded-md px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#cd8985]">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openPasswordModal = false"
                        class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-100 transition text-sm md:text-base">
                        إلغاء
                    </button>
                    <button type="submit"
                        class="bg-[#be6661] text-white px-6 py-2 rounded-md hover:bg-[#cd8985] transition text-sm md:text-base">
                        حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function previewAvatar(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
    @media (max-width: 640px) {
        /* تحسينات إضافية للشاشات الصغيرة جدًا */
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
</style>
@endsection