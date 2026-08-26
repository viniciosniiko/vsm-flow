<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowBlock extends Model
{
    protected $fillable = [
        'flow_id',
        'type',
        'title',
        'message',
        'options',
        'position_x',
        'position_y',
        'is_start',
    ];

    protected $casts = [
        'options' => 'array',
        'is_start' => 'boolean',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}
