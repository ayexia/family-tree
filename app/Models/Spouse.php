<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'gedcom_id',
        'first_spouse_id',
        'second_spouse_id',
        'marriage_date',
        'marriage_date_qualifier',
        'divorce_date',
        'divorce_date_qualifier',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'divorce_date' => 'date',
    ];

    public function firstSpouse()
    {
        return $this->belongsTo(Person::class);
    }

    public function secondSpouse()
    {
        return $this->belongsTo(Person::class);
    }

}
