<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\TableUnit;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['table', 'items.menuItem', 'payment'])
            ->when($request->query('venue_id'), fn ($query, $venueId) => $query->where('venue_id', $venueId))
            ->latest()
            ->get()
            ->map(fn (Order $order) => $this->toOrderDto($order));

        return response()->json(['orders' => $orders]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'uuid', 'exists:venues,venue_id'],
            'table_id' => ['nullable', 'uuid', 'exists:table_units,table_id'],
            'table_label' => ['nullable', 'string'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'payment_method' => ['required', 'string'],
            'payment_status' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'uuid', 'exists:menu_items,item_id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.special_instruction' => ['nullable', 'string'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $venue = Venue::findOrFail($data['venue_id']);
            $tableId = $data['table_id'] ?? null;

            if (! $tableId && ! empty($data['table_label'])) {
                $tableId = TableUnit::firstOrCreate(
                    ['venue_id' => $venue->venue_id, 'label' => $data['table_label']],
                    ['section' => 'Dining', 'is_active' => true]
                )->table_id;
            }

            $customer = null;
            if (! empty($data['customer_phone'])) {
                $customer = Customer::create([
                    'venue_id' => $venue->venue_id,
                    'phone_number' => $data['customer_phone'],
                    'created_at' => now(),
                ]);
            }

            $items = collect($data['items'])->map(function (array $input) {
                $menuItem = MenuItem::findOrFail($input['item_id']);

                return [
                    'menu_item' => $menuItem,
                    'quantity' => $input['quantity'],
                    'special_instruction' => $input['special_instruction'] ?? null,
                    'line_total' => (float) $menuItem->price * $input['quantity'],
                ];
            });

            $subtotal = $items->sum('line_total');
            $total = round($subtotal + ($subtotal * ((float) $venue->service_charge_pct / 100)), 2);

            $order = Order::create([
                'venue_id' => $venue->venue_id,
                'table_id' => $tableId,
                'customer_id' => $customer?->customer_id,
                'status' => 'ORDER_TAKEN',
                'total_amount' => $total,
                'estimated_wait_min' => 18,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'item_id' => $item['menu_item']->item_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['menu_item']->price,
                    'special_instruction' => $item['special_instruction'],
                ]);
            }

            Payment::create([
                'order_id' => $order->order_id,
                'amount' => $total,
                'currency' => $venue->currency,
                'method' => $data['payment_method'],
                'status' => $data['payment_status'],
                'paid_at' => $data['payment_status'] === 'PAID' ? now() : null,
            ]);

            Notification::create([
                'venue_id' => $venue->venue_id,
                'order_id' => $order->order_id,
                'recipient_type' => 'kitchen',
                'delivery_method' => 'web',
                'content_snapshot' => 'New order received for table '.$order->table?->label,
                'sent_at' => now(),
            ]);

            return $order->load(['table', 'items.menuItem', 'payment']);
        });

        return response()->json($this->toOrderDto($order), 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($this->toOrderDto($order->load(['table', 'items.menuItem', 'payment'])));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:ORDER_TAKEN,IN_KITCHEN,READY,SERVED,CANCELLED']]);
        $timestamps = [
            'served_at' => $data['status'] === 'SERVED' ? now() : $order->served_at,
            'cancelled_at' => $data['status'] === 'CANCELLED' ? now() : $order->cancelled_at,
        ];

        $order->update(['status' => $data['status']] + $timestamps);

        return response()->json($this->toOrderDto($order->load(['table', 'items.menuItem', 'payment'])));
    }

    public function cancel(Order $order): JsonResponse
    {
        abort_if($order->created_at->diffInMinutes(now()) > 5 || in_array($order->status, ['SERVED', 'CANCELLED'], true), 422, 'Order can no longer be cancelled.');

        $order->update(['status' => 'CANCELLED', 'cancelled_at' => now()]);

        return response()->json($this->toOrderDto($order->load(['table', 'items.menuItem', 'payment'])));
    }

    public function receipt(Payment $payment): JsonResponse
    {
        $receipt = Receipt::firstOrCreate(
            ['payment_id' => $payment->payment_id],
            ['delivery_channel' => 'screen', 'delivered_at' => now()]
        );

        return response()->json($receipt->load('payment.order'));
    }

    private function toOrderDto(Order $order): array
    {
        $payment = $order->payment;

        return [
            'id' => $order->order_id,
            'tableNumber' => $order->table?->label ?? 'Takeaway',
            'total' => (float) $order->total_amount,
            'status' => $order->status,
            'paymentStatus' => $payment?->status ?? 'PAY_ON_TABLE',
            'paymentMethod' => $payment?->method ?? 'CASH',
            'estimatedReadyAt' => $order->created_at->copy()->addMinutes($order->estimated_wait_min)->toISOString(),
            'createdAt' => $order->created_at->toISOString(),
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->order_item_id,
                'menuItemId' => $item->item_id,
                'name' => $item->menuItem?->name,
                'price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'specialInstructions' => $item->special_instruction,
            ])->values(),
        ];
    }
}
