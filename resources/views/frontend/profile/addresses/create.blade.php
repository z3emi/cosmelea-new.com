@extends('frontend.profile.layout')

@section('title', 'إضافة عنوان جديد')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { 
            height: 300px;
            width: 100%;
            background: #f9f5f1;
            border: 1px solid #eadbcd;
            border-radius: 8px;
            margin: 1rem 0;
            z-index: 1;
        }
        .leaflet-container {
            position: relative !important;
        }
        /* تحسينات للجوال */
        @media (max-width: 640px) {
            #map {
                height: 250px;
            }
        }
    </style>
@endpush

@section('profile-content')
<div class="bg-white rounded-lg shadow-sm border border-[#eadbcd] p-4 md:p-6">
    <h2 class="text-xl md:text-2xl font-bold text-[#4a3f3f] mb-4 md:mb-6">إضافة عنوان شحن جديد</h2>

    <form action="{{ route('profile.addresses.store') }}" method="POST">
        @csrf
        <div class="space-y-3 md:space-y-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-4">
                <div>
                    <label for="governorate" class="block text-sm md:text-base font-medium text-[#4a3f3f] mb-1">المحافظة</label>
                    <select id="governorate" name="governorate" required
                        class="w-full border border-[#eadbcd] rounded-md shadow-sm p-2">
                        <option value="">اختر المحافظة</option>
                        @php
                            $governorates = ['بغداد','نينوى','البصرة','صلاح الدين','دهوك','أربيل','السليمانية','ديالى','واسط','ميسان','ذي قار','المثنى','بابل','كربلاء','النجف','القادسية','الأنبار'];
                        @endphp
                        @foreach ($governorates as $gov)
                            <option value="{{ $gov }}" {{ old('governorate') == $gov ? 'selected' : '' }}>{{ $gov }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="city" class="block text-sm md:text-base font-medium text-[#4a3f3f] mb-1">المدينة / القضاء</label>
                    <input type="text" class="w-full border border-[#eadbcd] rounded-md shadow-sm p-2" 
                           id="city" name="city" value="{{ old('city') }}" required>
                </div>
            </div>
            
            <div>
                <label for="address_details" class="block text-sm md:text-base font-medium text-[#4a3f3f] mb-1">تفاصيل العنوان</label>
                <input type="text" class="w-full border border-[#eadbcd] rounded-md shadow-sm p-2" 
                       id="address_details" name="address_details" value="{{ old('address_details') }}" 
                       placeholder="اسم الشارع، رقم الزقاق، رقم الدار" required>
            </div>
            
            <div>
                <label for="nearest_landmark" class="block text-sm md:text-base font-medium text-[#4a3f3f] mb-1">أقرب نقطة دالة (اختياري)</label>
                <input type="text" class="w-full border border-[#eadbcd] rounded-md shadow-sm p-2" 
                       id="nearest_landmark" name="nearest_landmark" value="{{ old('nearest_landmark') }}"
                       placeholder="مثال: قرب جامع الحبوبي">
            </div>
            
            <hr class="border-[#eadbcd] my-3 md:my-4">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2">
                <h3 class="text-sm md:text-base font-bold text-[#4a3f3f]">تحديد الموقع على الخريطة (اختياري)</h3>
                <button type="button" class="text-xs md:text-sm font-semibold text-[#cd8985] hover:underline flex items-center gap-1" id="get_location_btn">
                    <i class="bi bi-geo-alt-fill"></i> تحديد موقعي الحالي
                </button>
            </div>
            
            <div id="map"></div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <div class="flex flex-col sm:flex-row justify-start gap-3 pt-3 md:pt-4">
                <button type="submit" class="bg-[#cd8985] text-white font-bold py-2 px-4 md:px-6 rounded-md hover:bg-[#be6661] transition-colors">
                    حفظ العنوان
                </button>
                <a href="{{ route('profile.addresses.index') }}" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 md:px-6 rounded-md hover:bg-gray-300 transition-colors text-center">
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تأخير بسيط لضمان تحميل DOM بالكامل
    setTimeout(initMap, 100);
    
    function initMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement) return;
        
        // إعداد الخريطة
        const map = L.map('map').setView([33.3152, 44.3661], 12);
        
        // إضافة طبقة الخريطة الأساسية
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            subdomains: ['a','b','c']
        }).addTo(map);

        // إضافة علامة قابلة للسحب
        const marker = L.marker(map.getCenter(), {
            draggable: true,
            autoPan: true
        }).addTo(map)
          .bindPopup('اسحبني لتحديد الموقع الدقيق')
          .openPopup();

        // تحديث الإحداثيات عند تغيير الموقع
        const updateLocation = (lat, lng) => {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            
            // جلب بيانات العنوان (اختياري)
            // يمكنك إضافة كود جلب البيانات هنا إذا لزم الأمر
        };

        // الأحداث
        marker.on('dragend', function(e) {
            const {lat, lng} = e.target.getLatLng();
            updateLocation(lat, lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateLocation(e.latlng.lat, e.latlng.lng);
        });

        // تحديد الموقع الحالي
        document.getElementById('get_location_btn').addEventListener('click', function() {
            if (navigator.geolocation) {
                this.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> جاري التحديد...';
                
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const {latitude, longitude} = pos.coords;
                        map.setView([latitude, longitude], 15);
                        marker.setLatLng([latitude, longitude]);
                        updateLocation(latitude, longitude);
                        this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> تحديد موقعي الحالي';
                    },
                    err => {
                        alert('خطأ في تحديد الموقع: ' + err.message);
                        this.innerHTML = '<i class="bi bi-geo-alt-fill"></i> تحديد موقعي الحالي';
                    },
                    {enableHighAccuracy: true, timeout: 10000}
                );
            } else {
                alert('المتصفح لا يدعم خدمة تحديد الموقع');
            }
        });

        // إصلاح مشكلة إعادة الحجم
        setTimeout(() => map.invalidateSize(), 200);
    }
});
</script>
@endpush