<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800">Cart</h2>
    </div>
  </x-slot>

  @php
    $items = $items ?? collect();
    $hasStockIssue = false;
    $memberTier = auth()->user()?->member_tier ?? 'bronze';
    $discountPercent = $memberTier === 'silver' ? 5 : ($memberTier === 'gold' ? 10 : 0);
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

    {{-- ส่วนหัว --}}
    <div class="flex items-baseline justify-between">
      <h3 class="text-2xl font-bold">รายการสินค้า</h3>
      <span class="text-gray-500">{{ $items->count() }} รายการ</span>
    </div>

    {{-- แจ้งส่วนลด --}}
    @if($discountPercent > 0)
      <div class="mt-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded px-4 py-2">
        🎉 คุณได้รับส่วนลด {{ $discountPercent }}% จากระดับสมาชิก {{ ucfirst($memberTier) }}
      </div>
    @endif

    {{-- รายการสินค้า --}}
    <div class="mt-4 space-y-5">
      @forelse($items as $item)
        @php
          $p     = $item->product;
          $img   = $p?->image_path ? asset('storage/'.$p->image_path)
                   : ($p?->image_url ?: 'https://picsum.photos/seed/'.$item->id.'/300/300');

          $stock = (int) ($p?->stock ?? 0);
          $qty   = (int) $item->qty;
          $originalPrice = (float) ($p?->price ?? $item->price);
          $discountedPrice = round($originalPrice * (1 - $discountPercent/100), 2);

          $outOfStock = $stock <= 0;
          $overStock  = !$outOfStock && $qty > $stock;
          $hasIssueRow = $outOfStock || $overStock;
          if ($hasIssueRow) { $hasStockIssue = true; }

          $safeInputQty = $outOfStock ? 1 : max(1, min($qty, $stock));
        @endphp

        <div class="border rounded-lg bg-white {{ $hasIssueRow ? 'ring-1 ring-red-300' : '' }}">
          <div class="grid grid-cols-[112px_1fr_auto_auto] items-center gap-4 p-4">

            {{-- รูป --}}
            <a href="#" class="block w-24 h-24 rounded overflow-hidden">
              <img src="{{ $img }}" alt="{{ $p->name ?? 'สินค้า #'.$item->product_id }}" class="w-24 h-24 object-cover">
            </a>

            {{-- ข้อมูลสินค้า --}}
            <div>
              <p class="text-lg font-semibold">{{ $p->name ?? 'สินค้า #'.$item->product_id }}</p>

              <div class="mt-1 text-sm text-gray-600">
                ราคาปกติ:
                <span class="line-through text-gray-400">
                  ฿{{ number_format($originalPrice, 2) }}
                </span>
              </div>
              <div class="text-sm text-amber-700 font-semibold">
                ราคาหลังลด {{ $discountPercent }}%:
                ฿{{ number_format($discountedPrice, 2) }}
              </div>

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

            {{-- จำนวน --}}
            <div class="text-right">
              <div class="text-sm text-gray-600">จำนวน {{ $qty }} ชิ้น</div>

              <form method="POST" action="{{ route('cart.update', $item) }}" class="mt-2 flex items-center justify-end gap-2">
                @csrf
                @method('PATCH')

                @if($outOfStock)
                  <input type="hidden" name="qty" value="{{ $safeInputQty }}">
                  <input type="number" value="{{ $safeInputQty }}" class="w-20 border text-right border-red-400 text-red-700" disabled>
                @else
                  <input
                    type="number"
                    name="qty"
                    min="1"
                    @if($stock > 0) max="{{ $stock }}" @endif
                    value="{{ $safeInputQty }}"
                    class="w-20 border text-right {{ $hasIssueRow ? 'border-red-400 text-red-700' : 'border-gray-300' }}">
                @endif

                <button type="submit"
                        class="px-2 py-1 border rounded hover:bg-gray-50"
                        @if($outOfStock) disabled @endif>
                  อัปเดต
                </button>
              </form>

              @if($outOfStock)
                <div class="mt-1 text-xs text-red-600">เอาออกหรือรอเติมสต็อกก่อน</div>
              @endif
            </div>

            {{-- รวม --}}
            <div class="text-right">
              <div class="text-xs text-gray-500">
                (฿{{ number_format($discountedPrice, 2) }} × {{ $qty }})
              </div>
              <div class="text-lg font-bold {{ $hasIssueRow ? 'text-red-600' : 'text-amber-600' }}">
                ฿{{ number_format($qty * $discountedPrice, 2) }}
              </div>

              <form method="POST" action="{{ route('cart.destroy', $item) }}"
                    onsubmit="return confirm('ลบรายการนี้?')" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50">
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

    {{-- สรุปราคารวม --}}
    @php
      $totalBeforeDiscount = $items->sum(fn($it) => (int)$it->qty * (float)($it->product?->price ?? $it->price));
      $totalAfterDiscount = round($totalBeforeDiscount * (1 - $discountPercent / 100), 2);
      $discountAmount = $totalBeforeDiscount - $totalAfterDiscount;
      $disableCheckout = $items->isEmpty() || $hasStockIssue;
    @endphp

    <div class="mt-6 border-t pt-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-gray-700">
          ระดับสมาชิก:
          <span class="font-semibold capitalize">{{ $memberTier }}</span>
          @if($discountPercent > 0)
            <span class="text-amber-600 ml-1">(ส่วนลด {{ $discountPercent }}%)</span>
          @endif
        </div>

        <div class="text-right space-y-1">
          <div class="text-gray-600">
            ราคารวมก่อนลด:
            <span class="font-semibold">฿{{ number_format($totalBeforeDiscount, 2) }}</span>
          </div>

          @if($discountPercent > 0)
            <div class="text-gray-600">
              ส่วนลดทั้งหมด:
              <span class="font-semibold text-green-600">-฿{{ number_format($discountAmount, 2) }}</span>
            </div>
          @endif

          <div class="text-lg font-bold {{ $hasStockIssue ? 'text-red-600' : 'text-amber-700' }}">
            ยอดสุทธิ: ฿{{ number_format($totalAfterDiscount, 2) }}
          </div>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
          เลือกสินค้าเพิ่ม
        </a>

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
