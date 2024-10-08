<?php

namespace App\Services;
use Carbon\Carbon;
use App\Models\Spouse;

class Node {
    public $id;
    public $name;
    public $surname;
    public $birth_date;
    public $death_date;
    public $birth_place;
    public $death_place;
    public $children = [];
    public $spouses = [];
    public $parents = [];
    public $father_id;
    public $mother_id;
    public $gender;
    public $image;
    public $isAdopted;
    
    public $marriage_dates = [];
    public $divorce_dates = [];
    public $pets = [];
    public $hobbies = [];
    public $notes = ''; 

    public function __construct($id, $name, $surname, $birth_date, $death_date, $birth_place, $death_place, $pets, $hobbies, $gender, $father_id = null, $mother_id = null, $image = null, $isAdopted, $notes) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        //formats dates via Carbon in year-month-date format, removing timestamp. if DOB/DOD is null replaces value with arbitrary "unknown date"
        $this->birth_date = $birth_date ? Carbon::parse($birth_date)->format('Y-m-d') : 'Unknown date'; 
        $this->death_date = $death_date ? Carbon::parse($death_date)->format('Y-m-d') : 'Unknown date';
        $this->birth_place = $birth_place;
        $this->death_place = $death_place;
        $this->gender = $gender;
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
        $this->image = $image;
        $this->isAdopted = $isAdopted;
        $this->pets = $pets;
        $this->hobbies = $hobbies;
        $this->notes = $notes; 
    }
    public function setMarriageDates($marriage_date, $divorce_date) {
        $this->marriage_dates[] = $marriage_date ? Carbon::parse($marriage_date)->format('Y-m-d') : 'Unknown date'; 
        $this->divorce_dates[] = $divorce_date ? Carbon::parse($divorce_date)->format('Y-m-d') : 'Unknown date'; 
    }

    public function addSpouse(Node $spouse) {
        $this->spouses[] = $spouse;
    }

    public function isCurrentSpouse(Node $spouse) {
        $knownMarriageDates = array_filter($this->marriage_dates, function ($date) {
            return $date !== null && $date !== 'Unknown date';
        });

    $spouseKnownMarriageDates = array_filter($spouse->marriage_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    $knownDivorceDates = array_filter($this->divorce_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    $spouseKnownDivorceDates = array_filter($spouse->divorce_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    if (empty($this->marriage_dates) || empty($spouse->marriage_dates) || empty($this->divorce_dates) || empty($spouse->divorce_dates)) {
        return true;
    }

    if (!empty(array_intersect($knownDivorceDates, $spouseKnownDivorceDates))) {
        return false;
    }

    if (empty($knownMarriageDates) || empty($spouseKnownMarriageDates)) {
        return true;
    }

    $latestMarriageDate = max($knownMarriageDates);
    $spouseLatestMarriageDate = max($spouseKnownMarriageDates);

    if ($latestMarriageDate < $spouseLatestMarriageDate && empty($knownDivorceDates)) {
        $this->updateSpouseDivorceDate($spouse->id, $spouseLatestMarriageDate);
    } elseif ($spouseLatestMarriageDate < $latestMarriageDate && empty($spouseKnownDivorceDates)) {
        $this->updateSpouseDivorceDate($this->id, $latestMarriageDate);
    }

    return $spouseLatestMarriageDate === $latestMarriageDate;
}

    private function updateSpouseDivorceDate($personId, $divorceDate) {
        $spouseRecord = Spouse::where(function ($query) use ($personId) {
            $query->where(function ($q) use ($personId) {
                $q->where('first_spouse_id', $this->id)
                ->where('second_spouse_id', $personId);
            })->orWhere(function ($q) use ($personId) {
                $q->where('first_spouse_id', $personId)
                ->where('second_spouse_id', $this->id);
            });
        })->first();

    if ($spouseRecord && $spouseRecord->divorce_date === null) {
        $spouseRecord->update(['divorce_date' => $divorceDate]);
    }
    }
    
    public function addChild(Node $child, $isAdopted = false) {
        $this->children[$child->id] = $child;
        $child->isAdopted = $isAdopted;
        if ($this->gender === 'M') {
            $child->father_id = $this->id;
        } else {
            $child->mother_id = $this->id;
        }
    }

    public function addParent(Node $parent) {
        if ($parent->gender === 'M') {
            $this->father_id = $parent->id;
        } else {
            $this->mother_id = $parent->id;
        }
        $this->parents[$parent->id] = $parent;
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