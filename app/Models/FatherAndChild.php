<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FatherAndChild extends Model
{
    use HasFactory;
    protected $fillable = [
    'gedcom_id',
    'father_id',
    'child_id',
    'child_number',
    'family_tree_id',
    'is_adopted'
    ];

    public function father() //a relationship indicating this model belongs to a person acting as the father
    {
        return $this->belongsTo(Person::class);
    }

    public function child() // a relationship indicating this model belongs to a person acting as the child
    {
        return $this->belongsTo(Person::class);
    }

}
