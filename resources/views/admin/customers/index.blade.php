@extends('admin.layout')

@section('title', 'Customer Directory & Order History')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-4">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950 flex items-center gap-2">
                <span>Customer Directory & Database</span>
                <span class="text-xs font-sans font-bold px-2.5 py-0.5 rounded-full bg-saffron-100 text-saffron-900 border border-saffron-300">
                    {{ $customers->total() }} Customers
                </span>
            </h1>
            <p class="text-xs text-stone-500">View customer profile details, total sweets ordered, aggregate spends, and full order history.</p>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.customers') }}" class="flex items-center gap-2">
            <div class="relative">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Search by name, phone, email, or user ID..." 
                    class="w-72 sm:w-80 px-3.5 py-2 pl-9 text-xs rounded-xl border border-stone-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-saffron-500"
                />
                <span class="absolute left-3 top-2.5 text-stone-400 text-xs">🔍</span>
            </div>
            @if(request('q'))
                <a href="{{ route('admin.customers') }}" class="px-3 py-2 rounded-xl bg-stone-200 hover:bg-stone-300 text-stone-700 text-xs font-semibold">
                    Clear
                </a>
            @endif
            <button type="submit" class="px-4 py-2 rounded-xl bg-saffron-600 hover:bg-saffron-700 text-white text-xs font-bold shadow transition-all">
                Search
            </button>
        </form>
    </div>

    @if($customers->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200 p-12 text-center space-y-3">
            <span class="text-3xl">👥</span>
            <h3 class="font-bold text-stone-800 text-sm">No customers found matching your search.</h3>
            <p class="text-xs text-stone-500">Try searching with a different mobile number, customer name, or order ID.</p>
            @if(request('q'))
                <a href="{{ route('admin.customers') }}" class="inline-block mt-2 px-4 py-1.5 rounded-full bg-saffron-600 text-white text-xs font-semibold">
                    Show All Customers
                </a>
            @endif
        </div>
    @else
        <!-- Customers Table -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Customer & Account</th>
                        <th class="px-4 py-3.5">Contact (Mobile & Email)</th>
                        <th class="px-4 py-3.5 text-center">Orders Placed</th>
                        <th class="px-4 py-3.5 text-right">Total Spent</th>
                        <th class="px-4 py-3.5">Latest Order Date</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($customers as $c)
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-saffron-500 to-amber-700 text-white font-serif font-bold text-sm flex items-center justify-center shadow-sm">
                                        {{ strtoupper(substr($c->customer_name ?: 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-saffron-950">{{ $c->customer_name ?: 'Guest Customer' }}</p>
                                        <p class="text-[10px] text-stone-400 font-mono">User ID: {{ $c->user_id ?: 'Guest Checkout' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-stone-900 font-mono flex items-center gap-1.5">
                                    <span>📱</span>
                                    <span>+91 {{ $c->customer_phone }}</span>
                                </p>
                                @if($c->customer_email)
                                    <p class="text-stone-500 text-[11px] mt-0.5 truncate max-w-xs flex items-center gap-1.5">
                                        <span>✉️</span>
                                        <span>{{ $c->customer_email }}</span>
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full font-bold bg-amber-100 text-amber-900 text-xs">
                                    {{ $c->total_orders }} {{ $c->total_orders == 1 ? 'order' : 'orders' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="font-serif font-bold text-sm text-saffron-950">
                                    ₹{{ number_format($c->total_spent_paise / 100, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-stone-500 text-[11px]">
                                {{ \Carbon\Carbon::parse($c->last_order_date)->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a 
                                    href="{{ route('admin.orders') }}?q={{ $c->customer_phone }}" 
                                    class="px-3 py-1.5 rounded-xl bg-saffron-600 hover:bg-saffron-700 text-white font-bold text-[11px] shadow-sm inline-flex items-center gap-1"
                                >
                                    <span>View All Orders</span>
                                    <span>➔</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    @endif

</div>
@endsection
