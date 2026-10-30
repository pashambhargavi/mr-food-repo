@extends('admin.layout')

@section('title', 'Categories Management')

@section('content')
<div class="space-y-6">

    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-stone-200 pb-4">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950">Store Categories & Taxonomy</h1>
            <p class="text-xs text-stone-500">Manage categories, change category photos, and organize where sweets, savouries, and pickles appear on the customer website.</p>
        </div>

        <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="px-4 py-2 rounded-full bg-saffron-600 hover:bg-saffron-700 text-white font-bold text-xs shadow transition-all">
            + Add New Category
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $cat)
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-4 flex gap-4 items-center">
                <img 
                    src="{{ $cat->image_url }}" 
                    alt="{{ $cat->name_en }}" 
                    class="w-20 h-20 rounded-xl object-cover border border-amber-200/60 bg-amber-50 flex-shrink-0"
                    onerror="this.src='http://localhost:8000/storage/products/kaju-katli.jpg'"
                />
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-sm text-saffron-950 truncate">{{ $cat->name_en }}</h3>
                        @if($cat->is_active)
                            <span class="w-2 h-2 rounded-full bg-emerald-500" title="Active"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-400" title="Inactive"></span>
                        @endif
                    </div>

                    <p class="text-[11px] text-amber-800 font-serif truncate">{{ $cat->name_te }}</p>
                    <p class="text-[10px] text-stone-400 font-mono mt-0.5 truncate">Slug: ?cat={{ $cat->slug }}</p>

                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-stone-100 text-xs">
                        <span class="px-2 py-0.5 rounded-md bg-stone-100 text-stone-700 font-semibold text-[11px]">
                            {{ $cat->products_count }} Products
                        </span>

                        <div class="flex items-center gap-2">
                            <a href="http://localhost:3000/catalog?cat={{ $cat->slug }}" target="_blank" class="text-saffron-700 hover:text-saffron-900 font-bold text-[11px]">
                                Preview ↗
                            </a>

                            <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('Are you sure you want to permanently delete category \'{{ $cat->name_en }}\'? Products under it will be moved to uncategorized.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-[11px] p-1 hover:bg-red-50 rounded" title="Delete Category">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-stone-200 text-xs">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <h3 class="font-serif font-bold text-base text-saffron-950">Add Store Category</h3>
            <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-stone-400 hover:text-stone-700 font-bold text-lg">×</button>
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="font-bold text-stone-700 block mb-1">Category Name (English) *</label>
                <input type="text" name="name_en" required placeholder="e.g. Traditional Andhra Sweets" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Telugu Name (తెలుగు)</label>
                    <input type="text" name="name_te" placeholder="ఆంధ్రా సాంప్రదాయ మిఠాయిలు" class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif" />
                </div>
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Hindi Name (हिन्दी)</label>
                    <input type="text" name="name_hi" placeholder="पारंपरिक आंध्र मिठाई" class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif" />
                </div>
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Category Image / Banner</label>
                <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-stone-600 file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:bg-saffron-600 file:text-white" />
            </div>

            <div>
                <label class="font-bold text-stone-700 block mb-1">Description</label>
                <textarea name="description_en" rows="2" placeholder="Fresh authentic Andhra sweets prepared with pure ghee..." class="w-full px-3 py-2 rounded-xl border border-stone-300"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="px-4 py-2 rounded-full border border-stone-300 text-stone-600 font-bold">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 rounded-full bg-saffron-600 hover:bg-saffron-700 text-white font-bold shadow">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
