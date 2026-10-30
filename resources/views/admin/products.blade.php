@extends('admin.layout')

@section('title', 'Sweets Catalogue & Stock')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950">Sweets Inventory Management</h1>
            <p class="text-xs text-stone-500">Live stock adjustment per weight variant (250g, 500g, 1kg) & price viewing.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden divide-y divide-stone-100">
        @foreach($products as $prod)
            <div class="p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                
                <div class="flex items-center gap-3.5">
                    <img src="{{ $prod->image_url }}" alt="{{ $prod->name_en }}" class="w-16 h-16 object-cover rounded-xl bg-amber-50 flex-shrink-0" />
                    <div>
                        <h2 class="font-serif font-bold text-base text-stone-900 leading-snug">{{ $prod->name_en }}</h2>
                        <p class="text-xs text-stone-500 font-serif">{{ $prod->name_te }} • {{ $prod->name_hi }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-900 px-2 py-0.5 rounded">
                                {{ strtoupper($prod->dietary_type) }}
                            </span>
                            <span class="text-[10px] text-stone-400">Shelf life: {{ $prod->shelf_life_days }} days</span>
                        </div>
                    </div>
                </div>

                <!-- Weight Variants & Stock Modifiers -->
                <div class="flex flex-wrap items-center gap-3">
                    @foreach($prod->variants as $variant)
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200 text-xs space-y-1.5 min-w-[140px]">
                            <div class="flex justify-between items-center font-bold text-saffron-950">
                                <span>{{ $variant->weight_label }}</span>
                                <span class="text-stone-700">₹{{ number_format($variant->price_paise / 100, 0) }}</span>
                            </div>

                            <form method="POST" action="{{ route('admin.products.stock', $variant->id) }}" class="flex items-center gap-1.5 pt-1 border-t border-stone-200">
                                @csrf
                                <label class="text-[10px] text-stone-400 font-medium">Stock:</label>
                                <input 
                                    type="number" 
                                    name="stock_quantity" 
                                    value="{{ $variant->stock_quantity }}" 
                                    min="0" 
                                    class="w-14 px-1.5 py-1 text-xs text-center font-bold rounded border border-stone-300 {{ $variant->stock_quantity <= 15 ? 'text-red-600 bg-red-50' : 'text-emerald-700' }}"
                                />
                                <button type="submit" class="px-2 py-1 rounded bg-stone-200 hover:bg-stone-300 text-stone-800 font-bold text-[10px]">
                                    ✓
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
    </div>

</div>
@endsection
