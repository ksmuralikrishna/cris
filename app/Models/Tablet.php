<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tablet extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'label',
        'location_zone',
        'api_token',
        'app_version',
        'is_active',
        'last_heartbeat_at',
    ];

    protected $hidden = [
        'api_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_heartbeat_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
