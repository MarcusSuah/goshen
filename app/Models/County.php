<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
 protected $fillable = ['name', 'city', 'code', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function activeDistricts(): HasMany
    {
        return $this->districts()->where('is_active', true);
    }

    public function communities()
    {
        return $this->hasManyThrough(Community::class, District::class);
    }

    public function totalResidents()
    {
        return $this->communities()
            ->join('blocks', 'communities.id', '=', 'blocks.community_id')
            ->join('streets', 'blocks.id', '=', 'streets.block_id')
            ->join('households', 'streets.id', '=', 'households.street_id')
            ->join('residents', 'households.id', '=', 'residents.household_id')
            ->where('residents.is_active', true)
            ->count();
    }

}
