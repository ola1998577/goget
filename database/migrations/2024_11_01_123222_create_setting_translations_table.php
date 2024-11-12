<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('settings')->onDelete('cascade'); // مفتاح خارجي يشير إلى جدول settings
            $table->string('locale'); // رمز اللغة (مثل 'en' أو 'ar')
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps(); // لتسجيل تاريخ الإنشاء والتحديث
        });

        DB::table('setting_translations')->insert([
            ['setting_id' => 1, 'locale' => 'en', 'title' => 'About Us', 'description' => 'Information about the application.'],
            ['setting_id' => 1, 'locale' => 'ar', 'title' => 'About Us', 'description' => 'معلومات حول التطبيق.'],
            ['setting_id' => 1, 'locale' => 'en', 'title' => 'Privacy & Policy', 'description' => 'Information about the application.'],
            ['setting_id' => 1, 'locale' => 'ar', 'title' => 'Privacy & Policy', 'description' => 'معلومات حول التطبيق.'],
            ['setting_id' => 1, 'locale' => 'en', 'title' => 'Terms & Conditions', 'description' => 'Information about the application.'],
            ['setting_id' => 1, 'locale' => 'ar', 'title' => 'Terms & Conditions', 'description' => 'معلومات حول التطبيق.'],
            ['setting_id' => 1, 'locale' => 'en', 'title' => 'Background', 'description' => ''],
            ['setting_id' => 1, 'locale' => 'ar', 'title' => 'Background', 'description' => ''],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_translations');
    }
};
