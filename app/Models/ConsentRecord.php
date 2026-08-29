<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use RuntimeException;

class ConsentRecord extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'registration_id',
        'consent_type',
        'granted',
        'granted_at',
        'document_version',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'granted_at' => 'datetime',
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
            throw new RuntimeException('Consent records are append-only and cannot be updated.');
        });

        static::deleting(function ($model) {
            throw new RuntimeException('Consent records are append-only and cannot be deleted.');
        });
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
