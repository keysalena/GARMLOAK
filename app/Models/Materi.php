<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materi extends Model
{
    use SoftDeletes;

    protected $table = 'materi';
    protected $primaryKey = 'materi_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'module_id',
        'judul',
        'tipe',
        'konten_url',
        'konten_teks',
        'urutan',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }

    public function userProgress()
    {
        return $this->hasOne(UserMaterialProgress::class, 'materi_id', 'materi_id');
    }
}
