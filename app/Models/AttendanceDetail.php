<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    protected $fillable = [
        'attendance_id','longitude','latitude','address','photo','type','notes'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
        'accuracy_m'  => 'decimal:2',
    ];

    public function attendance() { return $this->belongsTo(Attendance::class); }
    public function geofence()   { return $this->belongsTo(Geofence::class); }

}
