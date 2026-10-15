<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Account
        $adminEmail = env('ADMIN_DEFAULT_EMAIL', 'admin@floristgift.com');
        $adminPass = env('ADMIN_DEFAULT_PASSWORD', 'Admin#Florist2026!');
        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_DEFAULT_NAME', 'Owner Florist & Gift'),
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make($adminPass),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // 2. Demo Customer Account
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Siti Rahmawati',
                'phone' => '085711223344',
                'role' => 'customer',
                'password' => Hash::make('Customer123!'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Address for Demo Customer
        $customer->addresses()->firstOrCreate(
            ['label' => 'Rumah'],
            [
                'recipient_name' => 'Siti Rahmawati',
                'recipient_phone' => '085711223344',
                'full_address' => 'Jl. Mawar Melati No. 45, RT 03 / RW 07, Kebayoran Baru',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'province' => 'DKI Jakarta',
                'postal_code' => '12150',
                'notes' => 'Pagar putih, rumah tingkat 2 seberang minimarket',
                'is_default' => true,
            ]
        );

        // 3. Store Settings
        $settings = [
            'store_name' => 'MocYy',
            'store_tagline' => 'Buket Bunga & Hadiah Eksklusif',
            'store_phone' => '+62 813-5487-734',
            'store_whatsapp' => '628135487734',
            'store_email' => 'halo@mocyy.com',
            'store_address' => 'Jl. Senopati Indah No. 88, Kebayoran Baru, Jakarta Selatan 12190',
            'store_instagram' => '@mocyy.official',
            'working_hours' => 'Senin - Minggu: 08.00 - 21.00 WIB',
            'payment_expiry_minutes' => '120',
            'auto_cancel_unpaid_hours' => '24',
        ];
        foreach ($settings as $key => $val) {
            StoreSetting::set($key, $val);
        }

        // 4. Shipping Methods
        $shippings = [
            [
                'name' => 'Kurir Instan Toko / GoSend / GrabExpress',
                'code' => 'instant',
                'description' => 'Pengiriman khusus area Jakarta & sekitarnya, aman untuk bunga hidup dan bucket ukuran besar.',
                'cost' => 25000.00,
                'estimated_time' => '1 - 3 Jam',
                'is_active' => true,
            ],
            [
                'name' => 'Ekspedisi Reguler (JNE YES / SiCepat)',
                'code' => 'regular',
                'description' => 'Packing kardus tebal + bubble wrap khusus artificial bouquet & hampers snack.',
                'cost' => 15000.00,
                'estimated_time' => '1 - 2 Hari',
                'is_active' => true,
            ],
            [
                'name' => 'Ambil Sendiri di Toko (Self Pick-up)',
                'code' => 'pickup',
                'description' => 'Ambil langsung ke gallery store Senopati tanpa biaya ongkir.',
                'cost' => 0.00,
                'estimated_time' => 'Sesuai Jam Buka',
                'is_active' => true,
            ],
        ];
        foreach ($shippings as $s) {
            ShippingMethod::updateOrCreate(['code' => $s['code']], $s);
        }

        // 5. Payment Methods (Eksklusif QRIS MocYy, Kerajinan)
        $paymentMethods = [
            [
                'name' => 'QRIS Standar Pembayaran Nasional (MocYy, Kerajinan)',
                'code' => 'qris',
                'account_name' => 'MocYy, Kerajinan',
                'account_number' => 'NMID: ID1026613806474 (A01)',
                'qr_image_path' => '/images/qris-mocyan.jpg',
                'instructions' => "1. Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, ShopeePay, DANA).\n2. Pilih menu 'Bayar' / 'Scan QRIS'.\n3. Scan kode QR MocYy, Kerajinan yang tertera pada layar.\n4. Masukkan nominal tagihan sesuai total pesanan secara tepat.\n5. Selesaikan transaksi dan simpan bukti transfer (screenshot / resi).\n6. Konfirmasi melalui WhatsApp atau unggah bukti transfer ke admin.",
                'expiry_minutes' => 120,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'DANA Manual Transfer',
                'code' => 'dana',
                'account_name' => 'MocYy, Kerajinan',
                'account_number' => '0812-3456-7890',
                'qr_image_path' => '/images/qris-mocyan.jpg',
                'instructions' => "Gunakan pembayaran utama QRIS MocYy, Kerajinan.",
                'expiry_minutes' => 120,
                'is_active' => false,
                'sort_order' => 2,
            ],
        ];
        foreach ($paymentMethods as $pm) {
            PaymentMethod::updateOrCreate(['code' => $pm['code']], $pm);
        }

        // 6. Categories
        $categoriesData = [
            [
                'name' => 'Bucket Bunga',
                'slug' => 'bucket-bunga',
                'description' => 'Rangkaian mawar premium, bunga lili, krisan, dan baby breath segar maupun artificial.',
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Bucket Snack',
                'slug' => 'bucket-snack',
                'description' => 'Kombinasi aneka cokelat SilverQueen, Ferrero Rocher, biskuit, dan pocky lezat nan cantik.',
                'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Money Bouquet',
                'slug' => 'money-bouquet',
                'description' => 'Rangkaian uang asli tersusun elegan dengan pelindung mika steril dan aksen mawar mewah.',
                'image' => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Bucket Boneka',
                'slug' => 'bucket-boneka',
                'description' => 'Teddy bear lucu dan karakter favorit berpadu dengan bunga pastel manis.',
                'image' => 'https://images.unsplash.com/photo-1559454403-b8fb88521f11?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Bucket Wisuda',
                'slug' => 'bucket-wisuda',
                'description' => 'Hadiah kelulusan terbaik lengkap dengan boneka toga, selempang custom, dan bunga ceria.',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Hampers & Gift Box',
                'slug' => 'hampers-dan-gift-box',
                'description' => 'Kotak kado eksklusif berisi lilin aromaterapi, mug keramik, dan personalized card.',
                'image' => 'https://images.unsplash.com/photo-1513885535751-8b9238bd345a?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Bucket Ulang Tahun',
                'slug' => 'bucket-ulang-tahun',
                'description' => 'Rangkaian semarak dengan hiasan balon foil, pita glitter, dan topper selamat ulang tahun.',
                'image' => 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Hadiah Anniversary',
                'slug' => 'hadiah-anniversary',
                'description' => 'Ungkapan cinta tulus dengan red rose velvet, preserved flower abadi dalam dome kaca.',
                'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Bucket Custom',
                'slug' => 'bucket-custom',
                'description' => 'Bebas request isi, kombinasi tema warna kertas, dan pesan personal tak terbatas.',
                'image' => 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Produk Musiman',
                'slug' => 'produk-musiman',
                'description' => 'Koleksi edisi spesial Hari Ibu, Valentine, Lebaran, Natal, dan Tahun Baru.',
                'image' => 'https://images.unsplash.com/photo-1582794543139-8ac9cb0f7b11?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 7. Products (Minimal 16 produk beragam dengan detail lengkap)
        $productsData = [
            // Bunga
            [
                'category_slug' => 'bucket-bunga',
                'name' => 'Dusty Rose Romance Bouquet',
                'sku' => 'BKT-ROSE-01',
                'price' => 320000.00,
                'discount_price' => 285000.00,
                'stock' => 25,
                'weight_grams' => 600,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Kombinasi 12 tangkai mawar premium warna dusty pink dan putih salju, dihiasi daun eucalyptus dan baby breath harum. Dibungkus kertas premium waterproof bernuansa nude rose gold.',
                'main_image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Ukuran', 'variant_value' => 'Medium (12 Tangkai)', 'additional_price' => 0.00, 'stock' => 15],
                    ['variant_name' => 'Ukuran', 'variant_value' => 'Large (20 Tangkai)', 'additional_price' => 120000.00, 'stock' => 10],
                ]
            ],
            [
                'category_slug' => 'bucket-bunga',
                'name' => 'Pure White Lily & Hydrangea Elegance',
                'sku' => 'BKT-LILY-02',
                'price' => 450000.00,
                'discount_price' => null,
                'stock' => 18,
                'weight_grams' => 750,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Rangkaian mewah bunga Lili Casablanca wangi dipadukan dengan hydrangea putih kelopak besar dan pita satin champagne. Lambang ketulusan dan keanggunan sejati.',
                'main_image' => 'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Warna Kertas Wrapping', 'variant_value' => 'Warm White & Gold', 'additional_price' => 0.00, 'stock' => 10],
                    ['variant_name' => 'Warna Kertas Wrapping', 'variant_value' => 'Dusty Pink & Sage', 'additional_price' => 15000.00, 'stock' => 8],
                ]
            ],
            [
                'category_slug' => 'bucket-bunga',
                'name' => 'Sunlight Sunflower Cheerful Bucket',
                'sku' => 'BKT-SUN-03',
                'price' => 240000.00,
                'discount_price' => 210000.00,
                'stock' => 30,
                'weight_grams' => 500,
                'is_featured' => false,
                'lead_time_days' => 1,
                'description' => 'Bunga matahari segar mekar sempurna berpadu mawar kuning pastel dan daun ruscus. Memberikan energi kebahagiaan, motivasi, dan kehangatan bagi penerima.',
                'main_image' => 'https://images.unsplash.com/photo-1597848212624-a19eb35e2651?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            // Snack
            [
                'category_slug' => 'bucket-snack',
                'name' => 'Ferrero Rocher & SilverQueen Deluxe',
                'sku' => 'SNK-CHOC-01',
                'price' => 275000.00,
                'discount_price' => 249000.00,
                'stock' => 40,
                'weight_grams' => 800,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Bucket cokelat premium berisi 8 butir Ferrero Rocher emas, 3 batang SilverQueen chunky bar, dan sentuhan artificial baby breath. Sangat disukai untuk hadiah kekasih dan sahabat.',
                'main_image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Ukuran', 'variant_value' => 'Standard Box', 'additional_price' => 0.00, 'stock' => 25],
                    ['variant_name' => 'Ukuran', 'variant_value' => 'Jumbo Party Size', 'additional_price' => 95000.00, 'stock' => 15],
                ]
            ],
            [
                'category_slug' => 'bucket-snack',
                'name' => 'Pastel Sweet Pocky & Marshmallow Tower',
                'sku' => 'SNK-PCK-02',
                'price' => 165000.00,
                'discount_price' => null,
                'stock' => 35,
                'weight_grams' => 550,
                'is_featured' => false,
                'lead_time_days' => 1,
                'description' => 'Susunan 6 box Pocky strawberry dan cookies and cream berpadu permen marshmallow lembut. Dibalut warna pink pastel dan pita satin lembut yang menggemaskan.',
                'main_image' => 'https://images.unsplash.com/photo-1582293041079-7814c2f12063?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            // Money Bouquet
            [
                'category_slug' => 'money-bouquet',
                'name' => 'Royal Wealth Emerald Money Bouquet (20 Lembar)',
                'sku' => 'MNY-EMR-01',
                'price' => 220000.00, // jasa pembuatan & ornamen
                'discount_price' => 199000.00,
                'stock' => 20,
                'weight_grams' => 600,
                'is_featured' => true,
                'lead_time_days' => 2,
                'description' => 'Rangkaian money bouquet artistik berisi slot 20 lembar uang. Uang dilapisi plastik mika steril tanpa distaples atau dilem sehingga kondisi fisik uang tetap 100% terjaga dan mulus.',
                'main_image' => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Pilihan Pecahan Slot', 'variant_value' => 'Slot Pecahan Rp 50.000', 'additional_price' => 0.00, 'stock' => 10],
                    ['variant_name' => 'Pilihan Pecahan Slot', 'variant_value' => 'Slot Pecahan Rp 100.000', 'additional_price' => 0.00, 'stock' => 10],
                ]
            ],
            [
                'category_slug' => 'money-bouquet',
                'name' => 'Blossom Fortune 30-Slots Tower Bouquet',
                'sku' => 'MNY-BLS-02',
                'price' => 290000.00,
                'discount_price' => null,
                'stock' => 15,
                'weight_grams' => 700,
                'is_featured' => false,
                'lead_time_days' => 2,
                'description' => 'Bucket uang model mekar melingkar 30 lembar uang dihiasi mawar sabun merah wangi di bagian tengah. Tampil megah dan sangat berkesan untuk kado orang tua atau pasangan.',
                'main_image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            // Boneka
            [
                'category_slug' => 'bucket-boneka',
                'name' => 'Fluffy Teddy Bear with Cotton Flower',
                'sku' => 'BNK-TED-01',
                'price' => 195000.00,
                'discount_price' => 175000.00,
                'stock' => 30,
                'weight_grams' => 500,
                'is_featured' => false,
                'lead_time_days' => 1,
                'description' => 'Boneka teddy bear premium dengan bulu sangat halus memegang buket mini, dipadukan bunga kapas asli dan bunny tail dried flower bernuansa warm cream.',
                'main_image' => 'https://images.unsplash.com/photo-1559454403-b8fb88521f11?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Warna Boneka', 'variant_value' => 'Cream White', 'additional_price' => 0.00, 'stock' => 15],
                    ['variant_name' => 'Warna Boneka', 'variant_value' => 'Soft Brown', 'additional_price' => 0.00, 'stock' => 15],
                ]
            ],
            // Wisuda
            [
                'category_slug' => 'bucket-wisuda',
                'name' => 'Graduation Grandeur Bear & Sunflower',
                'sku' => 'WSD-GRD-01',
                'price' => 260000.00,
                'discount_price' => 235000.00,
                'stock' => 45,
                'weight_grams' => 650,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Hadiah kelulusan terbaik! Dilengkapi boneka beruang toga lengkap dengan selempang nama wisudawan custom, bunga matahari artificial cerah, dan ijazah gulung mini.',
                'main_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Warna Kerah Toga', 'variant_value' => 'Kuning Emas', 'additional_price' => 0.00, 'stock' => 20],
                    ['variant_name' => 'Warna Kerah Toga', 'variant_value' => 'Biru Royal', 'additional_price' => 0.00, 'stock' => 15],
                    ['variant_name' => 'Warna Kerah Toga', 'variant_value' => 'Merah Marun', 'additional_price' => 0.00, 'stock' => 10],
                ]
            ],
            // Hampers & Gift Box
            [
                'category_slug' => 'hampers-dan-gift-box',
                'name' => 'Serenity Spa & Scented Candle Gift Box',
                'sku' => 'HMP-SPA-01',
                'price' => 380000.00,
                'discount_price' => 345000.00,
                'stock' => 20,
                'weight_grams' => 1200,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Gift box kayu pinus eksklusif berisi lilin aromaterapi soy wax lavender, bath salt mawar, silk eye mask, greeting card letterpress, dan dried flower posy.',
                'main_image' => 'https://images.unsplash.com/photo-1513885535751-8b9238bd345a?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            [
                'category_slug' => 'hampers-dan-gift-box',
                'name' => 'Golden Luxury Tea & Coffee Hampers',
                'sku' => 'HMP-TEA-02',
                'price' => 490000.00,
                'discount_price' => null,
                'stock' => 15,
                'weight_grams' => 1500,
                'is_featured' => false,
                'lead_time_days' => 1,
                'description' => 'Artisan ceramic mug buatan tangan, tin can specialty artisan tea earl grey, honey dipper kayu, dan organic cookies butterscotch dalam hardbox berhias pita velvet.',
                'main_image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            // Ulang Tahun
            [
                'category_slug' => 'bucket-ulang-tahun',
                'name' => 'Sparkling Birthday Hot Air Balloon Bucket',
                'sku' => 'ULT-BLN-01',
                'price' => 350000.00,
                'discount_price' => 315000.00,
                'stock' => 18,
                'weight_grams' => 900,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Balon PVC transparan berisi kelopak mawar dan lampu LED fairy lights berkilau, ditopang box bucket berisi cokelat dan bunga segar. Bisa request tulisan nama di balon!',
                'main_image' => 'https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Warna Nuansa Balon', 'variant_value' => 'Rose Gold & Pink', 'additional_price' => 0.00, 'stock' => 10],
                    ['variant_name' => 'Warna Nuansa Balon', 'variant_value' => 'Navy Blue & Silver', 'additional_price' => 0.00, 'stock' => 8],
                ]
            ],
            // Anniversary
            [
                'category_slug' => 'hadiah-anniversary',
                'name' => 'Everlasting Preserved Red Rose in Glass Dome',
                'sku' => 'ANV-DME-01',
                'price' => 520000.00,
                'discount_price' => 485000.00,
                'stock' => 12,
                'weight_grams' => 1100,
                'is_featured' => true,
                'lead_time_days' => 1,
                'description' => 'Bunga mawar merah asli yang diawetkan khusus agar tahan hingga 3-5 tahun tanpa perlu disiram air. Berada di dalam dome kaca kristal dengan alas kayu jati dan fairy light warm glow.',
                'main_image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            [
                'category_slug' => 'hadiah-anniversary',
                'name' => 'Velvet 99 Red Roses Grandeur Cascade',
                'sku' => 'ANV-99R-02',
                'price' => 1250000.00,
                'discount_price' => 1150000.00,
                'stock' => 8,
                'weight_grams' => 2500,
                'is_featured' => true,
                'lead_time_days' => 2,
                'description' => 'Karya agung 99 tangkai mawar merah beludru segar impor melambangkan cinta abadi yang tak lekang waktu. Buket raksasa memukau yang menghadirkan momen lamaran atau hari jadi tak terlupakan.',
                'main_image' => 'https://images.unsplash.com/photo-1518709779341-56cf4535e94b?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ],
            // Custom
            [
                'category_slug' => 'bucket-custom',
                'name' => 'Custom Request Masterpiece Bouquet',
                'sku' => 'CST-REQ-01',
                'price' => 200000.00,
                'discount_price' => null,
                'stock' => 50,
                'weight_grams' => 600,
                'is_featured' => false,
                'lead_time_days' => 2,
                'description' => 'Jasa merangkai kado khusus sesuai ide kreatif Anda. Masukkan catatan berupa barang yang ingin dirangkai (skincare, foto polaroid, hijab, permen, aksesoris) dan florist kami akan mendesainnya.',
                'main_image' => 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?auto=format&fit=crop&w=700&q=80',
                'variants' => [
                    ['variant_name' => 'Ukuran Frame Buket', 'variant_value' => 'Medium (Tinggi 45cm)', 'additional_price' => 0.00, 'stock' => 25],
                    ['variant_name' => 'Ukuran Frame Buket', 'variant_value' => 'Large (Tinggi 65cm)', 'additional_price' => 75000.00, 'stock' => 25],
                ]
            ],
            // Musiman
            [
                'category_slug' => 'produk-musiman',
                'name' => 'Mother\'s Day Carnation & Pastel Lily Posy',
                'sku' => 'MSN-MTH-01',
                'price' => 280000.00,
                'discount_price' => 250000.00,
                'stock' => 30,
                'weight_grams' => 650,
                'is_featured' => false,
                'lead_time_days' => 1,
                'description' => 'Rangkaian anyelir (carnation) pink melambangkan cinta ibu yang tiada batas, dipadukan bunga lili putih bersih dan kartu ucapan berhias wax seal klasik.',
                'main_image' => 'https://images.unsplash.com/photo-1582794543139-8ac9cb0f7b11?auto=format&fit=crop&w=700&q=80',
                'variants' => []
            ]
        ];

        foreach ($productsData as $item) {
            $cat = $categories[$item['category_slug']] ?? null;
            if (!$cat) continue;

            $slug = Str::slug($item['name']);
            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'category_id' => $cat->id,
                    'name' => $item['name'],
                    'slug' => $slug,
                    'price' => $item['price'],
                    'discount_price' => $item['discount_price'],
                    'stock' => $item['stock'],
                    'weight_grams' => $item['weight_grams'],
                    'is_featured' => $item['is_featured'],
                    'is_active' => true,
                    'lead_time_days' => $item['lead_time_days'],
                    'allows_greeting_card' => true,
                    'allows_custom_note' => true,
                    'description' => $item['description'],
                    'main_image' => $item['main_image'],
                ]
            );

            // Create primary product image
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'image_path' => $item['main_image']],
                ['is_primary' => true, 'sort_order' => 1]
            );

            // Create Variants if any
            if (!empty($item['variants'])) {
                foreach ($item['variants'] as $v) {
                    ProductVariant::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'variant_name' => $v['variant_name'],
                            'variant_value' => $v['variant_value'],
                        ],
                        [
                            'additional_price' => $v['additional_price'],
                            'stock' => $v['stock'],
                            'sku' => $product->sku . '-' . strtoupper(Str::slug($v['variant_value'])),
                        ]
                    );
                }
            }
        }
    }
}
