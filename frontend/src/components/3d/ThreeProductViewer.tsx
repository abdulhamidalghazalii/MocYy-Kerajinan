"use client";

import React, { useEffect, useRef, useState } from "react";
import * as THREE from "three";
import Image from "next/image";
import { Product, ProductVariant } from "@/types/ecommerce";
import { RotateCw, ZoomIn, ZoomOut, Sparkles, Box, Image as ImageIcon, ShoppingBag } from "lucide-react";

interface ThreeProductViewerProps {
  product: Product;
  selectedVariant?: ProductVariant | null;
  onSelectVariant: (variant: ProductVariant) => void;
  onAddToCart: (product: Product, variant: ProductVariant | null) => void;
  onBuyNow: (product: Product, variant: ProductVariant | null) => void;
}

export default function ThreeProductViewer({
  product,
  selectedVariant,
  onSelectVariant,
  onAddToCart,
  onBuyNow,
}: ThreeProductViewerProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const [viewMode, setViewMode] = useState<"3d" | "photo">("3d");
  const [zoomLevel, setZoomLevel] = useState<number>(4.8);
  const [webglSupported, setWebglSupported] = useState<boolean>(true);
  const [isRotating, setIsRotating] = useState<boolean>(true);

  // Map theme colors according to variant or product
  const getThemeColors = () => {
    const val = selectedVariant?.variant_value?.toLowerCase() || "";
    if (val.includes("red") || val.includes("passion")) {
      return { flower: 0x991b34, wrapper: 0x1f1f1f, ribbon: 0xd4af37 };
    }
    if (val.includes("gold") || val.includes("ferrero") || val.includes("kuning")) {
      return { flower: 0xd4af37, wrapper: 0x242424, ribbon: 0xb8860b };
    }
    if (val.includes("white") || val.includes("putih")) {
      return { flower: 0xfafafa, wrapper: 0xd5bdaf, ribbon: 0x8e2b4f };
    }
    // Default Dusty Rose Romance
    return { flower: 0xc94d75, wrapper: 0xf0ded5, ribbon: 0x8e2b4f };
  };

  const themeColors = getThemeColors();

  // Three.js scene setup and lifecycle
  useEffect(() => {
    if (viewMode !== "3d") return;

    try {
      const testCanvas = document.createElement("canvas");
      const gl = testCanvas.getContext("webgl2") || testCanvas.getContext("webgl");
      if (!gl) {
        setWebglSupported(false);
        setViewMode("photo");
        return;
      }
    } catch {
      setWebglSupported(false);
      setViewMode("photo");
      return;
    }

    const container = containerRef.current;
    if (!container) return;

    const width = container.clientWidth || 500;
    const height = container.clientHeight || 500;
    const isMobile = window.innerWidth < 768;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, width / height, 0.1, 100);
    camera.position.set(0, 1.1, zoomLevel);

    const renderer = new THREE.WebGLRenderer({
      alpha: true,
      antialias: !isMobile,
      powerPreference: "high-performance",
    });

    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, isMobile ? 1.5 : 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.2;
    renderer.shadowMap.enabled = !isMobile;
    container.appendChild(renderer.domElement);

    // Studio Lighting
    const ambientLight = new THREE.AmbientLight(0xfff6f0, 1.3);
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.2);
    keyLight.position.set(4, 7, 5);
    scene.add(keyLight);

    const rimLight = new THREE.DirectionalLight(0xffc5db, 1.2);
    rimLight.position.set(-4, 3, -3);
    scene.add(rimLight);

    // Product Bouquet Model Group
    const bouquetGroup = new THREE.Group();
    scene.add(bouquetGroup);

    // Materials based on selected variant
    const roseMat = new THREE.MeshStandardMaterial({
      color: themeColors.flower,
      roughness: 0.35,
      metalness: 0.08,
    });

    const wrapperMat = new THREE.MeshStandardMaterial({
      color: themeColors.wrapper,
      roughness: 0.65,
      metalness: 0.1,
      side: THREE.DoubleSide,
    });

    const ribbonMat = new THREE.MeshStandardMaterial({
      color: themeColors.ribbon,
      roughness: 0.25,
      metalness: 0.2,
      side: THREE.DoubleSide,
    });

    // Helper: Rose bloom
    function createRose() {
      const g = new THREE.Group();
      const core = new THREE.Mesh(new THREE.SphereGeometry(0.18, 14, 14), roseMat);
      core.scale.set(1, 1.25, 1);
      g.add(core);

      const layers = [
        { count: 5, radius: 0.22, w: 0.28, h: 0.32, tilt: 0.3 },
        { count: 7, radius: 0.34, w: 0.38, h: 0.38, tilt: 0.55 },
        { count: 9, radius: 0.46, w: 0.48, h: 0.44, tilt: 0.75 },
      ];

      layers.forEach((l, idx) => {
        for (let i = 0; i < l.count; i++) {
          const a = (i / l.count) * Math.PI * 2 + idx * 0.4;
          const petal = new THREE.Mesh(
            new THREE.CylinderGeometry(l.w, l.w * 0.35, l.h, 10, 1, true, 0, Math.PI * 0.95),
            roseMat
          );
          petal.position.set(Math.cos(a) * l.radius, -idx * 0.04, Math.sin(a) * l.radius);
          petal.rotation.set(Math.sin(a) * l.tilt, a + Math.PI / 2, -Math.cos(a) * l.tilt);
          g.add(petal);
        }
      });
      return g;
    }

    // Arrangement dome
    const flowers = new THREE.Group();
    flowers.position.set(0, 0.4, 0);

    const centerRose = createRose();
    centerRose.position.set(0, 0.35, 0);
    flowers.add(centerRose);

    const ringPos = [
      { x: 0.4, y: 0.2, z: 0.22, rotX: 0.2, rotZ: -0.3 },
      { x: -0.38, y: 0.22, z: 0.25, rotX: 0.22, rotZ: 0.3 },
      { x: 0.42, y: 0.16, z: -0.28, rotX: -0.3, rotZ: -0.35 },
      { x: -0.4, y: 0.18, z: -0.3, rotX: -0.28, rotZ: 0.32 },
      { x: 0.0, y: 0.12, z: 0.45, rotX: 0.4, rotZ: 0.0 },
      { x: 0.0, y: 0.12, z: -0.48, rotX: -0.4, rotZ: 0.0 },
    ];

    ringPos.forEach((p) => {
      const r = createRose();
      r.position.set(p.x, p.y, p.z);
      r.rotation.set(p.rotX, 0, p.rotZ);
      flowers.add(r);
    });

    bouquetGroup.add(flowers);

    // Wrapping Cone
    const cone = new THREE.Mesh(
      new THREE.CylinderGeometry(0.85, 0.24, 1.7, 24, 1, true),
      wrapperMat
    );
    cone.position.set(0, -0.65, 0);
    bouquetGroup.add(cone);

    // Satin Ribbon Waist
    const waist = new THREE.Mesh(new THREE.TorusGeometry(0.34, 0.04, 10, 24), ribbonMat);
    waist.position.set(0, -0.32, 0);
    waist.rotation.x = Math.PI / 2;
    bouquetGroup.add(waist);

    bouquetGroup.position.y = -0.15;
    bouquetGroup.rotation.x = 0.18;

    // Pointer Drag Interaction
    let targetRotY = 0;
    let targetRotX = 0.18;
    let curRotY = 0;
    let curRotX = 0.18;
    let isDown = false;
    let lastX = 0;
    let lastY = 0;

    const onPointerDown = (e: PointerEvent) => {
      isDown = true;
      lastX = e.clientX;
      lastY = e.clientY;
    };

    const onPointerMove = (e: PointerEvent) => {
      if (!isDown) return;
      const dx = e.clientX - lastX;
      const dy = e.clientY - lastY;
      lastX = e.clientX;
      lastY = e.clientY;

      targetRotY += dx * 0.01;
      targetRotX += dy * 0.006;
      targetRotX = Math.max(-0.35, Math.min(0.5, targetRotX));
    };

    const onPointerUp = () => {
      isDown = false;
    };

    const dom = renderer.domElement;
    dom.style.touchAction = "none";
    dom.addEventListener("pointerdown", onPointerDown);
    window.addEventListener("pointermove", onPointerMove);
    window.addEventListener("pointerup", onPointerUp);

    // Resize handling
    const onResize = () => {
      if (!container) return;
      const w = container.clientWidth;
      const h = container.clientHeight;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    };
    window.addEventListener("resize", onResize);

    // Loop
    let animId: number;
    const animate = () => {
      animId = requestAnimationFrame(animate);

      if (isRotating && !isDown) {
        targetRotY += 0.005;
      }

      curRotY += (targetRotY - curRotY) * 0.1;
      curRotX += (targetRotX - curRotX) * 0.1;

      bouquetGroup.rotation.y = curRotY;
      bouquetGroup.rotation.x = curRotX;

      renderer.render(scene, camera);
    };
    animate();

    return () => {
      cancelAnimationFrame(animId);
      window.removeEventListener("resize", onResize);
      dom.removeEventListener("pointerdown", onPointerDown);
      window.removeEventListener("pointermove", onPointerMove);
      window.removeEventListener("pointerup", onPointerUp);

      scene.traverse((obj) => {
        if (obj instanceof THREE.Mesh) {
          obj.geometry?.dispose();
          if (Array.isArray(obj.material)) obj.material.forEach((m) => m.dispose());
          else if (obj.material) obj.material.dispose();
        }
      });

      renderer.dispose();
      renderer.forceContextLoss();
      if (dom.parentElement) dom.parentElement.removeChild(dom);
    };
  }, [viewMode, zoomLevel, themeColors.flower, themeColors.wrapper, themeColors.ribbon, isRotating]);

  // Handle zoom
  const handleZoom = (delta: number) => {
    setZoomLevel((prev) => Math.max(3.5, Math.min(6.5, prev + delta)));
  };

  // Compute live price
  const basePrice = product.effective_price;
  const currentPrice = selectedVariant
    ? basePrice + selectedVariant.additional_price
    : basePrice;

  return (
    <div className="w-full bg-white/95 rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-xl backdrop-blur-md grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
      {/* 3D Visualizer / Photo Showcase (Left Col - 7/12) */}
      <div className="lg:col-span-7 flex flex-col items-center">
        {/* Toggle Mode: 3D vs High-Res Photo */}
        <div className="flex items-center gap-2 mb-4 p-1.5 rounded-full bg-stone-100 border border-stone-200 shadow-inner">
          <button
            onClick={() => setViewMode("3d")}
            className={`flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold transition-all ${
              viewMode === "3d"
                ? "bg-stone-900 text-white shadow-sm"
                : "text-stone-600 hover:text-stone-900"
            }`}
          >
            <Box className="w-3.5 h-3.5" />
            <span>Interactive 3D</span>
          </button>
          <button
            onClick={() => setViewMode("photo")}
            className={`flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold transition-all ${
              viewMode === "photo"
                ? "bg-stone-900 text-white shadow-sm"
                : "text-stone-600 hover:text-stone-900"
            }`}
          >
            <ImageIcon className="w-3.5 h-3.5" />
            <span>Foto Studio HQ</span>
          </button>
        </div>

        {/* Canvas / Photo Container */}
        <div className="relative w-full h-[400px] sm:h-[480px] rounded-2xl bg-gradient-to-b from-stone-50 to-rose-50/20 border border-stone-200/70 overflow-hidden flex items-center justify-center">
          {viewMode === "3d" && webglSupported ? (
            <>
              <div ref={containerRef} className="w-full h-full cursor-grab active:cursor-grabbing" />
              {/* 3D Control Floating Bar */}
              <div className="absolute bottom-4 right-4 flex items-center gap-1.5 p-1.5 rounded-2xl glass-panel shadow-sm">
                <button
                  onClick={() => setIsRotating((prev) => !prev)}
                  title="Toggle Auto Rotate"
                  className={`p-2 rounded-xl text-stone-700 hover:bg-stone-200/60 transition ${
                    isRotating ? "text-rose-600" : ""
                  }`}
                >
                  <RotateCw className="w-4 h-4" />
                </button>
                <button
                  onClick={() => handleZoom(-0.5)}
                  title="Zoom In"
                  className="p-2 rounded-xl text-stone-700 hover:bg-stone-200/60 transition"
                >
                  <ZoomIn className="w-4 h-4" />
                </button>
                <button
                  onClick={() => handleZoom(0.5)}
                  title="Zoom Out"
                  className="p-2 rounded-xl text-stone-700 hover:bg-stone-200/60 transition"
                >
                  <ZoomOut className="w-4 h-4" />
                </button>
              </div>

              <div className="absolute top-4 left-4 px-3 py-1 rounded-full bg-white/80 backdrop-blur-md text-[11px] font-medium text-stone-600 border border-stone-200/60 flex items-center gap-1.5 shadow-xs">
                <Sparkles className="w-3 h-3 text-amber-500" />
                <span>Model 3D Interaktif Real-time</span>
              </div>
            </>
          ) : (
            <div className="relative w-full h-full p-4">
              <Image
                src={product.main_image}
                alt={product.name}
                fill
                sizes="(max-width: 768px) 100vw, 50vw"
                className="object-contain transition-all duration-700 hover:scale-105"
                priority
              />
            </div>
          )}
        </div>
      </div>

      {/* Product Details & Variant Chooser (Right Col - 5/12) */}
      <div className="lg:col-span-5 flex flex-col justify-center space-y-6">
        <div>
          <span className="inline-block px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-semibold tracking-wider uppercase mb-2">
            {product.category?.name || "Bunga Unggulan"}
          </span>
          <h2 className="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">
            {product.name}
          </h2>
          <p className="mt-2 text-stone-600 text-sm leading-relaxed">
            {product.description}
          </p>
        </div>

        {/* Pricing connected to Laravel API */}
        <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200/80 flex items-baseline justify-between">
          <div>
            <p className="text-xs text-stone-500 font-medium">Harga Resmi Toko</p>
            <div className="flex items-baseline gap-2 mt-1">
              <span className="text-2xl sm:text-3xl font-extrabold text-[#782046]">
                Rp {currentPrice.toLocaleString("id-ID")}
              </span>
              {product.has_discount && (
                <span className="text-sm text-stone-400 line-through">
                  Rp {product.price.toLocaleString("id-ID")}
                </span>
              )}
            </div>
          </div>
          {product.has_discount && (
            <span className="px-2.5 py-1 rounded-full bg-rose-600 text-white text-xs font-bold shadow-xs">
              Hemat {product.discount_percentage}%
            </span>
          )}
        </div>

        {/* Variations Selector (Theme Colors & Sizes) */}
        {product.variants && product.variants.length > 0 && (
          <div className="space-y-3">
            <label className="text-xs font-bold text-stone-700 uppercase tracking-wider flex items-center justify-between">
              <span>Pilih Variasi Produk:</span>
              <span className="text-rose-600 font-normal">
                {selectedVariant ? selectedVariant.variant_value : "Pilih salah satu"}
              </span>
            </label>

            <div className="grid grid-cols-2 gap-2">
              {product.variants.map((variant) => {
                const isSelected = selectedVariant?.id === variant.id;
                return (
                  <button
                    key={variant.id}
                    onClick={() => onSelectVariant(variant)}
                    className={`p-3 rounded-xl text-left border text-xs font-medium transition-all ${
                      isSelected
                        ? "border-[#782046] bg-rose-50/60 ring-2 ring-[#782046]/20 text-stone-900"
                        : "border-stone-200 hover:border-stone-300 text-stone-700 bg-white"
                    }`}
                  >
                    <div className="font-semibold">{variant.variant_value}</div>
                    {variant.additional_price > 0 && (
                      <div className="text-[11px] text-stone-500 mt-0.5">
                        +Rp {variant.additional_price.toLocaleString("id-ID")}
                      </div>
                    )}
                  </button>
                );
              })}
            </div>
          </div>
        )}

        {/* Stock & Delivery Guarantee */}
        <div className="flex items-center gap-4 text-xs text-stone-600 pt-1">
          <div className="flex items-center gap-1.5">
            <span className="w-2 h-2 rounded-full bg-emerald-500" />
            <span>Stok Ready: {product.stock} unit</span>
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-2 h-2 rounded-full bg-blue-500" />
            <span>Pengiriman Instan 1-3 Jam</span>
          </div>
        </div>

        {/* CTAs directly tied to checkout & cart */}
        <div className="flex flex-col sm:flex-row gap-3 pt-2">
          <button
            onClick={() => onBuyNow(product, selectedVariant || null)}
            className="flex-1 py-3.5 px-6 rounded-2xl bg-[#782046] hover:bg-[#621837] text-white font-semibold text-sm shadow-lg shadow-rose-900/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
          >
            <ShoppingBag className="w-4 h-4" />
            <span>Beli Sekarang</span>
          </button>
          <button
            onClick={() => onAddToCart(product, selectedVariant || null)}
            className="py-3.5 px-6 rounded-2xl bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold text-sm active:scale-[0.98] transition border border-stone-200 flex items-center justify-center gap-2"
          >
            <span>+ Tambah Keranjang</span>
          </button>
        </div>
      </div>
    </div>
  );
}
