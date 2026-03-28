<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ConstructionPhase extends Model
{
    use HasUuids;
    
    protected $table = 'construction_phases';

    protected $fillable = [
        'construction_id',
        'construction_phase',
        'status',
        'documentation',
    ];

}
