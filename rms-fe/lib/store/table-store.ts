"use client";

import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";

type TableStore = {
  joinedTable: string;
  notifyCount: number;
  joinTable: (table: string) => void;
  notifyTable: () => void;
  leaveTable: () => void;
};

export const useTableStore = create<TableStore>()(
  persist(
    (set) => ({
      joinedTable: "",
      notifyCount: 0,
      joinTable: (table) => set({ joinedTable: table.trim().toUpperCase() }),
      notifyTable: () => set((state) => ({ notifyCount: state.notifyCount + 1 })),
      leaveTable: () => set({ joinedTable: "", notifyCount: 0 })
    }),
    {
      name: "restaurant-table",
      storage: createJSONStorage(() => localStorage)
    }
  )
);
