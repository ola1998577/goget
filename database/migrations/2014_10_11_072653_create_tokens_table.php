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
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->boolean('isskip')->default('0');//0->off,1->on
            $table->boolean('islogin')->default('0');//0->off,1->on
            $table->string('key')->nullable();
            $table->timestamp('key_updated_at')->nullable();
            $table->boolean('quiz_today')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
