<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivityLog extends Model
{
    protected $table = 'daily_activity_log';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'tanggal',
        'waktu_belajar_menit',
        'ar_explore_count',
        'challenge_count',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_belajar_menit' => 'integer',
        'ar_explore_count' => 'integer',
        'challenge_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
