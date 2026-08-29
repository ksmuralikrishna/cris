<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $keyType = 'string';
    public $incrementing = false;
    
    const UPDATED_AT = null;

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'target_id',
        'metadata',
        'ip_address',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'occurred_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });

        static::updating(function ($model) {
            throw new RuntimeException('Audit logs are append-only and cannot be updated.');
        });

        static::deleting(function ($model) {
            throw new RuntimeException('Audit logs are append-only and cannot be deleted.');
        });
    }

    public static function record(string $actorType, string $actorId, string $action, ?string $targetId = null, ?array $metadata = null, ?string $ip = null)
    {
        return static::create([
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'action' => $action,
            'target_id' => $targetId,
            'metadata' => $metadata,
            'ip_address' => $ip ?? request()->ip(),
            'occurred_at' => now(),
        ]);
    }

    public static function tablet(string $tabletId, string $action, ?string $targetId = null, ?array $metadata = null)
    {
        return static::record('tablet', $tabletId, $action, $targetId, $metadata);
    }

    public static function system(string $action, ?string $targetId = null, ?array $metadata = null)
    {
        return static::record('system', 'system', $action, $targetId, $metadata, '127.0.0.1');
    }
}
