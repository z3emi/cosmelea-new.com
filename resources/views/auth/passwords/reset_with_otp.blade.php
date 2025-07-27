@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة السر')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-8 px-6 md:px-8">
            <h2 class="text-2xl font-bold text-center text-brand-text mb-2">أدخل الرمز وكلمة السر الجديدة</h2>
            <p class="text-center text-sm text-gray-600 mb-6">لقد أرسلنا رمزًا إلى رقم هاتفك {{ session('phone_number_for_reset') }}.</p>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update.with.otp') }}">
                @csrf
                <input type="hidden" name="phone_number" value="{{ session('phone_number_for_reset') }}">

                <div class="mb-4">
                    <label for="otp" class="block text-gray-700 text-sm font-bold mb-2">رمز التحقق (OTP)</label>
                    <input id="otp" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" name="otp" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">كلمة المرور الجديدة</label>
                    <input id="password" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md" name="password" required>
                </div>

                <div class="mb-6">
                    <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">تأكيد كلمة المرور</label>
                    <input id="password-confirm" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md" name="password_confirmation" required>
                </div>

                <div class="mb-4">
                    <button type="submit" class="w-full bg-brand-primary text-white font-bold py-3 px-4 rounded-md hover:bg-brand-dark transition duration-300">
                        إعادة تعيين كلمة السر
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
