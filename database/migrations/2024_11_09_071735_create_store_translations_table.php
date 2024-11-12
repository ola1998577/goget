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
        Schema::create('store_translations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->unsignedBigInteger('store_id'); // المفتاح الأجنبي لربط المنتج
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->string('language', 2); // رمز اللغة (مثل en أو ar)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_translations');
    }
};
