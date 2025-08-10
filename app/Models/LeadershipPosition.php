<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadershipPosition extends Model
{
    protected $fillable = ['title', 'description', 'hierarchy_level', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // Scope for active positions
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for positions by hierarchy level
    public function scopeByHierarchy($query, $level)
    {
        return $query->where('hierarchy_level', $level);
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(Leader::class);
    }
}
