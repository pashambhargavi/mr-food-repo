@extends('admin.layout')

@section('title', 'Overview')

@section('content')
<div class="space-y-6">

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Today's Revenue</span>
            <p class="text-2xl font-serif font-black text-saffron-950">₹{{ number_format($todayRevenue / 100, 2) }}</p>
            <p class="text-[11px] text-stone-400">Lifetime: ₹{{ number_format($totalRevenue / 100, 2) }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Today's Orders</span>
            <p class="text-2xl font-serif font-black text-saffron-950">{{ $todayOrders }}</p>
            <p class="text-[11px] text-stone-400">Total: {{ $totalOrders }} orders</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Pending Processing</span>
            <p class="text-2xl font-serif font-black text-amber-600">{{ $pendingOrders }}</p>
            <p class="text-[11px] text-amber-700 font-medium">{{ $awaitingPickup }} Pickup • {{ $awaitingDispatch }} Dispatch</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Low Stock Warnings</span>
            <p class="text-2xl font-serif font-black {{ $lowStockVariants->count() > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $lowStockVariants->count() }}
            </p>
            <p class="text-[11px] text-stone-400">Variants ≤ 15 units</p>
        </div>

    </div>

    <!-- 2 Column: Recent Orders & Low Stock Items -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Recent Orders -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-stone-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h2 class="font-serif font-bold text-base text-saffron-950">Recent Incoming Orders</h2>
                <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-saffron-700 hover:underline">View All Orders ➔</a>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse($recentOrders as $ord)
                    <div class="py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-saffron-900">{{ $ord->order_number }}</span>
                                <span class="font-semibold text-stone-800">{{ $ord->customer_name }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ord->fulfilment_type === 'delivery' ? 'bg-amber-100 text-amber-900' : 'bg-blue-100 text-blue-900' }}">
                                    {{ $ord->fulfilment_type }}
                                </span>
                            </div>
                            <p class="text-stone-500 text-[11px]">
                                {{ $ord->items->pluck('product_name')->join(', ') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="font-serif font-bold text-stone-900">₹{{ number_format($ord->total_paise / 100, 2) }}</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase 
                                {{ $ord->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : 
                                  ($ord->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900') }}">
                                {{ str_replace('_', ' ', $ord->status) }}
                            </span>
                            <a href="{{ route('admin.invoice', $ord->order_number) }}" target="_blank" class="p-1 text-stone-500 hover:text-saffron-700" title="Print Slip">
                                🖨️
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 py-4 text-center">No recent orders.</p>
                @endforelse
            </div>
        </div>

        <!-- Right: Low Stock Alert Card -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-stone-200 shadow-sm p-5 space-y-4">
            <h2 class="font-serif font-bold text-base text-saffron-950 border-b border-stone-100 pb-3">
                ⚠️ Inventory Stock Alerts
            </h2>

            <div class="space-y-2">
                @forelse($lowStockVariants as $variant)
                    <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-200/70 text-xs flex items-center justify-between">
                        <div>
                            <p class="font-bold text-stone-900">{{ $variant->product->name_en }}</p>
                            <p class="text-[11px] text-stone-500">{{ $variant->weight_label }} (SKU: {{ $variant->sku }})</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-red-600 text-sm">{{ $variant->stock_quantity }}</span>
                            <span class="text-[10px] text-stone-400 block">left</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-emerald-700 py-3 text-center">✓ All sweet stock levels are healthy!</p>
                @endforelse
            </div>

            <div class="pt-2">
                <a href="{{ route('admin.products') }}" class="block text-center py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold">
                    Manage All Sweet Stocks
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
