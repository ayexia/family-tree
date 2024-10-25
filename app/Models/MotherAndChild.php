<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotherAndChild extends Model
{
    use HasFactory;
    protected $fillable = [
    'gedcom_id',
    'mother_id',
    'child_id',
    'child_number',
    'family_tree_id',
    'is_adopted'
    ];

    public function mother() //a relationship where this model belongs to a person as a mother
    {
        return $this->belongsTo(Person::class);
    }


    public function child() //a relationship where this model belongs to a person as a child
    {
        return $this->belongsTo(Person::class);
    }

}
