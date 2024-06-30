<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotherAndChild extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
    'gedcom_id',
    'mother_id',
    'child_id',
    ];

    public function mother()
    {
        return $this->belongsTo(Person::class);
    }

    public function child()
    {
        return $this->belongsTo(Person::class);
    }

}
