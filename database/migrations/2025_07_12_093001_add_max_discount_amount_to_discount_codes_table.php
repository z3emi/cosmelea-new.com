<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            // هذا الحقل سيخزن الحد الأقصى لمبلغ الخصم للكوبونات النسبية
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->dropColumn('max_discount_amount');
        });
    }
};
