<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StaffUser;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'pin' => ['required', 'string']]);
        $staff = StaffUser::where('email', $data['email'])->first();

        abort_if(! $staff || ! Hash::check($data['pin'], $staff->pin_hash), 401, 'Invalid credentials.');

        $staff->update(['last_login_at' => now(), 'failed_logins' => 0]);

        return response()->json(['staff' => $staff->load('venue')]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $venueId = $request->query('venue_id') ?? Venue::query()->value('venue_id');
        $orders = Order::with(['table', 'items.menuItem', 'payment'])->where('venue_id', $venueId)->latest()->get();

        return response()->json([
            'venue_id' => $venueId,
            'totalOrders' => $orders->count(),
            'revenue' => round($orders->where('status', '!=', 'CANCELLED')->sum(fn ($order) => (float) $order->total_amount), 2),
            'activeOrders' => $orders->whereIn('status', ['ORDER_TAKEN', 'IN_KITCHEN', 'READY'])->count(),
            'completed' => $orders->where('status', 'SERVED')->count(),
            'cancelled' => $orders->where('status', 'CANCELLED')->count(),
            'orders' => $orders,
        ]);
    }
}
