"use client";

import { create } from "zustand";

export type ToastTone = "success" | "info" | "warning";

export type ToastItem = {
  id: string;
  title: string;
  description?: string;
  tone?: ToastTone;
};

type ToastStore = {
  toasts: ToastItem[];
  pushToast: (toast: Omit<ToastItem, "id">) => string;
  dismissToast: (id: string) => void;
};

function createToastId() {
  return `toast-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
}

export const useToastStore = create<ToastStore>()((set) => ({
  toasts: [],
  pushToast: (toast) => {
    const id = createToastId();
    set((state) => ({
      toasts: [...state.toasts, { id, ...toast }]
    }));
    return id;
  },
  dismissToast: (id) =>
    set((state) => ({
      toasts: state.toasts.filter((toast) => toast.id !== id)
    }))
}));
