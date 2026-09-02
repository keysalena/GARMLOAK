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
        'challenge_id',
        'pertanyaan',
        'urutan',
        'xp_reward',
        'penjelasan',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id', 'challenge_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'question_id');
    }
}
