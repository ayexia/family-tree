<?php

namespace App\Services;
use Carbon\Carbon;

class Node {
    public $id;
    public $name;
    public $birth_date;
    public $death_date;
    public $children = [];
    public $spouses = [];
    public $parents = [];
    public $father_id;
    public $mother_id;
    public $gender;

    public function __construct($id, $name, $birth_date, $death_date, $gender, $father_id = null, $mother_id = null) {
        $this->id = $id;
        $this->name = $name;
        //formats dates via Carbon in year-month-date format, removing timestamp. if DOB/DOD is null replaces value with arbitrary "unknown date"
        $this->birth_date = $birth_date ? Carbon::parse($birth_date)->format('Y-m-d') : 'Unknown date'; 
        $this->death_date = $death_date ? Carbon::parse($death_date)->format('Y-m-d') : 'Unknown date';
        $this->gender = $gender;
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
    }

    public function addChild(Node $child) {
        $this->children[$child->id] = $child;
    }

    public function addSpouse(Node $spouse, $marriage_date = null, $divorce_date = null) {
        $this->spouses[] = [
            'node' => $spouse,
            'marriage_date' => $marriage_date ? Carbon::parse($marriage_date)->format('Y-m-d') : 'Unknown date',
            'divorce_date' => $divorce_date ? Carbon::parse($divorce_date)->format('Y-m-d') : 'Unknown date'
        ];
    }

    public function addParent(Node $parent) {
        $this->parents[$parent->id] = $parent;
        if ($parent->gender === 'M') {
            $this->father_id = $parent->id;
        } else {
            $this->mother_id = $parent->id;
        }
    }

    public function getChildren() {
        return $this->children;
    }

    public function getSpouses() {
        return $this->spouses;
    }

    public function getParents() {
        return $this->parents;
    }
}