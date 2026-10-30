<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\SweetShopSeeder;
use App\Models\ProductVariant;

class SweetShopApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SweetShopSeeder::class);
    }

    public function test_categories_api_returns_active_categories(): void
    {
        $response = $this->getJson('/api/categories');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_products_api_returns_sweets_with_variants(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'slug', 'name_en', 'name_te', 'name_hi', 'variants']
                ]
            ]);
    }

    public function test_coupon_validation_with_minimum_cart(): void
    {
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'SWEET50',
            'subtotal_paise' => 53000,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_paise', 5000);
    }

    public function test_order_placement_and_stock_decrement(): void
    {
        $variant = ProductVariant::first();
        $initialStock = $variant->stock_quantity;

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Bhargavi Sharma',
            'customer_phone' => '9849098765',
            'customer_email' => 'customer@anandamsweets.in',
            'fulfilment_type' => 'pickup',
            'pickup_slot' => 'Today: 4:00 PM – 5:00 PM',
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        // Verify stock decremented by 2
        $this->assertEquals($initialStock - 2, $variant->fresh()->stock_quantity);
    }
}
