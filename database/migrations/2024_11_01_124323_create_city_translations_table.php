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
        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade'); // مفتاح خارجي يشير إلى جدول settings
            $table->string('locale'); // رمز اللغة (مثل 'en' أو 'ar')
            $table->string('name');
            $table->timestamps();
        });

        DB::table('city_translations')->insert([
            ['city_id' => 1, 'locale' => 'en', 'name' => 'Tartous'],
            ['city_id' => 1, 'locale' => 'ar', 'name' => 'طرطوس']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_translations');
    }
};
