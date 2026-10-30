@extends('admin.layout')

@section('title', 'Sweets Catalogue')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950">Sweets & Products Catalogue</h1>
            <p class="text-xs text-stone-500">Create new sweets, upload photos, set weight pricing, and publish directly to the website.</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-saffron-600 hover:bg-saffron-700 text-white font-bold text-xs shadow-md shadow-saffron-700/20 transition-all">
            <span>+ Add New Sweet / Product</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden divide-y divide-stone-100">
        @forelse($products as $prod)
            <div class="p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 hover:bg-stone-50/50 transition-colors">
                
                <!-- Left: Product Photo & Localized Names -->
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <img 
                        src="{{ $prod->image_url }}" 
                        alt="{{ $prod->name_en }}" 
                        class="w-20 h-20 object-cover rounded-2xl bg-amber-50 border border-stone-200 flex-shrink-0"
                    />
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="font-serif font-bold text-base text-stone-900 leading-snug truncate">
                                {{ $prod->name_en }}
                            </h2>
                            @if($prod->is_active)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    ● Published
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-200 text-stone-600">
                                    Draft
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-500 font-serif truncate">
                            {{ $prod->name_te }} • {{ $prod->name_hi }}
                        </p>
                        <div class="flex items-center gap-2 text-[11px] text-stone-400">
                            <span class="text-saffron-900 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                {{ $prod->category->name_en ?? 'Mithai' }}
                            </span>
                            <span>•</span>
                            <span>{{ $prod->dietary_type }}</span>
                            <span>•</span>
                            <span>Shelf life: {{ $prod->shelf_life_days }} days</span>
                        </div>
                    </div>
                </div>

                <!-- Middle: Weight Variants & Prices -->
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($prod->variants as $variant)
                        <div class="p-2 rounded-xl bg-stone-50 border border-stone-200 text-xs">
                            <span class="font-bold text-stone-900">{{ $variant->weight_label }}</span>:
                            <span class="font-semibold text-saffron-900">₹{{ number_format($variant->price_paise / 100, 0) }}</span>
                            <span class="text-[10px] {{ $variant->stock_quantity <= 15 ? 'text-red-600 font-bold' : 'text-stone-400' }}">
                                ({{ $variant->stock_quantity }} left)
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-2 w-full md:w-auto justify-end border-t md:border-t-0 pt-3 md:pt-0 border-stone-100">
                    <form method="POST" action="{{ route('admin.products.toggle', $prod->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-100 text-xs font-semibold">
                            {{ $prod->is_active ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.products.edit', $prod->id) }}" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold">
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.products.destroy', $prod->id) }}" onsubmit="return confirm('Are you sure you want to permanently delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-xl text-red-600 hover:bg-red-50 text-xs font-bold">
                            Delete
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="p-12 text-center text-stone-400 text-xs">
                No sweets in the catalogue yet. Click "+ Add New Sweet" to create your first sweet!
            </div>
        @endforelse
    </div>

</div>
@endsection
