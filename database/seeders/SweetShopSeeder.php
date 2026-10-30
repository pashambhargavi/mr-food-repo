<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\StoreSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;

class SweetShopSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin & Staff User
        User::updateOrCreate(
            ['email' => 'admin@anandamsweets.in'],
            [
                'name' => 'Sri Krishna (Owner/Admin)',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Categories
        $categories = [
            [
                'id' => 'cat-kaju',
                'slug' => 'kaju-sweets',
                'name_en' => 'Kaju Katli & Cashew Sweets',
                'name_te' => 'కాజూ కత్లీ & జీడిపప్పు మిఠాయిలు',
                'name_hi' => 'काजू कतली एवं काजू मिष्ठान',
                'description_en' => 'Made with premium Goan cashews and pure silver vark.',
                'icon' => 'Sparkles',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=80',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'id' => 'cat-laddu',
                'slug' => 'laddu-specials',
                'name_en' => 'Pure Ghee Laddus',
                'name_te' => 'స్వచ్ఛమైన నేతి లడ్డూలు',
                'name_hi' => 'शुद्ध घी के लड्डू',
                'description_en' => 'Melt-in-mouth golden laddus prepared with aromatic desi ghee.',
                'icon' => 'CircleDot',
                'image_url' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=600&q=80',
                'display_order' => 2,
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
                'image_url' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?auto=format&fit=crop&w=600&q=80',
                'display_order' => 3,
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
                'image_url' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'id' => 'cat-bengali',
                'slug' => 'bengali-sweets',
                'name_en' => 'Gulab Jamun & Rasgulla',
                'name_te' => 'గులాబ్ జామూన్ & రసగుల్లా',
                'name_hi' => 'गुलाब जामुन एवं रसगुल्ला',
                'description_en' => 'Soft spongy cottage cheese chenna and warm syrup delights.',
                'icon' => 'Heart',
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=600&q=80',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'id' => 'cat-savouries',
                'slug' => 'snacks-and-savouries',
                'name_en' => 'Snacks & Savouries',
                'name_te' => 'కారం పిండివంటలు & మిక్చర్',
                'name_hi' => 'नमकीन एवं चाट स्नैक्स',
                'description_en' => 'Crunchy South Indian mixtures, murukku, and spiced cashews.',
                'icon' => 'Wheat',
                'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=80',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'id' => 'cat-gift-boxes',
                'slug' => 'gift-boxes',
                'name_en' => 'Festival Gift Boxes',
                'name_te' => 'పండుగ స్పెషల్ గిఫ్ట్ బాక్స్‌లు',
                'name_hi' => 'उत्सव स्पेशल गिफ्ट बॉक्स',
                'description_en' => 'Curated royal assortment of premium sweets for celebrations.',
                'icon' => 'Gift',
                'image_url' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=600&q=80',
                'display_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['id' => $cat['id']], $cat);
        }

        // 3. Products & Variants
        $products = [
            [
                'data' => [
                    'id' => 'prod-kaju-katli',
                    'slug' => 'royal-kaju-katli',
                    'category_id' => 'cat-kaju',
                    'name_en' => 'Royal Silver Kaju Katli',
                    'name_te' => 'రాయల్ సిల్వర్ కాజూ కత్లీ',
                    'name_hi' => 'शाही चांदी वर्क काजू कतली',
                    'description_en' => 'The crown jewel of Indian sweets. Made from finely ground premium Goan cashews, mild organic sugar, and delicate edible pure silver foil (vark).',
                    'description_te' => 'గోవా నాణ్యమైన జీడిపప్పు, స్వచ్ఛమైన చక్కెర మరియు వెండి వర్క్‌తో సాంప్రదాయ పద్ధతిలో తయారుచేసిన మృదువైన కాజూ కత్లీ.',
                    'description_hi' => 'प्रीमियम काजू और शुद्ध चांदी वर्क से तैयार की गई पारंपरिक काजू कतली, जो हर उत्सव की शान है।',
                    'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=800&q=80',
                    'dietary_type' => 'dry_fruit',
                    'is_veg' => true,
                    'shelf_life_days' => 20,
                    'storage_en' => 'Store in an airtight container at room temperature away from direct sunlight.',
                    'storage_te' => 'గాలి చొరబడని డబ్బాలో ఎండ తగలకుండా గది ఉష్ణోగ్రత వద్ద నిల్వ చేయండి.',
                    'storage_hi' => 'सीधी धूप से दूर कमरे के सामान्य तापमान पर एयरटाइट डिब्बे में रखें।',
                    'ingredients_en' => 'Premium Cashews (72%), Fine Sugar, Cardamom, Edible Silver Leaf (Vark).',
                    'ingredients_te' => 'నాణ్యమైన జీడిపప్పు (72%), చక్కెర, యాలకులు, తినదగిన వెండి రేకు (వర్క్).',
                    'ingredients_hi' => 'उत्कृष्ट काजू (72%), शक्कर, इलायची, खाद्य चांदी वर्क।',
                    'allergens' => ['Tree Nuts (Cashews)'],
                    'is_active' => true,
                    'is_featured' => true,
                    'is_bestseller' => true,
                    'rating' => 4.9,
                    'review_count' => 342,
                ],
                'variants' => [
                    ['id' => 'var-kaju-250', 'weight_label' => '250g', 'weight_grams' => 250, 'price_paise' => 27500, 'compare_at_price_paise' => 30000, 'stock_quantity' => 45, 'sku' => 'KK-250G'],
                    ['id' => 'var-kaju-500', 'weight_label' => '500g', 'weight_grams' => 500, 'price_paise' => 53000, 'compare_at_price_paise' => 60000, 'stock_quantity' => 60, 'sku' => 'KK-500G'],
                    ['id' => 'var-kaju-1000', 'weight_label' => '1 kg', 'weight_grams' => 1000, 'price_paise' => 102000, 'compare_at_price_paise' => 120000, 'stock_quantity' => 30, 'sku' => 'KK-1KG'],
                ],
            ],
            [
                'data' => [
                    'id' => 'prod-motichoor-laddu',
                    'slug' => 'pure-ghee-motichoor-laddu',
                    'category_id' => 'cat-laddu',
                    'name_en' => 'Pure Cow Ghee Motichoor Laddu',
                    'name_te' => 'స్వచ్ఛమైన ఆవు నేతి మోతీచూర్ లడ్డూ',
                    'name_hi' => 'शुद्ध गाय के घी का मोतीचूर लड्डू',
                    'description_en' => 'Tiny pearls of gram flour fried in 100% pure desi cow ghee, steeped in fragrant saffron-infused cardamom sugar syrup.',
                    'description_te' => 'స్వచ్ఛమైన ఆవు నేతిలో వేయించిన సన్నని బూందీ, కుంకుమపువ్వు పాకంలో నానబెట్టి తయారుచేసిన రసవత్తరమైన మోతీచూర్ లడ్డూలు.',
                    'description_hi' => 'शुद्ध देसी घी में तली बारीक बूंदी, केसर और इलायची की चाशनी में डूबी अत्यंत रसीली एवं स्वादिष्ट मिठाई।',
                    'image_url' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=800&q=80',
                    'dietary_type' => 'pure_ghee',
                    'is_veg' => true,
                    'shelf_life_days' => 10,
                    'storage_en' => 'Store in a cool dry place. Best consumed within 10 days of packing.',
                    'storage_te' => 'చల్లని, పొడి ప్రదేశంలో నిల్వ చేయండి.',
                    'storage_hi' => 'ठंडी और सूखी जगह पर रखें।',
                    'ingredients_en' => 'Gram Flour (Besan), Pure Cow Desi Ghee, Sugar, Saffron, Green Cardamom, Magaz, Pistachio.',
                    'ingredients_te' => 'శనగపిండి, స్వచ్ఛమైన ఆవు నెయ్యి, చక్కెర, కుంకుమపువ్వు, యాలకులు, పుచ్చ గింజలు, పిస్తా.',
                    'ingredients_hi' => 'बेसन, शुद्ध देसी गाय का घी, शक्कर, कश्मीरी केसर, छोटी इलायची, मगज, पिस्ता।',
                    'allergens' => ['Dairy (Ghee)', 'Nuts (Pistachio)'],
                    'is_active' => true,
                    'is_featured' => true,
                    'is_bestseller' => true,
                    'rating' => 4.9,
                    'review_count' => 512,
                ],
                'variants' => [
                    ['id' => 'var-moti-250', 'weight_label' => '250g', 'weight_grams' => 250, 'price_paise' => 18000, 'compare_at_price_paise' => 20000, 'stock_quantity' => 50, 'sku' => 'ML-250G'],
                    ['id' => 'var-moti-500', 'weight_label' => '500g', 'weight_grams' => 500, 'price_paise' => 35000, 'compare_at_price_paise' => 40000, 'stock_quantity' => 75, 'sku' => 'ML-500G'],
                    ['id' => 'var-moti-1000', 'weight_label' => '1 kg', 'weight_grams' => 1000, 'price_paise' => 68000, 'compare_at_price_paise' => 76000, 'stock_quantity' => 40, 'sku' => 'ML-1KG'],
                ],
            ],
            [
                'data' => [
                    'id' => 'prod-mysore-pak',
                    'slug' => 'special-ghee-mysore-pak',
                    'category_id' => 'cat-mysore-pak',
                    'name_en' => 'Traditional Melt-in-Mouth Ghee Mysore Pak',
                    'name_te' => 'సాంప్రదాయ నేతి మైసూర్ పాక్',
                    'name_hi' => 'पारंपरिक शुद्ध घी मैसूर पाक',
                    'description_en' => 'The legendary royal recipe from South India. Soft, golden-hued, porous texture that dissolves instantly on the palate.',
                    'description_te' => 'స్వచ్ఛమైన నేతి సువాసనతో, నోట్లో వేస్తే అలా కరిగిపోయే అసలైన సాంప్రదాయ మైసూర్ పాక్.',
                    'description_hi' => 'शुद्ध देसी घी और भुने बेसन की सौंधी खुशबू वाला मैसूर पाक जो मुंह में रखते ही घुल जाता है।',
                    'image_url' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=800&q=80',
                    'dietary_type' => 'pure_ghee',
                    'is_veg' => true,
                    'shelf_life_days' => 25,
                    'storage_en' => 'Store at room temperature in a dry container.',
                    'storage_te' => 'గది ఉష్ణోగ్రత వద్ద పొడి డబ్బాలో నిల్వ చేయండి.',
                    'storage_hi' => 'कमरे के सामान्य तापमान पर सूखे डिब्बे में रखें।',
                    'ingredients_en' => 'Roasted Gram Flour, 100% Pure Desi Ghee, Sugar.',
                    'ingredients_te' => 'వేయించిన శనగపిండి, స్వచ్ఛమైన నెయ్యి, చక్కెర.',
                    'ingredients_hi' => 'भुना हुआ बेसन, शुद्ध देसी घी, शक्कर।',
                    'allergens' => ['Dairy (Ghee)'],
                    'is_active' => true,
                    'is_featured' => true,
                    'is_bestseller' => true,
                    'rating' => 4.8,
                    'review_count' => 288,
                ],
                'variants' => [
                    ['id' => 'var-mp-250', 'weight_label' => '250g', 'weight_grams' => 250, 'price_paise' => 21000, 'compare_at_price_paise' => 24000, 'stock_quantity' => 35, 'sku' => 'MP-250G'],
                    ['id' => 'var-mp-500', 'weight_label' => '500g', 'weight_grams' => 500, 'price_paise' => 40000, 'compare_at_price_paise' => 46000, 'stock_quantity' => 50, 'sku' => 'MP-500G'],
                    ['id' => 'var-mp-1000', 'weight_label' => '1 kg', 'weight_grams' => 1000, 'price_paise' => 78000, 'compare_at_price_paise' => 90000, 'stock_quantity' => 25, 'sku' => 'MP-1KG'],
                ],
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['id' => $p['data']['id']], $p['data']);
            foreach ($p['variants'] as $v) {
                ProductVariant::updateOrCreate(
                    ['id' => $v['id']],
                    array_merge($v, ['product_id' => $p['data']['id']])
                );
            }
        }

        // 4. Coupons
        Coupon::updateOrCreate(
            ['code' => 'SWEET50'],
            [
                'id' => 'c-welcome',
                'discount_type' => 'fixed',
                'discount_value' => 5000, // ₹50
                'min_order_amount_paise' => 40000,
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
                'usage_count' => 142,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'FESTIVE10'],
            [
                'id' => 'c-festive',
                'discount_type' => 'percentage',
                'discount_value' => 10, // 10%
                'min_order_amount_paise' => 60000,
                'max_discount_paise' => 15000,
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
                'usage_count' => 88,
            ]
        );

        // 5. Store Settings
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'shop_name' => 'Anandam Sweets & Savouries',
                'shop_name_te' => 'Anandam Sweets & Savouries',
                'shop_name_hi' => 'Anandam Sweets & Savouries',
                'tagline' => 'Bringing Sweetness to Every Celebration',
                'tagline_te' => 'Bringing Sweetness to Every Celebration',
                'tagline_hi' => 'Bringing Sweetness to Every Celebration',
                'phone' => '+91 98490 12345',
                'whatsapp' => '+91 98490 12345',
                'email' => 'orders@anandamsweets.in',
                'address' => 'Shop #14, Royal Heritage Square, Near Main Temple, MG Road, Hyderabad, Telangana 500003',
                'google_maps_url' => 'https://maps.google.com/?q=Hyderabad',
                'opening_hours' => 'Daily: 8:00 AM – 10:30 PM (365 Days Open)',
                'opening_hours_te' => 'Daily: 8:00 AM – 10:30 PM (365 Days Open)',
                'opening_hours_hi' => 'Daily: 8:00 AM – 10:30 PM (365 Days Open)',
                'min_order_paise' => 20000,
                'delivery_fee_paise' => 4000,
                'free_delivery_threshold_paise' => 50000,
                'delivery_pincodes' => ['500001', '500002', '500003', '500004', '500016', '500028', '500034', '500081', '500082'],
                'cod_enabled' => true,
                'upi_enabled' => true,
                'upi_id' => 'anandamsweets@upi',
                'gstin' => '36AAAAA0000A1Z5',
                'fssai_license' => '13622011000892',
            ]
        );

        // 6. Demo Orders
        $order1 = Order::create([
            'order_number' => 'MITHAI-2026-8941',
            'customer_name' => 'Bhargavi Sharma',
            'customer_phone' => '9849098765',
            'customer_email' => 'customer@anandamsweets.in',
            'fulfilment_type' => 'delivery',
            'status' => 'preparing',
            'payment_method' => 'upi',
            'payment_status' => 'paid',
            'payment_id' => 'pay_UPI_9876543210',
            'subtotal_paise' => 88000,
            'discount_paise' => 5000,
            'delivery_fee_paise' => 0,
            'total_paise' => 83000,
            'coupon_code' => 'SWEET50',
            'delivery_address' => [
                'recipient_name' => 'Bhargavi Sharma',
                'phone' => '9849098765',
                'address_line1' => 'Flat 402, Sai Nilayam Apartments, Road 12',
                'landmark' => 'Near Lotus Pond',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'pincode' => '500034',
            ],
            'delivery_notes' => 'Pack in festive silver bag',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 'prod-kaju-katli',
            'product_name' => 'Royal Silver Kaju Katli',
            'variant_id' => 'var-kaju-500',
            'variant_weight' => '500g',
            'unit_price_paise' => 53000,
            'quantity' => 1,
            'total_price_paise' => 53000,
            'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=800&q=80',
        ]);

        OrderStatusHistory::create([
            'order_id' => $order1->id,
            'status' => 'placed',
            'note' => 'Order placed via UPI',
        ]);
        OrderStatusHistory::create([
            'order_id' => $order1->id,
            'status' => 'confirmed',
            'note' => 'Accepted by shop manager',
        ]);
        OrderStatusHistory::create([
            'order_id' => $order1->id,
            'status' => 'preparing',
            'note' => 'Fresh sweets packing in kitchen',
        ]);
    }
}
