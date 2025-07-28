<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        // جلب كل الإعدادات دفعة واحدة
        $settings = Setting::pluck('value', 'key');

        // المزامنة مع حالة التطبيق الفعلية
        $settings['maintenance_mode'] = app()->isDownForMaintenance() ? 'on' : 'off';

        return view('admin.settings.index', compact('settings'));
    }

    public function logoutAllUsers()
    {
        \DB::table('sessions')->whereNotNull('user_id')->delete();
        return back()->with('success', 'تم تسجيل خروج جميع المستخدمين.');
    }

    public function update(Request $request)
    {
        // قائمة بكل الإعدادات التي نريد حفظها
        $settingsKeys = [
            'show_dashboard_notification',
            'dashboard_notification_content',
            'show_welcome_screen',
            'welcome_screen_content',
            'maintenance_mode',
            'session_lifetime'
        ];

        foreach ($settingsKeys as $key) {
            $value = null;
            if (str_starts_with($key, 'show_') || $key === 'maintenance_mode') {
                // إذا كان الحقل عبارة عن checkbox
                $value = $request->has($key) ? 'on' : 'off';
            } else {
                // إذا كان الحقل نصيًا
                $value = $request->input($key);
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // التعامل مع وضع الصيانة بشكل منفصل
        if ($request->has('maintenance_mode')) {
            Artisan::call('down');
        } else {
            Artisan::call('up');
        }

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح.');
    }
}
