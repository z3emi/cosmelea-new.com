<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoiceItem; // **THE FIX**: Corrected the model name here
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Apply permission middleware to protect controller actions.
     */
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;

        // Protect the inventory page with the 'view-inventory' permission.
        $this->middleware($permissionMiddleware . ':view-inventory', ['only' => ['index']]);
    }

    /**
     * Display the detailed inventory page.
     */
    public function index()
    {
        // **THE FIX**: Using the correct model name here as well
        $stockItems = PurchaseInvoiceItem::where('quantity_remaining', '>', 0)
                                    ->with(['product', 'purchaseInvoice.supplier'])
                                    ->get();

        // --- Summary Calculations ---
        $grandTotalValue = $stockItems->sum(function ($item) {
            return $item->quantity_remaining * $item->purchase_price;
        });

        $grandTotalQuantity = $stockItems->sum('quantity_remaining');

        // Group items by product for organized display and to count unique products
        $stockItemsGrouped = $stockItems->groupBy('product_id');
        $uniqueProductsCount = $stockItemsGrouped->count();
        
        return view('admin.inventory.index', [
            'stockItems' => $stockItemsGrouped,
            'grandTotalValue' => $grandTotalValue,
            'grandTotalQuantity' => $grandTotalQuantity,
            'uniqueProductsCount' => $uniqueProductsCount,
        ]);
    }
}
