<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyTree extends Model
{
    protected $fillable = ['user_id'];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function spouses()
    {
        return $this->hasMany(Spouse::class);
    }

    public function motherAndChildren()
    {
        return $this->hasMany(MotherAndChild::class);
    }

    public function fatherAndChildren()
    {
        return $this->hasMany(FatherAndChild::class);
    }
}