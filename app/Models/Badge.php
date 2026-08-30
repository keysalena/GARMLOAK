<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Badge extends Model
{
    use SoftDeletes;

    protected $table = 'badges';
    protected $primaryKey = 'badge_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'deskripsi',
        'icon_url',
        'kriteria',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
