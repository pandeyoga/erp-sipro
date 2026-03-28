<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PropertyQC extends Model
{
    use HasUuids;
    
    protected $table = 'property_quality_controls';

    protected $fillable = [
        'property_id',
        'name',
        'is_passed',
        'evidence',
        'comment',
    ];
}
