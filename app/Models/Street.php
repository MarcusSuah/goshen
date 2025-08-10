<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Street extends Model
{
    protected $fillable = ['block_id', 'name', 'street_type', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function households(): HasMany
    {
        return $this->hasMany(Household::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->street_type}";
    }
}
