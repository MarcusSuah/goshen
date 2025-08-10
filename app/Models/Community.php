<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Community extends Model
{
    protected $fillable = [
        'district_id', 'name', 'code', 'description',
        'latitude', 'longitude', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function activeBlocks(): HasMany
    {
        return $this->blocks()->where('is_active', true);
    }

    public function leaders(): MorphMany
    {
        return $this->morphMany(Leader::class, 'leaderable');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CommunityMembership::class);
    }

    public function getFullLocationAttribute(): string
    {
        return "{$this->name}, {$this->district->name}, {$this->district->county->name}";
    }
}
