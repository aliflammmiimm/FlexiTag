<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'target',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Record a sensitive administrative action into the audit trail (FR-ADM-10).
     */
    public static function record(string $action, string $target, ?int $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'target' => $target,
            'created_at' => now(),
        ]);
    }
}
