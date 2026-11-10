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
        Schema::create('domain_tlds', function (Blueprint $table) {
            $table->id();
            $table->string('tld', 60)->unique();
            $table->decimal('price_1yr', 12, 2)->nullable();
            $table->decimal('price_2yr', 12, 2)->nullable();
            $table->decimal('price_3yr', 12, 2)->nullable();
            $table->decimal('price_5yr', 12, 2)->nullable();
            $table->decimal('price_10yr', 12, 2)->nullable();
            $table->decimal('transfer_price', 12, 2)->nullable();
            // Renewal price per term, e.g. {"1": 1600, "2": 3200, ...}.
            $table->json('renew_prices')->nullable();
            $table->string('badge', 30)->nullable();
            // Position in each curated list (null = not listed).
            $table->unsignedSmallInteger('popular_order')->nullable()->index();
            $table->unsignedSmallInteger('featured_order')->nullable()->index();
            $table->unsignedSmallInteger('renew_order')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_tlds');
    }
};
