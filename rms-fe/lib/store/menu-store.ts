"use client";

import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";
import { sampleMenuItems } from "@/lib/data/menu";
import type { MenuItemDTO } from "@/lib/types";

type UpsertMenuItemInput = Omit<MenuItemDTO, "id"> & { id?: string };

type MenuStore = {
  items: MenuItemDTO[];
  setItems: (items: MenuItemDTO[]) => void;
  upsertItem: (item: UpsertMenuItemInput) => void;
  deleteItem: (id: string) => void;
  toggleAvailability: (id: string) => void;
  resetMenu: () => void;
};

function normalizeMenuItems(items: MenuItemDTO[]) {
  const byId = new Map(items.map((item) => [item.id, item] as const));

  for (const sampleItem of sampleMenuItems) {
    if (!byId.has(sampleItem.id)) {
      byId.set(sampleItem.id, sampleItem);
    }
  }

  return Array.from(byId.values());
}

export const useMenuStore = create<MenuStore>()(
  persist(
    (set) => ({
      items: sampleMenuItems,
      setItems: (items) => set({ items }),
      upsertItem: (item) =>
        set((state) => {
          const id = item.id ?? `menu-${Date.now()}`;
          const nextItem = { ...item, id } as MenuItemDTO;
          const exists = state.items.some((menuItem) => menuItem.id === id);
          return {
            items: exists
              ? state.items.map((menuItem) => (menuItem.id === id ? nextItem : menuItem))
              : [nextItem, ...state.items]
          };
        }),
      deleteItem: (id) =>
        set((state) => ({
          items: state.items.filter((item) => item.id !== id)
        })),
      toggleAvailability: (id) =>
        set((state) => ({
          items: state.items.map((item) =>
            item.id === id ? { ...item, available: !item.available } : item
          )
        })),
      resetMenu: () => set({ items: sampleMenuItems })
    }),
    {
      name: "restaurant-menu",
      storage: createJSONStorage(() => localStorage),
      merge: (persistedState, currentState) => {
        const typedPersisted = persistedState as Partial<MenuStore> | undefined;
        return {
          ...currentState,
          ...typedPersisted,
          items: normalizeMenuItems(typedPersisted?.items ?? currentState.items)
        };
      }
    }
  )
);
