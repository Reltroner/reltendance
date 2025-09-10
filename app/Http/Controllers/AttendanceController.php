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
        $this->authorizeAbility($request, 'attendance:view');

        $items = Attendance::with('details')
            ->where('user_id', $request->user()->id)
            ->latest('date')
            ->paginate(20);

        return $items;
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
