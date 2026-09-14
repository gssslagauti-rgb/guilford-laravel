<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookPost extends Model
{
    protected $fillable = [
        'source_page',
        'original_text',
        'summary',
        'post_url',
        'post_date',
        'added_by',
        'published',
    ];

    protected $casts = [
        'post_date' => 'date',
        'published' => 'boolean',
    ];

    const SOURCES = [
        'town.guilford.ct' => 'Town of Guilford (facebook.com/town.guilford.ct)',
        'GuilfordParksandRecreation' => 'Guilford Parks & Recreation',
        'Other' => 'Other source',
    ];
}