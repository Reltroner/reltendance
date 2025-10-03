<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        // KPI contoh (silakan sesuaikan query sebenarnya)
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $presentDays = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->where('status', 'present')
            ->count();

        $workdays = Carbon::now()->daysInMonth; // sederhanakan
        $presentPct = $workdays ? round($presentDays / $workdays * 100, 1) : 0;

        $kpi = [
            'present_days'       => $presentDays,
            'present_pct'        => $presentPct,
            'late_days'          => 0,           // isi sesuai logika kamu
            'late_avg_minutes'   => 0,           // isi sesuai logika kamu
            'avg_work_hours'     => 8,           // isi sesuai logika kamu
            'last_check_in'      => optional(Attendance::where('user_id', $user->id)->latest('date')->first())->check_in,
            'last_check_in_address' => optional(optional(Attendance::where('user_id', $user->id)->latest('date')->with('details')->first())->details->last())->address,
        ];

        // Riwayat terbaru (10 rows)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->latest('date')
            ->limit(10)
            ->get();

        // Chart 30 hari ke belakang
        $labels = [];
        $data   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $labels[] = $d->format('d');

            $count = Attendance::where('user_id', $user->id)
                ->whereDate('date', $d->toDateString())
                ->count();

            $data[] = $count;
        }
        $chartData = ['labels' => $labels, 'data' => $data];

        return view('home', compact('kpi', 'recentAttendances', 'chartData'));
    }
}
