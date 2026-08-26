<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flow extends Model
{
    protected $fillable = [
        'name',
        'client_name',
        'description',
        'status',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(FlowBlock::class)->orderBy('position_y')->orderBy('position_x');
    }

    public function connections(): HasMany
    {
        return $this->hasMany(FlowConnection::class);
    }
}
