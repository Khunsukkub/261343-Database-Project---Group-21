<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Validation\ValidationException;

class OrderHistoryController extends Controller
{
    public function index(Request $req)
    {
        $userId = $req->user()->id;

        // ดึงออเดอร์ (ยังคงสถานะเดิมใน DB ไว้ได้ แต่เราจะแสดงผลแบบ map ใน view หรือ map นับที่นี่)
        $orders = Order::with(['items.product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC') 
            ->orderBy('id', 'DESC')  
            ->paginate(10);

        // นับสถานะ แล้ว map legacy -> 3 สถานะใหม่
        $raw = Order::where('user_id', $userId)
            ->selectRaw('status, COUNT(*) c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        // รวมคีย์ที่อาจมีอยู่เดิม
        $legacyPending = ($raw['pending_payment'] ?? 0) + ($raw['unpaid'] ?? 0);
        // ให้ทุกอย่างที่ไม่ใช่ pending/cancelled ถือเป็น paid: รวม 'paid', 'processing', 'shipped'
        $paid = ($raw['paid'] ?? 0) + ($raw['processing'] ?? 0) + ($raw['shipped'] ?? 0);
        $cancelled = ($raw['cancelled'] ?? 0);

        $counts = [
            'paid'             => $paid,
            'pending_payment'  => $legacyPending,
            'cancelled'        => $cancelled,
        ];

        return view('orders.history', compact('orders', 'counts'));
    }

    public function cancel(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        // อนุญาตยกเลิกเฉพาะที่ยังค้างชำระ
        if (in_array($order->status, ['pending_payment', 'unpaid'], true)) {
            $order->update(['status' => 'cancelled']);
        }
        return back();
    }

    public function pay(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if (!in_array($order->status, ['pending_payment','unpaid'], true)) {
            return back();
        }

        DB::transaction(function () use ($order) {
            // 1) เปลี่ยนสถานะ
            $order->update(['status' => 'paid']);

            // 2) อัปเดตยอดสะสมผู้ใช้
            $user = $order->user()->lockForUpdate()->first(); // กันแข่งกันอัปเดต
            $user->lifetime_spent = (float)$user->lifetime_spent + (float)$order->total_amount;
            $user->save();

            // 3) คำนวณชั้นสมาชิกใหม่
            $user->recalcTier();
        });

        return back();
    }


    // app/Http/Controllers/OrderHistoryController.php
    public function checkout(Request $req)
    {
        $userId = $req->user()->id;

        // เอาเฉพาะรายการในตะกร้าที่ยังไม่ checkout
        $items = Cart::with('product')
            ->where('user_id', $userId)
            ->whereNull('checked_out_at')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('err', 'ตะกร้าว่าง');
        }

        DB::transaction(function () use ($items, $userId) {
            // รวมจำนวนต่อสินค้า
            $byPid = $items->groupBy('product_id')->map(function ($rows) {
                return [
                    'qty'   => (int) $rows->sum('qty'),
                    'price' => (float) $rows->first()->price,
                ];
            });

            // ล็อกสินค้า
            $products = \App\Models\Product::whereIn('id', $byPid->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // ตรวจสต็อก
            foreach ($byPid as $pid => $info) {
                $p = $products->get($pid);
                $pName  = $p ? $p->name : ('สินค้า #'.$pid);
                $remain = $p ? (int) $p->stock : 0;

                if (!$p || $info['qty'] > $remain) {
                    // หลีกเลี่ยงการใช้ ?-> และ ?? ใน string
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'qty' => "สินค้า {$pName} คงเหลือ {$remain} ชิ้น ไม่พอ",
                    ]);
                }
            }

            // สร้างออเดอร์หัว
            $totalQty = (int) $items->sum('qty');
            $totalAmt = (float) $items->sum(fn($i) => (int)$i->qty * (float)$i->price);

            $order = \App\Models\Order::create([
                'user_id'      => $userId,
                'status'       => 'pending_payment',
                'total_qty'    => $totalQty,
                'total_amount' => $totalAmt,
            ]);

            // รายการสินค้า: ใส่ name ให้ครบเพื่อกัน Error 1364
            $rows = [];
            foreach ($items as $i) {
                $prod   = $i->product;               // อาจเป็น null ได้
                $pName  = $prod ? $prod->name : ('สินค้า #'.$i->product_id);

                $rows[] = [
                    'order_id'   => $order->id,
                    'product_id' => $i->product_id,
                    'name'       => $pName,
                    'price'      => (float) $i->price,
                    'qty'        => (int) $i->qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            \App\Models\OrderItem::insert($rows);

            // ตัดสต็อก
            foreach ($byPid as $pid => $info) {
                $products[$pid]->decrement('stock', $info['qty']);
            }

            // ไม่ลบตะกร้า แค่ mark ว่าเช็คเอาต์แล้วเพื่อไม่ให้แสดง
            Cart::where('user_id', $userId)
                ->whereNull('checked_out_at')
                ->update(['checked_out_at' => now()]);
        });

        return redirect()->route('orders.history')->with('ok', 'สร้างคำสั่งซื้อแล้ว');
    }



}
