@extends('layouts.app')
{{-- resources/views/home.blade.php --}}
@section('content')
<div class="content-header py-3 border-bottom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h3 mb-0 fw-semibold text-dark">Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
            <div class="text-muted small">
                @php $user = auth()->user(); @endphp
                <span>Hi, <strong>{{ $user?->name ?? 'Guest' }}</strong></span>
            </div>
        </div>
    </div>
</div>

<section class="content py-4">
    <div class="container-fluid">

        {{-- Flash / status --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Hadir Bulan Ini</div>
                                <div class="fs-4 fw-bold">
                                    {{ $kpi['present_days'] ?? '--' }}
                                </div>
                            </div>
                            <i class="bi bi-clipboard-check fs-2 text-primary"></i>
                        </div>
                        <div class="progress mt-3" style="height: 6px;">
                            @php
                                $presentPct = $kpi['present_pct'] ?? 0;
                            @endphp
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ $presentPct }}%;"
                                 aria-valuenow="{{ $presentPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="small text-muted mt-1">{{ $presentPct }}% dari hari kerja</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Telat Bulan Ini</div>
                                <div class="fs-4 fw-bold">
                                    {{ $kpi['late_days'] ?? '--' }}
                                </div>
                            </div>
                            <i class="bi bi-alarm fs-2 text-danger"></i>
                        </div>
                        <div class="small text-muted mt-2">Rata-rata telat: {{ $kpi['late_avg_minutes'] ?? '—' }} menit</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Jam Kerja (Rata-rata)</div>
                                <div class="fs-4 fw-bold">
                                    {{ $kpi['avg_work_hours'] ?? '--' }} jam
                                </div>
                            </div>
                            <i class="bi bi-clock-history fs-2 text-success"></i>
                        </div>
                        <div class="small text-muted mt-2">Minggu ini</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Check-in Terakhir</div>
                            <div class="fs-5 fw-semibold">
                                {{ $kpi['last_check_in'] ?? '—' }}
                            </div>
                            <div class="small text-muted">
                                {{ $kpi['last_check_in_address'] ?? '' }}
                            </div>
                        </div>
                        <i class="bi bi-geo-alt fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart + List --}}
        <div class="row g-3">
            <div class="col-12 col-xl-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-line text-primary"></i>
                            Rekap Kehadiran (30 Hari)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" height="120"></canvas>
                        <div class="small text-muted mt-2" id="chartNote">
                            Menampilkan jumlah check-in per hari. Sumber: data server.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-list-check text-secondary"></i>
                            Riwayat Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $recent = $recentAttendances ?? [];
                        @endphp

                        @if(!empty($recent) && count($recent) > 0)
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Masuk</th>
                                            <th>Pulang</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent as $row)
                                            <tr>
                                                <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($row->date)->format('d M Y') }}</td>
                                                <td class="text-nowrap">{{ $row->check_in ?? '—' }}</td>
                                                <td class="text-nowrap">{{ $row->check_out ?? '—' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ ($row->status ?? 'present') === 'present' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($row->status ?? 'present') }}
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
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-emoji-neutral fs-3 d-block mb-2"></i>
                                Belum ada data riwayat.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
{{-- Chart.js CDN (boleh pindah ke Vite jika diperlukan) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx || !window.Chart) return;

    // Data dari server (jika controller mengirim $chartData)
    // Format yang diharapkan:
    // $chartData = [
    //   'labels' => ['01','02','03',...],
    //   'data'   => [1,0,1,...] // jumlah check-in per hari
    // ];
    const serverData = @json($chartData ?? null);

    let labels = [];
    let series = [];

    if (serverData && Array.isArray(serverData.labels) && Array.isArray(serverData.data)) {
        labels = serverData.labels;
        series = serverData.data;
    } else {
        // Fallback data contoh (30 hari ke belakang)
        const today = new Date();
        for (let i = 29; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(today.getDate() - i);
            labels.push(String(d.getDate()).padStart(2, '0'));
            // Dummy: akhir pekan lebih sedikit
            series.push([0,1,0,1,1,0,0][d.getDay()]);
        }
        const note = document.getElementById('chartNote');
        if (note) note.textContent = 'Data contoh ditampilkan karena data server tidak tersedia.';
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Check-in per Hari',
                data: series,
                borderWidth: 1
            }]
        },
    });
});
</script>
@endpush
