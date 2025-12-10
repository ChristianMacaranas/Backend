<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'role',
        'company',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    
    protected $appends = ['duration'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    
    public function getDurationAttribute()
    {
        if (!$this->start_date) {
            return null;
        }

        // If no end date, assume ongoing
        $end = $this->end_date ?? now();

        // Return number of years
        return $this->start_date->diffInYears($end) . ' years';
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('start_date', 'desc');
    }
}
