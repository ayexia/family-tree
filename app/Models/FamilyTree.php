<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyTree extends Model
{ 
    protected $fillable = ['user_id'];

    //relationship with user model; each family tree belongs to a user
    public function users()
    {
        return $this->belongsTo(User::class);
    } 
    //relationship with people model; a family tree can have many people
    public function people()
    {
        return $this->hasMany(Person::class);
    } 

    //relationship with spouse model; a family tree can have many spouses
    public function spouses()
    {
        return $this->hasMany(Spouse::class);
    }

    //relationship with mother and children; a family tree can have many mother/child records
    public function motherAndChildren()
    {
        return $this->hasMany(MotherAndChild::class);
    }

    //relationship with father and children; a family tree can have many father/child records
    public function fatherAndChildren()
    {
        return $this->hasMany(FatherAndChild::class);
    }
}