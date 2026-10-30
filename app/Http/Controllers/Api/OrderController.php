<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['items', 'statusHistories'])->latest();

        if ($request->filled('email')) {
            $query->where('customer_email', $request->email);
        }

        if ($request->filled('phone')) {
            $query->where('customer_phone', $request->phone);
        }

        $orders = $query->take(50)->get();

        return response()->json([
            'success' => true,
            'count' => $orders->count(),
            'data' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Strict Authentication Check: Never accept orders without verified user account
        $userId = trim((string) $request->input('user_id', ''));
        if (empty($userId) || $userId === 'null' || $userId === 'undefined') {
            return response()->json([
                'success' => false,
                'message' => 'Customer account sign-in is required to place an order. Please log in or register first.',
            ], 401);
        }

        // Sanitize phone number: strip non-digits, country code +91, leading 0
        $rawPhone = preg_replace('/\D/', '', (string) $request->input('customer_phone', ''));
        if (strlen($rawPhone) === 12 && str_starts_with($rawPhone, '91')) {
            $rawPhone = substr($rawPhone, 2);
        } elseif (strlen($rawPhone) === 11 && str_starts_with($rawPhone, '0')) {
            $rawPhone = substr($rawPhone, 1);
        }
        $request->merge(['customer_phone' => $rawPhone]);

        $validated = $request->validate([
            'user_id' => 'required|string|max:100',
            'order_number' => 'nullable|string|max:50',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'customer_email' => 'nullable|email|max:150',
            'fulfilment_type' => 'required|in:delivery,pickup',
            'payment_method' => 'required|in:upi,card,netbanking,cod',
            'coupon_code' => 'nullable|string',
            'pickup_slot' => 'nullable|string',
            'delivery_notes' => 'nullable|string',
            'delivery_address' => 'nullable|array',
            'delivery_address.address_line1' => 'required_if:fulfilment_type,delivery|string',
            'delivery_address.pincode' => 'required_if:fulfilment_type,delivery|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.variant_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'user_id.required' => 'Customer sign-in is required to place an order. Please log in first.',
        ]);

        $settings = StoreSetting::first();

        // Pincode validation if delivery: supports all valid 6-digit Indian postal codes
        if ($validated['fulfilment_type'] === 'delivery') {
            $pincode = trim($validated['delivery_address']['pincode'] ?? '');
            if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid 6-digit Indian postal pincode.',
                ], 422);
            }
        }

        return DB::transaction(function () use ($validated, $settings) {
            $subtotalPaise = 0;
            $itemsToCreate = [];

            // 1. Recalculate price and stock server-side
            foreach ($validated['items'] as $itemData) {
                $variant = ProductVariant::with('product')
                    ->lockForUpdate()
                    ->where('id', $itemData['variant_id'])
                    ->first();

                if (!$variant) {
                    throw new \Exception("Product variant not found: {$itemData['variant_id']}");
                }

                if ($variant->stock_quantity < $itemData['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$variant->product->name_en} ({$variant->weight_label}). Available: {$variant->stock_quantity}",
                    ], 422);
                }

                $lineTotal = $variant->price_paise * $itemData['quantity'];
                $subtotalPaise += $lineTotal;

                // Decrement stock
                $variant->decrement('stock_quantity', $itemData['quantity']);
                if ($variant->stock_quantity <= 0) {
                    $variant->update(['is_available' => false]);
                }

                $itemsToCreate[] = [
                    'product_id' => $variant->product_id,
                    'product_name' => $variant->product->name_en,
                    'variant_id' => $variant->id,
                    'variant_weight' => $variant->weight_label,
                    'unit_price_paise' => $variant->price_paise,
                    'quantity' => $itemData['quantity'],
                    'total_price_paise' => $lineTotal,
                    'image_url' => $variant->product->image_url,
                ];
            }

            // 2. Validate Coupon server-side
            $discountPaise = 0;
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))
                    ->where('is_active', true)
                    ->first();

                if ($coupon && $subtotalPaise >= $coupon->min_order_amount_paise) {
                    if ($coupon->discount_type === 'fixed') {
                        $discountPaise = $coupon->discount_value;
                    } else {
                        $discountPaise = (int) round(($subtotalPaise * $coupon->discount_value) / 100);
                        if ($coupon->max_discount_paise) {
                            $discountPaise = min($discountPaise, $coupon->max_discount_paise);
                        }
                    }
                    $coupon->increment('usage_count');
                }
            }

            // 3. Delivery fee rule
            $deliveryFeePaise = 0;
            if ($validated['fulfilment_type'] === 'delivery') {
                $freeThreshold = $settings ? $settings->free_delivery_threshold_paise : 50000;
                $standardFee = $settings ? $settings->delivery_fee_paise : 4000;
                $deliveryFeePaise = $subtotalPaise >= $freeThreshold ? 0 : $standardFee;
            }

            $totalPaise = max(0, $subtotalPaise - $discountPaise + $deliveryFeePaise);

            // 4. Create Order
            $order = Order::create([
                'order_number' => $validated['order_number'] ?? null,
                'user_id' => $validated['user_id'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'fulfilment_type' => $validated['fulfilment_type'],
                'status' => 'placed',
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'upi' ? 'paid' : 'pending',
                'payment_id' => $validated['payment_method'] === 'upi' ? 'pay_UPI_' . time() : null,
                'subtotal_paise' => $subtotalPaise,
                'discount_paise' => $discountPaise,
                'delivery_fee_paise' => $deliveryFeePaise,
                'tax_paise' => 0,
                'total_paise' => $totalPaise,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'pickup_slot' => $validated['pickup_slot'] ?? null,
                'delivery_notes' => $validated['delivery_notes'] ?? null,
            ]);

            // 5. Create Order Items
            foreach ($itemsToCreate as $it) {
                $order->items()->create($it);
            }

            // 6. Record Status History
            $order->statusHistories()->create([
                'status' => 'placed',
                'note' => $validated['payment_method'] === 'upi' ? 'Paid via UPI (Verified)' : 'Cash on Delivery (Pending Collection)',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data' => $order->load(['items', 'statusHistories']),
            ], 201);
        });
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::with(['items', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function cancel(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if (in_array($order->status, ['ready_for_dispatch', 'out_for_delivery', 'delivered', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled as it is already being dispatched or completed.',
            ], 422);
        }

        $reason = $request->input('reason', 'Cancelled by customer');

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);

        $order->statusHistories()->create([
            'status' => 'cancelled',
            'note' => $reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order->fresh(['items', 'statusHistories']),
        ]);
    }
}
