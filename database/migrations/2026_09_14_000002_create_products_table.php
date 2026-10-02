<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('reference');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['shop_id', 'reference']);
            $table->index(['shop_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
