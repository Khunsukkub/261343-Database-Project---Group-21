<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $r)
    {
        $items = Cart::with('product')->where('user_id', $r->user()->id)->get();
        $total = $items->where('selected', true)->sum(fn($i)=>$i->qty * (float)$i->price);
        return view('cart.index', compact('items','total'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('cart.create', compact('products'));
    }

    public function store(Request $r)
    {
        $data = $r->validate(['product_id'=>'required|exists:products,id','qty'=>'nullable|integer|min:1','selected'=>'nullable|boolean']);
        $p = Product::find($data['product_id']);
        $item = Cart::firstOrNew(['user_id'=>$r->user()->id,'product_id'=>$p->id,'price'=>$p->price]);
        $item->qty = ($item->exists ? $item->qty : 0) + ($data['qty'] ?? 1);
        $item->selected = $data['selected'] ?? true;
        $item->save();
        return redirect()->route('cart.index');
    }

    public function edit(Cart $item)
    {
        $this->authorizeItem($item);
        return view('cart.edit', compact('item'));
    }

    public function update(Request $r, Cart $item)
    {
        $this->authorizeItem($item);
        $data = $r->validate(['qty'=>'required|integer|min:1','selected'=>'nullable|boolean']);
        $item->qty = $data['qty'];
        if ($r->has('selected')) $item->selected = (bool)$data['selected'];
        $item->save();
        return redirect()->route('cart.index');
    }

    public function destroy(Cart $item)
    {
        $this->authorizeItem($item);
        $item->delete();
        return back();
    }

    private function authorizeItem(Cart $item): void
    {
        abort_if($item->user_id !== auth()->id(), 403);
    }
}
