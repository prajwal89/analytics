<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageView extends Model
{
    use HasFactory;

    public $table = 'an_page_views';

    protected $fillable = [
        'path',
        'query_string',
        'route_name',
        'viewable_id',
        'viewable_type',

        'ip_address',
        'country_code',
        'city',
        'latitude',
        'longitude',

        'user_agent',
        'device',
        'platform',
        'browser',

        'referrer_url',

        'time_on_page',
        'scroll_depth',
        'viewport_height',
        'viewport_width',
        'session_id',
        'user_id',

        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'query_string' => 'array',
    ];

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}
