<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';
    protected $primaryKey = 'level_number';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'level_number',
        'nama_level',
        'xp_min',
        'xp_max',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'level', 'level_number');
    }
}
