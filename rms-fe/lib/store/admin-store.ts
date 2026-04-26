"use client";

import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";

const defaultCredentials = {
  email: "admin@savoria.local",
  password: "admin123"
};

type AdminStore = {
  isAuthenticated: boolean;
  login: (email: string, password: string) => boolean;
  logout: () => void;
};

export const useAdminStore = create<AdminStore>()(
  persist(
    (set) => ({
      isAuthenticated: false,
      login: (email, password) => {
        const success =
          email.trim().toLowerCase() === defaultCredentials.email &&
          password === defaultCredentials.password;
        if (success) {
          set({ isAuthenticated: true });
        }
        return success;
      },
      logout: () => set({ isAuthenticated: false })
    }),
    {
      name: "restaurant-admin",
      storage: createJSONStorage(() => localStorage)
    }
  )
);
