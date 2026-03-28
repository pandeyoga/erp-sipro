<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    use HasUuids;

    protected $table = 'assets';

    protected $fillable = [
        'registration_number',
        'category_id',
        'sub_category_id',
        'name',
        'description',
        'quantity',
        'price',
        'acquisition_date',
        'useful_life',
        'has_depreciation',
        'depreciation_rate',
    ];
}
