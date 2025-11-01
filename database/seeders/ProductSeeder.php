<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // จะล้างตาราง products ทั้งหมด
        Schema::disableForeignKeyConstraints();
        DB::table('products')->truncate();
        Schema::enableForeignKeyConstraints();

        $now = now();

        $rows = [
            ['name'=>'ไดโนเสาร์',        'price'=>  5.00, 'stock'=> 60, 'image_path'=>'products/dino.jpg',      'description'=>'ของเล่นไดโนเสาร์ ขนาดพกพา'],
            ['name'=>'มังกรหุบเขา',       'price'=> 20.00, 'stock'=> 15, 'image_path'=>'products/dragon.jpg',    'description'=>'มังกรประดับตั้งโต๊ะ สีเขียวมรกต'],
            ['name'=>'หมีแพนด้า',         'price'=> 12.00, 'stock'=> 32, 'image_path'=>'products/panda.jpg',     'description'=>'ตุ๊กตาแพนด้า นุ่มมือ'],
            ['name'=>'คิตตี้บอย',         'price'=>  8.00, 'stock'=> 28, 'image_path'=>'products/kitty.jpg',     'description'=>'คาแรกเตอร์ขี้เล่น รุ่นบอย'],
            ['name'=>'จรวดกระดาษโปร',     'price'=>  3.00, 'stock'=>120, 'image_path'=>'products/rocket.jpg',    'description'=>'พับง่าย บินไกล เหมาะกับสายทดลอง'],
            ['name'=>'หมึกปีศาจ',         'price'=> 15.00, 'stock'=> 18, 'image_path'=>'products/squid.jpg',     'description'=>'ฟิกเกอร์หมึก 8 แขน ปรับท่าทางได้'],
            ['name'=>'ชามะนาว',           'price'=>  2.00, 'stock'=>200, 'image_path'=>'products/lemon.jpg', 'description'=>'ชามะนาวพร้อมชง แก้วละสบายคอ'],
            ['name'=>'คุกกี้ดำ',          'price'=>  6.00, 'stock'=> 95, 'image_path'=>'products/cookie.jpg',    'description'=>'คุกกี้ช็อกโก้เข้ม สูตรกรอบนอกนุ่มใน'],
            ['name'=>'เสือบรู๊ค',         'price'=> 18.00, 'stock'=> 14, 'image_path'=>'products/tiger.jpg',     'description'=>'ฟิกเกอร์เสือ ลายเส้นจัดจ้าน'],
            ['name'=>'นกฮูกบางกอก',       'price'=>  9.00, 'stock'=> 26, 'image_path'=>'products/owl.jpg',       'description'=>'นกฮูกนักคิด ตั้งโต๊ะอ่านหนังสือ'],
            ['name'=>'รองเท้าล่องหน',     'price'=> 25.00, 'stock'=>  7, 'image_path'=>'products/shoes.jpg',     'description'=>'คอนเซ็ปต์ไอเท็ม โหมดล่องหนมีเงื่อนไข ;)'],
            ['name'=>'หมวกไอเดีย',        'price'=>  7.00, 'stock'=> 40, 'image_path'=>'products/hat.jpg',       'description'=>'หมวกที่ช่วยโฟกัสเวลาอ่านหนังสือ'],
            ['name'=>'เมาส์ไร้เส้น',      'price'=> 11.00, 'stock'=> 22, 'image_path'=>'products/mouse.jpg',     'description'=>'เมาส์ไร้สาย DPI ปรับได้'],
            ['name'=>'แก้วเย็นใจ',        'price'=>  4.00, 'stock'=> 80, 'image_path'=>'products/cup.jpg',       'description'=>'แก้วคู่กับชามะนาว เย็นจัดไม่เวียนหัว'],
            ['name'=>'หูฟังลื่นหู',       'price'=> 19.00, 'stock'=> 16, 'image_path'=>'products/headset.jpg',   'description'=>'หูฟังฟังสบาย ไดรเวอร์จูนใส'],
        ];

        DB::table('products')->insert(
            array_map(fn($r) => $r + ['created_at'=>$now, 'updated_at'=>$now], $rows)
        );
    }
}
