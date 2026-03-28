<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'location',
        'developer',
        'area_total_sqm',
        'start_date',
        'status',
        'created_by',
        'site_plan_image'
    ];
}
