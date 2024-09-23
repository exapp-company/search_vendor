<?php

use App\Models\Shop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->foreignIdFor(Shop::class)->nullable();
            $table->unsignedInteger('offers_count')->default(0);
            $table->unsignedInteger('processed_offers_count')->default(0);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('feeds');
    }
};
