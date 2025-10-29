<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Home</h2>
  </x-slot>

  @php
    $products = [
      ['name' => 'ไดโนเสาร์', 'price' => 5],
      ['name' => 'มังกรแห่งหุบเขาป่าสุภ', 'price' => 20],
      ['name' => 'หมีแพนด้า', 'price' => 12],
      ['name' => 'คิตตี้บอย', 'price' => 8],
      ['name' => 'จรวดกระดาษโปร', 'price' => 3],
      ['name' => 'หมึกปีศาจ', 'price' => 15],
      ['name' => 'ชามะนาว', 'price' => 2],
      ['name' => 'คุกกี้ดำ', 'price' => 6],
      ['name' => 'เสือบรู๊ค', 'price' => 18],
      ['name' => 'นกฮูกบางกอก', 'price' => 9],
      ['name' => 'รองเท้าล่องหน', 'price' => 25],
      ['name' => 'หมวกไอเดีย', 'price' => 7],
      ['name' => 'เมาส์ไร้เส้น', 'price' => 11],
      ['name' => 'แก้วเย็นใจ', 'price' => 4],
      ['name' => 'หูฟังลื่นหู', 'price' => 19],
    ];
  @endphp

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex gap-6">

      <!-- LEFT SIDEBAR -->
      <aside class="w-64 shrink-0 sticky top-16 h-[calc(100vh-4rem)]
                   bg-white border rounded-lg shadow-sm p-4 hidden md:block">
        <nav class="space-y-1">
          <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-50">ทั้งหมด</a>
          <a href="#" class="block px-3 py-2 rounded hover:bg-gray-50">เครื่องครัว</a>
          <a href="#" class="block px-3 py-2 rounded hover:bg-gray-50">ความงาม</a>
          <a href="#" class="block px-3 py-2 rounded hover:bg-gray-50">เทคโนโลยี</a>
          <hr class="my-3">
          <a href="{{ route('cart.index') }}" class="block px-3 py-2 rounded hover:bg-gray-50">รายการสั่งซื้อ</a>
          <a href="{{ route('orders.history') }}" class="block px-3 py-2 rounded hover:bg-gray-50">ประวัติการสั่งซื้อ</a>
        </nav>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="flex-1 py-10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">ทั้งหมด</h3>
          <span class="text-sm text-gray-500">{{ count($products) }} รายการ</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          @foreach ($products as $p)
            <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
              <!-- กรอบรูป (ยังไม่ใส่รูปจริง) -->
              <div class="aspect-[4/3] bg-sky-200/60 flex items-center justify-center">
                <div class="w-11/12 h-5/6 border-2 border-dashed border-sky-400 rounded-lg flex items-center justify-center text-sky-700 text-sm">
                  พื้นที่รูปสินค้า
                </div>
              </div>

              <!-- ชื่อและราคา -->
              <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                  <p class="font-medium line-clamp-2">{{ $p['name'] }}</p>
                  <p class="text-amber-600 font-semibold">${{ number_format($p['price'], 0) }}</p>
                </div>

                <div class="mt-4 flex gap-2">
                  <button class="px-3 py-1.5 text-sm bg-sky-600 text-white rounded-md hover:bg-sky-700">เพิ่มลงตะกร้า</button>
                  <button class="px-3 py-1.5 text-sm border rounded-md hover:bg-gray-50">ดูรายละเอียด</button>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </main>

    </div>
  </div>
</x-app-layout>
