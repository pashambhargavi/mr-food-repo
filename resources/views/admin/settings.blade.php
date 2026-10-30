@extends('admin.layout')

@section('title', 'Shop Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="font-serif font-bold text-2xl text-saffron-950">Store Settings & Fulfilment Rules</h1>
        <p class="text-xs text-stone-500">Edit business contact, operating hours, delivery fees, and UPI VPA.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-6 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="font-bold text-stone-700 block mb-1">Shop Name (English)</label>
                <input type="text" name="shop_name" value="{{ $settings->shop_name }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Shop Name (Telugu)</label>
                <input type="text" name="shop_name_te" value="{{ $settings->shop_name_te }}" class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Contact Phone</label>
                <input type="text" name="phone" value="{{ $settings->phone }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">WhatsApp Number</label>
                <input type="text" name="whatsapp" value="{{ $settings->whatsapp }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div class="sm:col-span-2">
                <label class="font-bold text-stone-700 block mb-1">Top Announcement Bar Text (Storefront Header)</label>
                <input type="text" name="announcement_bar_text" value="{{ $settings->announcement_bar_text ?? 'FREE SHIPPING ABOVE RS.1500/- IN PAN INDIA | 100% PURE COW GHEE' }}" class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-amber-50/40 text-xs font-semibold" placeholder="e.g. FREE SHIPPING ABOVE RS.1500/- IN PAN INDIA | 100% PURE COW GHEE" />
                <span class="text-[10px] text-stone-400 mt-0.5 block">This text is displayed live at the very top of your website.</span>
            </div>

            <div class="sm:col-span-2">
                <label class="font-bold text-stone-700 block mb-1">Physical Store Address</label>
                <input type="text" name="address" value="{{ $settings->address }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Opening Hours</label>
                <input type="text" name="opening_hours" value="{{ $settings->opening_hours }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Merchant UPI VPA</label>
                <input type="text" name="upi_id" value="{{ $settings->upi_id }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Standard Delivery Fee (Paise)</label>
                <input type="number" name="delivery_fee_paise" value="{{ $settings->delivery_fee_paise }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
                <span class="text-[10px] text-stone-400">4000 = ₹40.00</span>
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Free Delivery Threshold (Paise)</label>
                <input type="number" name="free_delivery_threshold_paise" value="{{ $settings->free_delivery_threshold_paise }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
                <span class="text-[10px] text-stone-400">50000 = ₹500.00</span>
            </div>
        </div>

        <div class="pt-4 border-t border-stone-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 rounded-full bg-saffron-600 hover:bg-saffron-700 text-white font-bold text-xs shadow-md">
                Save Store Settings
            </button>
        </div>
    </form>

</div>
@endsection
