<?php
// app/Http/Controllers/AttendanceController.php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        // butuh ability untuk melihat data
        $this->authorizeAbility($request, 'attendance:view');

        // Validasi query params
        $v = $request->validate([
            'from'      => ['nullable', 'date'],                  // format: YYYY-MM-DD
            'to'        => ['nullable', 'date', 'after_or_equal:from'],
            'status'    => ['nullable', 'in:present,absent,leave,sick'],
            'type'      => ['nullable', 'in:check_in,check_out'], // filter detail berdasarkan tipe
            'sort'      => ['nullable', 'in:date_asc,date_desc'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
            'user_id'   => ['nullable', 'integer'],               // hanya admin yang boleh pakai
            'with'      => ['nullable', 'string'],                // contoh: details,user (comma)
        ]);

        // Scope user: default = user login
        $userId = $request->user()->id;

        // Kalau admin dan punya ability admin, boleh override user_id
        if (!empty($v['user_id']) && ($request->user()->is_admin || $request->user()->tokenCan('attendance:admin'))) {
            $userId = (int) $v['user_id'];
        }

        // Base query
        $query = Attendance::query()->where('user_id', $userId);

        // Filter tanggal
        if (!empty($v['from'])) {
            $query->whereDate('date', '>=', $v['from']);
        }
        if (!empty($v['to'])) {
            $query->whereDate('date', '<=', $v['to']);
        }

        // Filter status (opsional)
        if (!empty($v['status'])) {
            $query->where('status', $v['status']);
        }

        // Include relations
        $with = collect(explode(',', $v['with'] ?? 'details'))
            ->map(fn ($w) => trim($w))
            ->filter()
            ->values()
            ->all();

        // Sorting
        $sort = $v['sort'] ?? 'date_desc';
        $direction = $sort === 'date_asc' ? 'asc' : 'desc';

        // Pagination
        $perPage = (int) ($v['per_page'] ?? 20);

        // Pre-compute summary (tanpa pagination)
        $summaryBase = (clone $query);
        $totalRecords = (clone $summaryBase)->count();
        $presentDays  = (clone $summaryBase)->where('status', 'present')->count();
        $firstDate    = (clone $summaryBase)->min('date');
        $lastDate     = (clone $summaryBase)->max('date');

        // Hitung jumlah detail check_in / check_out dalam rentang yang sama
        // Agar efisien, ambil id attendance yang terlibat
        $attIds = (clone $summaryBase)->pluck('id');

        $checkinCount  = AttendanceDetail::whereIn('attendance_id', $attIds)
            ->where('type', 'check_in')->count();
        $checkoutCount = AttendanceDetail::whereIn('attendance_id', $attIds)
            ->where('type', 'check_out')->count();

        // Optional filter type di eager load (kalau diminta, supaya data details tidak membengkak)
        $withRelations = [];
        if (in_array('details', $with, true)) {
            $withRelations['details'] = function ($q) use ($v) {
                if (!empty($v['type'])) {
                    $q->where('type', $v['type']);
                }
                $q->orderBy('created_at', 'asc');
            };
        }
        if (in_array('user', $with, true)) {
            $withRelations[] = 'user';
        }

        // Query final + paginate
        $items = $query
            ->with($withRelations)
            ->orderBy('date', $direction)
            ->paginate($perPage)
            ->appends($request->query()); // keep query string di pagination links

        // Bentuk response konsisten
        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'per_page'     => $items->perPage(),
                    'total'        => $items->total(),
                    'last_page'    => $items->lastPage(),
                    'from'         => $items->firstItem(),
                    'to'           => $items->lastItem(),
                ],
                'summary' => [
                    'total_records'    => $totalRecords,
                    'present_days'     => $presentDays,
                    'checkin_count'    => $checkinCount,
                    'checkout_count'   => $checkoutCount,
                    'range'            => [
                        'from' => $v['from'] ?? $firstDate,
                        'to'   => $v['to']   ?? $lastDate,
                    ],
                ],
                'sort' => $sort,
                'filters' => [
                    'status' => $v['status'] ?? null,
                    'type'   => $v['type']   ?? null,
                    'user_id'=> ($userId !== $request->user()->id) ? $userId : null,
                ],
            ],
            'links' => [
                'first' => $items->url(1),
                'prev'  => $items->previousPageUrl(),
                'next'  => $items->nextPageUrl(),
                'last'  => $items->url($items->lastPage()),
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeAbility($request, 'attendance:view');

        $att = Attendance::with('details')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $att;
    }

    public function checkIn(Request $request)
    {
        $this->authorizeAbility($request, 'attendance:create');

        $data = $request->validate([
            'longitude' => ['nullable','string','max:64'],
            'latitude'  => ['nullable','string','max:64'],
            'address'   => ['required','string','max:255'],
            'photo'     => ['nullable','image','max:4096'], // 4MB
            'notes'     => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            $userId = $request->user()->id;
            $today  = now()->toDateString();

            // One attendance per day
            $att = Attendance::firstOrCreate(
                ['user_id' => $userId, 'date' => $today],
                ['status' => 'present', 'check_in' => now()->format('H:i:s')]
            );

            // Upload photo (opsional)
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('attendance', 'public');
            }

            $detail = new AttendanceDetail([
                'longitude' => $data['longitude'] ?? null,
                'latitude'  => $data['latitude'] ?? null,
                'address'   => $data['address'],
                'photo'     => $photoPath,
                'type'      => 'check_in',
                'notes'     => $data['notes'] ?? null,
            ]);

            $att->details()->save($detail);

            // update jam masuk (kalau belum)
            if (empty($att->check_in)) {
                $att->update(['check_in' => now()->format('H:i:s')]);
            }

            return response()->json(['attendance' => $att->load('details')], 201);
        });
    }

    public function checkOut(Request $request)
    {
        $this->authorizeAbility($request, 'attendance:create');

        $data = $request->validate([
            'longitude' => ['nullable','string','max:64'],
            'latitude'  => ['nullable','string','max:64'],
            'address'   => ['required','string','max:255'],
            'photo'     => ['nullable','image','max:4096'],
            'notes'     => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            $userId = $request->user()->id;
            $today  = now()->toDateString();

            $att = Attendance::where('user_id', $userId)->where('date', $today)->firstOrFail();

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('attendance', 'public');
            }

            $detail = new AttendanceDetail([
                'longitude' => $data['longitude'] ?? null,
                'latitude'  => $data['latitude'] ?? null,
                'address'   => $data['address'],
                'photo'     => $photoPath,
                'type'      => 'check_out',
                'notes'     => $data['notes'] ?? null,
            ]);
            $att->details()->save($detail);

            // update jam pulang
            $att->update(['check_out' => now()->format('H:i:s')]);

            return response()->json(['attendance' => $att->load('details')], 201);
        });
    }

    private function authorizeAbility(Request $request, string $ability): void
    {
        if (! $request->user()->tokenCan($ability)) {
            abort(403, 'Missing required ability: '.$ability);
        }
    }
}
