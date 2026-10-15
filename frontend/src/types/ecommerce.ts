export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  image: string | null;
  products_count?: number;
}

export interface ProductVariant {
  id: number;
  variant_name: string;
  variant_value: string;
  additional_price: number;
  effective_variant_price: number;
  stock: number;
}

export interface ProductImage {
  id: number;
  image_path: string;
  is_primary: boolean;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  sku: string;
  description: string;
  price: number;
  discount_price: number | null;
  effective_price: number;
  has_discount: boolean;
  discount_percentage: number;
  stock: number;
  weight_grams: number;
  main_image: string;
  is_featured: boolean;
  lead_time_days: number;
  allows_greeting_card: boolean;
  category?: Category | null;
  images?: ProductImage[];
  variants?: ProductVariant[];
  model_3d_url?: string;
}

export interface ShippingMethod {
  id: number;
  name: string;
  code: string;
  description: string;
  cost: number;
  estimated_time: string;
}

export interface PaymentMethod {
  id: number;
  name: string;
  code: string;
  account_name: string | null;
  account_number: string | null;
  qr_image_url: string | null;
  instructions: string;
  expiry_minutes: number;
}

export interface CartItem {
  product: Product;
  variant?: ProductVariant | null;
  quantity: number;
  custom_note?: string;
  greeting_card_note?: string;
}

export interface StoreSettings {
  store_name: string;
  store_tagline: string;
  store_phone: string;
  store_whatsapp: string;
  store_email: string;
  store_address: string;
  store_instagram: string;
  working_hours: string;
}

export interface CheckoutPayload {
  recipient_name: string;
  recipient_phone: string;
  full_address: string;
  city: string;
  postal_code: string;
  shipping_method_id: number;
  payment_method_id: number;
  items: Array<{
    product_id: number;
    variant_id?: number | null;
    quantity: number;
  }>;
  customer_note?: string;
  greeting_card_note?: string;
  customer_email?: string;
}

export interface OrderSuccessData {
  order_number: string;
  recipient_name: string;
  subtotal: number;
  shipping_cost: number;
  grand_total: number;
  payment_status: string;
  order_status: string;
  payment_method: {
    name: string;
    code: string;
    account_name: string | null;
    account_number: string | null;
    qr_image_url: string | null;
    instructions: string;
    expiry_minutes: number;
  };
  shipping_method: {
    name: string;
    estimated_time: string;
  };
}
