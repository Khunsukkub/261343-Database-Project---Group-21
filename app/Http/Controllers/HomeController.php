<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // ดึงสินค้าทั้งหมดมาแสดง (หรือแบ่งหน้า)
        $products = Product::latest('id')->paginate(12);

        // ส่งตัวแปร $products ไปยังหน้า welcome
        return view('welcome', compact('products'));
    }
}
