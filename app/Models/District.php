<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class District extends Model
{
   protected $fillable = ['county_id', 'name', 'code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    public function activeCommunities(): HasMany
    {
        return $this->communities()->where('is_active', true);
    }
}
