<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Summary</h2>
  </x-slot>

  @php
  // normalize สถานะที่อาจติดมาจากของเก่า
  $rawStatus = $order->status ?? 'pending_payment';
  $status = match ($rawStatus) {
      'unpaid'      => 'pending_payment',
      'processing', 'shipped' => 'paid',   // ของเก่าถือเป็น "ชำระเงินสำเร็จ"
      default       => $rawStatus,
  };

  $badgeMap = [
    'paid'             => 'ชำระเงินสำเร็จ',
    'pending_payment'  => 'ค้างชำระ',
    'cancelled'        => 'ยกเลิก',
  ];
  $badgeClass = [
    'paid'             => 'bg-green-100 text-green-800',
    'pending_payment'  => 'bg-yellow-100 text-yellow-800',
    'cancelled'        => 'bg-gray-100 text-gray-700',
  ];

  $label = $badgeMap[$status];
  $klass = $badgeClass[$status];
@endphp



  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex gap-6">
      {{-- Sidebar สรุปสถานะ: เหลือ 3 สถานะ --}}
      <aside class="w-64 shrink-0 hidden md:block">
        <div class="bg-white border rounded-lg p-4 space-y-3">
          <div class="flex justify-between">
            <span>ชำระเงินสำเร็จ</span>
            <span class="font-semibold">{{ $counts['paid'] ?? 0 }}</span>
          </div>
          <div class="flex justify-between">
            <span>ค้างชำระ</span>
            <span class="font-semibold">{{ $counts['pending_payment'] ?? 0 }}</span>
          </div>
          <div class="flex justify-between">
            <span>ยกเลิก</span>
            <span class="font-semibold">{{ $counts['cancelled'] ?? 0 }}</span>
          </div>
        </div>
      </aside>

      {{-- รายการออเดอร์ --}}
      <main class="flex-1">
        <h3 class="text-2xl font-extrabold mb-4">รายการการสั่งซื้อ</h3>

        @forelse($orders as $order)
          @php
            $status = $order->status ?? 'pending_payment';
            $label  = $badgeMap[$status]  ?? ucfirst($status);
            $klass  = $badgeClass[$status] ?? 'bg-gray-100 text-gray-700';

            // รองรับทั้ง total และ total_amount
            $orderTotal = (float) ($order->total ?? $order->total_amount ?? 0);
          @endphp

          <section class="bg-white border rounded-xl shadow-sm mb-5">
            <header class="px-4 py-3 border-b flex items-center justify-between">
              <div>
                <div class="font-semibold">คำสั่งซื้อ #{{ $order->id }}</div>
                <div class="text-sm text-gray-500">
                  {{ optional($order->created_at)->format('Y-m-d H:i') }}
                </div>
              </div>
              <div class="text-sm">
                <span class="px-2 py-1 rounded {{ $klass }}">{{ $label }}</span>
              </div>
            </header>

            {{-- รายการสินค้า --}}
            <div class="divide-y">
              @foreach($order->items as $it)
                @php
                  $prod = $it->product ?? null;
                  $img  = $prod?->image_path
                      ? asset('storage/'.$prod->image_path)
                      : ($prod?->image_url ?? 'https://picsum.photos/seed/'.$it->id.'/300/300');
                  $qty   = (int) ($it->qty ?? 0);
                  $price = (float) ($it->price ?? 0);
                @endphp
                <div class="grid grid-cols-[112px_1fr_auto] items-center gap-4 p-4">
                  <div class="w-24 h-24 rounded overflow-hidden bg-gray-100">
                    <img src="{{ $img }}" alt="" class="w-24 h-24 object-cover">
                  </div>
                  <div>
                    <div class="font-semibold">
                      {{ $prod->name ?? ($it->name ?? ('สินค้า #'.$it->product_id)) }}
                    </div>
                    <div class="text-sm text-gray-500">จำนวน {{ $qty }} ชิ้น</div>
                  </div>
                  <div class="text-right">
                    <div class="text-amber-600 font-extrabold text-xl">
                      ${{ number_format($price * max(1,$qty), 2) }}
                    </div>
                    <div class="text-xs text-gray-500">
                      (ราคา/ชิ้น ${{ number_format($price, 2) }})
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <footer class="px-4 py-3 flex items-center justify-between bg-gray-50 rounded-b-xl">
              <div class="text-lg">
                รวม <span class="font-extrabold">${{ number_format($orderTotal, 2) }}</span>
              </div>

              <div class="flex gap-2">
                @if($status === 'pending_payment')
                  <form method="POST" action="{{ route('orders.cancel', $order) }}">
                    @csrf
                    <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">ยกเลิก</button>
                  </form>
                  <form method="POST" action="{{ route('orders.pay', $order) }}">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">ชำระ</button>
                  </form>
                @endif
              </div>
            </footer>
          </section>
        @empty
          <p class="text-gray-500">ยังไม่มีประวัติการสั่งซื้อ</p>
        @endforelse

        <div class="mt-4">
          {{ method_exists($orders, 'links') ? $orders->links() : '' }}
        </div>
      </main>
    </div>
  </div>
</x-app-layout>
