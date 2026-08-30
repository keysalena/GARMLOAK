<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected $table = 'modules';
    protected $primaryKey = 'module_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kode_modul',
        'judul',
        'sub_cpmk',
        'deskripsi',
        'urutan',
        'icon_url',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function materi()
    {
        return $this->hasMany(Materi::class, 'module_id', 'module_id');
    }

    public function arContents()
    {
        return $this->hasMany(ArContent::class, 'module_id', 'module_id');
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class, 'module_id', 'module_id');
    }
}
