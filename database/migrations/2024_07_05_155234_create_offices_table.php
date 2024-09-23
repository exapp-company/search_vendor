<?php

use App\Models\Shop;
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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Shop::class);
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('feed')->nullable();
            $table->string('feed_mapping')->nullable();
            $table->boolean('use_parent_feed')->default(false);
            $table->boolean('use_parent_mapping')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
