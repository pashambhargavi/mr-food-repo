<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

$categories = [
    // Sweets
    [
        'id' => 'cat-laddu',
        'slug' => 'laddu-specials',
        'name_en' => 'Pure Ghee Laddus',
        'name_te' => 'స్వచ్ఛమైన నేతి లడ్డూలు',
        'name_hi' => 'शुद्ध घी के लड्डू',
        'description_en' => 'Aromatic melt-in-mouth golden laddus prepared with 100% pure cow ghee.',
        'icon' => 'CircleDot',
        'image_url' => 'http://localhost:8000/storage/products/motichoor-laddu.jpg',
        'display_order' => 1,
        'is_active' => true,
    ],
    [
        'id' => 'cat-kaju',
        'slug' => 'kaju-sweets',
        'name_en' => 'Kaju & Cashew Sweets',
        'name_te' => 'కాజూ కత్లీ & జీడిపప్పు మిఠాయిలు',
        'name_hi' => 'काजू कतली एवं काजू मिष्ठान',
        'description_en' => 'Made with premium Goan cashews and pure edible silver vark.',
        'icon' => 'Sparkles',
        'image_url' => 'http://localhost:8000/storage/products/kaju-katli.jpg',
        'display_order' => 2,
        'is_active' => true,
    ],
    [
        'id' => 'cat-mysore-pak',
        'slug' => 'mysore-pak-halwa',
        'name_en' => 'Mysore Pak & Halwa',
        'name_te' => 'మైసూర్ పాక్ & హల్వా',
        'name_hi' => 'मैसूर पाक एवं हलवा',
        'description_en' => 'Ghee-dripping porous classic Mysore Pak and Karachi Halwa.',
        'icon' => 'Flame',
        'image_url' => 'http://localhost:8000/storage/products/mysore-pak.jpg',
        'display_order' => 3,
        'is_active' => true,
    ],
    [
        'id' => 'cat-barfi-peda',
        'slug' => 'barfi-and-peda',
        'name_en' => 'Barfi & Peda Delights',
        'name_te' => 'బర్ఫీ & పేడ రకాలు',
        'name_hi' => 'बर्फी एवं पेड़ा डिलाइट्स',
        'description_en' => 'Rich mawa and khoya delicacies infused with saffron and cardamom.',
        'icon' => 'Layers',
        'image_url' => 'http://localhost:8000/storage/products/dharwad-peda.jpg',
        'display_order' => 4,
        'is_active' => true,
    ],
    [
        'id' => 'cat-bengali',
        'slug' => 'bengali-sweets',
        'name_en' => 'Gulab Jamun & Rasgulla',
        'name_te' => 'గులాబ్ జామూన్ & రసగుల్లా',
        'name_hi' => 'गुलाब जामुन एवं रसगुल्ला',
        'description_en' => 'Soft spongy chenna and warm fragrant sugar syrup delights.',
        'icon' => 'Heart',
        'image_url' => 'http://localhost:8000/storage/products/gulab-jamun.jpg',
        'display_order' => 5,
        'is_active' => true,
    ],
    [
        'id' => 'cat-telugu-traditional',
        'slug' => 'telugu-traditional',
        'name_en' => 'Telugu Traditional Sweets',
        'name_te' => 'తెలుగు సాంప్రదాయ మిఠాయిలు (పూతరేకులు)',
        'name_hi' => 'तेलुगु पारंपरिक मिष्ठान (पूथारेकुलु)',
        'description_en' => 'Authentic Andhra heritage sweets including Pootharekulu, Bobbatlu, and Ariselu.',
        'icon' => 'Sparkles',
        'image_url' => 'http://localhost:8000/storage/products/pootharekulu.jpg',
        'display_order' => 6,
        'is_active' => true,
    ],
    [
        'id' => 'cat-sugar-free',
        'slug' => 'sugar-free-sweets',
        'name_en' => 'Sugar Free Sweets',
        'name_te' => 'షుగర్ ఫ్రీ మిఠాయిలు',
        'name_hi' => 'शुगर फ्री मिठाई',
        'description_en' => 'Naturally sweetened treats made with dates, figs (anjeer), and nuts for diabetic-friendly indulgence.',
        'icon' => 'Heart',
        'image_url' => 'http://localhost:8000/storage/products/kaju-katli.jpg',
        'display_order' => 7,
        'is_active' => true,
    ],

    // Snacks & Savouries
    [
        'id' => 'cat-savouries',
        'slug' => 'khara-hot-snacks',
        'name_en' => 'Khara & Hot Snacks',
        'name_te' => 'ఖారా & కారం పిండివంటలు',
        'name_hi' => 'खारा एवं चटपटे स्नैक्स',
        'description_en' => 'Crispy hot savouries, ribbon pakoda, and spiced boondi.',
        'icon' => 'Wheat',
        'image_url' => 'http://localhost:8000/storage/products/mixture.jpg',
        'display_order' => 8,
        'is_active' => true,
    ],
    [
        'id' => 'cat-crunchy',
        'slug' => 'crunchy-treats',
        'name_en' => 'Crunchy Treats (Chekodilu & Murukku)',
        'name_te' => 'చేగోడీలు & మురుకులు',
        'name_hi' => 'कुरकुरे मुरुक्कु एवं चेकोडीलु',
        'description_en' => 'Authentic Andhra Chekodilu, Butter Murukku, and Janthikalu fried in pure groundnut oil.',
        'icon' => 'Wheat',
        'image_url' => 'http://localhost:8000/storage/products/murukku.jpg',
        'display_order' => 9,
        'is_active' => true,
    ],
    [
        'id' => 'cat-mixture',
        'slug' => 'madras-mixture',
        'name_en' => 'Madras Royal Mixture',
        'name_te' => 'మద్రాస్ రాయల్ మిక్చర్',
        'name_hi' => 'मद्रास रॉयल मिक्स्चर',
        'description_en' => 'Premium assortment of fried sev, nuts, curry leaves, and dry fruits.',
        'icon' => 'Wheat',
        'image_url' => 'http://localhost:8000/storage/products/mixture.jpg',
        'display_order' => 10,
        'is_active' => true,
    ],
    [
        'id' => 'cat-podis',
        'slug' => 'karam-podis',
        'name_en' => 'Spicy Powders (Karam Podis)',
        'name_te' => 'ఆంధ్రా కారం పొడులు (ఇడ్లీ, కంది పొడి)',
        'name_hi' => 'पारंपरिक आंध्र कारम पोड़ी',
        'description_en' => 'Stone-ground Telugu spice powders like Idli Karam, Kandi Podi, and Nallakaram.',
        'icon' => 'Flame',
        'image_url' => 'http://localhost:8000/storage/products/avakaya-pickle.jpg',
        'display_order' => 11,
        'is_active' => true,
    ],
    [
        'id' => 'cat-bakery',
        'slug' => 'bakery-cookies',
        'name_en' => 'Bakery & Cookies (Osmania)',
        'name_te' => 'బేకరీ ఉస్మానియా బిస్కెట్లు',
        'name_hi' => 'हैदराबादी उस्मानिया बिस्कुट',
        'description_en' => 'Famous melt-in-mouth Hyderabadi chai biscuits and butter cookies.',
        'icon' => 'Layers',
        'image_url' => 'http://localhost:8000/storage/products/dharwad-peda.jpg',
        'display_order' => 12,
        'is_active' => true,
    ],

    // Pickles
    [
        'id' => 'cat-pickles',
        'slug' => 'traditional-pickles',
        'name_en' => 'Traditional Pickles',
        'name_te' => 'ఆంధ్రా సాంప్రదాయ పచ్చళ్ళు (ఆవకాయ)',
        'name_hi' => 'पारंपरिक आंध्र अचार (आम का अवकाया)',
        'description_en' => 'Heritage Andhra homemade pickles made with cold-pressed sesame oil, Guntur chillies, and raw mangoes.',
        'icon' => 'Flame',
        'image_url' => 'http://localhost:8000/storage/products/avakaya-pickle.jpg',
        'display_order' => 13,
        'is_active' => true,
    ],

    // Gifting
    [
        'id' => 'cat-gift-boxes',
        'slug' => 'gift-boxes',
        'name_en' => 'Festival Gift Boxes',
        'name_te' => 'పండుగ స్పెషల్ గిఫ్ట్ బాక్స్‌లు',
        'name_hi' => 'उत्सव स्पेशल गिफ्ट बॉक्स',
        'description_en' => 'Curated royal assortment of premium sweets and savouries in luxury packaging.',
        'icon' => 'Gift',
        'image_url' => 'http://localhost:8000/storage/products/kaju-katli.jpg',
        'display_order' => 14,
        'is_active' => true,
    ],
];

