import {
  Category,
  CheckoutPayload,
  OrderSuccessData,
  PaymentMethod,
  Product,
  ShippingMethod,
  StoreSettings,
} from "@/types/ecommerce";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ||
  (typeof window !== "undefined" ? window.location.origin : "http://127.0.0.1:8000");

// Fallback seed-aligned data to guarantee zero visual downtime
export const FALLBACK_PRODUCTS: Product[] = [
  {
    id: 1,
    name: "Dusty Rose Romance Bouquet",
    slug: "dusty-rose-romance-bouquet",
    sku: "BKT-ROSE-01",
    description:
      "Rangkaian 12 tangkai mawar premium warna dusty pink & putih salju dengan daun eucalyptus dan baby breath harum. Dibungkus kertas premium waterproof bernuansa nude rose gold.",
    price: 320000,
    discount_price: 285000,
    effective_price: 285000,
    has_discount: true,
    discount_percentage: 11,
    stock: 25,
    weight_grams: 600,
    main_image:
      "https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=800&q=80",
    is_featured: true,
    lead_time_days: 1,
    allows_greeting_card: true,
    category: {
      id: 1,
      name: "Bucket Bunga",
      slug: "bucket-bunga",
      description: "Rangkaian mawar premium dan bunga segar.",
      image: "https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80",
    },
    variants: [
      {
        id: 1,
        variant_name: "Ukuran",
        variant_value: "Medium (12 Tangkai)",
        additional_price: 0,
        effective_variant_price: 285000,
        stock: 15,
      },
      {
        id: 2,
        variant_name: "Ukuran",
        variant_value: "Large (20 Tangkai)",
        additional_price: 120000,
        effective_variant_price: 405000,
        stock: 10,
      },
      {
        id: 3,
        variant_name: "Tema Warna",
        variant_value: "Dusty Pink Romance",
        additional_price: 0,
        effective_variant_price: 285000,
        stock: 15,
      },
      {
        id: 4,
        variant_name: "Tema Warna",
        variant_value: "Velvet Red Passion",
        additional_price: 20000,
        effective_variant_price: 305000,
        stock: 10,
      },
    ],
  },
  {
    id: 2,
    name: "Golden Sweet Ferrero Bouquet",
    slug: "golden-sweet-ferrero-bouquet",
    sku: "BKT-SNCK-01",
    description:
      "Kombinasi 16 butir cokelat Ferrero Rocher berlapis emas berpadu dengan mawar artificial emas dan pembungkus hitam elegan beraksen pita satin gold.",
    price: 380000,
    discount_price: 345000,
    effective_price: 345000,
    has_discount: true,
    discount_percentage: 9,
    stock: 18,
    weight_grams: 800,
    main_image:
      "https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=800&q=80",
    is_featured: true,
    lead_time_days: 1,
    allows_greeting_card: true,
    category: {
      id: 2,
      name: "Bucket Snack",
      slug: "bucket-snack",
      description: "Kombinasi cokelat dan camilan mewah.",
      image: "https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=80",
    },
    variants: [
      {
        id: 5,
        variant_name: "Ukuran",
        variant_value: "Isi 16 Butir Ferrero",
        additional_price: 0,
        effective_variant_price: 345000,
        stock: 12,
      },
      {
        id: 6,
        variant_name: "Ukuran",
        variant_value: "Isi 24 Butir Ferrero",
        additional_price: 150000,
        effective_variant_price: 495000,
        stock: 6,
      },
    ],
  },
  {
    id: 3,
    name: "Grand Royal 100K Money Bouquet",
    slug: "grand-royal-money-bouquet",
    sku: "BKT-MNY-01",
    description:
      "Rangkaian buket uang asli pecahan Rp 100.000 (total nominal Rp 2.000.000) tersusun rapi dengan proteksi mika premium dan dekorasi mawar merah velvet abadi.",
    price: 2350000,
    discount_price: 2280000,
    effective_price: 2280000,
    has_discount: true,
    discount_percentage: 3,
    stock: 10,
    weight_grams: 1200,
    main_image:
      "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=800&q=80",
    is_featured: true,
    lead_time_days: 2,
    allows_greeting_card: true,
    category: {
      id: 3,
      name: "Money Bouquet",
      slug: "money-bouquet",
      description: "Rangkaian uang asli tersusun elegan.",
      image: "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=600&q=80",
    },
    variants: [
      {
        id: 7,
        variant_name: "Nominal Lembar",
        variant_value: "20 Lembar (Total Rp 2.000.000)",
        additional_price: 0,
        effective_variant_price: 2280000,
        stock: 6,
      },
      {
        id: 8,
        variant_name: "Nominal Lembar",
        variant_value: "30 Lembar (Total Rp 3.000.000)",
        additional_price: 1100000,
        effective_variant_price: 3380000,
        stock: 4,
      },
    ],
  },
  {
    id: 4,
    name: "Scholar Teddy Graduation Bouquet",
    slug: "scholar-teddy-graduation-bouquet",
    sku: "BKT-WSD-01",
    description:
      "Kado wisuda favorit dengan boneka teddy bear bertoga, bunga matahari ceria, dan kartu ucapan kustom nama wisudawan.",
    price: 2750000,
    discount_price: 245000,
    effective_price: 245000,
    has_discount: true,
    discount_percentage: 11,
    stock: 30,
    weight_grams: 700,
    main_image:
      "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80",
    is_featured: true,
    lead_time_days: 1,
    allows_greeting_card: true,
    category: {
      id: 5,
      name: "Bucket Wisuda",
      slug: "bucket-wisuda",
      description: "Hadiah kelulusan terbaik.",
      image: "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=600&q=80",
    },
  },
  {
    id: 5,
    name: "Midnight Luxury Preserved Rose Dome",
    slug: "midnight-luxury-preserved-rose",
    sku: "BKT-ANV-01",
    description:
      "Mawar abadi yang diawetkan khusus bertahan 3-5 tahun tanpa layu, tersimpan dalam kubah kaca mewah dengan fairy lights hangat berbaterai.",
    price: 490000,
    discount_price: 450000,
    effective_price: 450000,
    has_discount: true,
    discount_percentage: 8,
    stock: 14,
    weight_grams: 950,
    main_image:
      "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80",
    is_featured: true,
    lead_time_days: 1,
    allows_greeting_card: true,
    category: {
      id: 8,
      name: "Hadiah Anniversary",
      slug: "hadiah-anniversary",
      description: "Ungkapan cinta tulus dengan mawar abadi.",
      image: "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80",
    },
  },
  {
    id: 6,
    name: "Aura Pastel Hug Bear Bouquet",
    slug: "aura-pastel-hug-bear-bouquet",
    sku: "BKT-BNK-01",
    description:
      "Boneka beruang mini lembut memeluk setangkai mawar pink pastel, dikelilingi bunga hydrangeas dan pembungkus pita satin korea.",
    price: 210000,
    discount_price: 189000,
    effective_price: 189000,
    has_discount: true,
    discount_percentage: 10,
    stock: 22,
    weight_grams: 550,
    main_image:
      "https://images.unsplash.com/photo-1559454403-b8fb88521f11?auto=format&fit=crop&w=800&q=80",
    is_featured: false,
    lead_time_days: 1,
    allows_greeting_card: true,
    category: {
      id: 4,
      name: "Bucket Boneka",
      slug: "bucket-boneka",
      description: "Teddy bear lucu dan karakter favorit.",
      image: "https://images.unsplash.com/photo-1559454403-b8fb88521f11?auto=format&fit=crop&w=600&q=80",
    },
  },
];

