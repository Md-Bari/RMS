import type { ReactNode } from "react";
import { cn } from "@/lib/utils";

export function Card({
  children,
  className
}: {
  children?: ReactNode;
  className?: string;
}) {
  return (
    <div
      className={cn(
        "glass-panel rounded-[32px] border border-[rgba(255,133,36,0.14)] bg-white/90 shadow-soft",
        className
      )}
    >
      {children}
    </div>
  );
}
