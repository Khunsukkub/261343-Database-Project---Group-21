<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      รายละเอียดสินค้า
    </h2>
  </x-slot>

  @php
    // สร้าง src ของรูป: ถ้ามี image_path ใช้ storage, ถ้าไม่มีใช้รูปสุ่ม
    $src = $product->image_path
      ? asset('storage/'.$product->image_path)
      : 'https://picsum.photos/seed/'.$product->id.'/1200/900';
  @endphp

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

      {{-- รูปสินค้า --}}
      <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <img
          src="{{ $src }}"
          alt="{{ $product->name }}"
          class="w-full h-[420px] md:h-[520px] object-cover"
          loading="lazy">
      </div>

      {{-- ข้อมูลสินค้า --}}
      <div class="bg-white border rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>

        <p class="mt-2 text-amber-600 text-xl font-bold">
          ${{ number_format($product->price, 0) }}
        </p>

        @if($product->description)
          <div class="mt-4 prose max-w-none">
            <p class="text-gray-700 whitespace-pre-line">
              {{ $product->description }}
            </p>
          </div>
        @endif

        <div class="mt-6 flex items-center gap-3">
          {{-- เพิ่มลงตะกร้า --}}
          <form action="{{ route('cart.store') }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <label class="sr-only" for="qty">จำนวน</label>
            <input
              id="qty"
              name="qty"
              type="number"
              inputmode="numeric"
              min="1"
              value="1"
              class="w-20 rounded-md border-gray-300">
            <button type="submit"
              class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
              เพิ่มลงตะกร้า
            </button>
          </form>

          {{-- กลับไปหน้า Product --}}
          <a href="{{ route('dashboard') }}"
             class="px-4 py-2 border rounded-md hover:bg-gray-50">
            กลับ
          </a>
        </div>

        {{-- ข้อมูลเสริม --}}
        <div class="mt-6 text-sm text-gray-500">
          <div>อัปเดตล่าสุด: {{ $product->updated_at?->diffForHumans() }}</div>
          <div>รหัสสินค้า: #{{ $product->id }}</div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
