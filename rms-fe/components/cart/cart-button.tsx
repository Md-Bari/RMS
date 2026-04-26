"use client";

import { ShoppingBag } from "lucide-react";
import { useCart } from "@/lib/store/cart-store";

export function CartButton() {
  const openCart = useCart((state) => state.openCart);
  const count = useCart((state) => state.count);

  return (
    <button
      type="button"
      onClick={openCart}
      className="relative rounded-full bg-[#23233f] px-5 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5"
    >
      <span className="flex items-center gap-2">
        <ShoppingBag className="h-4 w-4" />
        Cart
      </span>
      {count ? (
        <span className="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-orange-500 text-xs text-white">
          {count}
        </span>
      ) : null}
    </button>
  );
}
