<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">เพิ่มรายการตะกร้า</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form method="POST" action="{{ route('cart.store') }}" class="bg-white border rounded-lg shadow-sm p-6 space-y-5">
      @csrf

      <div>
        <label class="block text-sm text-gray-700 mb-1">สินค้า</label>
        <select name="product_id" class="w-full rounded-md border-gray-300 focus:border-sky-500 focus:ring-sky-500">
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }} — ${{ number_format($p->price,2) }}</option>
          @endforeach
        </select>
        @error('product_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-gray-700 mb-1">จำนวน</label>
          <input type="number" name="qty" min="1" value="{{ old('qty',1) }}"
                 class="w-full rounded-md border-gray-300 focus:border-sky-500 focus:ring-sky-500">
          @error('qty') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">เลือกคิดเงิน</label>
          <select name="selected" class="w-full rounded-md border-gray-300 focus:border-sky-500 focus:ring-sky-500">
            <option value="1" selected>ใช่</option>
            <option value="0">ไม่ใช่</option>
          </select>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <a href="{{ route('cart.index') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">ยกเลิก</a>
        <button class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">บันทึก</button>
      </div>
    </form>
  </div>
</x-app-layout>
