@csrf
<div class="row">
    {{-- Column for basic product info --}}
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="name_ar" class="form-label">اسم المنتج (عربي) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" id="name_ar" name="name_ar" value="{{ old('name_ar', $product->name_ar ?? '') }}" required>
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="name_en" class="form-label">Product Name (English)</label>
                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en', $product->name_en ?? '') }}">
                    @error('name_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="name_ku" class="form-label">ناوی بەرهەم (کوردی)</label>
                    <input type="text" class="form-control @error('name_ku') is-invalid @enderror" id="name_ku" name="name_ku" value="{{ old('name_ku', $product->name_ku ?? '') }}">
                    @error('name_ku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===== START: تم تعديل هذا القسم ===== --}}
                <div class="mb-3">
                    <label for="description_ar" class="form-label">الوصف (عربي) <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar" name="description_ar" rows="5" required>{{ old('description_ar', $product->description_ar ?? '') }}</textarea>
                    @error('description_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description_en" class="form-label">Description (English)</label>
                    <textarea class="form-control @error('description_en') is-invalid @enderror" id="description_en" name="description_en" rows="5">{{ old('description_en', $product->description_en ?? '') }}</textarea>
                    @error('description_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description_ku" class="form-label">وەسف (کوردی)</label>
                    <textarea class="form-control @error('description_ku') is-invalid @enderror" id="description_ku" name="description_ku" rows="5">{{ old('description_ku', $product->description_ku ?? '') }}</textarea>
                    @error('description_ku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- ===== END: تم تعديل هذا القسم ===== --}}

                {{-- Image Upload Section --}}
                <div class="mb-3">
                    <label for="images" class="form-label">
                        صور المنتج 
                        @if(!isset($product)) 
                            <span class="text-danger">*</span> 
                        @endif
                        <small class="text-muted">(يمكنك اختيار أكثر من صورة)</small>
                    </label>
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" multiple @if(!isset($product)) required @endif>
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Display Existing Images on Edit Page --}}
                @if(isset($product) && $product->images->isNotEmpty())
                <div class="mb-3">
                    <label class="form-label">الصور الحالية</label>
                    <div class="d-flex flex-wrap gap-2 border p-2 rounded" id="image-gallery">
                        @foreach($product->images as $image)
                            <div class="position-relative" id="image-container-{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail" width="100" alt="Product Image">
                                <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 delete-image-btn" 
                                        data-image-id="{{ $image->id }}" 
                                        style="transform: translate(50%, -50%); line-height: 1;">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Column for pricing, SKU, category, etc. --}}
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="sku" class="form-label">SKU (رمز المنتج) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required>
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">سعر البيع <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" required step="any">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sale_price" class="form-label">سعر الخصم (اختياري)</label>
                    <input type="number" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="any">
                    @error('sale_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sale_starts_at" class="form-label">تاريخ بدء الخصم</label>
                    <input type="datetime-local" class="form-control @error('sale_starts_at') is-invalid @enderror" id="sale_starts_at" name="sale_starts_at" value="{{ old('sale_starts_at', isset($product) && $product->sale_starts_at ? $product->sale_starts_at->format('Y-m-d\TH:i') : '') }}">
                    @error('sale_starts_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sale_ends_at" class="form-label">تاريخ انتهاء الخصم</label>
                    <input type="datetime-local" class="form-control @error('sale_ends_at') is-invalid @enderror" id="sale_ends_at" name="sale_ends_at" value="{{ old('sale_ends_at', isset($product) && $product->sale_ends_at ? $product->sale_ends_at->format('Y-m-d\TH:i') : '') }}">
                    @error('sale_ends_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">القسم <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">-- اختر القسم --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                                {{ $category->name_ar }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                    <label class="form-check-label" for="is_active">المنتج فعال</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 text-end">
    <button type="submit" class="btn btn-primary">حفظ المنتج</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">إلغاء</a>
</div>

@push('scripts')
{{-- ===== START: تم إضافة هذه السكربتات ===== --}}
<script src="https://cdn.tiny.cloud/1/du3z85vklq5w3g8vsio7qztxeemn1ljmqzedt7n5vndlf6e1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    ['#description_ar', '#description_en', '#description_ku'].forEach(function(sel) {
        if(document.querySelector(sel)) {
            tinymce.init({
                selector: sel,
                plugins: 'directionality link image code lists',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist | link image | code',
                directionality: sel === '#description_en' ? 'ltr' : 'rtl',
                height: 300,
                menubar: false,
            });
        }
    });
</script>
{{-- ===== END: تم إضافة هذه السكربتات ===== --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ensure TinyMCE content is saved before submitting
    const productForm = document.querySelector('form');
    if (productForm) {
        productForm.addEventListener('submit', function () {
            tinymce.triggerSave();
        });
    }

    const imageGallery = document.getElementById('image-gallery');
    if (imageGallery) {
        imageGallery.addEventListener('click', function (e) {
            if (e.target.closest('.delete-image-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-image-btn');
                const imageId = button.dataset.imageId;

                if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
                    fetch(`/admin/products/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`image-container-${imageId}`).remove();
                        } else {
                            alert(data.message || 'فشل حذف الصورة.');
                        }
                    })
                    .catch(() => alert('حدث خطأ في الاتصال.'));
                }
            }
        });
    }
});
</script>
@endpush
