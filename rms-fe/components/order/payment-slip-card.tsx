"use client";

import { ReceiptText } from "lucide-react";
import { Button } from "@/components/ui/button";
import type { OrderDTO } from "@/lib/types";
import { formatCurrency, getPaymentSlipData } from "@/lib/utils";

type PaymentSlipCardProps = {
  order: OrderDTO;
  onDownload: () => void;
};

export function PaymentSlipCard({ order, onDownload }: PaymentSlipCardProps) {
  const slip = getPaymentSlipData(order);

  return (
    <div className="mt-8 rounded-[30px] bg-[#fffaf6] p-6 shadow-soft">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p className="text-sm font-bold uppercase tracking-[0.2em] text-[#ff7a1a]">Payment Slip</p>
          <h3 className="font-display mt-2 text-3xl font-bold text-[#23233f]">Slip Details</h3>
          <p className="mt-2 text-sm text-slate-500">
            Your payment slip is prepared from the saved order data and shown here before download.
          </p>
        </div>
        <Button variant="secondary" onClick={onDownload}>
          Download Payment Slip
        </Button>
      </div>

      <div className="mt-6 rounded-[28px] bg-white p-5 shadow-soft">
        <div className="flex flex-col gap-5 border-b border-orange-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
          <div className="flex items-start gap-4">
            <div className="rounded-3xl bg-[#fff2e6] p-3 text-[#ff7a1a]">
              <ReceiptText className="h-6 w-6" />
            </div>
            <div>
              <p className="font-display text-3xl font-bold text-[#23233f]">{slip.restaurantName}</p>
              <p className="mt-1 text-sm text-slate-500">Payment slip prepared for table service.</p>
            </div>
          </div>
          <div className="space-y-2 text-sm text-slate-500 sm:text-right">
            <p>
              Slip No: <span className="font-semibold text-[#23233f]">{slip.slipNumber}</span>
            </p>
            <p>
              Issued: <span className="font-semibold text-[#23233f]">{slip.issuedAtLabel}</span>
            </p>
            <p>
              Status: <span className="font-semibold text-[#23233f]">{slip.statusLabel}</span>
            </p>
          </div>
        </div>

        <div className="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Order ID</p>
            <p className="mt-2 font-semibold text-[#23233f]">{order.id}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Table</p>
            <p className="mt-2 font-semibold text-[#23233f]">{order.tableNumber}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Payment Mode</p>
            <p className="mt-2 font-semibold text-[#23233f]">{slip.paymentLabel}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Payment State</p>
            <p className="mt-2 font-semibold text-[#23233f]">{slip.paymentStateLabel}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Reference</p>
            <p className="mt-2 font-semibold text-[#23233f]">{slip.reference}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Processed By</p>
            <p className="mt-2 font-semibold text-[#23233f]">{slip.cashierName}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Service Point</p>
            <p className="mt-2 font-semibold text-[#23233f]">{slip.servicePoint}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Guests</p>
            <p className="mt-2 font-semibold text-[#23233f]">{slip.guestCount}</p>
          </div>
        </div>

        <div className="mt-6 space-y-3">
          {order.items.map((item) => (
            <div
              key={item.id}
              className="flex flex-col gap-2 rounded-[24px] bg-[#fffaf6] px-4 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
              <div>
                <p className="font-display text-2xl font-bold text-[#23233f]">
                  {item.quantity}x {item.name}
                </p>
                {item.specialInstructions ? (
                  <p className="mt-1 text-sm text-slate-500">Note: {item.specialInstructions}</p>
                ) : null}
              </div>
              <p className="font-semibold text-[#23233f]">{formatCurrency(item.price * item.quantity)}</p>
            </div>
          ))}
        </div>

        <div className="mt-6 grid gap-4 border-t border-orange-100 pt-5 sm:grid-cols-3">
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Subtotal</p>
            <p className="mt-2 text-xl font-bold text-[#23233f]">{formatCurrency(slip.subtotal)}</p>
          </div>
          <div className="rounded-[24px] bg-[#fffaf6] p-4">
            <p className="text-sm text-slate-400">Service Charge</p>
            <p className="mt-2 text-xl font-bold text-[#23233f]">{formatCurrency(slip.serviceCharge)}</p>
          </div>
          <div className="rounded-[24px] bg-[#23233f] p-4 text-white">
            <p className="text-sm text-white/70">Total</p>
            <p className="mt-2 text-xl font-bold">{formatCurrency(slip.total)}</p>
          </div>
        </div>
      </div>
    </div>
  );
}
