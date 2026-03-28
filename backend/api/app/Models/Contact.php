<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Contact extends Model
{
    use HasUuids;

    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'location',
        'source',
    ];

    public function lead()
    {
        return $this->hasOne(Lead::class, 'contact_id', 'id');
    }

    public function duplicates()
    {
        return $this->hasMany(Contact::class, 'phone', 'phone');
    }
}
