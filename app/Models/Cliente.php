<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nome',
        'empresa',
        'email',
        'telefone',
        'whatsapp',
        'status'
    ];

    public function fluxos()
    {
        return $this->hasMany(Fluxo::class);
    }
}