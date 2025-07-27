<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate; // استيراد مكتبة الصلاحيات
use Illuminate\Support\Facades\View;  // استيراد مكتبة الـ View
use App\Models\Setting;              // استيراد موديل الإعدادات

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. استخدام تصميم Bootstrap 5 لروابط الصفحات
        Paginator::useBootstrapFive();

        // 2. منح صلاحية كاملة للـ Super-Admin
        // هذا الكود يتأكد من أن المدير الأعلى يمكنه الوصول لكل شيء
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super-Admin') ? true : null;
        });

        // 3. مشاركة إعدادات الإشعارات مع كل صفحات الواجهة الأمامية
        // هذا الكود ضروري لعمل الإشعار والشاشة الترحيبية
        View::composer('layouts.app', function ($view) {
            try {
                $settings = Setting::whereIn('key', [
                    'show_dashboard_notification',
                    'dashboard_notification_content',
                    'show_welcome_screen',
                    'welcome_screen_content'
                ])->pluck('value', 'key');

                $view->with($settings->all());
            } catch (\Exception $e) {
                // في حالة عدم وجود جدول الإعدادات (أثناء التثبيت مثلاً)، تجاهل الخطأ
            }
        });
    }
}
