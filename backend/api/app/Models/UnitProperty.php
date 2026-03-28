<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UnitProperty extends Model
{
    use HasUuids;

    protected $table = 'unit_properties';

    protected $fillable = [
        'project_id',
        'cluster_id',
        'unit_type_id',
        'unit_number',
        'price',
        'status',
        'dev_substatus',
        'notes',
        'construction_notes'
    ];

    public function propertyLoc()
    {
        return $this->hasOne(PropertyLoc::class, 'property_id', 'id');
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class, 'property_unit_id', 'id');
    }
}
