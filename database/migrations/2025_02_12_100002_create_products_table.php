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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->index()->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('category_id')->index()->constrained('categories')->cascadeOnDelete();
            $table->foreignUuid('product_group_id')->nullable()->index()->constrained('product_groups')->cascadeOnDelete();

            $table->string('name');
            $table->string('sku');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);

            $table->decimal('retail_price', 15, 2);
            $table->decimal('wholesale_price', 15, 2);
            $table->decimal('super_wholesale_price', 15, 2);

            $table->boolean('track_stock')->default(true);
            $table->integer('opening_stock')->default(0);
            $table->integer('low_stock_alert')->default(0);

            $table->string('video_url')->nullable();

            $table->string('slug');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('external_sync_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['sku', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
