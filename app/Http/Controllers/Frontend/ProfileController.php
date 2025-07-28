<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Address;
use App\Models\Order; // **هذا هو السطر الذي يحل المشكلة**

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي للمستخدم.
     */
    public function show()
    {
        return view('frontend.profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * تحديث بيانات الملف الشخصي.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];

        if ($request->filled('new_password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['new_password'] = ['required', Password::defaults(), 'confirmed'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('profile.show')
                        ->withErrors($validator)
                        ->withInput($request->input());
        }

        $validatedData = $validator->validated();
        $user->name = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $user->email = $validatedData['email'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if (isset($validatedData['new_password'])) {
            $user->password = Hash::make($validatedData['new_password']);
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }

    /**
     * عرض صفحة طلبات المستخدم.
     */
    public function orders()
    {
        // ===== START: التعديل المطلوب =====
        // تم تحديث الاستعلام ليقوم بتحميل المنتجات والصورة الأولى لكل منتج
        $orders = Auth::user()->orders()
                        ->with('items.product.firstImage')
                        ->latest()
                        ->paginate(5); // يمكنك تغيير عدد الطلبات في كل صفحة
        // ===== END: التعديل المطلوب =====

        return view('frontend.profile.orders', compact('orders'));
    }

    /**
     * عرض تفاصيل طلب محدد.
     */
    public function showOrderDetails(Order $order)
    {
        // التأكد من أن الطلب يخص المستخدم الحالي (حماية)
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // ===== START: التعديل المطلوب =====
        // تحميل كل البيانات المطلوبة بكفاءة (Eager Loading)
        $order->load('items.product.firstImage', 'discountCode');
        // ===== END: التعديل المطلوب =====

        return view('frontend.profile.order-details', compact('order'));
    }
    
    public function addresses()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('frontend.profile.addresses.index', compact('addresses'));
    }

    public function createAddress()
    {
        if (Auth::user()->addresses()->count() >= 5) {
            return redirect()->route('profile.addresses.index')->with('error', 'لا يمكنك إضافة أكثر من 5 عناوين.');
        }

        return view('frontend.profile.addresses.create');
    }

    public function storeAddress(Request $request)
    {
        if (Auth::user()->addresses()->count() >= 5) {
            return redirect()->back()->with('error', 'لا يمكنك إضافة أكثر من 5 عناوين.');
        }

        $validatedData = $request->validate([
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address_details' => 'required|string|max:255',
            'nearest_landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Auth::user()->addresses()->create($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'تم إضافة العنوان بنجاح.');
    }

    public function destroyAddress(Address $address)
    {
        // التأكد من أن العنوان يخص المستخدم الحالي (حماية)
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('profile.addresses.index')->with('success', 'تم حذف العنوان بنجاح.');
    }
    public function storeAddressAjax(Request $request)
    {
        if (Auth::user()->addresses()->count() >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إضافة أكثر من 5 عناوين.'
            ], 422);
        }

        $validatedData = $request->validate([
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address_details' => 'required|string|max:255',
            'nearest_landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address = Auth::user()->addresses()->create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ العنوان بنجاح!',
            'address' => $address
        ]);
    }
}

