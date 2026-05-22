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
        Schema::create('blog_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('blog_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('customer_asset_id')->constrained('customer_assets')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['blog_id', 'customer_asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_assets');
    }
};
