<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    public function index(Request $r)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $r->user()->id)
            ->latest()->paginate(10);

        $counts = Order::selectRaw('status, COUNT(*) c')
            ->where('user_id',$r->user()->id)
            ->groupBy('status')->pluck('c','status');

        return view('orders.history', [
            'orders' => $orders,
            'counts' => [
                'shipped' => $counts['shipped'] ?? 0,
                'processing' => $counts['processing'] ?? 0,
                'pending_payment' => $counts['pending_payment'] ?? 0,
            ],
        ]);
    }

    public function cancel(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        if ($order->status === 'pending_payment') $order->update(['status'=>'cancelled']);
        return back();
    }

    public function pay(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        if ($order->status === 'pending_payment') $order->update(['status'=>'processing']);
        return back();
    }
}

