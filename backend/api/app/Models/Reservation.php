<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasUuids;

    protected $table = 'reservations';

    protected $fillable = [
        'lead_id',
        'property_unit_id',
        'status',
        'reservation_date',
        'booking_document_url',
        'dp_proof_url',
        'dp_amount',
        'hook_additional_fee',
        'additional_land_area_fee',
        'additional_building_specifications_fee',
        'all_in_fee',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    public function propertyUnit()
    {
        return $this->belongsTo(UnitProperty::class, 'property_unit_id', 'id');
    }
}
