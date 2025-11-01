<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800">Cart</h2>
    </div>
  </x-slot>

  @php
    $items        = $items ?? collect();
    $grandTotal   = $items->sum(fn($it) => (int)$it->qty * (float)$it->price);
    $hasStockIssue = false;
  @endphp

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Flash messages --}}
    @if(session('ok'))
      <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-2 text-green-800">
        {{ session('ok') }}
      </div>
    @endif
    @if(session('err'))
      <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-2 text-red-800">
        {{ session('err') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-2 text-red-800">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="flex items-baseline justify-between">
      <h3 class="text-2xl font-bold">รายการ</h3>
      <span class="text-gray-500">{{ $items->count() }} รายการ</span>
    </div>

    <div class="mt-4 space-y-5">
      @forelse($items as $item)
        @php
          $p     = $item->product;
          $img   = $p?->image_path ? asset('storage/'.$p->image_path)
                   : ($p?->image_url ?: 'https://picsum.photos/seed/'.$item->id.'/300/300');

          $stock = (int) ($p?->stock ?? 0);
          $qty   = (int) $item->qty;
          $price = (float) $item->price;

          $outOfStock  = $stock <= 0;
          $overStock   = !$outOfStock && $qty > $stock;
          $hasIssueRow = $outOfStock || $overStock;
          if ($hasIssueRow) { $hasStockIssue = true; }

          // ค่าที่ปลอดภัยสำหรับใส่ใน input (อย่างน้อย 1 และไม่เกิน stock)
          $safeInputQty = $outOfStock ? 1 : max(1, min($qty, $stock));
        @endphp

        <div class="border rounded-lg bg-white {{ $hasIssueRow ? 'ring-1 ring-red-300' : '' }}">
          <div class="grid grid-cols-[112px_1fr_auto_auto] items-center gap-4 p-4">
            {{-- รูปสินค้า --}}
            <a href="{{ $p ? route('products.show', $p->id) : '#' }}" class="block w-24 h-24 rounded overflow-hidden">
              <img src="{{ $img }}" alt="{{ $p->name ?? ('สินค้า #'.$item->product_id) }}" class="w-24 h-24 object-cover">
            </a>

            {{-- ชื่อ/คำอธิบาย/ราคา + สต็อก --}}
            <div>
              <a href="{{ $p ? route('products.show', $p->id) : '#' }}" class="text-lg font-semibold hover:underline">
                {{ $p->name ?? 'สินค้า #'.$item->product_id }}
              </a>

              @if(filled($p?->description))
                <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $p->description }}</p>
              @endif

              <div class="mt-1 text-sm text-gray-500">ราคาต่อหน่วย ${{ number_format($price, 2) }}</div>

              <div class="mt-1 text-sm">
                @if($outOfStock)
                  <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-700">
                    สินค้าหมดสต็อก
                  </span>
                @else
                  <span class="text-gray-600">
                    คงเหลือ <span class="font-semibold">{{ $stock }}</span> ชิ้น
                  </span>
                @endif

                @if($overStock)
                  <span class="ml-2 text-red-600">(เกินสต็อก: สูงสุด {{ $stock }})</span>
                @endif
              </div>
            </div>

            {{-- จำนวน + อัปเดต (แก้เคส disabled แล้วไม่ส่งด้วย hidden) --}}
            <div class="text-right">
              <div class="text-sm text-gray-600">จำนวน {{ $qty }} ชิ้น</div>

              <div class="mt-2 flex items-center justify-end gap-2">
                <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                  @csrf
                  @method('PATCH')

                  @if($outOfStock)
                    {{-- แสดงค่าให้เห็น แต่ไม่ให้แก้ไข และส่งค่าผ่าน hidden --}}
                    <input type="hidden" name="qty" value="{{ $safeInputQty }}">
                    <input type="number"
                           value="{{ $safeInputQty }}"
                           class="w-20 rounded-md border text-right border-red-400 text-red-700"
                           disabled>
                  @else
                    <input
                      type="number"
                      name="qty"
                      min="1"
                      @if($stock > 0) max="{{ $stock }}" @endif
                      value="{{ $safeInputQty }}"
                      class="w-20 rounded-md border text-right {{ $hasIssueRow ? 'border-red-400 text-red-700' : 'border-gray-300' }}">
                  @endif

                  <button type="submit"
                          class="px-2 py-1 border rounded hover:bg-gray-50"
                          @if($outOfStock) disabled @endif>
                    อัปเดต
                  </button>
                </form>
              </div>

              @if($outOfStock)
                <div class="mt-1 text-xs text-red-600">เอาออกหรือรอเติมสต็อกก่อน</div>
              @endif
            </div>

            {{-- ราคารวม + ลบ --}}
            <div class="text-right">
              <div class="text-xl font-extrabold {{ $hasIssueRow ? 'text-red-600' : 'text-amber-600' }}">
                ${{ number_format($qty * $price, 2) }}
              </div>

              <form method="POST" action="{{ route('cart.destroy', $item) }}"
                    onsubmit="return confirm('ลบรายการนี้?')" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50">
                  ลบ
                </button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <p class="text-gray-500">ตะกร้าว่าง</p>
      @endforelse
    </div>

    @php
      $isEmpty         = $items->isEmpty() || $grandTotal <= 0;
      $disableCheckout = $isEmpty || $hasStockIssue;
    @endphp

    <div class="mt-6 flex items-center justify-between">
      <div class="text-lg">
        รวมทั้งสิ้น:
        <span class="{{ $hasStockIssue ? 'text-red-600' : 'text-amber-600' }} font-bold">
          ${{ number_format($grandTotal, 2) }}
        </span>
        @if($hasStockIssue)
          <span class="ml-2 text-sm text-red-600">(มีรายการหมด/เกินสต็อก)</span>
        @endif
      </div>

      <div class="flex gap-3">
        <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">เลือกสินค้าเพิ่ม</a>

        <form method="POST" action="{{ route('orders.checkout') }}">
          @csrf
          <button
            type="submit"
            @if($disableCheckout) disabled aria-disabled="true" @endif
            class="px-4 py-2 rounded-md text-white {{ $disableCheckout ? 'bg-green-600/50 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}">
            ยืนยันการสั่งซื้อ
          </button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
