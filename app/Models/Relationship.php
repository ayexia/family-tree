<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    protected $fillable = [
        'gedcom_id',
        'type',
        'marriage_date',
        'marriage_date_qualifier',
        'divorce_date',
        'divorce_date_qualifier',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'divorce_date' => 'date',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function relative()
    {
        return $this->belongsTo(Person::class, 'relative_id');
    }
}
