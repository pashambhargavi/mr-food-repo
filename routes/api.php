<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StoreSettingController;

/*
|--------------------------------------------------------------------------
| ANANDAM SWEETS REST API ROUTES (FOR NEXT.JS STOREFRONT)
|--------------------------------------------------------------------------
*/

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

// Orders endpoints
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel']);

Route::get('/settings', [StoreSettingController::class, 'index']);

// B2B Inquiry endpoints
Route::post('/b2b-inquiry', function (\Illuminate\Http\Request $request) {
    // Sanitize phone number: strip non-digits, country code +91, leading 0
    $rawPhone = preg_replace('/\D/', '', (string) $request->input('phone', ''));
    if (strlen($rawPhone) === 12 && str_starts_with($rawPhone, '91')) {
        $rawPhone = substr($rawPhone, 2);
    } elseif (strlen($rawPhone) === 11 && str_starts_with($rawPhone, '0')) {
        $rawPhone = substr($rawPhone, 1);
    }
    $request->merge(['phone' => $rawPhone]);

    $validated = $request->validate([
        'company_name' => 'required|string|max:150',
        'contact_person' => 'required|string|max:150',
        'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
        'email' => 'required|email|max:150',
        'occasion_type' => 'nullable|string',
        'estimated_quantity' => 'nullable|string',
        'delivery_date' => 'nullable|date',
        'delivery_location' => 'nullable|string',
        'special_requirements' => 'nullable|string',
    ]);

    $inquiry = \App\Models\B2bInquiry::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Thank you! Your bulk order inquiry has been received by our kitchen manager.',
        'data' => $inquiry,
    ], 201);
});

Route::get('/b2b-inquiries', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\B2bInquiry::latest()->get(),
    ]);
});
