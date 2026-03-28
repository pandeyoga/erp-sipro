<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Construction extends Model
{
    use HasUuids;

    protected $table = 'constructions';

    protected $fillable = [
        'project_id',
        'unit_property_id',
        'start_date',
        'estimated_end_date',
        'actual_end_date',
        'sub_contractor_id',
        'status',
        'notes',
        'spk',
    ];
}
