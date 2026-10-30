<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class StoreSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = StoreSetting::first();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
