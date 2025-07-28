<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * ===== START: تم إضافة هذا الجزء بالكامل لحماية الكنترولر =====
     * تطبيق صلاحيات Middleware لحماية كل الدوال.
     */
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;

        $this->middleware($permissionMiddleware . ':view-users', ['only' => ['index', 'showUserOrders', 'inactive']]);
        $this->middleware($permissionMiddleware . ':create-users', ['only' => ['create', 'store']]);
        $this->middleware($permissionMiddleware . ':edit-users', ['only' => ['edit', 'update']]);
        $this->middleware($permissionMiddleware . ':delete-users', ['only' => ['destroy']]);
        $this->middleware($permissionMiddleware . ':ban-users', ['only' => ['ban', 'unban']]);
        $this->middleware($permissionMiddleware . ':logout-users', ['only' => ['forceLogout', 'forceLogoutAll']]);
        $this->middleware($permissionMiddleware . ':impersonate-users', ['only' => ['impersonate', 'stopImpersonate']]);
    }
    // ===== END: تم إضافة هذا الجزء بالكامل =====

    /**
     * عرض جميع المستخدمين.
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders')->with('roles', 'permissions')->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->whereNotNull('banned_at');
            } elseif ($request->status === 'active') {
                $query->whereNull('banned_at')->whereNotNull('phone_verified_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNull('phone_verified_at');
            }
        }

        $users = $query->paginate(15)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->input('roles'));

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $orders = $user->orders()->latest()->take(5)->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'orders'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:20|unique:users,phone_number,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'permissions' => 'nullable|array'
        ]);

        $data = $request->only('name', 'email', 'phone_number');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }
    
    public function showUserOrders(User $user)
    {
        $orders = $user->orders()->latest()->paginate(15);
        return view('admin.users.orders', compact('user', 'orders'));
    }

    public function ban(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حظر حسابك الخاص.');
        }
        $user->update(['banned_at' => Carbon::now()]);
        return redirect()->route('admin.users.index')->with('success', 'تم حظر المستخدم بنجاح.');
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);
        return redirect()->route('admin.users.index')->with('success', 'تم إلغاء حظر المستخدم بنجاح.');
    }
    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الخاص.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }
    
    public function inactive()
    {
        $inactiveUsers = User::whereNull('phone_verified_at')
                               ->whereNotNull('whatsapp_otp')
                               ->latest()
                               ->paginate(20);

        return view('admin.users.inactive', compact('inactiveUsers'));
    }

    public function forceLogout(User $user)
    {
        \DB::table('sessions')->where('user_id', $user->id)->delete();
        return back()->with('success', 'تم تسجيل خروج المستخدم بنجاح.');
    }

    public function forceLogoutAll()
    {
        \DB::table('sessions')->whereNotNull('user_id')->delete();
        return back()->with('success', 'تم تسجيل خروج جميع المستخدمين.');
    }

    public function impersonate(User $user)
    {
        session(['impersonator_id' => auth()->id()]);
        auth()->login($user);
        return redirect('/')->with('success', 'تم تسجيل الدخول كمستخدم آخر.');
    }

    public function stopImpersonate()
    {
        $id = session('impersonator_id');
        if ($id) {
            auth()->loginUsingId($id);
            session()->forget('impersonator_id');
        }
        return redirect()->route('admin.users.index')->with('success', 'تم إيقاف وضع الانتحال.');
    }
}
