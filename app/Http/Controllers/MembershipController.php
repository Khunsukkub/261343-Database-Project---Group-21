<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ยอดสั่งซื้อรวมทั้งหมด (เฉพาะที่ชำระแล้ว)
        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('total_amount');

        // เกณฑ์ tier
        $th = config('membership.thresholds');
        $tiers = [
            'bronze' => [
                'min' => 0,
                'discount' => 0,
                'desc' => 'สมาชิกทั่วไป ไม่ลดราคา'
            ],
            'silver' => [
                'min' => $th['silver'] ?? 300,
                'discount' => 5,
                'desc' => 'ลดราคาสินค้า 5%'
            ],
            'gold' => [
                'min' => $th['gold'] ?? 1000,
                'discount' => 10,
                'desc' => 'ลดราคาสินค้า 10%'
            ],
        ];

        return view('membership', compact('user', 'totalSpent', 'tiers'));
    }
}
