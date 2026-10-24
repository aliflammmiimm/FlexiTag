<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeData extends Model
{
    use HasFactory;

    protected $table = 'mode_data';

    protected $fillable = [
        'user_id',
        'mode',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Relasi ke pemilik tag / pengguna.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
