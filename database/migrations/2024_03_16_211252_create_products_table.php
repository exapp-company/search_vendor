<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('picture')->nullable();

            $table->foreignId('shop_id')
                ->nullable()
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');

            $table->timestamps();

            $table->index('name');
            $table->index('brand');
            $table->index('price');
            $table->index('url');
            $table->index('shop_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
