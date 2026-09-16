<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'questions';
    protected $primaryKey = 'question_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'module_id',
        'pertanyaan',
        'urutan',
        'xp_reward',
        'penjelasan',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'module_id');
    }

    // Relasi ke Opsi Jawaban (A, B, C, D)
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'question_id');
    }
}
