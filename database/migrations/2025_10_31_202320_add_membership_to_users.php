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
        Schema::table('users', function (Blueprint $t) {
            $t->decimal('lifetime_spent', 12, 2)->default(0);
            $t->string('member_tier', 20)->default('bronze'); // bronze|silver|gold
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['lifetime_spent','member_tier']);
        });
    }

};
