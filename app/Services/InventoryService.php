<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseInvoiceItem;
use Exception;

class InventoryService
{
    /**
     * Deducts stock for a given product based on FIFO (First-In, First-Out).
     * Calculates the total cost of the deducted items.
     *
     * @param Product $product
     * @param int $quantityToDeduct
     * @return float Total cost of the deducted items.
     * @throws \Exception
     */
    public function deductStock(Product $product, int $quantityToDeduct): float
    {
        // **THE FIX**: Using the correct relationship name 'purchaseItems'
        $totalStock = $product->purchaseItems()->sum('quantity_remaining');
        if ($totalStock < $quantityToDeduct) {
            throw new Exception("الكمية المطلوبة للمنتج '{$product->name_ar}' غير متوفرة في المخزون. المتاح: {$totalStock}");
        }

        // **THE FIX**: Using the correct relationship name 'purchaseItems'
        $batches = $product->purchaseItems()
                           ->where('quantity_remaining', '>', 0)
                           ->orderBy('created_at', 'asc')
                           ->get();

        $totalCost = 0;
        $quantityLeftToDeduct = $quantityToDeduct;

        foreach ($batches as $batch) {
            if ($quantityLeftToDeduct <= 0) {
                break;
            }

            $deductFromThisBatch = min($batch->quantity_remaining, $quantityLeftToDeduct);

            $batch->quantity_remaining -= $deductFromThisBatch;
            $batch->save();

            $totalCost += $deductFromThisBatch * $batch->purchase_price;
            $quantityLeftToDeduct -= $deductFromThisBatch;
        }

        return $totalCost;
    }

    /**
     * Restores stock for a given product.
     * This is used when an order is cancelled or edited.
     *
     * @param Product $product
     * @param int $quantityToRestore
     * @return void
     */
    public function restoreStock(Product $product, int $quantityToRestore): void
    {
        if ($quantityToRestore <= 0) {
            return;
        }

        // **THE FIX**: Using the correct relationship name 'purchaseItems'
        $latestBatch = $product->purchaseItems()->latest()->first();

        if ($latestBatch) {
            $latestBatch->quantity_remaining += $quantityToRestore;
            $latestBatch->save();
        } else {
            // This case is unlikely but could happen if all purchase records for a product were deleted.
        }
    }
}