export const FALLBACK_CATEGORIES: Category[] = [
  { id: 1, name: "Bucket Bunga", slug: "bucket-bunga", description: "Rangkaian mawar & bunga segar", image: "https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80", products_count: 8 },
  { id: 2, name: "Bucket Snack", slug: "bucket-snack", description: "Kombinasi Ferrero Rocher & cokelat", image: "https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=80", products_count: 5 },
  { id: 3, name: "Money Bouquet", slug: "money-bouquet", description: "Rangkaian uang asli tersusun rapi", image: "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&w=600&q=80", products_count: 4 },
  { id: 4, name: "Bucket Wisuda", slug: "bucket-wisuda", description: "Kado kelulusan teddy toga", image: "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=600&q=80", products_count: 6 },
  { id: 5, name: "Anniversary & Dome", slug: "hadiah-anniversary", description: "Preserved rose abadi", image: "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80", products_count: 4 },
];

export const FALLBACK_SHIPPING: ShippingMethod[] = [
  { id: 1, name: "Kurir Instan Toko / GoSend / GrabExpress", code: "instant", description: "Pengiriman khusus Jakarta & sekitarnya, aman untuk bunga hidup.", cost: 25000, estimated_time: "1 - 3 Jam" },
  { id: 2, name: "Ekspedisi Reguler (JNE YES / SiCepat)", code: "regular", description: "Packing kardus tebal + bubble wrap khusus.", cost: 15000, estimated_time: "1 - 2 Hari" },
];

