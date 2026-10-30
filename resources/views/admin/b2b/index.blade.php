@extends('admin.layout')

@section('title', 'B2B & Bulk Order Inquiries')

@section('content')
<div class="space-y-6">

    <!-- Header & Summary Cards -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-4">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950">Corporate & Bulk Order Inquiries</h1>
            <p class="text-xs text-stone-500">Review leads from the &quot;We&apos;re Open for Bulk Orders&quot; customer form and follow up via WhatsApp or phone.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-amber-100 rounded-xl border border-amber-300 text-xs font-bold text-amber-900">
                <span>New / Uncontacted:</span>
                <span class="text-sm ml-1 text-saffron-950 font-black">{{ $newCount }}</span>
            </div>
            <div class="px-4 py-2 bg-stone-100 rounded-xl border border-stone-300 text-xs font-bold text-stone-700">
                <span>Total Leads:</span>
                <span class="text-sm ml-1 text-stone-900 font-black">{{ $totalCount }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto text-xs font-semibold pb-1">
        <a href="{{ route('admin.b2b') }}" class="px-3.5 py-1.5 rounded-xl border {{ !request('status') || request('status') === 'all' ? 'bg-saffron-600 text-white border-saffron-600 shadow-sm' : 'bg-white text-stone-700 hover:bg-stone-50 border-stone-200' }}">
            All Inquiries ({{ $totalCount }})
        </a>
        <a href="{{ route('admin.b2b', ['status' => 'new']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'new' ? 'bg-saffron-600 text-white border-saffron-600 shadow-sm' : 'bg-white text-stone-700 hover:bg-stone-50 border-stone-200' }}">
            New ({{ $newCount }})
        </a>
        <a href="{{ route('admin.b2b', ['status' => 'contacted']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'contacted' ? 'bg-saffron-600 text-white border-saffron-600 shadow-sm' : 'bg-white text-stone-700 hover:bg-stone-50 border-stone-200' }}">
            Contacted
        </a>
        <a href="{{ route('admin.b2b', ['status' => 'quoted']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'quoted' ? 'bg-saffron-600 text-white border-saffron-600 shadow-sm' : 'bg-white text-stone-700 hover:bg-stone-50 border-stone-200' }}">
            Quoted
        </a>
        <a href="{{ route('admin.b2b', ['status' => 'closed']) }}" class="px-3.5 py-1.5 rounded-xl border {{ request('status') === 'closed' ? 'bg-saffron-600 text-white border-saffron-600 shadow-sm' : 'bg-white text-stone-700 hover:bg-stone-50 border-stone-200' }}">
            Closed / Completed
        </a>
    </div>

    <!-- Table of Inquiries -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden text-xs">
        @if($inquiries->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xl">
                    📋
                </div>
                <h3 class="font-serif font-bold text-stone-900 text-base">No B2B Inquiries Yet</h3>
                <p class="text-stone-500 max-w-sm mx-auto">
                    When visitors submit the bulk order form on the customer storefront, their contact info and sweet requirements will appear here.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-stone-50 text-stone-600 uppercase font-bold text-[10px] tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Company / Event</th>
                            <th class="py-3 px-4">Contact Person</th>
                            <th class="py-3 px-4">Volume & Occasion</th>
                            <th class="py-3 px-4">Required Date</th>
                            <th class="py-3 px-4">Requirements</th>
                            <th class="py-3 px-4">Status & Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($inquiries as $inq)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-stone-400 whitespace-nowrap">
                                    {{ $inq->created_at->format('d M Y, h:i A') }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <strong class="font-bold text-stone-900 text-sm block">{{ $inq->company_name }}</strong>
                                    <span class="text-[11px] text-stone-500">{{ $inq->delivery_location }}</span>
                                </td>

                                <td class="py-3.5 px-4 space-y-1">
                                    <p class="font-semibold text-stone-900">{{ $inq->contact_person }}</p>
                                    <div class="flex items-center gap-2">
                                        <a href="tel:{{ $inq->phone }}" class="text-stone-600 hover:text-stone-900 underline">
                                            📞 {{ $inq->phone }}
                                        </a>
                                        <a 
                                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inq->phone) }}?text=Hello%20{{ urlencode($inq->contact_person) }},%20this%20is%20Anandam%20Sweets%20regarding%20your%20bulk%20order%20inquiry%20for%20{{ urlencode($inq->company_name) }}."
                                            target="_blank"
                                            class="px-2 py-0.5 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-[10px]"
                                        >
                                            WhatsApp
                                        </a>
                                    </div>
                                    <p class="text-[11px] text-stone-400">
                                        <a href="mailto:{{ $inq->email }}" class="hover:underline">{{ $inq->email }}</a>
                                    </p>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-900 font-bold text-[11px] block text-center mb-1">
                                        {{ $inq->estimated_quantity }}
                                    </span>
                                    <span class="text-[11px] text-stone-500 capitalize block">
                                        {{ str_replace('_', ' ', $inq->occasion_type) }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-stone-700">
                                    {{ $inq->delivery_date ? $inq->delivery_date->format('d M Y') : 'Not specified' }}
                                </td>

                                <td class="py-3.5 px-4 max-w-xs">
                                    <p class="text-stone-600 text-[11px] leading-relaxed line-clamp-2" title="{{ $inq->special_requirements }}">
                                        {{ $inq->special_requirements ?: 'None specified' }}
                                    </p>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.b2b.status', $inq->id) }}" class="flex items-center gap-1.5">
                                        @csrf
                                        <select 
                                            name="status" 
                                            onchange="this.form.submit()"
                                            class="text-xs px-2.5 py-1.5 rounded-xl border border-stone-300 font-bold {{ $inq->status === 'new' ? 'bg-red-50 text-red-700 border-red-300' : ($inq->status === 'contacted' ? 'bg-amber-50 text-amber-800 border-amber-300' : ($inq->status === 'quoted' ? 'bg-blue-50 text-blue-800 border-blue-300' : 'bg-emerald-50 text-emerald-800 border-emerald-300')) }}"
                                        >
                                            <option value="new" {{ $inq->status === 'new' ? 'selected' : '' }}>● New Lead</option>
                                            <option value="contacted" {{ $inq->status === 'contacted' ? 'selected' : '' }}>● Contacted</option>
                                            <option value="quoted" {{ $inq->status === 'quoted' ? 'selected' : '' }}>● Quote Sent</option>
                                            <option value="closed" {{ $inq->status === 'closed' ? 'selected' : '' }}>● Closed / Won</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-100">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
