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
    ];

    public function father()
    {
        return $this->belongsTo(Person::class);
    }

    public function child()
    {
        return $this->belongsTo(Person::class);
    }

}
