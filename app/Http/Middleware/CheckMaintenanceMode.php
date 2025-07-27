<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // التأكد من أن جدول الإعدادات موجود لتجنب الأخطاء أثناء التثبيت
        if (!Schema::hasTable('settings')) {
            return $next($request);
        }

        $maintenanceMode = Setting::where('key', 'maintenance_mode')->first();

        // التحقق إذا كان وضع الصيانة مفعلًا
        if ($maintenanceMode && $maintenanceMode->value === 'on') {
            
            // السماح بالوصول إلى لوحة التحكم، صفحات تسجيل الدخول، وصفحة الصيانة نفسها
            if ($request->is('admin/*') || $request->is('login') || $request->is('register') || $request->routeIs('maintenance.page')) {
                return $next($request);
            }

            // تحويل أي شخص آخر إلى صفحة الصيانة
            return redirect()->route('maintenance.page');
        }

        return $next($request);
    }
}
