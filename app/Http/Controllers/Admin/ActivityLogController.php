<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * حماية المتحكم بصلاحية خاصة.
     */
    public function __construct()
    {
        $this->middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-activity-log', ['only' => ['index']]);
    }

    /**
     * عرض صفحة سجل الأنشطة.
     */
    public function index()
    {
        $logs = ActivityLog::with('user', 'loggable')->latest()->paginate(25);
        return view('admin.logs.index', compact('logs'));
    }
}
