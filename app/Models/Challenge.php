<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;

    protected $table = 'challenges';
    protected $primaryKey = 'challenge_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'module_id',
        'judul',
        'jumlah_soal',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'challenge_id', 'challenge_id');
    }
}
