<?php

namespace App\Services;
//testing code for OOP purposes
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
        $this->birth_date = $birth_date;
        $this->death_date = $death_date;
    }

    public function addChild(Node $child) {
        $this->children[] = $child;
    }

    public function addSpouse(Node $spouse) {
        $this->spouses[] = $spouse;
    }

    public function addParent(Node $parent) {
        $this->parents[] = $parent;
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