export const FALLBACK_PAYMENT: PaymentMethod[] = [
  {
    id: 1,
    name: "QRIS Standar Pembayaran Nasional (MocYy, Kerajinan)",
    code: "qris",
    account_name: "MocYy, Kerajinan",
    account_number: "NMID: ID1026613806474 (A01)",
    qr_image_url: "/images/qris-mocyan.jpg",
    instructions:
      "1. Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, ShopeePay, DANA).\n2. Pilih menu 'Bayar' atau 'Scan QRIS'.\n3. Scan kode QR MocYy, Kerajinan yang tertera.\n4. Masukkan nominal tagihan sesuai total pesanan secara tepat.\n5. Selesaikan transaksi dan simpan bukti transfer (screenshot/resi).\n6. Konfirmasi melalui WhatsApp atau unggah bukti transfer ke admin.",
    expiry_minutes: 120,
  },
];

export async function fetchProducts(params?: { category?: string; featured?: boolean; search?: string }): Promise<Product[]> {
  try {
    const searchParams = new URLSearchParams();
    if (params?.category) searchParams.set("category", params.category);
    if (params?.featured) searchParams.set("featured", "1");
    if (params?.search) searchParams.set("search", params.search);

    const res = await fetch(`${API_BASE}/api/products?${searchParams.toString()}`, {
      next: { revalidate: 60 },
    });

    if (!res.ok) throw new Error("Gagal mengambil data produk");
    const json = await res.json();
    return json.data || FALLBACK_PRODUCTS;
  } catch {
    return FALLBACK_PRODUCTS;
  }
}

export async function fetchProductBySlug(slug: string): Promise<Product | null> {
  try {
    const res = await fetch(`${API_BASE}/api/products/${slug}`, {
      next: { revalidate: 60 },
    });
    if (!res.ok) throw new Error("Gagal mengambil produk");
    const json = await res.json();
    return json.data;
  } catch {
    return FALLBACK_PRODUCTS.find((p) => p.slug === slug) || null;
  }
}

export async function fetchCategories(): Promise<Category[]> {
  try {
    const res = await fetch(`${API_BASE}/api/categories`, {
      next: { revalidate: 120 },
    });
    if (!res.ok) throw new Error("Gagal mengambil kategori");
    const json = await res.json();
    return json.data || FALLBACK_CATEGORIES;
  } catch {
    return FALLBACK_CATEGORIES;
  }
}

export async function fetchShippingMethods(): Promise<ShippingMethod[]> {
  try {
    const res = await fetch(`${API_BASE}/api/store/shipping-methods`, {
      next: { revalidate: 300 },
    });
    if (!res.ok) throw new Error("Gagal mengambil pengiriman");
    const json = await res.json();
    return json.data || FALLBACK_SHIPPING;
  } catch {
    return FALLBACK_SHIPPING;
  }
}

export async function fetchPaymentMethods(): Promise<PaymentMethod[]> {
  try {
    const res = await fetch(`${API_BASE}/api/store/payment-methods`, {
      next: { revalidate: 300 },
    });
    if (!res.ok) throw new Error("Gagal mengambil pembayaran");
    const json = await res.json();
    return json.data || FALLBACK_PAYMENT;
  } catch {
    return FALLBACK_PAYMENT;
  }
}

export async function submitCheckout(payload: CheckoutPayload): Promise<OrderSuccessData> {
  const res = await fetch(`${API_BASE}/api/checkout`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify(payload),
  });

  const json = await res.json();
  if (!res.ok || !json.success) {
    throw new Error(json.message || "Terjadi kesalahan saat memproses pesanan.");
  }

  return json.data;
}

export async function trackOrder(orderNumber: string) {
  const res = await fetch(`${API_BASE}/api/orders/track/${encodeURIComponent(orderNumber)}`, {
    headers: { Accept: "application/json" },
  });
  const json = await res.json();
  if (!res.ok || !json.success) {
    throw new Error(json.message || "Pesanan tidak ditemukan.");
  }
  return json.data;
}
