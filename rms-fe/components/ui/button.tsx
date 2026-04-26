import * as React from "react";
import { cn } from "@/lib/utils";

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: "primary" | "secondary" | "ghost" | "danger";
}

export const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = "primary", ...props }, ref) => {
    return (
      <button
        ref={ref}
        className={cn(
          "inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-orange-300 disabled:cursor-not-allowed disabled:opacity-50",
          variant === "primary" &&
            "bg-gradient-to-r from-[#ff7a1a] via-[#ff8a1f] to-[#ff9f36] text-white shadow-soft hover:-translate-y-0.5 hover:shadow-lg",
          variant === "secondary" &&
            "border border-[rgba(255,133,36,0.18)] bg-white text-[#23233f] hover:bg-[#fff4e8]",
          variant === "ghost" && "bg-transparent text-slate-600 hover:bg-white/70",
          variant === "danger" && "bg-[#f25a5a] text-white hover:bg-[#e04747]",
          className
        )}
        {...props}
      />
    );
  }
);

Button.displayName = "Button";
