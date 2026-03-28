<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PropertyLoc extends Model
{
    use HasUuids;

    protected $table = 'property_locs';

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'top',
        'left',
        'width',
        'height',
        'rotate',
    ];

    public function property()
    {
        return $this->belongsTo(UnitProperty::class);
    }
}
