<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('shop_user', function (Blueprint $table) {
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('manager');
            $table->timestamps();
            $table->primary(['shop_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_user');
        Schema::dropIfExists('shops');
    }
};
