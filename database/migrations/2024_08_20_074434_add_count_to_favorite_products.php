<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('favorite_products', function (Blueprint $table) {
            $table->integer('count')
                ->unsigned()
                ->default(1)
                ->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorite_products', function (Blueprint $table) {
            $table->dropColumn('count');
        });
    }
};
