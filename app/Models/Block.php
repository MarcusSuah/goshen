<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Block extends Model
{
    protected $fillable = ['community_id', 'name', 'block_number', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function streets(): HasMany
    {
        return $this->hasMany(Street::class);
    }

    public function leaders(): MorphMany
    {
        return $this->morphMany(Leader::class, 'leaderable');
    }

    public function households()
    {
        return $this->hasManyThrough(Household::class, Street::class);
    }

}
