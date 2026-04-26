import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
import type { OrderDTO } from "@/lib/types";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatCurrency(amount: number) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD"
  }).format(amount);
}

export function getRestaurantName() {
  return process.env.NEXT_PUBLIC_RESTAURANT_NAME ?? "Savoria Table";
}

export function minutesFromNow(minutes: number) {
  return new Date(Date.now() + minutes * 60 * 1000).toISOString();
}

export function getPaymentLabel(method: string) {
  if (method === "BKASH") return "bKash";
  if (method === "NAGAD") return "Nagad";
  if (method === "ROCKET") return "Rocket";
  if (method === "CARD") return "Card";
  return "Cash";
}

function getCashierName(orderId: string) {
  const names = ["Mira Sen", "Rafi Noor", "Lina Das", "Arman Roy"];
  const seed = orderId.split("").reduce((sum, char) => sum + char.charCodeAt(0), 0);
  return names[seed % names.length];
}

export function getPaymentSlipData(order: OrderDTO) {
  const subtotal = order.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const paymentLabel = getPaymentLabel(order.paymentMethod);
  const orderDigits = order.id.replace(/\D/g, "");
  const numericSeed = Number(orderDigits || "1");
  const guestCount = Math.max(1, Math.min(6, order.items.reduce((sum, item) => sum + item.quantity, 0)));
  const paymentStateLabel = order.paymentStatus === "PAY_ON_TABLE" ? "Pending At Table" : "Paid";
  const reference =
    order.paymentMethod === "CARD"
      ? `CARD-${order.paymentLast4 ?? String(numericSeed).slice(-4).padStart(4, "0")}`
      : order.paymentMethod === "CASH"
        ? `CASH-TABLE-${order.tableNumber}`
        : `${order.paymentMethod}-${order.paymentAccount ?? String(numericSeed).slice(-4).padStart(4, "0")}`;

  return {
    restaurantName: getRestaurantName(),
    slipNumber: `PS-${order.id}`,
    issuedAtLabel: new Date(order.createdAt).toLocaleString(),
    statusLabel: order.status.replaceAll("_", " "),
    paymentLabel,
    paymentStateLabel,
    reference,
    cashierName: getCashierName(order.id),
    servicePoint: `Dining Hall ${((numericSeed % 4) + 1).toString().padStart(2, "0")}`,
    guestCount,
    subtotal,
    serviceCharge: 0,
    total: order.total
  };
}

function escapeHtml(value: string) {
  return value
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

export function openInvoicePdf(order: OrderDTO) {
  if (typeof window === "undefined") {
    return;
  }

  const invoiceWindow = window.open("", "_blank", "noopener,noreferrer,width=960,height=720");
  if (!invoiceWindow) {
    return;
  }

  const itemsMarkup = order.items
    .map(
      (item) => `
        <tr>
          <td>${escapeHtml(item.name)}</td>
          <td>${item.quantity}</td>
          <td>${formatCurrency(item.price)}</td>
          <td>${formatCurrency(item.price * item.quantity)}</td>
        </tr>
      `
    )
    .join("");

  const slip = getPaymentSlipData(order);

  invoiceWindow.document.write(`
    <!doctype html>
    <html>
      <head>
        <title>Payment Slip ${escapeHtml(order.id)}</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 32px;
            color: #23233f;
            background: #fffaf4;
          }
          .sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 12px 40px rgba(35, 35, 63, 0.08);
          }
          .brand {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.04em;
          }
          .brand span { color: #ff7a1a; }
          .meta, .summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
          }
          .card {
            background: #fff6ee;
            border-radius: 18px;
            padding: 16px;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
          }
          th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #f3e4d7;
          }
          th { color: #7b7288; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; }
          .total {
            margin-top: 24px;
            text-align: right;
            font-size: 28px;
            font-weight: 800;
          }
          .note {
            margin-top: 20px;
            font-size: 13px;
            color: #6b7280;
          }
          .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
          }
          .button {
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
          }
          .button-primary {
            background: #ff7a1a;
            color: white;
          }
          .button-secondary {
            background: #23233f;
            color: white;
          }
          @media print {
            body { background: #ffffff; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; }
            .actions { display: none; }
          }
        </style>
      </head>
      <body>
        <div class="sheet">
          <div class="brand">SAV<span>OR.</span></div>
          <p style="margin: 8px 0 0; color: #6b7280;">Payment slip for your restaurant order</p>

          <div class="meta">
            <div class="card">
              <strong>Slip Number</strong>
              <div>${escapeHtml(slip.slipNumber)}</div>
            </div>
            <div class="card">
              <strong>Table</strong>
              <div>${escapeHtml(order.tableNumber)}</div>
            </div>
            <div class="card">
              <strong>Payment</strong>
              <div>${escapeHtml(slip.paymentLabel)} - ${escapeHtml(slip.paymentStateLabel)}</div>
            </div>
            <div class="card">
              <strong>Reference</strong>
              <div>${escapeHtml(slip.reference)}</div>
            </div>
            <div class="card">
              <strong>Issued</strong>
              <div>${escapeHtml(slip.issuedAtLabel)}</div>
            </div>
            <div class="card">
              <strong>Processed By</strong>
              <div>${escapeHtml(slip.cashierName)}</div>
            </div>
            <div class="card">
              <strong>Service Point</strong>
              <div>${escapeHtml(slip.servicePoint)}</div>
            </div>
            <div class="card">
              <strong>Guests</strong>
              <div>${slip.guestCount}</div>
            </div>
          </div>

          <table>
            <thead>
              <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>${itemsMarkup}</tbody>
          </table>

          <div class="summary">
            <div class="card">
              <strong>Status</strong>
              <div>${escapeHtml(slip.statusLabel)}</div>
            </div>
            <div class="card">
              <strong>Estimated Ready</strong>
              <div>${new Date(order.estimatedReadyAt).toLocaleString()}</div>
            </div>
          </div>

          <div class="total">Grand Total: ${formatCurrency(slip.total)}</div>
          <div class="note">Use the print button below and choose "Save as PDF" to download this payment slip.</div>
          <div class="actions">
            <button class="button button-primary" onclick="window.print()">Print Or Save PDF</button>
            <button class="button button-secondary" onclick="window.close()">Close</button>
          </div>
        </div>
      </body>
    </html>
  `);
  invoiceWindow.document.close();
}
