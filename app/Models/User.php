<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kelas;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kelas_id',
        'role',
        'nama',
        'email',
        'password_hash',
        'avatar_url',
        'level',
        'total_xp',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'level' => 'integer',
        'total_xp' => 'integer',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    public function levelInfo()
    {
        return $this->belongsTo(Level::class, 'level', 'level_number');
    }
}
