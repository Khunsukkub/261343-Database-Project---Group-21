<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        
        $products = Product::orderBy('id')->paginate(12);
        return view('dashboard', compact('products'));
    }

    public function show(\App\Models\Product $product)
    {
        return view('products.show', compact('product'));
    }
}
