<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มคอลัมน์ profile_photo เป็น string และอนุญาตให้เป็น null ได้
            $table->string('profile_photo', 2048)->nullable()->after('email'); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // เมธอดสำหรับย้อนกลับ (ลบคอลัมน์)
            $table->dropColumn('profile_photo');
        });
    }
};
