<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    protected $fillable = [
        'gedcom_id', 
        'name', 
        'gender', 
        'surname',
        'birth_date', 
        'birth_date_qualifier',
        'death_date',
        'death_date_qualifier',
        'image',
        'family_tree_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];
    
    public function firstSpouses()
    {
        return $this->hasMany(Spouse::class, 'first_spouse_id');
    }

    public function secondSpouses()
    {
        return $this->hasMany(Spouse::class, 'second_spouse_id');
    }

    public function fatherOfChildren()
    {
        return $this->hasMany(FatherAndChild::class, 'father_id');
    }

    public function motherOfChildren()
    {
        return $this->hasMany(MotherAndChild::class, 'mother_id');
    }

    public function childrenofFather()
    {
    return $this->hasManyThrough(Person::class, FatherAndChild::class, 'father_id', 'id', 'id', 'child_id');
    }

    public function childrenofMother()
    {
    return $this->hasManyThrough(Person::class, MotherAndChild::class, 'mother_id', 'id', 'id', 'child_id');
    }

    public function familyTree()
    {
        return $this->belongsTo(FamilyTree::class);
    }

}
