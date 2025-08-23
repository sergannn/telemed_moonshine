<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'html',
        'category_id',
        'duration'
    ];

    public function category(): BelongsTo {
        return $this->belongsTo( Category::class );
    }
}