foreach ($categories as $cat) {
    Category::updateOrCreate(['id' => $cat['id']], $cat);
    echo "Category ready: {$cat['name_en']}\n";
}

// Update existing products with authentic photos
$productUpdates = [
    'prod-kaju-katli' => [
        'name_en' => 'Royal Silver Kaju Katli',
        'image_url' => 'http://localhost:8000/storage/products/kaju-katli.jpg',
        'category_id' => 'cat-kaju'
    ],
    'prod-motichoor-laddu' => [
        'name_en' => 'Pure Cow Ghee Motichoor Laddu',
        'image_url' => 'http://localhost:8000/storage/products/motichoor-laddu.jpg',
        'category_id' => 'cat-laddu'
    ],
    'prod-mysore-pak' => [
        'name_en' => 'Traditional Melt-in-Mouth Ghee Mysore Pak',
        'image_url' => 'http://localhost:8000/storage/products/mysore-pak.jpg',
        'category_id' => 'cat-mysore-pak'
    ],
    'prod-8b5d1b77-d390-499f-964c-d7ba5d41374c' => [
        'name_en' => 'Dry Fruit Rava Laddu',
        'image_url' => 'http://localhost:8000/storage/products/motichoor-laddu.jpg',
        'category_id' => 'cat-laddu'
    ]
];

