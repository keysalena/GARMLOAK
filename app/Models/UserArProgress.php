<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserArProgress extends Model
{
    protected $table = 'user_ar_progress';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'ar_id',
        'status',
        'selesai_pada',
    ];

    protected $casts = [
        'selesai_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function arContent()
    {
        return $this->belongsTo(ArContent::class, 'ar_id', 'ar_id');
    }
}
