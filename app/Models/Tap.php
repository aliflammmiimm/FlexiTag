<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tap extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'tag_id',
        'tapped_at',
        'source',
        'device_type',
        'region',
        'created_at',
    ];

    protected $casts = [
        'tapped_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
