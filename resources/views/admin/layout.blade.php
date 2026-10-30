<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Anandam Sweets</title>
    <!-- Tailwind CSS via CDN for zero-build instant admin styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saffron: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-stone-50 text-stone-900 font-sans antialiased min-h-screen flex flex-col">

    <!-- Global Floating New Order Audio Toast -->
    <div id="globalOrderToast" class="hidden fixed top-4 right-4 z-50 max-w-sm bg-gradient-to-r from-amber-600 to-red-600 text-white p-4 rounded-2xl shadow-2xl border-2 border-amber-300 flex items-center justify-between gap-3 animate-bounce">
        <div class="flex items-center gap-2.5">
            <span class="text-2xl">🔔</span>
            <div class="text-xs">
                <p class="font-bold text-sm" id="globalToastTitle">New Sweet Order!</p>
                <p class="text-[11px] text-amber-100" id="globalToastText">Customer placed an order.</p>
            </div>
        </div>
        <a href="{{ route('admin.orders') }}" class="px-3 py-1.5 rounded-xl bg-white text-stone-900 font-bold text-xs hover:bg-amber-100 whitespace-nowrap shadow-sm">
            View ➔
        </a>
    </div>

    <!-- Top Navigation Header -->
    <header class="bg-saffron-950 text-amber-50 border-b border-saffron-800 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-saffron-600 text-white flex items-center justify-center font-serif font-black text-sm border border-amber-300 shadow">
                    AS
                </div>
                <div>
                    <h1 class="font-serif font-bold text-lg text-amber-100 leading-tight">Anandam Sweets — Admin Portal</h1>
                    <p class="text-[11px] text-amber-300/80">Independent Laravel 11 Backend & Fulfilment Hub</p>
                </div>
            </a>

            <nav class="flex items-center gap-2 sm:gap-3 text-xs font-semibold">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    Overview
                </a>
                <a href="{{ route('admin.orders') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.orders') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    Orders Pipeline
                </a>
                <a href="{{ route('admin.products') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.products*') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    Sweets Catalogue
                </a>
                <a href="{{ route('admin.categories') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.categories*') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    Categories
                </a>
                <a href="{{ route('admin.customers') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.customers*') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    Customers
                </a>
                <a href="{{ route('admin.b2b') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.b2b*') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    B2B Inquiries
                </a>
                <a href="{{ route('admin.settings') }}" class="px-3 py-1.5 rounded-lg hover:bg-saffron-900 transition-colors {{ request()->routeIs('admin.settings') ? 'bg-saffron-800 text-yellow-300 font-bold' : 'text-amber-100' }}">
                    Store Settings
                </a>

                <a href="{{ env('STOREFRONT_URL', request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' ? 'http://localhost:3000' : 'https://sweet-shop-tan.vercel.app') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-saffron-950 font-bold ml-2 shadow-sm flex items-center gap-1.5">
                    <span>View Customer Site</span>
                    <span class="text-[10px]">↗</span>
                </a>

                @if(auth()->check())
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline ml-1">
                        @csrf
                        <button type="submit" class="px-2.5 py-1.5 rounded-lg text-amber-300 hover:text-white hover:bg-saffron-900 transition-colors text-[11px]">
                            Log Out
                        </button>
                    </form>
                @endif
            </nav>
        </div>
    </header>

    <!-- Flash message -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-xl flex items-center justify-between shadow-sm">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">×</button>
            </div>
        </div>
    @endif

    <!-- Main Body Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-stone-200 py-4 text-center text-xs text-stone-500">
        Anandam Sweets Admin Portal • Laravel 11 Backend running on <code>http://127.0.0.1:8000/admin</code>
    </footer>

    <!-- Global Audio Bell Chime Script -->
    <script>
        let globalAudioCtx = null;
        let globalLastKnownOrder = null;

        function playGlobalChime() {
            try {
                if (!globalAudioCtx) {
                    globalAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (globalAudioCtx.state === 'suspended') {
                    globalAudioCtx.resume();
                }
                const now = globalAudioCtx.currentTime;

                const osc1 = globalAudioCtx.createOscillator();
                const gain1 = globalAudioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(880, now);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc1.connect(gain1);
                gain1.connect(globalAudioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.35);

                const osc2 = globalAudioCtx.createOscillator();
                const gain2 = globalAudioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(587.33, now + 0.2);
                gain2.gain.setValueAtTime(0.35, now + 0.2);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                osc2.connect(gain2);
                gain2.connect(globalAudioCtx.destination);
                osc2.start(now + 0.2);
                osc2.stop(now + 0.8);
            } catch(e) {}
        }

        async function pollNewOrdersGlobal() {
            // If already on the orders pipeline page, let orders.blade.php handle live prepend & chime
            if (window.location.pathname.endsWith('/admin/orders') || window.location.pathname.endsWith('/admin/orders/')) {
                return;
            }
            try {
                const res = await fetch('/api/orders');
                if (!res.ok) return;
                const json = await res.json();
                if (json && json.success && json.data && json.data.length > 0) {
                    const topOrder = json.data[0];
                    if (globalLastKnownOrder && topOrder.order_number !== globalLastKnownOrder) {
                        playGlobalChime();
                        const toast = document.getElementById('globalOrderToast');
                        if (toast) {
                            document.getElementById('globalToastTitle').innerText = `🔔 New Order: ${topOrder.order_number}`;
                            document.getElementById('globalToastText').innerText = `${topOrder.customer_name} • ₹${(topOrder.total_paise / 100).toFixed(2)}`;
                            toast.classList.remove('hidden');
                        }
                    }
                    globalLastKnownOrder = topOrder.order_number;
                }
            } catch(e) {}
        }

        setInterval(pollNewOrdersGlobal, 5000);
    </script>

</body>
</html>
