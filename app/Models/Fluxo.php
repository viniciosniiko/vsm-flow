<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fluxo extends Model
{
    protected $fillable = [
        'cliente_id',
        'nome',
        'descricao',
        'versao',
        'status',
        'json_fluxo'
    ];

    protected $casts = [
        'json_fluxo' => 'array'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}