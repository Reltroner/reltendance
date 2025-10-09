<?php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        // Rentang bulan berjalan (gunakan string 'Y-m-d' karena attend_date = DATE)
        $start = Carbon::now()->startOfMonth()->toDateString();
        $end   = Carbon::now()->endOfMonth()->toDateString();

        // Hari hadir di bulan ini
        $presentDays = Attendance::where('user_id', $user->id)
            ->whereBetween('attend_date', [$start, $end])
            ->where('status', 'present')
            ->count();

        $workdays = Carbon::now()->daysInMonth;
        $presentPct = $workdays ? round($presentDays / $workdays * 100, 1) : 0;

        // Last check-in (pakai kolom ringkasan di attendances)
        $lastAttendance = Attendance::where('user_id', $user->id)
            ->latest('attend_date')
            ->first();

        $lastCheckIn = $lastAttendance?->first_check_in_at;

        // Kalau mau alamat terakhir dari attendance_details
        $lastDetail = AttendanceDetail::whereHas('attendance', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('occurred_at')
            ->first();

        $kpi = [
            'present_days'         => $presentDays,
            'present_pct'          => $presentPct,
            'late_days'            => 0,
            'late_avg_minutes'     => 0,
            'avg_work_hours'       => 8,
            'last_check_in'        => $lastCheckIn,
            'last_check_in_address'=> $lastDetail?->address,
        ];

        // Riwayat terbaru (10 rows)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->latest('attend_date')
            ->limit(10)
            ->get();

        // Chart 30 hari ke belakang (count per hari)
        $labels = [];
        $data   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($d)->format('d');

            $count = Attendance::where('user_id', $user->id)
                ->whereDate('attend_date', $d)
                ->count();

            $data[] = $count;
        }
        $chartData = ['labels' => $labels, 'data' => $data];

        return view('home', compact('kpi', 'recentAttendances', 'chartData'));
    }
}
