<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} — Home</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
      <style>/* inline tailwind fallback */</style>
    @endif
  </head>

  <body class="bg-white text-gray-800 flex flex-col items-center min-h-screen p-6 lg:p-8">

    {{-- Header --}}
    <header class="w-full max-w-7xl text-sm mb-6">
      @if (Route::has('login'))
        <nav class="flex items-center justify-between">
          {{-- Left: Homepage Title --}}
          <h1 class="text-3xl font-bold text-gray-800">HomePage</h1>

          {{-- Right: Auth buttons --}}
          <div class="flex items-center gap-4">
            @auth
              <a href="{{ url('/dashboard') }}"
                class="inline-block px-5 py-1.5 bg-white text-gray-800 border border-gray-300 hover:bg-gray-50 rounded-sm text-sm">
                Product
              </a>
            @else
              <a href="{{ route('login') }}"
                class="inline-block px-5 py-1.5 bg-white text-gray-800 border border-gray-300 hover:bg-gray-50 rounded-sm text-sm">
                Log in
              </a>
              @if (Route::has('register'))
                <a href="{{ route('register') }}"
                  class="inline-block px-5 py-1.5 bg-white text-gray-800 border border-gray-300 hover:bg-gray-50 rounded-sm text-sm">
                  Register
                </a>
              @endif
            @endauth
          </div>
        </nav>
      @endif
    </header>

    @php
      $items = $products ?? collect();
      $total = 0;
      if (is_object($products) && method_exists($products, 'total')) {
          $total = $products->total();
      } elseif (is_countable($items)) {
          $total = $items->count();
      }
    @endphp

    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <main class="py-10">
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-semibold text-xl text-gray-800">Product</h2>
          <span class="text-sm text-gray-500">{{ $total }} รายการ</span>
        </div>

        {{-- Product grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          @forelse ($items as $p)
            <div class="bg-white border rounded-xl shadow-sm overflow-hidden flex flex-col">
              {{-- Product image --}}
              <div class="aspect-[4/3] bg-gray-100">
                @php
                  $img = $p->image_url ?? ($p->image_path ? asset($p->image_path) : null);
                @endphp
                <img src="{{ $img ?: asset('images/placeholder.png') }}"
                     alt="{{ $p->name }}"
                     class="w-full h-full object-cover">
              </div>

              {{-- Product info --}}
              <div class="p-4 flex-1 flex flex-col justify-between">
                <div>
                  <h4 class="font-semibold text-lg text-gray-800">{{ $p->name ?? '-' }}</h4>

                  @if(!empty($p->description))
                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $p->description }}</p>
                  @endif

                  @if(isset($p->stock))
                    <p class="mt-2 text-sm text-gray-500">
                      คงเหลือ <span class="font-semibold">{{ (int) $p->stock }}</span> ชิ้น
                    </p>
                  @endif

                  <p class="mt-1 text-amber-600 font-semibold text-lg">
                    ฿{{ number_format($p->price ?? 0, 2) }}
                  </p>
                </div>

                {{-- Action --}}
                <div class="mt-4 flex gap-2">
                  @auth
                    <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $p->id }}">
                      <input type="hidden" name="qty" value="1">
                      <button type="submit"
                        class="w-full px-3 py-2 text-sm bg-sky-600 text-white rounded-md hover:bg-sky-700 disabled:opacity-50"
                        @disabled(isset($p->stock) && (int)$p->stock <= 0)>
                        เพิ่มลงตะกร้า
                      </button>
                    </form>
                  @endauth

                  @guest
                    <a href="{{ route('login') }}"
                      class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm bg-gray-300 text-gray-700 rounded-md hover:bg-gray-200">
                      เข้าสู่ระบบเพื่อสั่งซื้อ
                    </a>
                  @endguest
                </div>
              </div>
            </div>
          @empty
            <div class="col-span-full text-center text-gray-500">ยังไม่มีสินค้า</div>
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

  </body>
</html>
