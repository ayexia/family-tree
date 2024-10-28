<?php

namespace App\Services;

//imports required model classes
use App\Models\Person;
use App\Models\MotherAndChild;
use App\Models\FatherAndChild;
use App\Models\Spouse;

/**
 * Handles the export of family tree data to GEDCOM format.
 * Converts database records into standard GEDCOM 5.5.1 format.
 */
class GedcomExporter
{
    /**
     * Exports entire family tree to GEDCOM format.
     * 
     * @param int $familyTreeId ID of the family tree to export
     * @return string Complete GEDCOM file content
     */
    public function export($familyTreeId)
    {
        // Creates standard GEDCOM header with version and character encoding
        $gedcom = "0 HEAD\n1 GEDC\n2 VERS 5.5.1\n2 FORM LINEAGE-LINKED\n1 CHAR UTF-8\n";
        
        //retrieves and processes all people in the family tree
        $people = Person::where('family_tree_id', $familyTreeId)->get();
        foreach ($people as $person) {
            $gedcom .= $this->exportPerson($person); //adds each person's GEDCOM record
        }

        //retrieves and processes all family units
        $families = $this->getAllFamilies($familyTreeId);
        foreach ($families as $family) {
            $gedcom .= $this->exportFamily($family);
        }
        
        //adds required GEDCOM trailer
        $gedcom .= "0 TRLR\n";

        return $gedcom;
    }

    /**
     * Converts individual person's data to GEDCOM format.
     * Includes personal details, events, and family relationships.
     * 
     * @param Person $person Person model instance to export
     * @return string GEDCOM formatted person record
     */
    private function exportPerson($person)
    {
        //separates first and last names for GEDCOM format
        $nameParts = explode(' ', $person->name);
        $lastName = array_pop($nameParts);
        $firstName = implode(' ', $nameParts);

        //creates individual record with unique ID
        $gedcom = "0 @I{$person->id}@ INDI\n";
        //adds name in standard GEDCOM format with slashes around surname
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

    /**
    * Converts family unit data to GEDCOM format.
    * Includes spouse relationships, marriage details, and children.
    * 
    * @param array $family Array containing family unit information
    * @return string GEDCOM formatted family record
    */
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

    /**
    * Retrieves and organizes all family relationships for a family tree.
    * Combines spouse, parent-child relationships into family units.
    * 
    * @param int $familyTreeId ID of the family tree
    * @return array Array of organized family units
    */
   private function getAllFamilies($familyTreeId)
   {
       $families = [];

       //processes spouse relationships first
       $spouses = Spouse::where('family_tree_id', $familyTreeId)->get();
       foreach ($spouses as $spouse) {
           $familyId = $this->removeSuffix($spouse->gedcom_id);
           //creates basic family structure with spouse information
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

       //adds mother-child relationships to family units
       $motherAndChildren = MotherAndChild::where('family_tree_id', $familyTreeId)->get();
       foreach ($motherAndChildren as $relation) {
           $familyId = $this->removeSuffix($relation->gedcom_id);
           //creates new family unit if doesn't exist
           if (!isset($families[$familyId])) {
               $families[$familyId] = [
                   'id' => $familyId,
                   'wife_id' => $relation->mother_id,
                   'children' => []
               ];
           }
           //adds child to family unit
           $families[$familyId]['children'][$relation->child_id] = [
               'id' => $relation->child_id,
               'is_adopted' => $relation->is_adopted
           ];
       }
       
       //adds father-child relationships to family units
       $fatherAndChildren = FatherAndChild::where('family_tree_id', $familyTreeId)->get();
       foreach ($fatherAndChildren as $relation) {
           $familyId = $this->removeSuffix($relation->gedcom_id);
           //creates new family unit if doesn't exist
           if (!isset($families[$familyId])) {
               $families[$familyId] = [
                   'id' => $familyId,
                   'husband_id' => $relation->father_id,
                   'children' => []
               ];
           } elseif (!isset($families[$familyId]['husband_id'])) {
               //adds father to existing family unit
               $families[$familyId]['husband_id'] = $relation->father_id;
           }
           //adds child to family unit
           $families[$familyId]['children'][$relation->child_id] = [
               'id' => $relation->child_id,
               'is_adopted' => $relation->is_adopted
           ];
       }

       return $families;
   }

   /**
    * Finds all families where a person appears as a child.
    * 
    * @param int $personId ID of the person
    * @return array Array of family IDs
    */
   private function getFamiliesAsChild($personId)
   {
       $familyIds = [];
       
       //checks for mother-child relationship
       $motherRelation = MotherAndChild::where('child_id', $personId)->first();
       if ($motherRelation) {
           $familyIds[] = $motherRelation->gedcom_id;
       }

       //checks for father-child relationship
       $fatherRelation = FatherAndChild::where('child_id', $personId)->first();
       if ($fatherRelation) {
           $familyIds[] = $fatherRelation->gedcom_id;
       }

       return array_unique($familyIds); //returns deduplicated list
   }

    /**
    * Finds all families where a person appears as a spouse.
    * 
    * @param int $personId ID of the person
    * @return array Array of family IDs where person is either spouse
    */
   private function getFamiliesAsSpouse($personId)
   {
       //retrieves all family IDs where person is either first or second spouse
       return Spouse::where('first_spouse_id', $personId)
           ->orWhere('second_spouse_id', $personId)
           ->pluck('gedcom_id')
           ->toArray();
   }

   /**
    * Formats dates according to GEDCOM standard with qualifiers.
    * Converts database date format to GEDCOM format (DD MMM YYYY).
    * 
    * @param string $date Date to format
    * @param string $qualifier Date qualifier (ABT/BEF/AFT/EXACT)
    * @return string Formatted date string with qualifier if applicable
    */
   private function formatDate($date, $qualifier)
   {
       //converts date to GEDCOM format (e.g., "01 JAN 1900")
       $formattedDate = date('d M Y', strtotime($date));
       
       //adds qualifier prefix if date is not exact
       if ($qualifier !== 'EXACT') {
           return $qualifier . ' ' . $formattedDate;
       }
       return $formattedDate;
   }

   /**
    * Removes child number suffix from GEDCOM IDs.
    * Converts IDs like "F1-CHILD 2" to just "F1".
    * 
    * @param string $gedcomId GEDCOM ID possibly containing child suffix
    * @return string Clean GEDCOM ID
    */
   private function removeSuffix($gedcomId)
   {
       //removes "-CHILD X" suffix from family IDs
       return preg_replace('/-CHILD.*$/', '', $gedcomId);
   }
}