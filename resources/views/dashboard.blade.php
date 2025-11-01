<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product</h2>
  </x-slot>

  @php
    $items = $products ?? collect();
    $total = method_exists($products ?? null, 'total')
      ? $products->total()
      : (is_countable($items) ? $items->count() : 0);
  @endphp

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex gap-6">

      {{-- Sidebar --}}
      <!--<aside class="w-64 shrink-0 sticky top-16 h-[calc(100vh-4rem)]
                   bg-white border rounded-lg shadow-sm p-4 hidden md:block">
        <nav class="space-y-1">
          <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-50">Product</a>
          <a href="{{ route('cart.index') }}" class="block px-3 py-2 rounded hover:bg-gray-50">Carts</a>
          <a href="{{ route('orders.history') }}" class="block px-3 py-2 rounded hover:bg-gray-50">History</a>
        </nav>
      </aside>-->

      {{-- Main content --}}
      <main class="flex-1 py-10">
        <div class="flex items-center justify-between mb-6">
          <span class="text-sm text-gray-500">{{ $total }} รายการ</span>
        </div>

        {{-- Product grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          @forelse ($items as $p)
            <div class="bg-white border rounded-xl shadow-sm overflow-hidden flex flex-col">
              
              {{-- Product image --}}
              <div class="aspect-[4/3] bg-gray-100">
                <img src="{{ $p->image_url }}"
                     alt="{{ $p->name }}"
                     class="w-full h-full object-cover">
              </div>

              {{-- Product info --}}
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h4 class="font-semibold text-lg text-gray-800">{{ $p->name ?? '-' }}</h4>

                  @if($p->description)
                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $p->description }}</p>
                  @endif

                  <p class="mt-2 text-sm text-gray-500">
                    คงเหลือ <span class="font-semibold">{{ (int)$p->stock }}</span> ชิ้น
                  </p>

                  <p class="mt-1 text-amber-600 font-semibold text-lg">
                    ${{ number_format($p->price ?? 0, 2) }}
                  </p>
                </div>

                {{-- Action buttons --}}
                <div class="mt-4 flex gap-2">
                  {{-- Add to cart --}}
                  <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                    <input type="hidden" name="qty" value="1">

                    <button type="submit"
                            class="w-full px-3 py-2 text-sm bg-sky-600 text-white rounded-md hover:bg-sky-700 disabled:opacity-50"
                            @disabled(($p->stock ?? 0) <= 0)>
                      เพิ่มลงตะกร้า
                    </button>
                  </form>

                  <!--{{-- View details --}}
                  <a href="{{ route('products.show', $p->id) }}"
                     class="px-3 py-2 text-sm border rounded-md hover:bg-gray-50 whitespace-nowrap">
                    รายละเอียด
                  </a>-->
                </div>
              </div>

            </div>
          @empty
            <div class="col-span-full text-center text-gray-500">
              ยังไม่มีสินค้า
            </div>
          @endforelse
        </div>

        {{-- Pagination --}}
        @if (method_exists($products ?? null, 'links'))
          <div class="mt-6">
            {{ $products->withQueryString()->links() }}
          </div>
        @endif
      </main>

    </div>
  </div>
</x-app-layout>
