<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Auth Routes (Guest)
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Panel Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Orders Management
    Route::get('/orders', [DashboardController::class, 'orders'])->name('admin.orders');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('admin.customers');
    Route::post('/orders/{id}/status', [DashboardController::class, 'updateOrderStatus'])->name('admin.orders.status');
    Route::post('/orders/{id}/cod', [DashboardController::class, 'markCodPaid'])->name('admin.orders.cod');
    Route::get('/orders/{orderNumber}/invoice', [DashboardController::class, 'invoice'])->name('admin.invoice');
    
    // Product Catalogue Management (CRUD + File Uploads)
    Route::get('/products', [ProductAdminController::class, 'index'])->name('admin.products');
    Route::get('/products/all', [ProductAdminController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductAdminController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductAdminController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductAdminController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductAdminController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/{id}/toggle', [ProductAdminController::class, 'togglePublish'])->name('admin.products.toggle');
    Route::post('/products/variants/{variantId}/stock', [DashboardController::class, 'updateStock'])->name('admin.products.stock');
    
    // B2B Bulk Inquiries Management
    Route::get('/b2b-inquiries', [DashboardController::class, 'b2bInquiries'])->name('admin.b2b');
    Route::post('/b2b-inquiries/{id}/status', [DashboardController::class, 'updateB2bStatus'])->name('admin.b2b.status');

    // Categories Management
    Route::get('/categories', [CategoryAdminController::class, 'index'])->name('admin.categories');
    Route::post('/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{id}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');
    Route::post('/categories/{id}/toggle', [CategoryAdminController::class, 'toggle'])->name('admin.categories.toggle');

    // Store Settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
});
