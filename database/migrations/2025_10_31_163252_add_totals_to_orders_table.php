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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders','status')) {
                $table->string('status')->default('unpaid')->after('user_id');
            }
            if (!Schema::hasColumn('orders','total_qty')) {
                $table->unsignedInteger('total_qty')->default(0)->after('status');
            }
            if (!Schema::hasColumn('orders','total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('total_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders','total_amount')) $table->dropColumn('total_amount');
            if (Schema::hasColumn('orders','total_qty'))    $table->dropColumn('total_qty');
            if (Schema::hasColumn('orders','status'))       $table->dropColumn('status');
        });
    }

};
