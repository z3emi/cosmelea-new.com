@extends('admin.layout')
@section('title', 'إعدادات الموقع')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form">
    @csrf
    @method('PATCH')

    <div class="row">
        {{-- Main Settings Column --}}
        <div class="col-lg-8">
            {{-- Welcome Screen Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0">محرر الشاشة الترحيبية</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="show_welcome_screen" name="show_welcome_screen" 
                               @checked(old('show_welcome_screen', $settings['show_welcome_screen'] ?? 'off') == 'on')>
                        <label class="form-check-label" for="show_welcome_screen">عرض الشاشة الترحيبية للزوار (تظهر مرة واحدة)</label>
                    </div>
                    <textarea name="welcome_screen_content" id="welcome-editor">
                        {{ old('welcome_screen_content', $settings['welcome_screen_content'] ?? '') }}
                    </textarea>
                </div>
            </div>

            {{-- Dashboard Notification Card --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">محرر شريط الإشعارات</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="show_dashboard_notification" name="show_dashboard_notification"
                               @checked(old('show_dashboard_notification', $settings['show_dashboard_notification'] ?? 'off') == 'on')>
                        <label class="form-check-label" for="show_dashboard_notification">عرض شريط الإشعارات في أعلى الموقع</label>
                    </div>
                    <textarea name="dashboard_notification_content" id="notification-editor">
                        {{ old('dashboard_notification_content', $settings['dashboard_notification_content'] ?? '') }}
                    </textarea>
                </div>
            </div>
        </div>

        {{-- Side Column --}}
        <div class="col-lg-4">
            {{-- Maintenance Mode Card --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">وضع الصيانة</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="maintenance_mode" name="maintenance_mode" 
                               @checked(old('maintenance_mode', $settings['maintenance_mode'] ?? 'off') == 'on')>
                        <label class="form-check-label" for="maintenance_mode">تفعيل وضع الصيانة</label>
                    </div>
                    <small class="form-text text-muted">عند التفعيل، سيتم إيقاف الموقع بالكامل وعرض صفحة 503.</small>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
{{-- TinyMCE Rich Text Editor --}}
<script src="https://cdn.tiny.cloud/1/du3z85vklq5w3g8vsio7qztxeemn1ljmqzedt7n5vndlf6e1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#welcome-editor, #notification-editor',
        plugins: 'directionality link image code lists',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist | link image | code',
        directionality: 'rtl',
        height: 300,
        menubar: false,
    });

    // ===== START: هذا هو الكود الجديد والمهم =====
    // هذا الكود يتأكد من أن محتوى المحرر يتم حفظه عند الضغط على زر "حفظ"
    document.addEventListener('DOMContentLoaded', function() {
        const settingsForm = document.getElementById('settings-form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function(e) {
                // This is the crucial part: it saves the content from the editor
                // back to the original textarea elements before the form is submitted.
                tinymce.triggerSave();
            });
        }
    });
    // ===== END: هذا هو الكود الجديد والمهم =====
</script>
@endpush
