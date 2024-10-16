<?php

namespace App\Services;

use App\Models\Person;
use App\Models\MotherAndChild;
use App\Models\FatherAndChild;
use App\Models\Spouse;

class GedcomExporter
{
    public function export($familyTreeId)
    {
        $gedcom = "0 HEAD\n1 GEDC\n2 VERS 5.5.1\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8\n";

        $people = Person::where('family_tree_id', $familyTreeId)->get();
        foreach ($people as $person) {
            $gedcom .= $this->exportPerson($person);
        }

        $families = $this->getAllFamilies($familyTreeId);
        foreach ($families as $family) {
            $gedcom .= $this->exportFamily($family);
        }

        $gedcom .= "0 TRLR\n";

        return $gedcom;
    }

    private function exportPerson($person)
    {
    
        $nameParts = explode(' ', $person->name);
        $lastName = array_pop($nameParts);
        $firstName = implode(' ', $nameParts);

        $gedcom = "0 @I{$person->id}@ INDI\n";
        $gedcom .= "1 NAME {$firstName} /{$lastName}/\n";
        $gedcom .= "1 GIVN {$firstName}\n";
        $gedcom .= "2 SURN {$person->surname}\n";
        $gedcom .= "1 SEX {$person->gender}\n";

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

        $familiesAsChild = $this->getFamiliesAsChild($person->id);
        foreach ($familiesAsChild as $familyId) {
            $gedcom .= "1 FAMC @{$this->removeSuffix($familyId)}@\n";
        }

        $familiesAsSpouse = $this->getFamiliesAsSpouse($person->id);
        foreach ($familiesAsSpouse as $familyId) {
            $gedcom .= "1 FAMS @{$this->removeSuffix($familyId)}@\n";
        }

        return $gedcom;
    }

    private function exportFamily($family)
    {
    $gedcom = "0 @{$this->removeSuffix($family['id'])}@ FAM\n";
    
    if (isset($family['husband_id'])) {
        $gedcom .= "1 HUSB @I{$family['husband_id']}@\n";
    }
    
    if (isset($family['wife_id'])) {
        $gedcom .= "1 WIFE @I{$family['wife_id']}@\n";
    }

    if (isset($family['marriage_date'])) {
        $gedcom .= "1 MARR\n";
        $gedcom .= "2 DATE " . $this->formatDate($family['marriage_date'], $family['marriage_date_qualifier']) . "\n";
        if (isset($family['marriage_place'])) {
            $gedcom .= "2 PLAC {$family['marriage_place']}\n";
        }
    }

    foreach ($family['children'] as $child) {
        $gedcom .= "1 CHIL @I{$child['id']}@\n";
        if ($child['is_adopted']) {
            $gedcom .= "2 ADOP\n";
        }
    }

    return $gedcom;
    }

    private function getAllFamilies($familyTreeId)
    {
    $families = [];

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

    return $families;
    }

    private function getFamiliesAsChild($personId)
    {
        $familyIds = [];

        $motherRelation = MotherAndChild::where('child_id', $personId)->first();
        if ($motherRelation) {
            $familyIds[] = $motherRelation->gedcom_id;
        }

        $fatherRelation = FatherAndChild::where('child_id', $personId)->first();
        if ($fatherRelation) {
            $familyIds[] = $fatherRelation->gedcom_id;
        }

        return array_unique($familyIds);
    }

    private function getFamiliesAsSpouse($personId)
    {
        return Spouse::where('first_spouse_id', $personId)
            ->orWhere('second_spouse_id', $personId)
            ->pluck('gedcom_id')
            ->toArray();
    }

    private function formatDate($date, $qualifier)
    {
        $formattedDate = date('d M Y', strtotime($date));
        if ($qualifier !== 'EXACT') {
            return $qualifier . ' ' . $formattedDate;
        }
        return $formattedDate;
    }

    private function removeSuffix($gedcomId)
    {
        return preg_replace('/-CHILD.*$/', '', $gedcomId);
    }
}