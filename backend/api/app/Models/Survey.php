<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasUuids;

    protected $fillable = [
        'lead_id',
        'status',
        'scheduled_date',
        'actual_survey_date',
        'scheduled_at',
        'created_by',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
