<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('slug', 100)->unique();
            $table->string('name_en');
            $table->string('name_te');
            $table->string('name_hi');
            $table->text('description_en')->nullable();
            $table->string('icon', 50)->nullable();
            $table->text('image_url')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Products
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('slug', 150)->unique();
            $table->string('category_id', 50)->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            
            // Multilingual titles & descriptions
            $table->string('name_en');
            $table->string('name_te');
            $table->string('name_hi');
            $table->text('description_en');
            $table->text('description_te');
            $table->text('description_hi');
            
            $table->text('image_url');
            $table->string('dietary_type', 50)->default('pure_ghee'); // pure_ghee, dry_fruit, milk_sweet, etc.
            $table->boolean('is_veg')->default(true);
            $table->integer('shelf_life_days')->default(15);
            
            // Storage instructions
            $table->text('storage_en')->nullable();
            $table->text('storage_te')->nullable();
            $table->text('storage_hi')->nullable();
            
            // Ingredients
            $table->text('ingredients_en')->nullable();
            $table->text('ingredients_te')->nullable();
            $table->text('ingredients_hi')->nullable();
            
            $table->json('allergens')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->decimal('rating', 3, 2)->default(4.9);
            $table->integer('review_count')->default(0);
            $table->timestamps();
        });

        // 3. Product Variants (Weight options & Prices in Paise)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('product_id', 50);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('weight_label', 50); // e.g. '250g', '500g', '1kg'
            $table->integer('weight_grams');
            $table->bigInteger('price_paise'); // Stored in paise
            $table->bigInteger('compare_at_price_paise')->nullable();
            $table->integer('stock_quantity')->default(50);
            $table->boolean('is_available')->default(true);
            $table->string('sku', 50)->unique();
            $table->timestamps();
        });

        // 4. Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('code', 50)->unique();
            $table->string('discount_type', 20)->default('percentage'); // percentage or fixed
            $table->integer('discount_value'); // e.g. 10 for 10% or 5000 for ₹50
            $table->bigInteger('min_order_amount_paise')->default(0);
            $table->bigInteger('max_discount_paise')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        // 5. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 50)->unique(); // MITHAI-2026-XXXX
            $table->string('user_id', 50)->nullable();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->string('customer_email')->nullable();
            $table->string('fulfilment_type', 20)->default('delivery'); // delivery or pickup
            $table->string('status', 30)->default('placed'); // placed, confirmed, preparing, ready_for_dispatch, ready_for_pickup, out_for_delivery, delivered, cancelled
            $table->string('payment_method', 20)->default('upi'); // upi or cod
            $table->string('payment_status', 20)->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_id')->nullable();
            $table->bigInteger('subtotal_paise');
            $table->bigInteger('discount_paise')->default(0);
            $table->bigInteger('delivery_fee_paise')->default(0);
            $table->bigInteger('tax_paise')->default(0);
            $table->bigInteger('total_paise');
            $table->string('coupon_code', 50)->nullable();
            $table->json('delivery_address')->nullable();
            $table->string('pickup_slot')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        // 6. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('product_id', 50);
            $table->string('product_name');
            $table->string('variant_id', 50);
            $table->string('variant_weight', 50);
            $table->bigInteger('unit_price_paise');
            $table->integer('quantity');
            $table->bigInteger('total_price_paise');
            $table->text('image_url')->nullable();
            $table->timestamps();
        });

        // 7. Order Status Audit Log
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 8. Store Settings
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name');
            $table->string('shop_name_te')->nullable();
            $table->string('shop_name_hi')->nullable();
            $table->string('tagline')->nullable();
            $table->string('tagline_te')->nullable();
            $table->string('tagline_hi')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('whatsapp', 25)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('google_maps_url')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('opening_hours_te')->nullable();
            $table->string('opening_hours_hi')->nullable();
            $table->bigInteger('min_order_paise')->default(20000);
            $table->bigInteger('delivery_fee_paise')->default(4000);
            $table->bigInteger('free_delivery_threshold_paise')->default(50000);
            $table->json('delivery_pincodes')->nullable();
            $table->boolean('cod_enabled')->default(true);
            $table->boolean('upi_enabled')->default(true);
            $table->string('upi_id')->default('anandamsweets@upi');
            $table->string('gstin')->nullable();
            $table->string('fssai_license')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
