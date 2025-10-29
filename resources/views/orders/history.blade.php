<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">ประวัติการสั่งซื้อ</h2>
  </x-slot>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex gap-6">
      {{-- Sidebar สรุปสถานะ --}}
      <aside class="w-64 shrink-0 hidden md:block">
        <div class="bg-white border rounded-lg p-4 space-y-3">
          <div class="flex justify-between"><span>จัดส่งสำเร็จ</span><span class="font-semibold">{{ $counts['shipped'] }}</span></div>
          <div class="flex justify-between"><span>กำลังจัดส่ง</span><span class="font-semibold">{{ $counts['processing'] }}</span></div>
          <div class="flex justify-between"><span>ค้างชำระ</span><span class="font-semibold">{{ $counts['pending_payment'] }}</span></div>
        </div>
      </aside>

      {{-- รายการออเดอร์ --}}
      <main class="flex-1">
        <h3 class="text-2xl font-extrabold mb-4">รายการการสั่งซื้อ</h3>

        @forelse($orders as $order)
          <section class="bg-white border rounded-xl shadow-sm mb-5">
            <header class="px-4 py-3 border-b flex items-center justify-between">
              <div>
                <div class="font-semibold">คำสั่งซื้อ #{{ $order->id }}</div>
                <div class="text-sm text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</div>
              </div>
              <div class="text-sm">
                <span class="px-2 py-1 rounded
                  @class([
                    'bg-yellow-100 text-yellow-800'=> $order->status==='pending_payment',
                    'bg-indigo-100 text-indigo-800'=> $order->status==='processing',
                    'bg-green-100 text-green-800'  => $order->status==='shipped',
                    'bg-gray-100 text-gray-700'    => $order->status==='cancelled',
                  ])">
                  {{ [
                    'pending_payment'=>'ค้างชำระ',
                    'processing'=>'กำลังจัดส่ง',
                    'shipped'=>'จัดส่งสำเร็จ',
                    'cancelled'=>'ยกเลิก',
                  ][$order->status] }}
                </span>
              </div>
            </header>

            {{-- รายการสินค้าในออเดอร์ --}}
            <div class="divide-y">
              @foreach($order->items as $it)
                <div class="grid grid-cols-[112px_1fr_auto] items-center gap-4 p-4">
                  <div class="w-24 h-24 bg-sky-200 flex items-center justify-center rounded">
                    <div class="w-20 h-20 border-2 border-dashed border-sky-400 rounded"></div>
                  </div>
                  <div>
                    <div class="font-semibold">{{ $it->product->name ?? 'สินค้า #'.$it->product_id }}</div>
                    <div class="text-sm text-gray-500">จำนวน {{ $it->qty }} ชิ้น</div>
                  </div>
                  <div class="text-right">
                    <div class="text-amber-600 font-extrabold text-xl">
                      ${{ number_format($it->price,2) }}
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <footer class="px-4 py-3 flex items-center justify-between bg-gray-50 rounded-b-xl">
              <div class="text-lg">
                รวม <span class="font-extrabold">${{ number_format($order->total,2) }}</span>
              </div>

              <div class="flex gap-2">
                @if($order->status === 'pending_payment')
                  <form method="POST" action="{{ route('orders.cancel',$order) }}">
                    @csrf
                    <button class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">ยกเลิก</button>
                  </form>
                  <form method="POST" action="{{ route('orders.pay',$order) }}">
                    @csrf
                    <button class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">ชำระ</button>
                  </form>
                @endif
              </div>
            </footer>
          </section>
        @empty
          <p class="text-gray-500">ยังไม่มีประวัติการสั่งซื้อ</p>
        @endforelse

        <div class="mt-4">
          {{ $orders->links() }}
        </div>
      </main>
    </div>
  </div>
</x-app-layout>
