<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Apply permission middleware to protect controller actions.
     */
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;

        $this->middleware($permissionMiddleware . ':view-customers', ['only' => ['index', 'show']]);
        $this->middleware($permissionMiddleware . ':create-customers', ['only' => ['create', 'store']]);
        $this->middleware($permissionMiddleware . ':edit-customers', ['only' => ['edit', 'update']]);
        $this->middleware($permissionMiddleware . ':delete-customers', ['only' => ['destroy']]);
        $this->middleware($permissionMiddleware . ':ban-customers', ['only' => ['ban', 'unban']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $query = Customer::with('user')
            ->withCount(['orders' => function ($query) {
                $query->where('status', 'delivered');
            }])
            ->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->whereHas('user', function ($q) {
                    $q->whereNotNull('banned_at');
                });
            } elseif ($request->status === 'active') {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($sub) {
                        $sub->whereNull('banned_at');
                    })->orWhereDoesntHave('user');
                });
            }
        }

        $customers = $query->paginate($perPage)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:customers,phone_number',
            'email' => 'nullable|string|email|max:255|unique:customers,email',
            'governorate' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address_details' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'تم إنشاء العميل بنجاح.');
    }

    /**
     * Display the specified resource, their orders, and their addresses.
     */
    public function show(Request $request, Customer $customer)
    {
        $customer->load('user.addresses');
        $query = $customer->orders()->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('status', 'like', "%{$searchTerm}%");
            });
        }

        // ===== START: التعديل المطلوب =====
        // إضافة إمكانية التحكم في عدد الطلبات المعروضة
        $perPage = $request->input('per_page', 5);
        $orders = $query->paginate($perPage)->withQueryString();

        $totalOrders = $customer->orders()->count();
        $orderCounts = $customer->orders()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
        $deliveredAmount = $customer->orders()
            ->where('status', 'delivered')
            ->sum('total_amount');
        // ===== END: التعديل المطلوب =====

        return view('admin.customers.show', compact('customer', 'orders', 'totalOrders', 'orderCounts', 'deliveredAmount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:customers,phone_number,' . $customer->id,
            'email' => 'nullable|string|email|max:255|unique:customers,email,' . $customer->id,
            'governorate' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address_details' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'تم تحديث بيانات العميل بنجاح.');
    }

    /**
     * Ban the specified customer.
     */
    public function ban(Customer $customer)
    {
        if ($customer->user) {
            $customer->user->update(['banned_at' => Carbon::now()]);
        } else {
            $newUser = User::create([
                'name' => $customer->name,
                'phone_number' => $customer->phone_number,
                'email' => $customer->email,
                'password' => Hash::make(uniqid()),
                'banned_at' => Carbon::now(),
            ]);
            
            $newUser->assignRole('user');
            $customer->update(['user_id' => $newUser->id]);
        }
        return redirect()->route('admin.customers.index')->with('success', 'تم حظر العميل بنجاح.');
    }

    /**
     * Unban the specified customer.
     */
    public function unban(Customer $customer)
    {
        if ($customer->user) {
            $customer->user->update(['banned_at' => null]);
        }
        return redirect()->route('admin.customers.index')->with('success', 'تم إلغاء حظر العميل بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'تم حذف العميل بنجاح.');
    }
}
