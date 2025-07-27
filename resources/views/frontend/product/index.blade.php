@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
    <div class="flex flex-col md:flex-row gap-8">
        <aside class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold text-primary-text mb-4">التصنيفات</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="block hover:text-accent-2">الكل</a></li>
                    <li><a href="#" class="block text-accent-2 font-bold">عناية بالبشرة</a></li>
                    <li><a href="#" class="block hover:text-accent-2">مكياج</a></li>
                    <li><a href="#" class="block hover:text-accent-2">عطور</a></li>
                </ul>
                <hr class="my-6 border-dark-accent">
                <h3 class="text-xl font-bold text-primary-text mb-4">السعر</h3>
                <input type="range" min="0" max="1000" value="500" class="w-full">
                <div class="flex justify-between text-sm mt-2">
                    <span>0 ريال</span>
                    <span>1000 ريال</span>
                </div>
            </div>
        </aside>

        <main class="w-full md:w-3/4">
            <h1 class="text-3xl font-bold text-primary-text mb-6">كل المنتجات</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <a href="/products/product-slug">
                        <img src="https://via.placeholder.com/300x300.png/eadbcd/cd8985?text=Product" alt="اسم المنتج" class="w-full h-56 object-cover">
                    </a>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-primary-text mb-2">
                            <a href="/products/product-slug">اسم المنتج</a>
                        </h3>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-accent-2">99 ريال</span>
                            <button class="text-primary-text hover:text-accent-2">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                </div>
            <div class="mt-8">
                <nav class="flex justify-center">
                    <a href="#" class="px-4 py-2 mx-1 bg-white text-primary-text rounded-md border border-primary-text">1</a>
                    <a href="#" class="px-4 py-2 mx-1 bg-primary-text text-white rounded-md">2</a>
                    <a href="#" class="px-4 py-2 mx-1 bg-white text-primary-text rounded-md border border-primary-text">3</a>
                </nav>
            </div>
        </main>
    </div>
@endsection