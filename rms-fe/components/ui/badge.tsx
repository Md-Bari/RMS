import type { ReactNode } from "react";
import { cn } from "@/lib/utils";

export function Badge({
  children,
  className
}: {
  children: ReactNode;
  className?: string;
}) {
  return (
    <span
      className={cn(
        "inline-flex items-center rounded-full border border-[rgba(255,133,36,0.14)] bg-[#fff2e6] px-3 py-1 text-xs font-semibold text-[#ff7a1a]",
        className
      )}
    >
      {children}
    </span>
  );
}
