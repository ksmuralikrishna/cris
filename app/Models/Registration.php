<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Registration extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tablet_id',
        'full_name',
        'mobile_number',
        'emirates_id_number',
        'emirates_id_hash',
        'emirates_id_image_path',
        'image_uploaded_at',
        'emirates_id_image_back_path',
        'image_back_uploaded_at',
        'nationality',
        'area_of_residence',
        'preferred_language',
        'age_group',
        'session_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'image_uploaded_at' => 'datetime',
    ];

    protected $hidden = [
        'emirates_id_hash',
        'emirates_id_image_path',
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

    public static function hashEmiratesId(string $id): string
    {
        $normalised = strtoupper(str_replace('-', '', trim($id)));
        return hash('sha256', $normalised);
    }

    public static function isDuplicate(string $id): bool
    {
        return static::where('emirates_id_hash', self::hashEmiratesId($id))->exists();
    }

    public static function findByEmiratesId(string $id): ?self
    {
        return static::where('emirates_id_hash', self::hashEmiratesId($id))->first();
    }

    public function tablet(): BelongsTo
    {
        return $this->belongsTo(Tablet::class);
    }

    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
