<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArContent extends Model
{
    use SoftDeletes;

    protected $table = 'ar_content';
    protected $primaryKey = 'ar_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'module_id',
        'judul',
        'marker_image_url',
        'model_3d_url',
        'penjelasan',
        'urutan',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }
}
