<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowConnection extends Model
{
    protected $fillable = [
        'flow_id',
        'from_block_id',
        'to_block_id',
        'condition_label',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}
