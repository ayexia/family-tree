<?php

namespace App\Services;
use Carbon\Carbon;
use App\Models\Spouse;

/**
 * Node class representing a person in the family tree.
 * Stores personal information and manages relationships between family members.
 */
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
    /**
    * Adds marriage and divorce dates for a relationship.
    * Formats dates using Carbon to ensure consistent format.
    * 
    * @param string|null $marriage_date Date of marriage
    * @param string|null $divorce_date Date of divorce if applicable
    */
   public function setMarriageDates($marriage_date, $divorce_date) {
    $this->marriage_dates[] = $marriage_date ? Carbon::parse($marriage_date)->format('Y-m-d') : 'Unknown date'; 
    $this->divorce_dates[] = $divorce_date ? Carbon::parse($divorce_date)->format('Y-m-d') : 'Unknown date'; 
}

/**
 * Adds a spouse to this person's relationships.
 * 
 * @param Node $spouse Node object representing the spouse
 */
public function addSpouse(Node $spouse) {
    $this->spouses[] = $spouse;
}

/**
 * Determines if given spouse is currently married to this person.
 * Checks marriage and divorce dates to determine current status.
 * 
 * @param Node $spouse Node object representing potential current spouse
 * @return bool True if currently married, false if divorced
 */
public function isCurrentSpouse(Node $spouse) {
    //filters out unknown or null marriage dates
    $knownMarriageDates = array_filter($this->marriage_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    $spouseKnownMarriageDates = array_filter($spouse->marriage_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    //filters out unknown or null divorce dates
    $knownDivorceDates = array_filter($this->divorce_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    $spouseKnownDivorceDates = array_filter($spouse->divorce_dates, function ($date) {
        return $date !== null && $date !== 'Unknown date';
    });

    //assumes current marriage if dates are missing
    if (empty($this->marriage_dates) || empty($spouse->marriage_dates) || 
        empty($this->divorce_dates) || empty($spouse->divorce_dates)) {
        return true;
    }

    //checks if there's a matching divorce date
    if (!empty(array_intersect($knownDivorceDates, $spouseKnownDivorceDates))) {
        return false;
    }

    //assumes current marriage if marriage dates unknown
    if (empty($knownMarriageDates) || empty($spouseKnownMarriageDates)) {
        return true;
    }

    //finds most recent marriage date for each person
    $latestMarriageDate = max($knownMarriageDates);
    $spouseLatestMarriageDate = max($spouseKnownMarriageDates);

    //updates divorce dates if newer marriage exists
    if ($latestMarriageDate < $spouseLatestMarriageDate && empty($knownDivorceDates)) {
        $this->updateSpouseDivorceDate($spouse->id, $spouseLatestMarriageDate);
    } elseif ($spouseLatestMarriageDate < $latestMarriageDate && empty($spouseKnownDivorceDates)) {
        $this->updateSpouseDivorceDate($this->id, $latestMarriageDate);
    }

    //considers marriage current if latest dates match
    return $spouseLatestMarriageDate === $latestMarriageDate;
}

   /**
    * Updates divorce date in database for a specific spouse relationship.
    * Called when a new marriage indicates previous marriage must have ended.
    * 
    * @param int $personId ID of the spouse
    * @param string $divorceDate Date to set as divorce date
    */
    private function updateSpouseDivorceDate($personId, $divorceDate) {
        //finds spouse record checking both possible arrangements of spouse IDs
        $spouseRecord = Spouse::where(function ($query) use ($personId) {
            $query->where(function ($q) use ($personId) {
                $q->where('first_spouse_id', $this->id)
                ->where('second_spouse_id', $personId);
            })->orWhere(function ($q) use ($personId) {
                $q->where('first_spouse_id', $personId)
                ->where('second_spouse_id', $this->id);
            });
        })->first();
 
        //updates divorce date if record exists and no divorce date set
        if ($spouseRecord && $spouseRecord->divorce_date === null) {
            $spouseRecord->update(['divorce_date' => $divorceDate]);
        }
    }
    
    //adds a child to current node, updating child id accordingly and adoption status if adopted
    public function addChild(Node $child, $isAdopted = false) {
        $this->children[$child->id] = $child;
        $child->isAdopted = $isAdopted;
        if ($this->gender === 'M') {
            $child->father_id = $this->id;
        } else {
            $child->mother_id = $this->id;
        }
    }
    //adds a parent to current node based on gender, updating parent id accordingly 
    public function addParent(Node $parent) {
        if ($parent->gender === 'M') {
            $this->father_id = $parent->id;
        } else {
            $this->mother_id = $parent->id;
        }
        $this->parents[$parent->id] = $parent;
    }
    //returns the children of current node
    public function getChildren() {
        return $this->children;
    }
    //returns the spouse of current node
    public function getSpouses() {
        return $this->spouses;
    }
    //returns the parent of current node
    public function getParents() {
        return $this->parents;
    }
}