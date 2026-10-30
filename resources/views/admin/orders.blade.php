@extends('admin.layout')

@section('title', 'Orders Processing')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Notification Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950 flex items-center gap-2">
                <span>Orders Pipeline & Fulfilment</span>
                <span id="livePill" class="text-[10px] font-sans font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                    <span>Live Listener Active</span>
                </span>
            </h1>
            <p class="text-xs text-stone-500">Update preparation statuses, record COD collection, and print kitchen slips.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Audio Chime Button -->
            <button 
                type="button" 
                id="soundToggleBtn"
                onclick="toggleSound()" 
                class="px-3 py-1.5 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs flex items-center gap-1.5 transition-colors shadow-sm"
                title="Click to test order bell sound"
            >
                <span id="soundIcon">🔊</span>
                <span id="soundLabel">Sound Chime: ON</span>
            </button>

            <!-- Filter & Search Bar -->
            <form method="GET" action="{{ route('admin.orders') }}" class="flex flex-wrap items-center gap-2">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Search Order ID / Phone..." 
                    class="px-3 py-1.5 text-xs rounded-xl border border-stone-300 bg-white focus:outline-none focus:ring-1 focus:ring-saffron-600"
                />
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="px-3 py-1.5 text-xs rounded-xl border border-stone-300 bg-white text-stone-700"
                >
                    <option value="all">All Statuses</option>
                    <option value="placed" {{ request('status') === 'placed' ? 'selected' : '' }}>Placed</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready_for_dispatch" {{ request('status') === 'ready_for_dispatch' ? 'selected' : '' }}>Ready for Dispatch</option>
                    <option value="ready_for_pickup" {{ request('status') === 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                    <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered / Collected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-saffron-600 hover:bg-saffron-700 text-white text-xs font-semibold">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Live Toast Alert Banner for New Orders -->
    <div id="newOrderBanner" onclick="focusNewOrder()" class="hidden p-4 rounded-2xl bg-gradient-to-r from-amber-600 via-amber-700 to-red-600 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-bounce cursor-pointer hover:shadow-2xl transition-all border-2 border-amber-300">
        <div class="flex items-center gap-3">
            <span class="text-3xl">🔔</span>
            <div>
                <p class="font-bold text-sm sm:text-base flex items-center gap-2" id="bannerTitle">NEW ONLINE ORDER RECEIVED!</p>
                <p class="text-xs text-amber-100" id="bannerSubtitle">A customer has just placed an order. Click here to view and manage.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); focusNewOrder()" class="px-4 py-2 rounded-xl bg-white text-stone-900 font-bold text-xs hover:bg-amber-100 shadow-md whitespace-nowrap">
                View & Manage Order ➔
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <div id="ordersContainer" class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden divide-y divide-stone-100">
        @forelse($orders as $ord)
            <div id="order-row-{{ $ord->id }}" class="p-5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 hover:bg-stone-50/50 transition-colors">
                
                <!-- Left: Order Details -->
                <div class="space-y-1.5 flex-1 min-w-0 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono font-bold text-saffron-950 text-sm">{{ $ord->order_number }}</span>
                        <span class="font-bold text-stone-900">• {{ $ord->customer_name }}</span>
                        <span class="text-stone-500 font-mono">({{ $ord->customer_phone }})</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ord->fulfilment_type === 'delivery' ? 'bg-amber-100 text-amber-900' : 'bg-blue-100 text-blue-900' }}">
                            {{ $ord->fulfilment_type }}
                        </span>
                        <span class="text-stone-400 text-[11px]">{{ $ord->created_at->timezone('Asia/Kolkata')->format('d M, h:i A') }}</span>
                    </div>

                    <div class="text-stone-700">
                        <strong>Items:</strong> {{ $ord->items->map(fn($i) => "{$i->product_name} ({$i->variant_weight} × {$i->quantity})")->join(', ') }}
                    </div>

                    @if($ord->fulfilment_type === 'delivery' && !empty($ord->delivery_address))
                        <div class="text-[11px] text-stone-500">
                            <strong>Address:</strong> {{ $ord->delivery_address['address_line1'] ?? '' }}, {{ $ord->delivery_address['city'] ?? '' }} - {{ $ord->delivery_address['pincode'] ?? '' }}
                        </div>
                    @elseif($ord->fulfilment_type === 'pickup')
                        <div class="text-[11px] text-stone-500">
                            <strong>Pickup Window:</strong> {{ $ord->pickup_slot }}
                        </div>
                    @endif

                    @if($ord->delivery_notes)
                        <p class="text-[11px] text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md inline-block">
                            Note: {{ $ord->delivery_notes }}
                        </p>
                    @endif
                </div>

                <!-- Right: Status Dropdown & Payment Action -->
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 pt-3 lg:pt-0 border-stone-100">
                    
                    <div class="text-right mr-2">
                        <span class="text-[10px] text-stone-400 block uppercase">Total</span>
                        <span class="font-serif font-bold text-base text-saffron-950">₹{{ number_format($ord->total_paise / 100, 2) }}</span>
                    </div>

                    <!-- Update Status Form -->
                    <form method="POST" action="{{ route('admin.orders.status', $ord->id) }}" class="flex items-center gap-1.5">
                        @csrf
                        <select name="status" class="px-2.5 py-1.5 rounded-xl border border-stone-300 text-xs font-semibold bg-white text-stone-800">
                            <option value="placed" {{ $ord->status === 'placed' ? 'selected' : '' }}>Placed</option>
                            <option value="confirmed" {{ $ord->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="preparing" {{ $ord->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready_for_dispatch" {{ $ord->status === 'ready_for_dispatch' ? 'selected' : '' }}>Ready for Dispatch</option>
                            <option value="ready_for_pickup" {{ $ord->status === 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                            <option value="out_for_delivery" {{ $ord->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $ord->status === 'delivered' ? 'selected' : '' }}>Delivered / Collected</option>
                            <option value="cancelled" {{ $ord->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-saffron-600 hover:bg-saffron-700 text-white text-xs font-bold shadow-sm">
                            Save
                        </button>
                    </form>

                    <!-- COD Payment toggle -->
                    @if($ord->payment_status === 'paid')
                        <span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center gap-1">
                            ✓ Paid ({{ strtoupper($ord->payment_method) }})
                        </span>
                    @else
                        <form method="POST" action="{{ route('admin.orders.cod', $ord->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm">
                                Collect COD
                            </button>
                        </form>
                    @endif

                    <!-- Invoice Print -->
                    <a href="{{ route('admin.invoice', $ord->order_number) }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-stone-200 text-stone-700 hover:bg-stone-100 text-xs font-semibold flex items-center gap-1" title="Print Slip">
                        🖨️ Slip
                    </a>

                </div>

            </div>
        @empty
            <div class="p-8 text-center text-xs text-stone-400">No orders found matching filters.</div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $orders->links() }}
    </div>

</div>

<!-- Real-time Order Bell Chime & Polling Engine -->
<script>
    let soundEnabled = true;
    let audioCtx = null;
    let initialCount = {{ $orders->total() }};
    let lastKnownOrderId = '{{ $orders->first()?->order_number ?? '' }}';

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    // Melodic restaurant doorbell chime (880Hz then 587Hz)
    function playOrderChime() {
        if (!soundEnabled) return;
        try {
            initAudio();
            const now = audioCtx.currentTime;

            // Tone 1: High Bell Note (880Hz)
            const osc1 = audioCtx.createOscillator();
            const gain1 = audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, now);
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(audioCtx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Tone 2: Warm Second Note (587.33Hz)
            const osc2 = audioCtx.createOscillator();
            const gain2 = audioCtx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(587.33, now + 0.2);
            gain2.gain.setValueAtTime(0.35, now + 0.2);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.start(now + 0.2);
            osc2.stop(now + 0.8);
        } catch (e) {
            console.warn('Audio chime note:', e);
        }
    }

    function toggleSound() {
        initAudio();
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        const label = document.getElementById('soundLabel');
        const btn = document.getElementById('soundToggleBtn');

        if (soundEnabled) {
            icon.innerText = '🔊';
            label.innerText = 'Sound Chime: ON';
            btn.classList.remove('bg-stone-100', 'text-stone-500', 'border-stone-300');
            btn.classList.add('bg-amber-50', 'text-amber-900', 'border-amber-300');
            playOrderChime();
        } else {
            icon.innerText = '🔇';
            label.innerText = 'Sound Chime: MUTED';
            btn.classList.remove('bg-amber-50', 'text-amber-900', 'border-amber-300');
            btn.classList.add('bg-stone-100', 'text-stone-500', 'border-stone-300');
        }
    }

    let currentHighlightedOrderId = null;

    function renderOrderRow(ord) {
        const itemsText = (ord.items || []).map(i => `${i.product_name} (${i.variant_weight} × ${i.quantity})`).join(', ');
        
        let addressHtml = '';
        if (ord.fulfilment_type === 'delivery' && ord.delivery_address) {
            addressHtml = `<div class="text-[11px] text-stone-500"><strong>Address:</strong> ${ord.delivery_address.address_line1 || ''}, ${ord.delivery_address.city || ''} - ${ord.delivery_address.pincode || ''}</div>`;
        } else if (ord.fulfilment_type === 'pickup') {
            addressHtml = `<div class="text-[11px] text-stone-500"><strong>Pickup Window:</strong> ${ord.pickup_slot || 'Today'}</div>`;
        }

        const noteHtml = ord.delivery_notes 
            ? `<p class="text-[11px] text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md inline-block">Note: ${ord.delivery_notes}</p>` 
            : '';

        const csrfToken = '{{ csrf_token() }}';
        const statusAction = `/admin/orders/${ord.id}/status`;
        const codAction = `/admin/orders/${ord.id}/cod`;
        const invoiceUrl = `/admin/orders/${ord.order_number}/invoice`;

        const paymentBadge = ord.payment_status === 'paid'
            ? `<span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center gap-1">✓ Paid (${(ord.payment_method || '').toUpperCase()})</span>`
            : `<form method="POST" action="${codAction}">
                 <input type="hidden" name="_token" value="${csrfToken}">
                 <button type="submit" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm">Collect COD</button>
               </form>`;

        const div = document.createElement('div');
        div.id = `order-row-${ord.id}`;
        div.className = 'p-5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-amber-50/90 border-2 border-amber-400 rounded-2xl shadow-lg transition-all animate-pulse';
        div.innerHTML = `
            <div class="space-y-1.5 flex-1 min-w-0 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-600 text-white shadow-sm">✨ NEW ORDER</span>
                    <span class="font-mono font-bold text-saffron-950 text-sm">${ord.order_number}</span>
                    <span class="font-bold text-stone-900">• ${ord.customer_name}</span>
                    <span class="text-stone-500 font-mono">(${ord.customer_phone})</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${ord.fulfilment_type === 'delivery' ? 'bg-amber-100 text-amber-900' : 'bg-blue-100 text-blue-900'}">
                        ${ord.fulfilment_type}
                    </span>
                    <span class="text-stone-400 text-[11px]">Just now</span>
                </div>
                <div class="text-stone-700"><strong>Items:</strong> ${itemsText}</div>
                ${addressHtml}
                ${noteHtml}
            </div>
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 pt-3 lg:pt-0 border-stone-100">
                <div class="text-right mr-2">
                    <span class="text-[10px] text-stone-400 block uppercase">Total</span>
                    <span class="font-serif font-bold text-base text-saffron-950">₹${(ord.total_paise / 100).toFixed(2)}</span>
                </div>
                <form method="POST" action="${statusAction}" class="flex items-center gap-1.5">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <select name="status" class="px-2.5 py-1.5 rounded-xl border border-stone-300 text-xs font-semibold bg-white text-stone-800">
                        <option value="placed" selected>Placed</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready_for_dispatch">Ready for Dispatch</option>
                        <option value="ready_for_pickup">Ready for Pickup</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered / Collected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-saffron-600 hover:bg-saffron-700 text-white text-xs font-bold shadow-sm">Save</button>
                </form>
                ${paymentBadge}
                <a href="${invoiceUrl}" target="_blank" class="px-3 py-1.5 rounded-xl border border-stone-200 text-stone-700 hover:bg-stone-100 text-xs font-semibold flex items-center gap-1" title="Print Slip">
                    🖨️ Slip
                </a>
            </div>
        `;
        return div;
    }

    function focusNewOrder() {
        const banner = document.getElementById('newOrderBanner');
        if (banner) banner.classList.add('hidden');

        if (currentHighlightedOrderId) {
            const row = document.getElementById(`order-row-${currentHighlightedOrderId}`);
            if (row) {
                row.classList.remove('animate-pulse');
                row.classList.add('ring-4', 'ring-amber-500', 'bg-amber-100/60');
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
        }
        // If not found in current view (e.g. status filter active), navigate to clean orders pipeline
        window.location.href = "{{ route('admin.orders') }}";
    }

    // Poll for new orders every 3.5 seconds
    async function checkNewOrders() {
        try {
            const res = await fetch('/api/orders');
            if (!res.ok) return;
            const data = await res.json();
            if (data && data.success && data.data && data.data.length > 0) {
                const latestOrder = data.data[0];
                if (lastKnownOrderId && latestOrder.order_number !== lastKnownOrderId) {
                    lastKnownOrderId = latestOrder.order_number;
                    currentHighlightedOrderId = latestOrder.id;
                    playOrderChime();

                    // Immediately prepend the new order directly into the table
                    const container = document.getElementById('ordersContainer');
                    const existingRow = document.getElementById(`order-row-${latestOrder.id}`);
                    if (!existingRow && container) {
                        // Clear "no orders found" empty state if it was shown
                        if (container.children.length === 1 && container.children[0].innerText.includes('No orders found')) {
                            container.innerHTML = '';
                        }
                        const newRow = renderOrderRow(latestOrder);
                        container.insertBefore(newRow, container.firstChild);
                    }

                    // Show notification banner with exact order details
                    const banner = document.getElementById('newOrderBanner');
                    if (banner) {
                        document.getElementById('bannerTitle').innerText = `🔔 NEW ORDER: ${latestOrder.order_number} (${latestOrder.customer_name})`;
                        document.getElementById('bannerSubtitle').innerText = `Amount: ₹${(latestOrder.total_paise / 100).toFixed(2)} • Fulfilment: ${latestOrder.fulfilment_type.toUpperCase()} • Click anywhere to view & manage!`;
                        banner.classList.remove('hidden');
                    }
                } else if (!lastKnownOrderId) {
                    lastKnownOrderId = latestOrder.order_number;
                }
            }
        } catch (err) {
            console.warn('Order poll error:', err);
        }
    }

    setInterval(checkNewOrders, 3500);
</script>
@endsection
