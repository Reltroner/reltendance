<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','date','check_in','check_out','status','notes'
    ];

    protected $casts = [
        'attend_date'       => 'date',
        'first_check_in_at' => 'datetime',
        'last_check_out_at' => 'datetime',
        'is_late'           => 'boolean',
    ];

    
    public function shift(){ 
        return $this->belongsTo(Shift::class); 
    }
    
    public function details()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
