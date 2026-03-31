// User types
export enum UserRole {
  ADMIN = 'admin',
  USER = 'user'
}

export interface User {
  id: number;
  email: string;
  role: UserRole;
  created_at: string;
}

export interface AuthResponse {
  token: string;
  user: User;
}

// Product types
export enum ProductCategory {
  MOTORCYCLES = 'motorcycles',
  ATV = 'atv',
  SNOWMOBILES = 'snowmobiles',
  BOATS = 'boats',
  OUTBOARD_MOTORS = 'outboard_motors'
}

export interface Category {
  id: number;
  name: string;
  slug: ProductCategory;
}

export enum ProductStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive'
}

export interface Product {
  id: number;
  category_id: number;
  title: string;
  description: string;
  price: number;
  image: string;
  status: ProductStatus;
  created_at: string;
  updated_at: string;
}

// Order types
export enum OrderStatus {
  PENDING = 'pending',
  PROCESSING = 'processing',
  COMPLETED = 'completed',
  CANCELLED = 'cancelled'
}

export interface Order {
  id: number;
  user_id: number;
  status: OrderStatus;
  total: number;
  created_at: string;
}

export interface OrderItem {
  id: number;
  order_id: number;
  product_id: number;
  quantity: number;
  price: number;
  product?: Product;
}

// Cart types
export interface CartItem {
  product: Product;
  quantity: number;
}

// News types
export interface News {
  id: number;
  title: string;
  content: string;
  image: string;
  created_at: string;
}

// Promotion types
export interface Promotion {
  id: number;
  title: string;
  content: string;
  image: string;
  discount: number;
  active: boolean;
  created_at: string;
}

// Feedback types
export interface Feedback {
  id: number;
  user_id: number;
  message: string;
  status: 'new' | 'read' | 'replied';
  created_at: string;
}

// Promo code types
export interface PromoCode {
  id: number;
  code: string;
  discount: number;
  active: boolean;
  expires_at: string;
}

// API response type
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
}