foreach ($productUpdates as $pid => $data) {
    $p = Product::find($pid);
    if ($p) {
        $p->update($data);
        echo "Updated product: {$p->name_en} with image {$data['image_url']}\n";
    }
}

// Also add a sample Pickle product and sample Pootharekulu product so that every section has mouthwatering items!
if (!Product::where('slug', 'andhra-special-avakaya-pickle')->exists()) {
    $pickle = Product::create([
        'id' => 'prod-avakaya-pickle',
        'slug' => 'andhra-special-avakaya-pickle',
        'category_id' => 'cat-pickles',
        'name_en' => 'Andhra Special Cut Mango Avakaya',
        'name_te' => 'ఆంధ్రా స్పెషల్ ఆవకాయ పచ్చడి',
        'name_hi' => 'आंध्र स्पेशल अवकाया आम का अचार',
        'description_en' => 'Traditional spicy Andhra raw mango pickle made with freshly ground mustard, Guntur red chillies, and pure cold-pressed sesame oil.',
        'description_te' => 'స్వచ్ఛమైన నువ్వుల నూనె, గుంటూరు కారం, ఘాటైన ఆవపిండితో తయారుచేసిన అసలైన ఆవకాయ పచ్చడి.',
        'description_hi' => 'शुद्ध तिल के तेल, गुंटूर मिर्च और राई से तैयार किया गया पारंपरिक तीखा आम का अचार।',
        'image_url' => 'http://localhost:8000/storage/products/avakaya-pickle.jpg',
        'dietary_type' => 'savouries',
        'is_veg' => true,
        'shelf_life_days' => 180,
        'storage_en' => 'Store in a dry ceramic jar using dry spoons.',
        'storage_te' => 'తేమ లేని జాడీలో భద్రపరచండి.',
        'storage_hi' => 'सूखे कांच या मिट्टी के मर्तबान में रखें।',
        'ingredients_en' => 'Raw Mangoes, Sesame Oil, Mustard, Red Chilli Powder, Fenugreek, Garlic, Salt.',
        'allergens' => ['Mustard', 'Sesame'],
        'is_active' => true,
        'is_featured' => true,
        'is_bestseller' => true,
        'rating' => 4.9,
        'review_count' => 186
    ]);
    ProductVariant::create([
        'id' => 'var-avakaya-500',
        'product_id' => $pickle->id,
        'weight_label' => '500g',
        'weight_grams' => 500,
        'price_paise' => 29000,
        'compare_at_price_paise' => 33000,
        'stock_quantity' => 40,
        'is_available' => true,
        'sku' => 'AVK-500G'
    ]);
    ProductVariant::create([
        'id' => 'var-avakaya-1000',
        'product_id' => $pickle->id,
        'weight_label' => '1 kg',
        'weight_grams' => 1000,
        'price_paise' => 55000,
        'compare_at_price_paise' => 62000,
        'stock_quantity' => 25,
        'is_available' => true,
        'sku' => 'AVK-1KG'
    ]);
    echo "Created Avakaya Pickle product!\n";
}

