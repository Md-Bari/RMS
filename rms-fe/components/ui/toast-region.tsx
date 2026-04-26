"use client";

import { useEffect, useState } from "react";
import { CheckCircle2, Info, X, AlertCircle } from "lucide-react";
import { useToastStore, type ToastItem } from "@/lib/store/toast-store";

function ToastCard({
  toast,
  onDismiss
}: {
  toast: ToastItem;
  onDismiss: (id: string) => void;
}) {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const enterTimer = window.setTimeout(() => setVisible(true), 10);
    const dismissTimer = window.setTimeout(() => {
      setVisible(false);
      window.setTimeout(() => onDismiss(toast.id), 220);
    }, 3400);

    return () => {
      window.clearTimeout(enterTimer);
      window.clearTimeout(dismissTimer);
    };
  }, [onDismiss, toast.id]);

  const tone = toast.tone ?? "info";
  const icon =
    tone === "success" ? (
      <CheckCircle2 className="h-5 w-5" />
    ) : tone === "warning" ? (
      <AlertCircle className="h-5 w-5" />
    ) : (
      <Info className="h-5 w-5" />
    );

  return (
    <div
      className={`pointer-events-auto w-full max-w-md rounded-[28px] border bg-white/95 p-4 shadow-soft backdrop-blur transition-all duration-200 ${
        visible ? "translate-y-0 opacity-100" : "-translate-y-4 opacity-0"
      } ${
        tone === "success"
          ? "border-emerald-100"
          : tone === "warning"
            ? "border-amber-100"
            : "border-[rgba(255,133,36,0.14)]"
      }`}
    >
      <div className="flex items-start gap-3">
        <div
          className={`mt-0.5 rounded-full p-2 ${
            tone === "success"
              ? "bg-emerald-50 text-emerald-600"
              : tone === "warning"
                ? "bg-amber-50 text-amber-600"
                : "bg-[#fff2e6] text-[#ff7a1a]"
          }`}
        >
          {icon}
        </div>
        <div className="min-w-0 flex-1">
          <p className="text-sm font-semibold text-[#23233f]">{toast.title}</p>
          {toast.description ? (
            <p className="mt-1 text-sm leading-6 text-slate-500">{toast.description}</p>
          ) : null}
        </div>
        <button
          type="button"
          className="rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
          onClick={() => onDismiss(toast.id)}
          aria-label="Dismiss notification"
        >
          <X className="h-4 w-4" />
        </button>
      </div>
    </div>
  );
}

export function ToastRegion() {
  const toasts = useToastStore((state) => state.toasts);
  const dismissToast = useToastStore((state) => state.dismissToast);

  return (
    <div className="pointer-events-none fixed inset-x-0 top-4 z-[100] flex flex-col items-center gap-3 px-4">
      {toasts.map((toast) => (
        <ToastCard key={toast.id} toast={toast} onDismiss={dismissToast} />
      ))}
    </div>
  );
}
