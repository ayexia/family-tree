<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'gedcom_id', 
        'name', 
        'gender', 
        'birth_date', 
        'birth_date_qualifier',
        'death_date',
        'death_date_qualifier',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    public function spouses()
    {
        return $this->hasMany(Relationship::class, 'person_id')
            ->where('type', 'spouse');
    }

    public function children()
    {
        return $this->hasMany(Relationship::class, 'person_id')
            ->where('type', 'child');
    }
}
