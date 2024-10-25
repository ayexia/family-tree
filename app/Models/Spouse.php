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
        'family_tree_id',
    ];

    protected $casts = [
    //casts ‘marriage_date’ and ‘divorce_date’to date format
        'marriage_date' => 'date',
        'divorce_date' => 'date',
    ];
    
    public function firstSpouse()
    {
//defines relationship where this model belongs to a person as first spouse 
        return $this->belongsTo(Person::class);
    }
    
    public function secondSpouse()
    {
//defines relationship where this model belongs to a person as second spouse
        return $this->belongsTo(Person::class);
    }        

}
