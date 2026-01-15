<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AkunPanenPoin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'akun_panen_poin';

    protected $fillable = [
        'uuid',
        'user_id',
        'nama_akun',
        'email_client',
        'password',
        'source',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    
    public function username()
    {
        return 'email_client';
    }

    public function getAuthIdentifierName()
    {
        return 'email_client';
    }

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
