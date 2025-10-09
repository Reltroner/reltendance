{{-- resources/views/home.blade.php --}}
@extends('auth.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6 border-b border-neutral-200/70 dark:border-neutral-800">
  <div class="mx-auto max-w-7xl px-6 flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Dashboard</h1>
      <nav class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
        <ol class="flex items-center gap-1">
          <li><a href="{{ route('home') }}" class="hover:text-neutral-700 dark:hover:text-neutral-300">Home</a></li>
          <li>/</li>
          <li>Dashboard</li>
        </ol>
      </nav>
    </div>
    <div class="text-sm text-neutral-600 dark:text-neutral-300">
      Hi, <strong>{{ auth()->user()?->name ?? 'Guest' }}</strong>
    </div>
  </div>
</div>

<section class="py-6">
  <div class="mx-auto max-w-7xl px-6">

    {{-- Flash --}}
    @if (session('status'))
      <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-200">
        {{ session('status') }}
      </div>
    @endif

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      {{-- Hadir Bulan Ini --}}
      <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-sm text-neutral-500 dark:text-neutral-400">Hadir Bulan Ini</div>
            <div class="mt-1 text-3xl font-bold">{{ $kpi['present_days'] ?? '--' }}</div>
          </div>
          <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        @php $presentPct = (int)($kpi['present_pct'] ?? 0); @endphp
        <div class="mt-4 h-2 w-full rounded-full bg-neutral-200 dark:bg-neutral-800 overflow-hidden">
          <div class="h-2 rounded-full bg-indigo-600 dark:bg-indigo-500" style="width: {{ $presentPct }}%"></div>
        </div>
        <div class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">{{ $presentPct }}% dari hari kerja</div>
      </div>

      {{-- Telat Bulan Ini --}}
      <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-sm text-neutral-500 dark:text-neutral-400">Telat Bulan Ini</div>
            <div class="mt-1 text-3xl font-bold">{{ $kpi['late_days'] ?? '--' }}</div>
            <div class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
              Rata-rata telat: {{ $kpi['late_avg_minutes'] ?? '—' }} menit
            </div>
          </div>
          <svg class="h-8 w-8 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.5" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>

      {{-- Jam Kerja --}}
      <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-sm text-neutral-500 dark:text-neutral-400">Jam Kerja (Rata-rata)</div>
            <div class="mt-1 text-3xl font-bold">{{ $kpi['avg_work_hours'] ?? '--' }} jam</div>
            <div class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">Minggu ini</div>
          </div>
          <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.5" d="M12 8v4l3 3m7-3a10 10 0 11-20 0 10 10 0 0120 0z"/>
          </svg>
        </div>
      </div>

      {{-- Check-in Terakhir --}}
      <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-sm text-neutral-500 dark:text-neutral-400">Check-in Terakhir</div>
            <div class="mt-1 text-lg font-semibold">{{ $kpi['last_check_in'] ?? '—' }}</div>
            <div class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ $kpi['last_check_in_address'] ?? '' }}</div>
          </div>
          <svg class="h-8 w-8 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.5" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11z"/>
            <path stroke-width="1.5" d="M12 22s7-6.364 7-12.5A7 7 0 105 9.5C5 15.636 12 22 12 22z"/>
          </svg>
        </div>
      </div>
    </div>

    {{-- Chart + List --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
      {{-- Chart --}}
      <div class="xl:col-span-7">
        <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-3">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="1.5" d="M3 3v18h18M7 13v5M12 9v9M17 5v13"/>
            </svg>
            <h3 class="font-semibold">Rekap Kehadiran (30 Hari)</h3>
          </div>
          <canvas id="attendanceChart" height="120"></canvas>
          <div class="mt-2 text-xs text-neutral-500 dark:text-neutral-400" id="chartNote">
            Menampilkan jumlah check-in per hari. Sumber: data server.
          </div>
        </div>
      </div>

      {{-- Riwayat --}}
      <div class="xl:col-span-5">
        <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-3">
            <svg class="h-5 w-5 text-neutral-600 dark:text-neutral-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <h3 class="font-semibold">Riwayat Terbaru</h3>
          </div>

          @php $recent = $recentAttendances ?? []; @endphp

          @if(!empty($recent) && count($recent) > 0)
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="text-left text-neutral-500 dark:text-neutral-400">
                  <tr>
                    <th class="py-2 pr-4">Tanggal</th>
                    <th class="py-2 pr-4">Masuk</th>
                    <th class="py-2 pr-4">Pulang</th>
                    <th class="py-2 pr-2">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                  @foreach($recent as $row)
                    <tr class="text-neutral-800 dark:text-neutral-100">
                      <td class="py-2 pr-4 whitespace-nowrap">
                        {{ \Illuminate\Support\Carbon::parse($row->date ?? $row->attend_date ?? now())->format('d M Y') }}
                      </td>
                      <td class="py-2 pr-4 whitespace-nowrap">{{ $row->check_in ?? $row->first_check_in_at ?? '—' }}</td>
                      <td class="py-2 pr-4 whitespace-nowrap">{{ $row->check_out ?? $row->last_check_out_at ?? '—' }}</td>
                      <td class="py-2 pr-2">
                        @php $st = strtolower($row->status ?? 'present'); @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs
                          {{ $st === 'present' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'
                                               : 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300' }}">
                          {{ ucfirst($st) }}
                        </span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            @if(method_exists($recent, 'links'))
              <div class="mt-3">
                {{ $recent->links() }}
              </div>
            @endif
          @else
            <div class="text-center text-neutral-500 dark:text-neutral-400 py-6">
              Belum ada data riwayat.
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const ctx = document.getElementById('attendanceChart');
  if (!ctx || !window.Chart) return;

  const serverData = @json($chartData ?? null);
  let labels = [], series = [];

  if (serverData && Array.isArray(serverData.labels) && Array.isArray(serverData.data)) {
    labels = serverData.labels; series = serverData.data;
  } else {
    const today = new Date();
    for (let i = 29; i >= 0; i--) {
      const d = new Date(today); d.setDate(today.getDate() - i);
      labels.push(String(d.getDate()).padStart(2, '0'));
      series.push([0,1,0,1,1,0,0][d.getDay()]);
    }
    const note = document.getElementById('chartNote');
    if (note) note.textContent = 'Data contoh ditampilkan karena data server tidak tersedia.';
  }

  new Chart(ctx, { type: 'bar', data: { labels, datasets: [{ label: 'Check-in per Hari', data: series, borderWidth: 1 }] } });
});
</script>
@endpush
