<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function validateCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal_paise' => 'required|integer|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $subtotal = $request->subtotal_paise;

        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive coupon code.',
            ], 422);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired.',
            ], 422);
        }

        if ($subtotal < $coupon->min_order_amount_paise) {
            $minRupees = $coupon->min_order_amount_paise / 100;
            return response()->json([
                'success' => false,
                'message' => "This coupon requires a minimum cart subtotal of ₹{$minRupees}.",
            ], 422);
        }

        // Calculate discount
        $discountPaise = 0;
        if ($coupon->discount_type === 'fixed') {
            $discountPaise = $coupon->discount_value;
        } else {
            $discountPaise = (int) round(($subtotal * $coupon->discount_value) / 100);
            if ($coupon->max_discount_paise) {
                $discountPaise = min($discountPaise, $coupon->max_discount_paise);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Coupon {$coupon->code} applied successfully!",
            'discount_paise' => $discountPaise,
            'coupon' => $coupon,
        ]);
    }
}