if (!Product::where('slug', 'atreyapuram-ghee-pootharekulu')->exists()) {
    $pootha = Product::create([
        'id' => 'prod-pootharekulu',
        'slug' => 'atreyapuram-ghee-pootharekulu',
        'category_id' => 'cat-telugu-traditional',
        'name_en' => 'Atreyapuram Ghee Dry Fruit Pootharekulu',
        'name_te' => 'ఆత్రేయపురం నేతి డ్రై ఫ్రూట్ పూతరేకులు',
        'name_hi' => 'आत्रेयपुरम शुद्ध घी पूथारेकुलु (पेपर स्वीट)',
        'description_en' => 'World-famous paper-thin rice starch wafer sweet rolled with pure desi cow ghee, fine jaggery, cardamom, and chopped cashews & pistachios.',
        'description_te' => 'ఆత్రేయపురం ప్రసిద్ధ సాంప్రదాయ పూతరేకులు. స్వచ్ఛమైన ఆవు నెయ్యి, బెల్లం మరియు డ్రై ఫ్రూట్లతో తయారుచేయబడింది.',
        'description_hi' => 'विश्व प्रसिद्ध पेपर स्वीट, शुद्ध गाय के घी, गुड़ और मेवों से भरी पारंपरिक मिठाई।',
        'image_url' => 'http://localhost:8000/storage/products/pootharekulu.jpg',
        'dietary_type' => 'pure_ghee',
        'is_veg' => true,
        'shelf_life_days' => 30,
        'storage_en' => 'Store in an airtight container at room temperature.',
        'storage_te' => 'గాలి తగలకుండా డబ్బాలో నిల్వ చేయండి.',
        'storage_hi' => 'कमरे के तापमान पर एयरटाइट डिब्बे में रखें।',
        'ingredients_en' => 'Rice Starch Wafers, Pure Cow Ghee, Organic Jaggery, Cashews, Pistachios, Cardamom.',
        'allergens' => ['Dairy', 'Cashews', 'Pistachios'],
        'is_active' => true,
        'is_featured' => true,
        'is_bestseller' => true,
        'rating' => 5.0,
        'review_count' => 412
    ]);
    ProductVariant::create([
        'id' => 'var-pootha-10pcs',
        'product_id' => $pootha->id,
        'weight_label' => 'Pack of 10 Pcs',
        'weight_grams' => 350,
        'price_paise' => 36000,
        'compare_at_price_paise' => 40000,
        'stock_quantity' => 50,
        'is_available' => true,
        'sku' => 'PTR-10PC'
    ]);
    echo "Created Pootharekulu product!\n";
}

if (!Product::where('slug', 'butter-murukku-janthikalu')->exists()) {
    $murukku = Product::create([
        'id' => 'prod-butter-murukku',
        'slug' => 'butter-murukku-janthikalu',
        'category_id' => 'cat-crunchy',
        'name_en' => 'Andhra Butter Murukku (Janthikalu)',
        'name_te' => 'ఆంధ్రా వెన్న మురుకులు (జంతికలు)',
        'name_hi' => 'आंध्र बटर मुरुक्कु (जंतिकालु)',
        'description_en' => 'Crispy melt-in-mouth traditional spiral savoury snack made from roasted rice flour, white butter (benne), cumin seeds, and asafetida.',
        'description_te' => 'తాజా వెన్న, జీలకర్ర, ఇంగువతో గుల్లగా చేసిన రుచికరమైన జంతికలు.',
        'description_hi' => 'मक्खन, जीरा और हींग के तड़के से तैयार कुरकुरा दक्षिण भारतीय नाश्ता।',
        'image_url' => 'http://localhost:8000/storage/products/murukku.jpg',
        'dietary_type' => 'savouries',
        'is_veg' => true,
        'shelf_life_days' => 45,
        'storage_en' => 'Store in an airtight container away from moisture.',
        'storage_te' => 'తేమ లేని డబ్బాలో భద్రపరచండి.',
        'storage_hi' => 'एयरटाइट जार में रखें।',
        'ingredients_en' => 'Rice Flour, Urad Dal Flour, Fresh Butter, Cumin, Hing, Salt.',
        'allergens' => ['Dairy'],
        'is_active' => true,
        'is_featured' => true,
        'is_bestseller' => true,
        'rating' => 4.8,
        'review_count' => 98
    ]);
    ProductVariant::create([
        'id' => 'var-murukku-500',
        'product_id' => $murukku->id,
        'weight_label' => '500g',
        'weight_grams' => 500,
        'price_paise' => 22000,
        'compare_at_price_paise' => 25000,
        'stock_quantity' => 60,
        'is_available' => true,
        'sku' => 'MRK-500G'
    ]);
    echo "Created Butter Murukku product!\n";
}

echo "Database successfully refreshed with categories, products, and authentic images!\n";
