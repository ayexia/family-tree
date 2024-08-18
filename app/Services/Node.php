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
    public $children = [];
    public $spouses = [];
    public $parents = [];
    public $father_id;
    public $mother_id;
    public $gender;
    public $image;
    
    public $marriage_dates = [];
    public $divorce_dates = [];

    public function __construct($id, $name, $surname, $birth_date, $death_date, $gender, $father_id = null, $mother_id = null, $image = null) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        //formats dates via Carbon in year-month-date format, removing timestamp. if DOB/DOD is null replaces value with arbitrary "unknown date"
        $this->birth_date = $birth_date ? Carbon::parse($birth_date)->format('Y-m-d') : 'Unknown date'; 
        $this->death_date = $death_date ? Carbon::parse($death_date)->format('Y-m-d') : 'Unknown date';
        $this->gender = $gender;
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
        $this->image = $image;
    }
    public function setMarriageDates($marriage_date, $divorce_date) {
        $this->marriage_dates[] = $marriage_date ? Carbon::parse($marriage_date)->format('Y-m-d') : 'Unknown date'; 
        $this->divorce_dates[] = $divorce_date ? Carbon::parse($divorce_date)->format('Y-m-d') : 'Unknown date'; 
    }

    public function addSpouse(Node $spouse) {
        $this->spouses[] = $spouse;
    }

    public function isCurrentSpouse(Node $spouse) {
        $knownMarriageDates = array_filter($this->marriage_dates, function($date) {
            return $date !== 'Unknown date' && $date !== null;
        });
        $spouseKnownMarriageDates = array_filter($spouse->marriage_dates, function($date) {
            return $date !== 'Unknown date' && $date !== null;
        });

        $knownDivorceDates = array_filter($this->divorce_dates, function($date) {
            return $date !== 'Unknown date' && $date !== null;
        });
        $spouseKnownDivorceDates = array_filter($spouse->divorce_dates, function($date) {
            return $date !== 'Unknown date' && $date !== null;
        });

        $sharedDivorceDates = array_intersect($knownDivorceDates, $spouseKnownDivorceDates);
        if (!empty($sharedDivorceDates)) {
            return false;
        }

        if (empty($knownMarriageDates) || empty($spouseKnownMarriageDates)) {
            return true;
        }

        $latestMarriageDate = max($knownMarriageDates);
        $spouseLatestMarriageDate = max($spouseKnownMarriageDates);

        $overallLatestMarriageDate = max($latestMarriageDate, $spouseLatestMarriageDate);

        if ($latestMarriageDate < $spouseLatestMarriageDate) {
            if (!empty($knownMarriageDates) && empty($knownDivorceDates)) {
                $this->updateSpouseDivorceDate($spouse->id, $spouseLatestMarriageDate);
            }
        } elseif ($spouseLatestMarriageDate < $latestMarriageDate) {
            if (!empty($spouseKnownMarriageDates) && empty($spouseKnownDivorceDates)) {
                $this->updateSpouseDivorceDate($this->id, $latestMarriageDate);
            }
        }

        if ($spouseLatestMarriageDate === $overallLatestMarriageDate) {
            return true;
        }

        return false;
    }

    private function updateSpouseDivorceDate($personId, $divorceDate) {
        try {
            $spouseRecord = Spouse::where(function ($query) use ($personId) {
                $query->where(function ($q) use ($personId) {
                    $q->where('first_spouse_id', $this->id)
                      ->where('second_spouse_id', $personId);
                })->orWhere(function ($q) use ($personId) {
                    $q->where('first_spouse_id', $personId)
                      ->where('second_spouse_id', $this->id);
                });
            })->first();

            if ($spouseRecord) {
                $spouseRecord->update(['divorce_date' => $divorceDate]);
            } else {
                Log::warning('Spouse record not found for IDs', ['person_id' => $personId]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating spouse divorce date: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function addChild(Node $child) {
        $this->children[$child->id] = $child;
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