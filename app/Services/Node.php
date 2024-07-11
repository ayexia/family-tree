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

    public function __construct($id, $name, $birth_date, $death_date) {
        $this->id = $id;
        $this->name = $name;
        $this->birth_date = $birth_date ? Carbon::parse($birth_date)->format('Y-m-d') : 'Unknown date';
        $this->death_date = $death_date ? Carbon::parse($death_date)->format('Y-m-d') : 'Unknown date';
    }

    public function addChild(Node $child) {
        $this->children[$child->id] = $child;
    }

    public function addSpouse(Node $spouse) {
        $this->spouses[$spouse->id] = $spouse;
    }

    public function addParent(Node $parent) {
        $this->parents[$parent->id] = $parent;
    }

    public function getChildren(){
        return $this->children;
    }

    public function getSpouses(){
        return $this->spouses;
    }

    public function getParents(){
        return $this->parents;
    }
}
