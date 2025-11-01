<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $req)
    {
        // ถ้าใน Cart model ไม่มี scopeActive() ให้ลบ ->active() ทิ้งได้
        $items = Cart::with('product')
            ->when(method_exists(Cart::class, 'scopeActive'), fn($q) => $q->active())
            ->where('user_id', $req->user()->id)
            ->orderByDesc('id')
            ->get();

        $total = $items->sum(fn($i) => ($i->selected ? 1 : 0) * ((int)$i->qty * (float)$i->price));

        return view('cart.index', compact('items','total'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('cart.create', compact('products'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $product = Product::findOrFail($request->input('product_id'));
        $qty = max(1, (int) $request->input('qty', 1));

        // ราคาปกติ
        $price = $product->price;

        // ส่วนลดตาม tier
        $discount = 0;
        if ($user->member_tier === 'silver') {
            $discount = 0.05;
        } elseif ($user->member_tier === 'gold') {
            $discount = 0.10;
        }

        // ราคาหลังหักส่วนลด
        $finalPrice = round($price * (1 - $discount), 2);

        // ตรวจสอบว่ามีสินค้าในตะกร้าแล้วหรือไม่
        $existing = Cart::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            // ถ้ามีอยู่แล้วให้เพิ่มจำนวน
            $existing->qty += $qty;
            $existing->price = $finalPrice;
            $existing->save();
        } else {
            // ถ้ายังไม่มีให้สร้างใหม่
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $finalPrice,
            ]);
        }

        return redirect()
        ->route('cart.index')
        ->with('success', "เพิ่มสินค้า {$product->name} ลงตะกร้าแล้ว (ลดราคา " . ($discount * 100) . "% สำหรับสมาชิก {$user->member_tier})");

    }

    public function edit(Cart $cart)
    {
        $this->authorizeItem($cart);
        return view('cart.edit', ['item' => $cart]);
    }

    public function update(Request $r, Cart $cart)
    {
        // ต้องชื่อ $cart ให้ตรงกับพารามิเตอร์ {cart} ใน routes เพื่อ binding จะได้แม่น
        $this->authorizeItem($cart);

        $data = $r->validate([
            'qty' => ['required','integer','min:1'],
        ]);
        $qty = (int)$data['qty'];

        $product = $cart->product; // eager loaded หรือไม่ก็ได้
        $stock   = (int)($product->stock ?? 0);

        if ($stock <= 0) {
            return back()->with('err', 'สินค้าหมดสต็อก')->withInput();
        }
        if ($qty > $stock) {
            return back()->with('err', "จำนวนเกินสต็อก (สูงสุด {$stock})")->withInput();
        }

        $cart->qty = $qty;
        $cart->save();

        return redirect()->route('cart.index')->with('ok', 'อัปเดตแล้ว');
    }

    public function destroy(Cart $cart)
    {
        $this->authorizeItem($cart);
        $cart->delete();
        return back()->with('ok','ลบแล้ว');
    }

    private function authorizeItem(Cart $item): void
    {
        abort_if($item->user_id !== auth()->id(), 403);
    }

    
}
