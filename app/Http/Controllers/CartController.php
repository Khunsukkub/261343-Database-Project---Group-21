<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function store(Request $req)
    {
        $data = $req->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['nullable','integer','min:1'],
        ]);
        $qty = $data['qty'] ?? 1;

        $userId  = $req->user()->id;
        $product = Product::findOrFail($data['product_id']);

        $item = Cart::firstOrNew([
            'user_id'    => $userId,
            'product_id' => $product->id,
        ]);

        $want = ($item->exists ? (int)$item->qty : 0) + (int)$qty;
        $stock = (int)($product->stock ?? 0);

        if ($stock <= 0) {
            return back()->with('err', "สินค้า {$product->name} หมดสต็อก");
        }
        if ($want > $stock) {
            return back()->with('err', "สินค้า {$product->name} มีแค่ {$stock} ชิ้น");
        }

        $item->qty      = $want;
        $item->price    = (float)$product->price;
        $item->selected = $item->selected ?? true;
        $item->save();

        return redirect()->route('cart.index')->with('ok','เพิ่มลงตะกร้าแล้ว');
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
