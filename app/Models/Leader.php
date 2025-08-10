<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Leader extends Model
{
    use HasFactory;

    protected $fillable = [
        'leadership_position_id', 'first_name', 'last_name', 'middle_name',
        'phone', 'email', 'date_of_birth', 'gender', 'appointment_date',
        'term_end_date', 'is_active', 'leaderable_type', 'leaderable_id', 'image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
        'appointment_date' => 'date',
        'term_end_date' => 'date'
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(LeadershipPosition::class, 'leadership_position_id');
    }

    public function leaderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
