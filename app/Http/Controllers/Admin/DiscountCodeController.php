<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    /**
     * Apply permission middleware to protect controller actions.
     */
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;

        // Please ensure these permissions exist in your seeder.
        $this->middleware($permissionMiddleware . ':view-discount-codes', ['only' => ['index']]);
        $this->middleware($permissionMiddleware . ':create-discount-codes', ['only' => ['create', 'store']]);
        $this->middleware($permissionMiddleware . ':edit-discount-codes', ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware($permissionMiddleware . ':delete-discount-codes', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $perPage = $request->input('per_page', 15); // القيمة الافتراضية 15
    $discountCodes = DiscountCode::withCount('usages')->latest()->paginate($perPage)->withQueryString();

    return view('admin.discount_codes.index', compact('discountCodes'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.discount_codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:discount_codes,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0|required_if:type,percentage',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        DiscountCode::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'max_discount_amount' => $request->type == 'percentage' ? $request->max_discount_amount : null,
            'max_uses' => $request->max_uses,
            'max_uses_per_user' => $request->max_uses_per_user,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->route('admin.discount_codes.index')->with('success', 'تم إنشاء كود الخصم بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiscountCode $discount_code)
    {
        return view('admin.discount_codes.edit', compact('discount_code'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscountCode $discount_code)
    {
        $request->validate([
            'code' => 'required|string|unique:discount_codes,code,' . $discount_code->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0|required_if:type,percentage',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $discount_code->update([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'max_discount_amount' => $request->type == 'percentage' ? $request->max_discount_amount : null,
            'max_uses' => $request->max_uses,
            'max_uses_per_user' => $request->max_uses_per_user,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.discount_codes.index')->with('success', 'تم تحديث كود الخصم بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountCode $discount_code)
    {
        $discount_code->delete();
        return redirect()->route('admin.discount_codes.index')->with('success', 'تم حذف كود الخصم نهائيًا.');
    }

    /**
     * Toggle the active status of the specified discount code.
     */
    public function toggleStatus(DiscountCode $discount_code)
    {
        $discount_code->is_active = !$discount_code->is_active;
        $discount_code->save();
        return redirect()->route('admin.discount_codes.index')->with('success', 'تم تحديث حالة الكود.');
    }
}
