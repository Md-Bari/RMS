"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { BellRing, MapPin } from "lucide-react";
import { Button } from "@/components/ui/button";
import { CartButton } from "@/components/cart/cart-button";
import { Input } from "@/components/ui/input";
import { useTableStore } from "@/lib/store/table-store";
import { useToastStore } from "@/lib/store/toast-store";

export function CustomerActions() {
  const joinedTable = useTableStore((state) => state.joinedTable);
  const joinTable = useTableStore((state) => state.joinTable);
  const notifyTable = useTableStore((state) => state.notifyTable);
  const pushToast = useToastStore((state) => state.pushToast);
  const [hydrated, setHydrated] = useState(false);
  const [tableInput, setTableInput] = useState("");

  useEffect(() => {
    setHydrated(true);
  }, []);

  useEffect(() => {
    if (joinedTable) {
      setTableInput(joinedTable);
    }
  }, [joinedTable]);

  return (
    <div className="flex flex-wrap items-center gap-3">
      <Link href="/track">
        <Button variant="secondary" className="bg-white">
          Track Order
        </Button>
      </Link>
      <div className="flex flex-wrap items-center gap-2 rounded-full bg-white p-2 shadow-soft">
        <div className="flex items-center gap-2 rounded-full bg-[#fff5ea] px-4 py-3 text-sm font-semibold text-slate-500">
          <MapPin className="h-4 w-4 text-[#ff7a1a]" />
          {hydrated && joinedTable ? `Table ${joinedTable}` : "Set Table"}
        </div>
        <Input
          value={tableInput}
          onChange={(event) => setTableInput(event.target.value.toUpperCase())}
          placeholder="Table no."
          className="w-32 border-0 bg-transparent px-3 py-2 shadow-none focus:ring-0"
        />
        <Button
          type="button"
          variant="secondary"
          className="bg-white"
          onClick={() => {
            if (!tableInput.trim()) return;
            const normalizedTable = tableInput.trim().toUpperCase();
            joinTable(normalizedTable);
            setTableInput(normalizedTable);
            pushToast({
              title: `Joined table ${normalizedTable}`,
              description: "Your order will stay connected to this table on this device.",
              tone: "info"
            });
          }}
        >
          Save
        </Button>
      </div>
      <Button
        type="button"
        variant="secondary"
        className="bg-white text-slate-700"
        onClick={() => {
          let activeTable = joinedTable;
          if (!activeTable && tableInput.trim()) {
            activeTable = tableInput.trim().toUpperCase();
            joinTable(activeTable);
          }
          if (!activeTable) return;
          notifyTable();
          pushToast({
            title: `Staff notified for table ${activeTable}`,
            description: "A service request has been logged in.",
            tone: "success"
          });
        }}
      >
        <BellRing className="mr-2 h-4 w-4" />
        Notify Table
      </Button>
      <CartButton />
    </div>
  );
}
