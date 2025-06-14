<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'html',
        'category_id'
    ];

    public function category(): BelongsTo {
        return $this->belongsTo( Category::class );
    }
}
