<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacySetting extends Model
{
    use HasFactory;

    protected $table = 'privacy_settings';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'show_phone',
        'show_email',
        'show_location',
        'indexable',
    ];

    protected $casts = [
        'show_phone' => 'boolean',
        'show_email' => 'boolean',
        'show_location' => 'boolean',
        'indexable' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
