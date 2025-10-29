<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">แก้ไขรายการตะกร้า</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form method="POST" action="{{ route('cart.update', $item) }}" class="bg-white border rounded-lg shadow-sm p-6 space-y-5">
      @csrf @method('PATCH')

      <div>
        <label class="block text-sm text-gray-700 mb-1">สินค้า</label>
        <input type="text" value="{{ $item->product->name ?? 'สินค้า #'.$item->product_id }}" disabled
               class="w-full rounded-md border-gray-200 bg-gray-50">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-gray-700 mb-1">จำนวน</label>
          <input type="number" name="qty" min="1" value="{{ old('qty',$item->qty) }}"
                 class="w-full rounded-md border-gray-300 focus:border-sky-500 focus:ring-sky-500">
          @error('qty') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">เลือกคิดเงิน</label>
          <select name="selected" class="w-full rounded-md border-gray-300 focus:border-sky-500 focus:ring-sky-500">
            <option value="1" @selected($item->selected)>ใช่</option>
            <option value="0" @selected(!$item->selected)>ไม่ใช่</option>
          </select>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <a href="{{ route('cart.index') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">ย้อนกลับ</a>
        <div class="flex gap-2">
          <form method="POST" action="{{ route('cart.destroy',$item) }}" onsubmit="return confirm('ลบรายการนี้?')">
            @csrf @method('DELETE')
            <button class="px-4 py-2 text-red-600 border border-red-200 rounded-md hover:bg-red-50">ลบ</button>
          </form>
          <button class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">บันทึกการแก้ไข</button>
        </div>
      </div>
    </form>
  </div>
</x-app-layout>
