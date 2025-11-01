<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Membership Details</h2>
  </x-slot>

  <div class="py-8 max-w-4xl mx-auto space-y-6">
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-2xl font-bold mb-4">สวัสดี, {{ $user->name }}</h3>
      <p>ระดับสมาชิกปัจจุบัน: 
        <span class="font-semibold text-sky-600">{{ ucfirst($user->member_tier) }}</span>
      </p>
      <p class="mt-2">ยอดสั่งซื้อสะสมทั้งหมด: 
        <span class="font-semibold text-gray-900">฿{{ number_format($totalSpent, 2) }}</span>
      </p>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-xl font-semibold mb-3">เกณฑ์ระดับสมาชิก</h3>
      <table class="min-w-full border text-sm">
        <thead>
          <tr class="bg-gray-100">
            <th class="border px-4 py-2 text-left">Tier</th>
            <th class="border px-4 py-2 text-left">ยอดสั่งซื้อขั้นต่ำ (฿)</th>
            <th class="border px-4 py-2 text-left">ส่วนลด</th>
            <th class="border px-4 py-2 text-left">สิทธิประโยชน์</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tiers as $name => $tier)
            <tr @class(['bg-amber-50' => $user->member_tier === $name])>
              <td class="border px-4 py-2 font-semibold capitalize">{{ $name }}</td>
              <td class="border px-4 py-2">{{ number_format($tier['min'], 0) }}</td>
              <td class="border px-4 py-2">{{ $tier['discount'] }}%</td>
              <td class="border px-4 py-2">{{ $tier['desc'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>
