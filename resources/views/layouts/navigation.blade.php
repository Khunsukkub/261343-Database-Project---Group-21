{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="border-b bg-white">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between gap-4">
      {{-- Brand --}}
      <a href="{{ url('/') }}" class="flex items-center gap-2">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded bg-gray-900 text-white font-bold">L</span>
        <span class="sr-only">Home</span>
      </a>

      {{-- Main tabs: แสดงตลอด ทั้งมือถือและเดสก์ท็อป --}}
      <div class="flex items-center gap-6">
        <a href="{{ route('dashboard') }}"
           class="text-sm {{ request()->routeIs('dashboard') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
          Product
        </a>

        @if (Route::has('cart.index'))
          <a href="{{ route('cart.index') }}"
             class="text-sm {{ request()->routeIs('cart.*') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
            Cart
          </a>
        @endif

        @if (Route::has('orders.history'))
          <a href="{{ route('orders.history') }}"
             class="text-sm {{ request()->routeIs('orders.history') ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
            Summary
          </a>
        @endif
      </div>

      {{-- User area ขวาบน --}}
      <div class="relative flex items-center gap-3">
        @auth
          @php
            $user = auth()->user();
            $tier = strtolower($user->member_tier ?? 'bronze');
            $badgeClass = [
              'gold'   => 'bg-yellow-100 text-yellow-800',
              'silver' => 'bg-gray-100 text-gray-700',
              'bronze' => 'bg-amber-100 text-amber-800',
            ][$tier] ?? 'bg-gray-100 text-gray-700';
            // ลบ $avatar ออก
          @endphp

          <div x-data="{ open:false }" class="relative">
            {{-- ปุ่มที่ไม่มีรูปภาพ --}}
            <button @click="open=!open" class="group flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-1.5 hover:bg-gray-50">
              
              {{-- ลบ <img src="{{ $avatar }}" ... > ออก --}}
              
              <span class="text-sm font-medium text-gray-800">{{ Str::limit($user->name, 14) }}</span>
              <span class="rounded px-2 py-0.5 text-xs {{ $badgeClass }}">{{ ucfirst($tier) }}</span>
              <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
            </button>

            {{-- dropdown เฉพาะเมนูผู้ใช้เท่านั้น ไม่ยุ่งกับ Product/Cart/Summary --}}
            <div x-cloak x-show="open" @click.away="open=false"
                 class="absolute right-0 mt-2 w-56 overflow-hidden rounded-md border border-gray-200 bg-white shadow z-50">
              <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
              @if (Route::has('membership'))
                <a href="{{ route('membership') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Membership</a>
              @endif
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Log Out</button>
              </form>
            </div>
          </div>
        @else
          {{-- Login/Register เหมือนเดิม --}}
          @if (Route::has('login'))
            <a href="{{ route('login') }}"
               class="rounded border border-gray-300 bg-white px-4 py-1.5 text-sm text-gray-800 hover:bg-gray-50">
              Log in
            </a>
          @endif
          @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="rounded border border-gray-300 bg-white px-4 py-1.5 text-sm text-gray-800 hover:bg-gray-50">
              Register
            </a>
          @endif
        @endauth
      </div>
    </div>
  </div>
</nav>