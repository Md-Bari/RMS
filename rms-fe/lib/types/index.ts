export const categories = ["Food", "Drinks", "Desserts"] as const;
export type MenuCategory = (typeof categories)[number];

export const dietaryFilters = [
  "Vegetarian",
  "Vegan",
  "Halal",
  "Gluten-Free",
  "Spicy"
] as const;
export type DietaryFilter = (typeof dietaryFilters)[number];

export const orderStatuses = [
  "ORDER_TAKEN",
  "IN_KITCHEN",
  "READY",
  "SERVED",
  "CANCELLED"
] as const;
export type OrderStatus = (typeof orderStatuses)[number];

export const paymentMethods = ["CASH", "CARD", "BKASH", "NAGAD", "ROCKET"] as const;
export type PaymentMethod = (typeof paymentMethods)[number];

export const paymentStatuses = ["PAID", "PAY_ON_TABLE"] as const;
export type PaymentStatus = (typeof paymentStatuses)[number];

export type MenuItemDTO = {
  id: string;
  name: string;
  description: string;
  price: number;
  category: MenuCategory;
  image?: string;
  imageUrl: string;
  tags: string[];
  dietaryLabels: string[];
  nutritionCalories: number;
  nutritionProtein: number;
  nutritionCarbs: number;
  spicy: boolean;
  vegetarian: boolean;
  vegan: boolean;
  halal: boolean;
  glutenFree: boolean;
  available: boolean;
  healthScore: number;
};

export type CartItem = {
  id: string;
  name: string;
  price: number;
  imageUrl: string;
  quantity: number;
  specialInstructions?: string;
};

export type OrderItemDTO = {
  id: string;
  menuItemId: string;
  name: string;
  price: number;
  quantity: number;
  specialInstructions: string | null;
};

export type OrderDTO = {
  id: string;
  tableNumber: string;
  total: number;
  status: OrderStatus;
  paymentStatus: PaymentStatus;
  paymentMethod: PaymentMethod;
  paymentLast4?: string;
  paymentAccount?: string;
  estimatedReadyAt: string;
  createdAt: string;
  items: OrderItemDTO[];
};
