"use client";

import React, { useEffect, useState, useTransition } from "react";
import Navbar from "@/components/ui/Navbar";
import HeroContent from "@/components/ui/HeroContent";
import Hero3DWrapper from "@/components/3d/Hero3DWrapper";
import ThreeProductViewer from "@/components/3d/ThreeProductViewer";
import ProductCard from "@/components/ui/ProductCard";
import CartDrawer from "@/components/ui/CartDrawer";
import CheckoutModal from "@/components/ui/CheckoutModal";
import OrderTrackingModal from "@/components/ui/OrderTrackingModal";
import Footer from "@/components/ui/Footer";
import {
  Category,
  CartItem,
  OrderSuccessData,
  PaymentMethod,
  Product,
  ProductVariant,
  ShippingMethod,
} from "@/types/ecommerce";
import {
  fetchCategories,
  fetchPaymentMethods,
  fetchProducts,
  fetchShippingMethods,
  FALLBACK_PRODUCTS,
  FALLBACK_CATEGORIES,
  FALLBACK_PAYMENT,
  FALLBACK_SHIPPING,
} from "@/services/api";
import { Sparkles, ArrowRight, ShieldCheck, HeartHandshake, Truck, Eye } from "lucide-react";

export default function HomePage() {
  // Products & Categories Data
  const [products, setProducts] = useState<Product[]>(FALLBACK_PRODUCTS);
  const [categories, setCategories] = useState<Category[]>(FALLBACK_CATEGORIES);
  const [shippingMethods, setShippingMethods] = useState<ShippingMethod[]>(FALLBACK_SHIPPING);
  const [paymentMethods, setPaymentMethods] = useState<PaymentMethod[]>(FALLBACK_PAYMENT);

  // Active Filters
  const [selectedCategory, setSelectedCategory] = useState<string>("all");
  const [searchQuery, setSearchQuery] = useState<string>("");
  const [, startTransition] = useTransition();

  // Featured 3D Showcase State
  const [featuredProduct, setFeaturedProduct] = useState<Product>(FALLBACK_PRODUCTS[0]);
  const [selectedVariant, setSelectedVariant] = useState<ProductVariant | null>(
    FALLBACK_PRODUCTS[0]?.variants ? FALLBACK_PRODUCTS[0].variants[0] : null
  );

  // Shopping Cart & Modals
  const [cartItems, setCartItems] = useState<CartItem[]>([]);
  const [isCartOpen, setIsCartOpen] = useState<boolean>(false);
  const [isCheckoutOpen, setIsCheckoutOpen] = useState<boolean>(false);
  const [isTrackingOpen, setIsTrackingOpen] = useState<boolean>(false);
  const [justAddedId, setJustAddedId] = useState<number | null>(null);

  // Load live data from Laravel API on mount
  useEffect(() => {
    let isMounted = true;

    async function loadData() {
      try {
        const [prods, cats, shippings, payments] = await Promise.all([
          fetchProducts(),
          fetchCategories(),
          fetchShippingMethods(),
          fetchPaymentMethods(),
        ]);

        if (isMounted) {
          if (prods && prods.length > 0) {
            setProducts(prods);
            setFeaturedProduct(prods[0]);
            if (prods[0].variants && prods[0].variants.length > 0) {
              setSelectedVariant(prods[0].variants[0]);
            }
          }
          if (cats && cats.length > 0) setCategories(cats);
          if (shippings && shippings.length > 0) setShippingMethods(shippings);
          if (payments && payments.length > 0) setPaymentMethods(payments);
        }
      } catch {
        // Fallback data is already present
      }
    }

    loadData();

    // Hydrate cart from local storage if available
    try {
      const savedCart = localStorage.getItem("mocyy_cart");
      if (savedCart) {
        setCartItems(JSON.parse(savedCart));
      }
    } catch {
      // Ignore
    }

    return () => {
      isMounted = false;
    };
  }, []);

  // Sync cart to local storage
  const updateCartState = (items: CartItem[]) => {
    setCartItems(items);
    try {
      localStorage.setItem("mocyy_cart", JSON.stringify(items));
    } catch {
      // Ignore
    }
  };

  // Add to cart handler
  const handleAddToCart = (product: Product, variant?: ProductVariant | null) => {
    const existingIndex = cartItems.findIndex(
      (item) =>
        item.product.id === product.id &&
        ((!item.variant && !variant) || item.variant?.id === variant?.id)
    );

    let updatedCart: CartItem[];
    if (existingIndex > -1) {
      updatedCart = [...cartItems];
      updatedCart[existingIndex].quantity += 1;
    } else {
      updatedCart = [
        ...cartItems,
        {
          product,
          variant: variant || null,
          quantity: 1,
        },
      ];
    }

    updateCartState(updatedCart);
    setJustAddedId(product.id);
    setTimeout(() => setJustAddedId(null), 1500);
  };

  // Immediate Buy Now handler
  const handleBuyNow = (product: Product, variant?: ProductVariant | null) => {
    handleAddToCart(product, variant);
    setIsCheckoutOpen(true);
  };

  // Cart quantity updates
  const handleUpdateQuantity = (index: number, quantity: number) => {
    if (quantity <= 0) {
      handleRemoveItem(index);
      return;
    }
    const updated = [...cartItems];
    updated[index].quantity = quantity;
    updateCartState(updated);
  };

  const handleRemoveItem = (index: number) => {
    const updated = cartItems.filter((_, i) => i !== index);
    updateCartState(updated);
  };

  const handleClearCart = () => {
    updateCartState([]);
  };

  // Trigger 3D inspector on any product
  const handleInspect3D = (product: Product) => {
    setFeaturedProduct(product);
    if (product.variants && product.variants.length > 0) {
      setSelectedVariant(product.variants[0]);
    } else {
      setSelectedVariant(null);
    }

    // Smooth scroll to showcase
    const element = document.getElementById("showcase-3d");
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  // Filtered products list
  const filteredProducts = products.filter((p) => {
    const matchesCategory =
      selectedCategory === "all" || p.category?.slug === selectedCategory;
    const matchesSearch =
      !searchQuery.trim() ||
      p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      p.description.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  const cartTotalCount = cartItems.reduce((acc, item) => acc + item.quantity, 0);

  return (
    <div className="min-h-screen flex flex-col bg-[#faf7f5] text-[#1c1917] selection:bg-[#f3d0dd] selection:text-[#782046]">
      {/* 1. Header Navigation */}
      <Navbar
        cartCount={cartTotalCount}
        onOpenCart={() => setIsCartOpen(true)}
        onOpenTracking={() => setIsTrackingOpen(true)}
        onSearch={(q) => {
          setSearchQuery(q);
          const elem = document.getElementById("koleksi");
          if (elem) elem.scrollIntoView({ behavior: "smooth" });
        }}
      />

      <main className="flex-1 flex flex-col">
        {/* 2. Hero Section 3D (Requirement A) */}
        <section
          id="hero"
          className="relative w-full overflow-hidden min-h-[600px] lg:min-h-[720px] flex items-center hero-glow"
        >
          {/* Subtle decorative luxury ambient glow spheres */}
          <div className="absolute top-1/4 left-1/3 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-rose-200/35 rounded-full blur-3xl pointer-events-none" />
          <div className="absolute bottom-10 right-1/4 w-80 h-80 bg-amber-100/40 rounded-full blur-3xl pointer-events-none" />

          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-center py-6 sm:py-12">
            {/* Left Column: High-Impact Typography & Conversion Content */}
            <div className="lg:col-span-6 z-10">
              <HeroContent
                onShopNow={() => {
                  const elem = document.getElementById("koleksi");
                  if (elem) elem.scrollIntoView({ behavior: "smooth" });
                }}
                onExploreCollection={() => {
                  const elem = document.getElementById("showcase-3d");
                  if (elem) elem.scrollIntoView({ behavior: "smooth" });
                }}
              />
            </div>

            {/* Right Column: Realistic 3D Flower Bouquet Object (Requirement A) */}
            <div className="lg:col-span-6 flex items-center justify-center relative min-h-[450px] lg:min-h-[640px]">
              <Hero3DWrapper
                bouquetColor="#c94d75"
                wrapperColor="#f0ded5"
                ribbonColor="#8e2b4f"
              />
            </div>
          </div>
        </section>

        {/* 3. Featured 3D Product Interactive Showcase (Requirement B) */}
        <section
          id="showcase-3d"
          className="w-full py-16 sm:py-24 bg-gradient-to-b from-transparent via-rose-50/30 to-transparent border-t border-b border-stone-200/50"
        >
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-12">
              <span className="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[#782046]/10 text-[#782046] text-xs font-bold uppercase tracking-wider mb-3">
                <Sparkles className="w-3.5 h-3.5" />
                <span>Interaksi Realistis 3D</span>
              </span>
              <h2 className="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight font-serif">
                Inspeksi Detail 360° Produk Pilihan
              </h2>
              <p className="mt-3 text-stone-600 text-sm sm:text-base leading-relaxed">
                Putar objek, sesuaikan variasi tema warna, dan lihat tekstur keindahan rangkaian
                bunga kami dari segala sudut sebelum memutuskan membeli.
              </p>
            </div>

            {/* Interactive 3D Showcase Component */}
            <div className="max-w-5xl mx-auto">
              <ThreeProductViewer
                product={featuredProduct}
                selectedVariant={selectedVariant}
                onSelectVariant={(v) => setSelectedVariant(v)}
                onAddToCart={(prod, v) => handleAddToCart(prod, v)}
                onBuyNow={(prod, v) => handleBuyNow(prod, v)}
              />
            </div>
          </div>
        </section>

        {/* 4. Product Catalog & Category Filters */}
        <section id="koleksi" className="w-full py-16 sm:py-24">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {/* Section Header */}
            <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
              <div>
                <span className="text-xs font-bold uppercase tracking-widest text-stone-500">
                  Koleksi Hadiah Terlengkap
                </span>
                <h2 className="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight font-serif mt-1">
                  Pilihan Bucket Bunga & Kado Mewah
                </h2>
                <p className="mt-2 text-stone-600 text-sm max-w-xl">
                  Didesain khusus untuk momen kelulusan, hari jadian, ucapan selamat, dan kejutan istimewa.
                </p>
              </div>

              {/* Search Badge Counter */}
              <div className="text-xs font-semibold text-stone-500 bg-stone-100 px-4 py-2 rounded-full self-start md:self-auto border border-stone-200">
                Menampilkan {filteredProducts.length} Produk Siap Kirim
              </div>
            </div>

            {/* Category Filter Tabs */}
            <div className="flex items-center gap-2 overflow-x-auto pb-4 scrollbar-none mb-8">
              <button
                onClick={() => {
                  startTransition(() => setSelectedCategory("all"));
                }}
                className={`px-5 py-2.5 rounded-full text-xs font-bold transition-all shrink-0 ${
                  selectedCategory === "all"
                    ? "bg-[#782046] text-white shadow-md shadow-rose-900/20"
                    : "bg-white text-stone-700 hover:bg-stone-100 border border-stone-200"
                }`}
              >
                Semua Produk
              </button>

              {categories.map((cat) => {
                const isSelected = selectedCategory === cat.slug;
                return (
                  <button
                    key={cat.id}
                    onClick={() => {
                      startTransition(() => setSelectedCategory(cat.slug));
                    }}
                    className={`px-5 py-2.5 rounded-full text-xs font-bold transition-all shrink-0 ${
                      isSelected
                        ? "bg-[#782046] text-white shadow-md shadow-rose-900/20"
                        : "bg-white text-stone-700 hover:bg-stone-100 border border-stone-200"
                    }`}
                  >
                    {cat.name}
                  </button>
                );
              })}
            </div>

            {/* Products Grid */}
            {filteredProducts.length === 0 ? (
              <div className="p-16 rounded-3xl bg-white border border-stone-200 text-center max-w-md mx-auto">
                <p className="text-stone-500 text-sm">
                  Tidak ditemukan produk dengan kata kunci &quot;{searchQuery}&quot;.
                </p>
                <button
                  onClick={() => {
                    setSearchQuery("");
                    setSelectedCategory("all");
                  }}
                  className="mt-4 px-5 py-2 rounded-xl bg-stone-900 text-white text-xs font-semibold"
                >
                  Reset Filter
                </button>
              </div>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {filteredProducts.map((prod) => (
                  <ProductCard
                    key={prod.id}
                    product={prod}
                    onInspect3D={(p) => handleInspect3D(p)}
                    onAddToCart={(p) => handleAddToCart(p)}
                    isAdded={justAddedId === prod.id}
                  />
                ))}
              </div>
            )}
          </div>
        </section>

        {/* 5. Trust & Artisan Excellence Section */}
        <section id="keunggulan" className="w-full py-16 bg-white border-t border-stone-200">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="p-8 rounded-3xl bg-stone-50 border border-stone-200/80 space-y-3">
                <div className="w-12 h-12 rounded-2xl bg-rose-100 text-[#782046] flex items-center justify-center">
                  <ShieldCheck className="w-6 h-6" />
                </div>
                <h3 className="text-lg font-bold text-stone-900 font-serif">
                  Bunga Segar Terverifikasi
                </h3>
                <p className="text-xs text-stone-600 leading-relaxed">
                  Kami mengimpor mawar dan bunga segar langsung dari perkebunan pilihan.
                  Disusun dengan teknik hidrasi profesional sehingga mekar sempurna lebih lama.
                </p>
              </div>

              <div className="p-8 rounded-3xl bg-stone-50 border border-stone-200/80 space-y-3">
                <div className="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                  <Truck className="w-6 h-6" />
                </div>
                <h3 className="text-lg font-bold text-stone-900 font-serif">
                  Pengiriman Instan Aman
                </h3>
                <p className="text-xs text-stone-600 leading-relaxed">
                  Didukung kurir khusus toko, GoSend, dan GrabExpress dengan packing tabung proteksi
                  anti-goncangan, memastikan bucket tiba tanpa cela di tangan penerima.
                </p>
              </div>

              <div className="p-8 rounded-3xl bg-stone-50 border border-stone-200/80 space-y-3">
                <div className="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                  <HeartHandshake className="w-6 h-6" />
                </div>
                <h3 className="text-lg font-bold text-stone-900 font-serif">
                  Pelayanan Ramah & Amanah
                </h3>
                <p className="text-xs text-stone-600 leading-relaxed">
                  Konsultasi bebas biaya lewat WhatsApp, update foto rangkaian sebelum pengiriman,
                  dan kartu ucapan gratis berbahan kertas linen mewah.
                </p>
              </div>
            </div>
          </div>
        </section>
      </main>

      {/* 6. Modals & Drawers */}
      <CartDrawer
        isOpen={isCartOpen}
        onClose={() => setIsCartOpen(false)}
        cartItems={cartItems}
        onUpdateQuantity={handleUpdateQuantity}
        onRemoveItem={handleRemoveItem}
        onClearCart={handleClearCart}
        onCheckout={() => {
          setIsCartOpen(false);
          setIsCheckoutOpen(true);
        }}
      />

      <CheckoutModal
        isOpen={isCheckoutOpen}
        onClose={() => setIsCheckoutOpen(false)}
        cartItems={cartItems}
        shippingMethods={shippingMethods}
        paymentMethods={paymentMethods}
        onOrderSuccess={(orderData: OrderSuccessData) => {
          // Clear cart on successful order
          updateCartState([]);
        }}
      />

      <OrderTrackingModal
        isOpen={isTrackingOpen}
        onClose={() => setIsTrackingOpen(false)}
      />

      {/* 7. Boutique Footer */}
      <Footer />
    </div>
  );
}
