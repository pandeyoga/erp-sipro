<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AssetSubCategory extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_category_id',
        'name',
    ];
}
