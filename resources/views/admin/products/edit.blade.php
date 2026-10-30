@extends('admin.layout')

@section('title', 'Edit ' . $product->name_en)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between border-b border-stone-200 pb-4">
        <div>
            <h1 class="font-serif font-bold text-2xl text-saffron-950">Edit Sweet: {{ $product->name_en }}</h1>
            <p class="text-xs text-stone-500">Update photo, descriptions in 3 languages, weight variants, pricing, and live website status.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900">
            ← Back to Catalogue
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-700 space-y-1">
            <strong class="font-bold">Please correct the following errors:</strong>
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 space-y-8 text-xs">
        @csrf
        @method('PUT')

        <!-- 1. Basic Details & Localized Names -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h2 class="text-sm font-bold font-serif text-saffron-950 uppercase tracking-wider">
                    1. Product Names & Category
                </h2>
                <button 
                    type="button" 
                    onclick="triggerAutoTranslate()"
                    class="px-3 py-1 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-[11px] flex items-center gap-1.5 transition-colors shadow-sm"
                >
                    <span>✨ Auto-Translate Telugu & Hindi</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Sweet Name (English) *</label>
                    <input 
                        type="text" 
                        id="name_en"
                        name="name_en" 
                        required 
                        value="{{ old('name_en', $product->name_en) }}" 
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 focus:outline-none focus:ring-1 focus:ring-saffron-600 text-xs font-semibold" 
                        oninput="debounceTranslate()"
                        onblur="triggerAutoTranslate()"
                    />
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-bold text-stone-700 block">Sweet Name (Telugu - తెలుగు)</label>
                        <span id="badge_te" class="text-[10px] text-emerald-700 font-bold hidden">✨ Auto-updated</span>
                    </div>
                    <input 
                        type="text" 
                        id="name_te"
                        name="name_te" 
                        value="{{ old('name_te', $product->name_te) }}" 
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif text-xs bg-amber-50/30" 
                    />
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="font-bold text-stone-700 block">Sweet Name (Hindi - हिन्दी)</label>
                        <span id="badge_hi" class="text-[10px] text-emerald-700 font-bold hidden">✨ Auto-updated</span>
                    </div>
                    <input 
                        type="text" 
                        id="name_hi"
                        name="name_hi" 
                        value="{{ old('name_hi', $product->name_hi) }}" 
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif text-xs bg-amber-50/30" 
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Category (Where this sweet appears) *</label>
                    <select name="category_id" required class="w-full px-3 py-2 rounded-xl border border-stone-300 bg-white font-medium">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id === $cat->id ? 'selected' : '' }}>
                                {{ $cat->name_en }} ({{ $cat->name_te }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Dietary Speciality *</label>
                    <select name="dietary_type" required class="w-full px-3 py-2 rounded-xl border border-stone-300 bg-white">
                        <option value="pure_ghee" {{ $product->dietary_type === 'pure_ghee' ? 'selected' : '' }}>100% Pure Cow Ghee</option>
                        <option value="dry_fruit" {{ $product->dietary_type === 'dry_fruit' ? 'selected' : '' }}>Cashews & Dry Fruits</option>
                        <option value="milk_sweet" {{ $product->dietary_type === 'milk_sweet' ? 'selected' : '' }}>Khoya / Mawa Delicacy</option>
                        <option value="syrup" {{ $product->dietary_type === 'syrup' ? 'selected' : '' }}>Syrup Sweet (Gulab Jamun/Rasgulla)</option>
                        <option value="savouries" {{ $product->dietary_type === 'savouries' ? 'selected' : '' }}>Spicy Savouries / Namkeen</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Shelf Life (Days) *</label>
                    <input type="number" name="shelf_life_days" required value="{{ old('shelf_life_days', $product->shelf_life_days) }}" min="1" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
                </div>
            </div>
        </div>

        <!-- 2. Product Photo -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold font-serif text-saffron-950 uppercase tracking-wider border-b border-stone-100 pb-2">
                2. Sweet Photograph / Image
            </h2>

            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Current image preview -->
                <div class="flex-shrink-0 text-center">
                    <span class="block text-[11px] font-bold text-stone-500 mb-1">Current Photo:</span>
                    <img src="{{ $product->image_url }}" alt="{{ $product->name_en }}" class="w-24 h-24 object-cover rounded-2xl border-2 border-amber-300 shadow-sm" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div class="p-4 rounded-2xl border-2 border-dashed border-stone-300 hover:border-saffron-500 bg-stone-50/50 transition-colors">
                        <label class="font-bold text-stone-800 block mb-1">Upload New Photo to Replace</label>
                        <p class="text-[11px] text-stone-500 mb-2">JPG, PNG, WEBP up to 5MB. Stored locally on server.</p>
                        <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-saffron-600 file:text-white hover:file:bg-saffron-700 cursor-pointer" />
                    </div>

                    <div>
                        <label class="font-bold text-stone-700 block mb-1">OR Update Image URL</label>
                        <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
                        <p class="text-[11px] text-stone-400 mt-1">Leave unchanged to keep current photograph.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Descriptions -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold font-serif text-saffron-950 uppercase tracking-wider border-b border-stone-100 pb-2">
                3. Descriptions & Culinary Details
            </h2>

            <div>
                <label class="font-bold text-stone-700 block mb-1">English Description *</label>
                <textarea 
                    id="description_en"
                    name="description_en" 
                    required 
                    rows="2" 
                    class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs"
                    onblur="translateDescription()"
                >{{ old('description_en', $product->description_en) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Telugu Description (తెలుగు)</label>
                    <textarea 
                        id="description_te"
                        name="description_te" 
                        rows="2" 
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif text-xs bg-amber-50/30"
                    >{{ old('description_te', $product->description_te) }}</textarea>
                </div>
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Hindi Description (हिन्दी)</label>
                    <textarea 
                        id="description_hi"
                        name="description_hi" 
                        rows="2" 
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 font-serif text-xs bg-amber-50/30"
                    >{{ old('description_hi', $product->description_hi) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Key Ingredients (English)</label>
                    <input type="text" name="ingredients_en" value="{{ old('ingredients_en', $product->ingredients_en) }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
                </div>
                <div>
                    <label class="font-bold text-stone-700 block mb-1">Storage Instructions</label>
                    <input type="text" name="storage_en" value="{{ old('storage_en', $product->storage_en) }}" class="w-full px-3 py-2 rounded-xl border border-stone-300" />
                </div>
            </div>
        </div>

        <!-- 4. Weight Options & Pricing -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold font-serif text-saffron-950 uppercase tracking-wider border-b border-stone-100 pb-2">
                4. Weight Variants, Pricing (₹) & Stock
            </h2>
            <p class="text-stone-500 text-[11px]">Adjust prices and available inventory for each weight option.</p>

            <div class="space-y-3">
                @foreach($product->variants as $variant)
                    <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200 grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                        <div>
                            <label class="font-bold text-stone-600 block mb-1">Weight Label</label>
                            <input type="text" name="variants[{{ $variant->id }}][weight_label]" value="{{ $variant->weight_label }}" class="w-full px-2.5 py-1.5 rounded-lg border border-stone-300 font-bold" />
                        </div>
                        <div>
                            <label class="font-bold text-stone-600 block mb-1">Selling Price (₹) *</label>
                            <input type="number" step="1" name="variants[{{ $variant->id }}][price_rupees]" required value="{{ round($variant->price_paise / 100) }}" class="w-full px-2.5 py-1.5 rounded-lg border border-stone-300 font-bold text-saffron-950" />
                        </div>
                        <div>
                            <label class="font-bold text-stone-600 block mb-1">Inventory in Stock *</label>
                            <input type="number" name="variants[{{ $variant->id }}][stock_quantity]" required value="{{ $variant->stock_quantity }}" min="0" class="w-full px-2.5 py-1.5 rounded-lg border border-stone-300 font-bold" />
                        </div>
                        <div class="text-[11px] {{ $variant->stock_quantity <= 10 ? 'text-amber-600 font-bold' : 'text-stone-400' }} pb-2">
                            Status: {{ $variant->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 5. Status & Badges -->
        <div class="space-y-4 pt-2 border-t border-stone-100">
            <h2 class="text-sm font-bold font-serif text-saffron-950 uppercase tracking-wider">
                5. Publishing & Badges
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="p-3 rounded-xl border border-stone-200 flex items-center gap-2 cursor-pointer bg-stone-50">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-saffron-600 focus:ring-saffron-500" />
                    <div>
                        <span class="font-bold text-stone-900 block">Published</span>
                        <span class="text-[10px] text-stone-500">Visible on customer store</span>
                    </div>
                </label>

                <label class="p-3 rounded-xl border border-stone-200 flex items-center gap-2 cursor-pointer bg-stone-50">
                    <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="rounded text-saffron-600 focus:ring-saffron-500" />
                    <div>
                        <span class="font-bold text-stone-900 block">Bestseller Badge</span>
                        <span class="text-[10px] text-stone-500">Highlighted on homepage</span>
                    </div>
                </label>

                <label class="p-3 rounded-xl border border-stone-200 flex items-center gap-2 cursor-pointer bg-stone-50">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded text-saffron-600 focus:ring-saffron-500" />
                    <div>
                        <span class="font-bold text-stone-900 block">Festive Special</span>
                        <span class="text-[10px] text-stone-500">Shown in festive collection</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-stone-200 flex items-center justify-between">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-full border border-stone-300 text-stone-700 font-bold hover:bg-stone-100">
                Cancel
            </a>

            <button type="submit" class="px-8 py-3 rounded-full bg-gradient-to-r from-saffron-600 to-saffron-700 hover:from-saffron-700 hover:to-saffron-800 text-white font-serif font-bold text-sm shadow-lg shadow-saffron-600/30">
                ✓ Save Changes & Update Website
            </button>
        </div>

    </form>

</div>

<!-- Auto-translate & Transliteration Script -->
<script>
    const sweetsLexicon = {
        'kaju': { te: 'కాజూ', hi: 'काजू' },
        'katli': { te: 'కత్లీ', hi: 'कतली' },
        'laddu': { te: 'లడ్డు', hi: 'लड्डू' },
        'ladoo': { te: 'లడ్డు', hi: 'लड्डू' },
        'motichoor': { te: 'మోతీచూర్', hi: 'मोतीचूर' },
        'besan': { te: 'బేసన్', hi: 'बेसन' },
        'mysore': { te: 'మైసూర్', hi: 'मैसूर' },
        'pak': { te: 'పాక్', hi: 'पाक' },
        'gulab': { te: 'గులాబ్', hi: 'गुलाब' },
        'jamun': { te: 'జామూన్', hi: 'जामुन' },
        'rasgulla': { te: 'రసగుల్లా', hi: 'रसगुल्ला' },
        'rasmalai': { te: 'రస్‌మలై', hi: 'रसमलाई' },
        'halwa': { te: 'హల్వా', hi: 'हलवा' },
        'badam': { te: 'బాదాం', hi: 'बादाम' },
        'peda': { te: 'పేడా', hi: 'पेड़ा' },
        'barfi': { te: 'బర్ఫీ', hi: 'बर्फी' },
        'burfi': { te: 'బర్ఫీ', hi: 'बर्फी' },
        'pista': { te: 'పిస్తా', hi: 'पिस्ता' },
        'roll': { te: 'రోల్', hi: 'रोल' },
        'kalakand': { te: 'కళాకండ్', hi: 'कलाकंद' },
        'jalebi': { te: 'జిలేబీ', hi: 'जलेबी' },
        'soan': { te: 'సోన్', hi: 'सोन' },
        'papdi': { te: 'పాపిడి', hi: 'पापड़ी' },
        'milk': { te: 'మిల్క్', hi: 'मिल्क' },
        'cake': { te: 'కేక్', hi: 'केक' },
        'dry': { te: 'డ్రై', hi: 'ड्राई' },
        'fruit': { te: 'ఫ్రూట్', hi: 'फ्रूट' },
        'anjeer': { te: 'అంజీర్', hi: 'अंजीर' },
        'royal': { te: 'రాయల్', hi: 'शाही' },
        'silver': { te: 'సిల్వర్', hi: 'चांदी वर्क' },
        'pure': { te: 'స్వచ్ఛమైన', hi: 'शुद्ध' },
        'ghee': { te: 'నేతి', hi: 'देसी घी' },
        'special': { te: 'స్పెషల్', hi: 'खास' },
        'sweet': { te: 'మిఠాయి', hi: 'मिठाई' },
        'sweets': { te: 'మిఠాయిలు', hi: 'मिठाइयां' },
        'box': { te: 'బాక్స్', hi: 'डिब्बा' }
    };

    let debounceTimer;
    function debounceTranslate() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            triggerAutoTranslate();
        }, 600);
    }

    async function triggerAutoTranslate() {
        const nameEn = document.getElementById('name_en').value.trim();
        if (!nameEn) return;

        let teluguResult = '';
        let hindiResult = '';

        try {
            const [teRes, hiRes] = await Promise.all([
                fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=te&dt=t&q=${encodeURIComponent(nameEn)}`).then(r => r.json()),
                fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=hi&dt=t&q=${encodeURIComponent(nameEn)}`).then(r => r.json())
            ]);

            if (teRes && teRes[0] && teRes[0][0]) {
                teluguResult = teRes[0].map(s => s[0]).join('');
            }
            if (hiRes && hiRes[0] && hiRes[0][0]) {
                hindiResult = hiRes[0].map(s => s[0]).join('');
            }
        } catch (e) {
            console.warn('Network translation fallback to dictionary:', e);
        }

        if (!teluguResult || !hindiResult) {
            const words = nameEn.toLowerCase().split(/\s+/);
            const teWords = [];
            const hiWords = [];

            words.forEach(w => {
                const clean = w.replace(/[^a-z]/g, '');
                if (sweetsLexicon[clean]) {
                    teWords.push(sweetsLexicon[clean].te);
                    hiWords.push(sweetsLexicon[clean].hi);
                } else {
                    teWords.push(w);
                    hiWords.push(w);
                }
            });

            if (!teluguResult) teluguResult = teWords.join(' ');
            if (!hindiResult) hindiResult = hiWords.join(' ');
        }

        document.getElementById('name_te').value = teluguResult;
        document.getElementById('name_hi').value = hindiResult;

        document.getElementById('badge_te').classList.remove('hidden');
        document.getElementById('badge_hi').classList.remove('hidden');
    }

    async function translateDescription() {
        const descEn = document.getElementById('description_en').value.trim();
        if (!descEn) return;

        try {
            const [teRes, hiRes] = await Promise.all([
                fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=te&dt=t&q=${encodeURIComponent(descEn)}`).then(r => r.json()),
                fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=hi&dt=t&q=${encodeURIComponent(descEn)}`).then(r => r.json())
            ]);

            if (teRes && teRes[0]) {
                document.getElementById('description_te').value = teRes[0].map(s => s[0]).join('');
            }
            if (hiRes && hiRes[0]) {
                document.getElementById('description_hi').value = hiRes[0].map(s => s[0]).join('');
            }
        } catch (e) {
            console.warn('Description translation fallback:', e);
        }
    }
</script>
@endsection
