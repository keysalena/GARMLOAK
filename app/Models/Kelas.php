<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
        'tahun_ajaran',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'kelas_id', 'kelas_id');
    }
}
