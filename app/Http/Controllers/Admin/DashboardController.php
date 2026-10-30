<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\StoreSetting;
use App\Models\B2bInquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayStr = now()->toDateString();

        $todayOrders = Order::whereDate('created_at', $todayStr)->count();
        $todayRevenue = Order::whereDate('created_at', $todayStr)
            ->where('payment_status', 'paid')
            ->sum('total_paise');

        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_paise');

        $pendingOrders = Order::whereNotIn('status', ['delivered', 'cancelled'])->count();
        $awaitingPickup = Order::where('fulfilment_type', 'pickup')
            ->where('status', 'ready_for_pickup')
            ->count();
        $awaitingDispatch = Order::where('fulfilment_type', 'delivery')
            ->where('status', 'ready_for_dispatch')
            ->count();

        $lowStockVariants = ProductVariant::with('product')
            ->where('stock_quantity', '<=', 15)
            ->get();

        $recentOrders = Order::with('items')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders',
            'todayRevenue',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'awaitingPickup',
            'awaitingDispatch',
            'lowStockVariants',
            'recentOrders'
        ));
    }

    public function orders(Request $request): View
    {
        $query = Order::with(['items', 'statusHistories'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = '%' . strtolower(trim($request->q)) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(order_number) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(customer_name) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(customer_email) LIKE ?', [$term])
                  ->orWhere('customer_phone', 'LIKE', $term)
                  ->orWhere('user_id', 'LIKE', $term)
                  ->orWhereRaw('LOWER(delivery_address) LIKE ?', [$term]);
            });
        }

        $orders = $query->paginate(15);

        return view('admin.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, string $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $status = $request->input('status');

        $order->update(['status' => $status]);
        $order->statusHistories()->create([
            'status' => $status,
            'note' => $request->input('note', "Status updated to {$status} via Admin Dashboard"),
        ]);

        return back()->with('success', "Order {$order->order_number} status updated to {$status}!");
    }

    public function markCodPaid(string $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $order->update(['payment_status' => 'paid']);

        return back()->with('success', "COD payment marked as collected for order {$order->order_number}!");
    }

    public function products(): View
    {
        $products = Product::with(['variants', 'category'])->get();
        return view('admin.products', compact('products'));
    }

    public function updateStock(Request $request, string $variantId): RedirectResponse
    {
        $variant = ProductVariant::findOrFail($variantId);
        $stock = (int) $request->input('stock_quantity', 0);

        $variant->update([
            'stock_quantity' => $stock,
            'is_available' => $stock > 0,
        ]);

        return back()->with('success', "Stock updated for {$variant->weight_label} to {$stock} units.");
    }

    public function settings(): View
    {
        $settings = StoreSetting::first();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $settings = StoreSetting::first();
        $settings->update($request->only([
            'shop_name',
            'shop_name_te',
            'shop_name_hi',
            'tagline',
            'announcement_bar_text',
            'phone',
            'whatsapp',
            'email',
            'address',
            'opening_hours',
            'upi_id',
            'min_order_paise',
            'delivery_fee_paise',
            'free_delivery_threshold_paise',
        ]));

        return back()->with('success', 'Store settings updated successfully!');
    }

    public function b2bInquiries(Request $request): View
    {
        $query = B2bInquiry::latest();
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $inquiries = $query->paginate(20);
        $newCount = B2bInquiry::where('status', 'new')->count();
        $totalCount = B2bInquiry::count();

        return view('admin.b2b.index', compact('inquiries', 'newCount', 'totalCount'));
    }

    public function updateB2bStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate(['status' => 'required|in:new,contacted,quoted,closed']);
        $inquiry = B2bInquiry::findOrFail($id);
        $inquiry->update(['status' => $request->status]);

        return back()->with('success', 'B2B Inquiry status updated successfully!');
    }

    public function invoice(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $settings = StoreSetting::first();

        return view('admin.invoice', compact('order', 'settings'));
    }

    public function customers(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));

        // Query grouped customer summaries from orders table
        $query = Order::select(
                'customer_phone',
                \Illuminate\Support\Facades\DB::raw('MAX(customer_name) as customer_name'),
                \Illuminate\Support\Facades\DB::raw('MAX(customer_email) as customer_email'),
                \Illuminate\Support\Facades\DB::raw('MAX(user_id) as user_id'),
                \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_orders'),
                \Illuminate\Support\Facades\DB::raw('SUM(total_paise) as total_spent_paise'),
                \Illuminate\Support\Facades\DB::raw('MAX(created_at) as last_order_date')
            )
            ->whereNotNull('customer_phone')
            ->groupBy('customer_phone');

        if (!empty($search)) {
            $term = '%' . strtolower($search) . '%';
            $query->havingRaw('LOWER(MAX(customer_name)) LIKE ?', [$term])
                  ->orHavingRaw('customer_phone LIKE ?', [$term])
                  ->orHavingRaw('LOWER(MAX(customer_email)) LIKE ?', [$term])
                  ->orHavingRaw('MAX(user_id) LIKE ?', [$term]);
        }

        $customers = $query->orderBy('last_order_date', 'desc')->paginate(20);

        // Fetch recent orders for expanded customer view
        $allCustomerPhones = $customers->pluck('customer_phone')->toArray();
        $customerOrders = Order::with('items')
            ->whereIn('customer_phone', $allCustomerPhones)
            ->latest()
            ->get()
            ->groupBy('customer_phone');

        return view('admin.customers.index', compact('customers', 'customerOrders', 'search'));
    }
}
