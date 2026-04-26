"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { useAdminStore } from "@/lib/store/admin-store";
import { useToastStore } from "@/lib/store/toast-store";

export function AdminLoginForm() {
  const router = useRouter();
  const login = useAdminStore((state) => state.login);
  const pushToast = useToastStore((state) => state.pushToast);
  const [email, setEmail] = useState("admin@savoria.local");
  const [password, setPassword] = useState("admin123");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  return (
    <form
      className="space-y-5"
      onSubmit={async (event) => {
        event.preventDefault();
        setLoading(true);
        setError("");

        const success = login(email, password);
        setLoading(false);

        if (!success) {
          setError("Invalid credentials. Use admin@savoria.local / admin123.");
          return;
        }

        pushToast({
          title: "Admin login successful",
          description: "You can now manage orders and menu items.",
          tone: "success"
        });
        router.push("/admin/dashboard");
      }}
    >
      <div className="space-y-2">
        <label className="text-sm font-semibold text-slate-700">Email</label>
        <Input value={email} onChange={(event) => setEmail(event.target.value)} type="email" required />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold text-slate-700">Password</label>
        <Input
          value={password}
          onChange={(event) => setPassword(event.target.value)}
          type="password"
          required
        />
      </div>
      <div className="rounded-[24px] bg-[#fff7f0] px-4 py-4 text-sm text-slate-500">
        Demo credentials: <span className="font-semibold text-[#23233f]">admin@savoria.local</span> /{" "}
        <span className="font-semibold text-[#23233f]">admin123</span>
      </div>
      {error ? <p className="text-sm text-rose-500">{error}</p> : null}
      <Button className="w-full" disabled={loading}>
        {loading ? "Signing In..." : "Sign In"}
      </Button>
    </form>
  );
}
