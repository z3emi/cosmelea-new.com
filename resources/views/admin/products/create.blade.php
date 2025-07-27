@extends('admin.layout')

@section('title', 'إضافة منتج جديد')

@section('content')
<div class="card">
    <div class="card-header">
        <strong>🛒 فورم إضافة منتج جديد</strong>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
</div>
@endsection
