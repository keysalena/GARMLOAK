<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOption extends Model
{
    use SoftDeletes;

    protected $table = 'question_options';
    protected $primaryKey = 'option_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'teks_opsi',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'deleted_at' => 'datetime',
    ];
    
    protected $hidden = [
        'is_correct',
        'created_at',
        'updated_at'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }
}
