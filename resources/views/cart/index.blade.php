<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800">ตะกร้าสินค้า</h2>
    </div>
  </x-slot>

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-baseline justify-between">
      <h3 class="text-2xl font-bold">รายการ</h3>
      <span class="text-gray-500">{{ $items->count() }} รายการ</span>
    </div>

    <div class="mt-4 space-y-5">
      @forelse($items as $item)
        <div class="border rounded-lg bg-white">
          <div class="grid grid-cols-[112px_1fr_auto_auto] items-center gap-4 p-4">
            <div class="w-24 h-24 bg-sky-200 flex items-center justify-center rounded">
              <div class="w-20 h-20 border-2 border-dashed border-sky-400 rounded"></div>
            </div>

            <div>
              <div class="text-lg font-semibold">
                {{ $item->product->name ?? 'สินค้า #'.$item->product_id }}
              </div>
              <div class="text-sm text-gray-500">ราคาต่อหน่วย ${{ number_format($item->price,2) }}</div>
            </div>

            <div class="text-right">
              <div class="text-sm text-gray-600">จำนวน {{ $item->qty }} ชิ้น</div>
              <div class="text-xl font-extrabold text-amber-600">${{ number_format($item->qty * (float)$item->price, 2) }}</div>
            </div>

            <div class="flex items-center gap-2">
              <a href="{{ route('cart.edit',$item) }}" class="px-3 py-1.5 text-sm border rounded hover:bg-gray-50">แก้ไข</a>
              <form method="POST" action="{{ route('cart.destroy',$item) }}" onsubmit="return confirm('ลบรายการนี้?')">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50">ลบ</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <p class="text-gray-500">ตะกร้าว่าง</p>
      @endforelse
    </div>

    <div class="mt-6 flex justify-end">
      <a href="{{ route('cart.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md">ไปเพิ่มสินค้า</a>
    </div>
  </div>
</x-app-layout>
