<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RetentionCase extends Model
{
    use HasUuids;

    protected $fillable = [
        'property_id',
        'opened_at',
        'description',
        'status',
        'resolved_at',
        'estimated_resolved_at',
        'case_pictures',
        'case_documentations',
        'sub_contractor_id',
        'notes',
    ];

    protected $casts = [
        'case_pictures' => 'array',
        'case_documentations' => 'array',
    ];
}
