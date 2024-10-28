<?php

namespace App\Services;

use App\Models\Person;
use App\Models\MotherAndChild;
use App\Models\FatherAndChild;
use App\Models\Spouse;

class GedcomExporter
{
//exports gedcom representation of the family tree identified by $familytreeid
    public function export($familyTreeId)
    {
        // Initialise the gedcom file header
        $gedcom = "0 HEAD\n1 GEDC\n2 VERS 5.5.1\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8\n";
    //retrieve all people associated with specified family tree
        $people = Person::where('family_tree_id', $familyTreeId)->get();
        foreach ($people as $person) {
            $gedcom .= $this->exportPerson($person); //export each person
        }

        //retrieve all families within family tree
        $families = $this->getAllFamilies($familyTreeId);
        foreach ($families as $family) {
            $gedcom .= $this->exportFamily($family);
        }
        
        //close gedcom file
        $gedcom .= "0 TRLR\n";

        return $gedcom; //return complete gedcom string
    }

    //exports individuals info in gedcom format
    private function exportPerson($person)
    {

        //extracts first and last name by splitting name into parts
        $nameParts = explode(' ', $person->name);
        $lastName = array_pop($nameParts);
        $firstName = implode(' ', $nameParts);

        //start individuals record
        $gedcom = "0 @I{$person->id}@ INDI\n";
        $gedcom .= "1 NAME {$firstName} /{$lastName}/\n";
        $gedcom .= "1 GIVN {$firstName}\n";
        $gedcom .= "2 SURN {$person->surname}\n";
        $gedcom .= "1 SEX {$person->gender}\n";
    
        //if birth and/or death date and place provided include in record
        if ($person->birth_date) {
            $gedcom .= "1 BIRT\n";
            $gedcom .= "2 DATE " . $this->formatDate($person->birth_date, $person->birth_date_qualifier) . "\n";
            if ($person->birth_place) {
                $gedcom .= "2 PLAC {$person->birth_place}\n";
            }
        }

        if ($person->death_date) {
            $gedcom .= "1 DEAT\n";
            $gedcom .= "2 DATE " . $this->formatDate($person->death_date, $person->death_date_qualifier) . "\n";
            if ($person->death_place) {
                $gedcom .= "2 PLAC {$person->death_place}\n";
            }
        }
        //retrieve family records as child
      $familiesAsChild = $this->getFamiliesAsChild($person->id);
        foreach ($familiesAsChild as $familyId) {
            $gedcom .= "1 FAMC @{$this->removeSuffix($familyId)}@\n";
        }
        //retrieve family record as spouse
        $familiesAsSpouse = $this->getFamiliesAsSpouse($person->id);
        foreach ($familiesAsSpouse as $familyId) {
            $gedcom .= "1 FAMS @{$this->removeSuffix($familyId)}@\n";
        }

        return $gedcom;
    }

    private function exportFamily($family)
    {
    $gedcom = "0 @{$this->removeSuffix($family['id'])}@ FAM\n";

    //include details of husband if available     
    if (isset($family['husband_id'])) {
        $gedcom .= "1 HUSB @I{$family['husband_id']}@\n";
    }

    //include details of wife if available 
    if (isset($family['wife_id'])) {
        $gedcom .= "1 WIFE @I{$family['wife_id']}@\n";
    }

    //include details of marriage if available 
    if (isset($family['marriage_date'])) {
        $gedcom .= "1 MARR\n";
        $gedcom .= "2 DATE " . $this->formatDate($family['marriage_date'], $family['marriage_date_qualifier']) . "\n";
        if (isset($family['marriage_place'])) {
            $gedcom .= "2 PLAC {$family['marriage_place']}\n";
        }
    }
    //list children in the family
    foreach ($family['children'] as $child) {
        $gedcom .= "1 CHIL @I{$child['id']}@\n";
        if ($child['is_adopted']) {
            $gedcom .= "2 ADOP\n"; //indicate if child is adopted 
        }
    }

    return $gedcom;
    }

    private function getAllFamilies($familyTreeId)
    {
    $families = [];
    //retrieve spouse records
    $spouses = Spouse::where('family_tree_id', $familyTreeId)->get();
    foreach ($spouses as $spouse) {
        $familyId = $this->removeSuffix($spouse->gedcom_id);
        $families[$familyId] = [
            'id' => $familyId,
            'husband_id' => $spouse->second_spouse_id,
            'wife_id' => $spouse->first_spouse_id,
            'marriage_date' => $spouse->marriage_date,
            'marriage_date_qualifier' => $spouse->marriage_date_qualifier,
            'marriage_place' => $spouse->marriage_place,
            'children' => []
        ];
    }

    //retrieve mother and child relationships
    $motherAndChildren = MotherAndChild::where('family_tree_id', $familyTreeId)->get();
    foreach ($motherAndChildren as $relation) {
        $familyId = $this->removeSuffix($relation->gedcom_id);
        if (!isset($families[$familyId])) {
            $families[$familyId] = [
                'id' => $familyId,
                'wife_id' => $relation->mother_id,
                'children' => []
            ];
        }
        $families[$familyId]['children'][$relation->child_id] = [
            'id' => $relation->child_id,
            'is_adopted' => $relation->is_adopted
        ];
    }
    
    //retrieve father and child relationships
    $fatherAndChildren = FatherAndChild::where('family_tree_id', $familyTreeId)->get();
    foreach ($fatherAndChildren as $relation) {
        $familyId = $this->removeSuffix($relation->gedcom_id);
        if (!isset($families[$familyId])) {
            $families[$familyId] = [
                'id' => $familyId,
                'husband_id' => $relation->father_id,
                'children' => []
            ];
        } elseif (!isset($families[$familyId]['husband_id'])) {
            $families[$familyId]['husband_id'] = $relation->father_id;
        }
        $families[$familyId]['children'][$relation->child_id] = [
            'id' => $relation->child_id,
            'is_adopted' => $relation->is_adopted
        ];
    }

    return $families; //return the compiled families
    }

    private function getFamiliesAsChild($personId)
    {
        $familyIds = []; //initialsises family IDs
        //check mother/child relationship and if it exists find family ID
        $motherRelation = MotherAndChild::where('child_id', $personId)->first();
        if ($motherRelation) {
            $familyIds[] = $motherRelation->gedcom_id;
        }
        //check father/child relationship and if it exists find family ID
        $fatherRelation = FatherAndChild::where('child_id', $personId)->first();
        if ($fatherRelation) {
            $familyIds[] = $fatherRelation->gedcom_id;
        }

        return array_unique($familyIds);//return unique family IDs
    }

    private function getFamiliesAsSpouse($personId)
    {
        //where the person is spouse retrieve family IDs
        return Spouse::where('first_spouse_id', $personId)
            ->orWhere('second_spouse_id', $personId)
            ->pluck('gedcom_id')
            ->toArray();
    }

    private function formatDate($date, $qualifier)
    {
        $formattedDate = date('d M Y', strtotime($date)); //adjusts date appropriately for GEDCOM specification
        if ($qualifier !== 'EXACT') {
            return $qualifier . ' ' . $formattedDate;   //append qualifier if not exact 
        }
        return $formattedDate;
    }

    private function removeSuffix($gedcomId)
    {
        //remove suffix from gedcom Id
        return preg_replace('/-CHILD.*$/', '', $gedcomId);
    }
}
