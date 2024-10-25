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
        'birth_place',
        'death_place',
        'pets',
        'hobbies',
        'notes',
    ];

    protected $casts = [
    //casts ‘birth_date’ and ‘death_date’ to date format
        'birth_date' => 'date',
        'death_date' => 'date',
    //casts ‘pets’ and ‘hobbies’ to array format        
        'pets' => 'array',
        'hobbies' => 'array',
    ];
    
    
    public function firstSpouses()
    {//defines one to many relationships for first spouses
        return $this->hasMany(Spouse::class, 'first_spouse_id');
    }

    public function secondSpouses()
    {//defines one to many relationships for second spouses
        return $this->hasMany(Spouse::class, 'second_spouse_id');
    }

    public function fatherOfChildren()
    {
        //defines one to many relationships for fathers and their children
        return $this->hasMany(FatherAndChild::class, 'father_id');
    }

    public function motherOfChildren()
    {//defines one to many relationships for mothers and their children
        return $this->hasMany(MotherAndChild::class, 'mother_id');
    }

    public function childrenofFather() //finds all children of a particular father
    {
    return $this->hasManyThrough(Person::class, FatherAndChild::class, 'father_id', 'id', 'id', 'child_id');
    }

    public function childrenofMother() //finds all children of a particular father
    {
    return $this->hasManyThrough(Person::class, MotherAndChild::class, 'mother_id', 'id', 'id', 'child_id');
    }

    public function familyTree() //belongs to a family tree
    {
        return $this->belongsTo(FamilyTree::class);
    }

}
