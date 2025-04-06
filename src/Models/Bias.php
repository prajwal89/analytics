<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bias extends Model
{
    protected $table = 'an_bias';

    protected $fillable = [
        'biasable1_type',
        'biasable1_id',
        'biasable1_route_name',
        'biasable2_type',
        'biasable2_id',
        'biasable2_route_name',
        'bias',
        'last_session_id',
        'created_at',
        'updated_at',
    ];

    public function biasable1(): MorphTo
    {
        return $this->morphTo();
    }

    public function biasable2(): MorphTo
    {
        return $this->morphTo();
    }
}
