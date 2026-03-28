<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasUuids;
    protected $table = 'leads';

    protected $fillable = [
        'contact_id',
        'order_number',
        'assign_to',
        'status',
        'survey_date',
        'survey_location_id',
        'due_date',
        'note',
        'pic',
        'actual_survey_date',
        'survey_documentation',
        'unit_preference_id'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    public function history()
    {
        return $this->hasMany(LeadHistory::class);
    }

    public function reservations()
    {
        return $this->hasOne(Reservation::class, 'lead_id', 'id');
    }

    public function collectionDocuments()
    {
        return $this->hasOne(CollectionDocument::class, 'lead_id');
    }

    public function payment()
    {
        return $this->hasOne(LeadPayment::class, 'lead_id');
    }
}
