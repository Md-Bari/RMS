"use client";

import type { ReactNode } from "react";
import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";
import type { CartItem } from "@/lib/types";

type CartStore = {
  items: CartItem[];
  isOpen: boolean;
  total: number;
  count: number;
  openCart: () => void;
  closeCart: () => void;
  addItem: (item: Omit<CartItem, "quantity">, quantity?: number) => void;
  removeItem: (id: string) => void;
  updateQuantity: (id: string, quantity: number) => void;
  clearCart: () => void;
};

const computeTotals = (items: CartItem[]) => ({
  total: items.reduce((sum, item) => sum + item.price * item.quantity, 0),
  count: items.reduce((sum, item) => sum + item.quantity, 0)
});

export const useCart = create<CartStore>()(
  persist(
    (set, get) => ({
      items: [],
      isOpen: false,
      total: 0,
      count: 0,
      openCart: () => set({ isOpen: true }),
      closeCart: () => set({ isOpen: false }),
      addItem: (item, quantity = 1) =>
        set((state) => {
          const existing = state.items.find(
            (cartItem) =>
              cartItem.id === item.id &&
              (cartItem.specialInstructions ?? "") === (item.specialInstructions ?? "")
          );

          const items = existing
            ? state.items.map((cartItem) =>
                cartItem === existing
                  ? { ...cartItem, quantity: cartItem.quantity + quantity }
                  : cartItem
              )
            : [...state.items, { ...item, quantity }];

          return { items, ...computeTotals(items) };
        }),
      removeItem: (id) =>
        set((state) => {
          const items = state.items.filter(
            (item) => `${item.id}-${item.specialInstructions ?? ""}` !== id
          );
          return { items, ...computeTotals(items) };
        }),
      updateQuantity: (id, quantity) =>
        set((state) => {
          const items = state.items.flatMap((item) => {
            const key = `${item.id}-${item.specialInstructions ?? ""}`;
            if (key !== id) return [item];
            if (quantity <= 0) return [];
            return [{ ...item, quantity }];
          });
          return { items, ...computeTotals(items) };
        }),
      clearCart: () => set({ items: [], ...computeTotals([]) })
    }),
    {
      name: "restaurant-cart",
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        items: state.items,
        total: state.total,
        count: state.count
      }),
      onRehydrateStorage: () => (state) => {
        if (!state) return;
        const totals = computeTotals(state.items);
        state.total = totals.total;
        state.count = totals.count;
      }
    }
  )
);

export function CartProvider({ children }: { children: ReactNode }) {
  return children;
}
