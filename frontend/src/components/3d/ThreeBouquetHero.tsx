"use client";

import React, { useEffect, useRef, useState } from "react";
import * as THREE from "three";
import Image from "next/image";

interface ThreeBouquetHeroProps {
  bouquetColor?: string;
  wrapperColor?: string;
  ribbonColor?: string;
  interactive?: boolean;
}

export default function ThreeBouquetHero({
  bouquetColor = "#c94d75", // Dusty Rose Pink
  wrapperColor = "#f0ded5", // Champagne Kraft
  ribbonColor = "#8e2b4f",  // Deep Wine Satin
  interactive = true,
}: ThreeBouquetHeroProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const [webglSupported, setWebglSupported] = useState<boolean>(true);
  const [isLoaded, setIsLoaded] = useState<boolean>(false);
  const [isDragging, setIsDragging] = useState<boolean>(false);

  useEffect(() => {
    // 1. Preflight WebGL capability check
    try {
      const testCanvas = document.createElement("canvas");
      const gl = testCanvas.getContext("webgl2") || testCanvas.getContext("webgl");
      if (!gl) {
        setWebglSupported(false);
        return;
      }
    } catch {
      setWebglSupported(false);
      return;
    }

    const container = containerRef.current;
    if (!container) return;

    // Check prefers-reduced-motion
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const isMobile = window.innerWidth < 768;

    // 2. Setup Three.js Scene, Camera, Renderer
    const scene = new THREE.Scene();
    const width = container.clientWidth || 600;
    const height = container.clientHeight || 650;

    const camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
    camera.position.set(0, 1.2, isMobile ? 6.2 : 5.2);

    const renderer = new THREE.WebGLRenderer({
      alpha: true,
      antialias: !isMobile, // Disable MSAA on mobile for performance
      powerPreference: "high-performance",
    });

    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, isMobile ? 1.5 : 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.shadowMap.enabled = !isMobile;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    container.appendChild(renderer.domElement);

    // 3. Studio Lighting Rig
    const ambientLight = new THREE.AmbientLight(0xfff6f0, 1.2);
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.0);
    keyLight.position.set(4, 7, 5);
    keyLight.castShadow = !isMobile;
    if (!isMobile) {
      keyLight.shadow.mapSize.width = 1024;
      keyLight.shadow.mapSize.height = 1024;
      keyLight.shadow.bias = -0.001;
    }
    scene.add(keyLight);

    const rimLight = new THREE.DirectionalLight(0xffc5db, 1.4);
    rimLight.position.set(-4, 3, -3);
    scene.add(rimLight);

    const fillLight = new THREE.DirectionalLight(0xffe0c8, 0.9);
    fillLight.position.set(0, -3, 4);
    scene.add(fillLight);

    // 4. Construct Bouquet Hierarchy
    const bouquetGroup = new THREE.Group();
    scene.add(bouquetGroup);

    // Materials
    const rosePrimaryMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(bouquetColor),
      roughness: 0.35,
      metalness: 0.05,
    });

    const roseInnerMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(bouquetColor).offsetHSL(-0.02, 0.1, -0.08),
      roughness: 0.3,
      metalness: 0.05,
    });

    const leafMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(0x385e43),
      roughness: 0.5,
      metalness: 0.02,
    });

    const babyBreathMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(0xfcfbfa),
      roughness: 0.4,
      metalness: 0.1,
    });

    const wrapperOuterMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(wrapperColor),
      roughness: 0.65,
      metalness: 0.08,
      side: THREE.DoubleSide,
    });

    const wrapperInnerMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(0xfdfaf7),
      roughness: 0.7,
      side: THREE.DoubleSide,
    });

    const ribbonMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(ribbonColor),
      roughness: 0.25,
      metalness: 0.2,
      side: THREE.DoubleSide,
    });

    const goldTrimMaterial = new THREE.MeshStandardMaterial({
      color: new THREE.Color(0xdfb76c),
      roughness: 0.3,
      metalness: 0.8,
    });

    // Helper: Build a single Rose blossom
    function createRoseMesh(scale = 1): THREE.Group {
      const rose = new THREE.Group();

      // Flower core
      const coreGeo = new THREE.SphereGeometry(0.16 * scale, 16, 16);
      const coreMesh = new THREE.Mesh(coreGeo, roseInnerMaterial);
      coreMesh.scale.set(1, 1.2, 1);
      rose.add(coreMesh);

      // Layered Spiral Petals
      const petalLayers = [
        { count: 4, radius: 0.18, petalW: 0.24, petalH: 0.28, tilt: 0.2 },
        { count: 6, radius: 0.27, petalW: 0.32, petalH: 0.34, tilt: 0.4 },
        { count: 8, radius: 0.38, petalW: 0.42, petalH: 0.42, tilt: 0.65 },
        { count: 9, radius: 0.50, petalW: 0.52, petalH: 0.48, tilt: 0.85 },
      ];

      petalLayers.forEach((layer, layerIdx) => {
        const mat = layerIdx < 2 ? roseInnerMaterial : rosePrimaryMaterial;
        for (let i = 0; i < layer.count; i++) {
          const angle = (i / layer.count) * Math.PI * 2 + (layerIdx * 0.4);
          const petalShape = new THREE.CylinderGeometry(
            layer.petalW * scale,
            layer.petalW * 0.4 * scale,
            layer.petalH * scale,
            12,
            1,
            true,
            0,
            Math.PI * 0.95
          );

          const petal = new THREE.Mesh(petalShape, mat);
          petal.position.x = Math.cos(angle) * layer.radius * scale;
          petal.position.z = Math.sin(angle) * layer.radius * scale;
          petal.position.y = -layerIdx * 0.04 * scale;

          petal.rotation.y = angle + Math.PI / 2;
          petal.rotation.x = Math.sin(angle) * layer.tilt;
          petal.rotation.z = -Math.cos(angle) * layer.tilt;
          petal.castShadow = !isMobile;
          rose.add(petal);
        }
      });

      return rose;
    }

    // Build Flower arrangement dome
    const flowerHeadGroup = new THREE.Group();
    flowerHeadGroup.position.set(0, 0.45, 0);

    // Center focal rose
    const centerRose = createRoseMesh(1.15);
    centerRose.position.set(0, 0.4, 0);
    flowerHeadGroup.add(centerRose);

    // Surrounding ring of roses
    const surroundingPoses = [
      { x: 0.45, y: 0.22, z: 0.25, rotX: 0.25, rotZ: -0.35, scale: 0.95 },
      { x: -0.42, y: 0.24, z: 0.28, rotX: 0.28, rotZ: 0.32, scale: 0.92 },
      { x: 0.48, y: 0.18, z: -0.32, rotX: -0.35, rotZ: -0.4, scale: 0.9 },
      { x: -0.45, y: 0.2, z: -0.34, rotX: -0.32, rotZ: 0.38, scale: 0.93 },
      { x: 0.0, y: 0.15, z: 0.52, rotX: 0.45, rotZ: 0.0, scale: 1.0 },
      { x: 0.0, y: 0.14, z: -0.54, rotX: -0.45, rotZ: 0.0, scale: 0.94 },
      { x: 0.65, y: -0.02, z: 0.0, rotX: 0.0, rotZ: -0.55, scale: 0.88 },
      { x: -0.65, y: -0.02, z: 0.0, rotX: 0.0, rotZ: 0.55, scale: 0.88 },
    ];

    surroundingPoses.forEach((pose) => {
      const rose = createRoseMesh(pose.scale);
      rose.position.set(pose.x, pose.y, pose.z);
      rose.rotation.x = pose.rotX;
      rose.rotation.z = pose.rotZ;
      flowerHeadGroup.add(rose);
    });

    // Add Eucalyptus and greenery sprigs
    const leafGeo = new THREE.SphereGeometry(0.2, 8, 8);
    leafGeo.scale(0.8, 0.1, 1.4);

    for (let i = 0; i < 14; i++) {
      const angle = (i / 14) * Math.PI * 2 + Math.random() * 0.2;
      const rad = 0.72 + Math.random() * 0.15;
      const leaf = new THREE.Mesh(leafGeo, leafMaterial);
      leaf.position.set(Math.cos(angle) * rad, -0.05 + Math.random() * 0.15, Math.sin(angle) * rad);
      leaf.rotation.y = angle + Math.PI / 2;
      leaf.rotation.z = 0.5 + Math.random() * 0.3;
      flowerHeadGroup.add(leaf);
    }

    // Add Baby's breath florets
    const floretGeo = new THREE.DodecahedronGeometry(0.04, 0);
    const floretInstanced = new THREE.InstancedMesh(floretGeo, babyBreathMaterial, 30);
    const dummy = new THREE.Object3D();
    for (let i = 0; i < 30; i++) {
      const angle = Math.random() * Math.PI * 2;
      const dist = 0.35 + Math.random() * 0.45;
      dummy.position.set(
        Math.cos(angle) * dist,
        0.1 + Math.random() * 0.4,
        Math.sin(angle) * dist
      );
      dummy.scale.setScalar(0.7 + Math.random() * 0.8);
      dummy.updateMatrix();
      floretInstanced.setMatrixAt(i, dummy.matrix);
    }
    flowerHeadGroup.add(floretInstanced);

    bouquetGroup.add(flowerHeadGroup);

    // 5. Bouquet Wrapping Cone & Origami Folds
    const wrapConeOuterGeo = new THREE.CylinderGeometry(0.95, 0.25, 1.8, 28, 1, true);
    const wrapConeOuter = new THREE.Mesh(wrapConeOuterGeo, wrapperOuterMaterial);
    wrapConeOuter.position.set(0, -0.65, 0);
    wrapConeOuter.castShadow = !isMobile;
    wrapConeOuter.receiveShadow = !isMobile;
    bouquetGroup.add(wrapConeOuter);

    // Inner lining wrapper (showing contrast collar)
    const wrapConeInnerGeo = new THREE.CylinderGeometry(0.91, 0.24, 1.76, 24, 1, true);
    const wrapConeInner = new THREE.Mesh(wrapConeInnerGeo, wrapperInnerMaterial);
    wrapConeInner.position.set(0, -0.63, 0);
    bouquetGroup.add(wrapConeInner);

    // Origami Flared Collar Wings
    const flareCount = 6;
    for (let i = 0; i < flareCount; i++) {
      const angle = (i / flareCount) * Math.PI * 2;
      const wingGeo = new THREE.PlaneGeometry(0.65, 0.7);
      const wing = new THREE.Mesh(wingGeo, wrapperOuterMaterial);
      wing.position.set(Math.cos(angle) * 0.82, 0.12, Math.sin(angle) * 0.82);
      wing.rotation.y = angle + Math.PI / 2;
      wing.rotation.x = 0.45;
      bouquetGroup.add(wing);

      // Gold accent edge line on paper
      const goldEdgeGeo = new THREE.BoxGeometry(0.65, 0.015, 0.01);
      const goldEdge = new THREE.Mesh(goldEdgeGeo, goldTrimMaterial);
      goldEdge.position.set(0, 0.34, 0.01);
      wing.add(goldEdge);
    }

    // 6. Satin Ribbon Waistband & Bow
    const waistbandGeo = new THREE.TorusGeometry(0.38, 0.045, 12, 28);
    const waistband = new THREE.Mesh(waistbandGeo, ribbonMaterial);
    waistband.position.set(0, -0.3, 0);
    waistband.rotation.x = Math.PI / 2;
    bouquetGroup.add(waistband);

    // Ribbon Bow Loops
    const bowLoopLeftGeo = new THREE.TorusGeometry(0.18, 0.038, 10, 20, Math.PI * 1.5);
    const bowLeft = new THREE.Mesh(bowLoopLeftGeo, ribbonMaterial);
    bowLeft.position.set(-0.16, -0.3, 0.42);
    bowLeft.rotation.set(0.3, 0.4, 0.6);
    bouquetGroup.add(bowLeft);

    const bowRight = bowLeft.clone();
    bowRight.position.set(0.16, -0.3, 0.42);
    bowRight.rotation.set(0.3, -0.4, -0.6);
    bouquetGroup.add(bowRight);

    // Ribbon Tails hanging down
    const ribbonTailGeo = new THREE.PlaneGeometry(0.11, 0.85);
    const tailLeft = new THREE.Mesh(ribbonTailGeo, ribbonMaterial);
    tailLeft.position.set(-0.12, -0.75, 0.42);
    tailLeft.rotation.set(0.15, 0, 0.2);
    bouquetGroup.add(tailLeft);

    const tailRight = new THREE.Mesh(ribbonTailGeo, ribbonMaterial);
    tailRight.position.set(0.12, -0.78, 0.42);
    tailRight.rotation.set(0.15, 0, -0.25);
    bouquetGroup.add(tailRight);

    // Overall bouquet vertical position & tilt
    bouquetGroup.position.y = -0.2;
    bouquetGroup.rotation.x = 0.15; // Gentle front-facing slant

    setIsLoaded(true);

    // 7. Interaction States (Drag, Auto-Rotate, Parallax, Damping)
    let autoRotate = !prefersReducedMotion;
    let targetRotationY = 0;
    let targetRotationX = 0.15;
    let currentRotationY = 0;
    let currentRotationX = 0.15;

    let mouseParallaxX = 0;
    let mouseParallaxY = 0;
    let currentParallaxX = 0;
    let currentParallaxY = 0;

    let isPointerDown = false;
    let startX = 0;
    let startY = 0;
    let lastInteractionTime = Date.now();

    // Event Handlers
    const handlePointerDown = (e: PointerEvent) => {
      if (!interactive) return;
      isPointerDown = true;
      setIsDragging(true);
      startX = e.clientX;
      startY = e.clientY;
      lastInteractionTime = Date.now();
    };

    const handlePointerMove = (e: PointerEvent) => {
      // Parallax effect on pointer move
      const rect = container.getBoundingClientRect();
      const normX = (e.clientX - rect.left) / rect.width - 0.5;
      const normY = (e.clientY - rect.top) / rect.height - 0.5;
      mouseParallaxX = normX * 0.45;
      mouseParallaxY = -normY * 0.3;

      if (!isPointerDown || !interactive) return;

      const deltaX = e.clientX - startX;
      const deltaY = e.clientY - startY;
      startX = e.clientX;
      startY = e.clientY;

      targetRotationY += deltaX * 0.008;
      targetRotationX += deltaY * 0.005;

      // Clamp vertical tilt to keep bouquet elegant
      targetRotationX = Math.max(-0.25, Math.min(0.45, targetRotationX));
      lastInteractionTime = Date.now();
    };

    const handlePointerUp = () => {
      isPointerDown = false;
      setIsDragging(false);
      lastInteractionTime = Date.now();
    };

    const domElement = renderer.domElement;
    domElement.style.touchAction = "none";
    domElement.addEventListener("pointerdown", handlePointerDown);
    window.addEventListener("pointermove", handlePointerMove);
    window.addEventListener("pointerup", handlePointerUp);

    // Resize Observer
    const handleResize = () => {
      if (!container) return;
      const w = container.clientWidth;
      const h = container.clientHeight;
      camera.aspect = w / h;
      camera.position.z = window.innerWidth < 768 ? 6.2 : 5.2;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    };

    window.addEventListener("resize", handleResize);

    // 8. Animation Loop with Demand Throttling & Visibility Observer
    let animationFrameId: number;
    let isVisible = true;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          isVisible = entry.isIntersecting;
        });
      },
      { threshold: 0.1 }
    );
    observer.observe(container);

    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);

      if (!isVisible) return; // Sleep when scrolled out of view

      const now = Date.now();
      const timeSinceInteraction = now - lastInteractionTime;

      // Slow auto rotation resumes smoothly after 3s of idle
      if (autoRotate && !isPointerDown && timeSinceInteraction > 2500) {
        targetRotationY += 0.0045;
      }

      // Smooth inertia damping
      currentRotationY += (targetRotationY - currentRotationY) * 0.08;
      currentRotationX += (targetRotationX - currentRotationX) * 0.08;

      currentParallaxX += (mouseParallaxX - currentParallaxX) * 0.05;
      currentParallaxY += (mouseParallaxY - currentParallaxY) * 0.05;

      bouquetGroup.rotation.y = currentRotationY;
      bouquetGroup.rotation.x = currentRotationX;

      // Parallax camera displacement
      camera.position.x = currentParallaxX;
      camera.position.y = 1.2 + currentParallaxY;
      camera.lookAt(0, 0, 0);

      renderer.render(scene, camera);
    };

    animate();

    // 9. Comprehensive Resource Disposal on Unmount
    return () => {
      cancelAnimationFrame(animationFrameId);
      observer.disconnect();
      window.removeEventListener("resize", handleResize);
      domElement.removeEventListener("pointerdown", handlePointerDown);
      window.removeEventListener("pointermove", handlePointerMove);
      window.removeEventListener("pointerup", handlePointerUp);

      scene.traverse((obj) => {
        if (obj instanceof THREE.Mesh) {
          obj.geometry?.dispose();
          if (Array.isArray(obj.material)) {
            obj.material.forEach((m) => m.dispose());
          } else if (obj.material) {
            obj.material.dispose();
          }
        }
      });

      renderer.dispose();
      renderer.forceContextLoss();
      if (domElement.parentElement) {
        domElement.parentElement.removeChild(domElement);
      }
    };
  }, [bouquetColor, wrapperColor, ribbonColor, interactive]);

  // Fallback for devices without WebGL
  if (!webglSupported) {
    return (
      <div className="relative w-full h-[480px] sm:h-[580px] flex items-center justify-center p-4">
        <div className="relative w-full max-w-md h-full rounded-3xl overflow-hidden shadow-2xl border border-white/50 bg-gradient-to-br from-rose-100/50 via-white to-pink-50/30">
          <Image
            src="https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=1000&q=85"
            alt="Dusty Rose Romance Bouquet 3D Fallback"
            fill
            priority
            sizes="(max-width: 768px) 100vw, 50vw"
            className="object-cover transition-transform duration-700 hover:scale-105"
          />
          <div className="absolute bottom-4 left-4 right-4 p-3 rounded-2xl glass-panel text-xs text-stone-700 text-center font-medium shadow-md">
            🌸 MocYy Signature Romance Bouquet
          </div>
        </div>
      </div>
    );
  }

  return (
    <div
      ref={containerRef}
      className={`relative w-full h-[450px] sm:h-[560px] lg:h-[640px] select-none flex items-center justify-center cursor-grab ${
        isDragging ? "cursor-grabbing" : ""
      }`}
      aria-label="3D Interactive Flower Bouquet Hero Object"
      role="img"
    >
      {/* Loading Shimmer Placeholder */}
      {!isLoaded && (
        <div className="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-rose-50/30 rounded-3xl backdrop-blur-sm animate-pulse">
          <div className="w-16 h-16 rounded-full border-3 border-rose-300 border-t-rose-600 animate-spin" />
          <p className="text-sm font-medium text-stone-500">Memuat Visual 3D Bouquet...</p>
        </div>
      )}

      {/* Floating 3D Interaction Badge */}
      <div className="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full glass-panel text-xs font-semibold text-stone-700 tracking-wide flex items-center gap-2 pointer-events-none shadow-sm z-10 transition-opacity duration-300 opacity-90 hover:opacity-100">
        <span className="w-2 h-2 rounded-full bg-emerald-500 animate-ping" />
        <span>Sentuh & Geser untuk Memutar 360°</span>
      </div>
    </div>
  );
}
