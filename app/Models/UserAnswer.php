<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $table = 'user_answers';
    protected $primaryKey = 'answer_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'answer_id',
        'user_id',
        'question_id',
        'option_id',
        'is_correct',
        'xp_didapat',
        'dijawab_pada',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'xp_didapat' => 'integer',
        'dijawab_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'option_id', 'option_id');
    }
}
