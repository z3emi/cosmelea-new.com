@extends('layouts.app')

@section('title', 'تأكيد رقم الهاتف')

@section('content')
<div class="container mx-auto px-4 py-16" 
    x-data="{
        timer: 60,
        canResend: false,
        message: '',
        messageType: '',

        startTimer() {
            this.canResend = false;
            let interval = setInterval(() => {
                this.timer--;
                if (this.timer === 0) {
                    clearInterval(interval);
                    this.canResend = true;
                    this.timer = 60;
                }
            }, 1000);
        },

        async resendCode() {
            if (!this.canResend) return;

            this.canResend = false;
            this.message = 'جاري إرسال الرمز...';
            this.messageType = 'info';

            try {
                const response = await fetch('{{ route('otp.verification.resend') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'حدث خطأ ما');
                }

                this.message = data.message;
                this.messageType = 'success';
                this.startTimer();

            } catch (error) {
                this.message = error.message || 'حدث خطأ. يرجى المحاولة مرة أخرى.';
                this.messageType = 'error';
                this.canResend = true;
            }
        }
    }"
    x-init="startTimer()"
>
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="py-8 px-6 md:px-8">
            <h2 class="text-2xl font-bold text-center text-brand-text mb-2">تأكيد رقم الهاتف</h2>
            <p class="text-center text-sm text-gray-600 mb-6">لقد أرسلنا رمز تحقق مكون من 6 أرقام إلى رقم هاتفك عبر واتساب.</p>
            
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div x-show="message" 
                 :class="{
                     'bg-green-100 border-green-400 text-green-700': messageType === 'success',
                     'bg-red-100 border-red-400 text-red-700': messageType === 'error',
                     'bg-blue-100 border-blue-400 text-blue-700': messageType === 'info'
                 }"
                 class="border px-4 py-3 rounded relative mb-4" 
                 role="alert"
                 x-text="message">
            </div>

            <form method="POST" action="{{ route('otp.verification.verify') }}">
                @csrf
                <div class="mb-4">
                    <label for="otp" class="block text-gray-700 text-sm font-bold mb-2">رمز التحقق</label>
                    <input id="otp" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-primary @error('otp') border-red-500 @enderror"
                        name="otp" required autofocus inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
                    @error('otp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-brand-primary text-white font-bold py-3 px-4 rounded-md hover:bg-brand-dark transition duration-300">
                        تحقق
                    </button>
                </div>
            </form>

            <div class="text-center text-sm text-gray-600 mt-4">
                <p x-show="!canResend">
                    يمكنك طلب رمز جديد خلال <span x-text="timer" class="font-bold"></span> ثانية.
                </p>
                <button @click="resendCode()" :disabled="!canResend" x-show="canResend"
                    class="font-bold text-brand-primary hover:underline disabled:text-gray-400 disabled:cursor-not-allowed">
                    إعادة إرسال الرمز
                </button>
            </div>

            {{-- زر واتساب --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">بحاجة لمساعدة؟ تواصل معنا على الواتساب:</p>
                <a href="https://wa.me/9647701234567" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md shadow">
                    <i class="bi bi-whatsapp text-lg ml-2"></i>
                    تواصل عبر واتساب
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
