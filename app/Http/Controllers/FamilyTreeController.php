<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\FatherAndChild;
use App\Models\MotherAndChild;
use App\Models\Spouse;

class FamilyTreeController extends Controller
{     
    /**
     * IN PROGRESS - function to display the family tree in standard pedigree tree format.
     * This involves processing all of the relationships between people (spouse, parent and child), storing them in adjacency list format.
     * This consists of each person being a node, with details such as their name and containing lists of their parents, spouse(s) and children.
     * Currently working on: 
     * - a search function which allows users to search a person in the tree (by name/ID - TBC), and being able to select a level of relationships they wish to view revolving the chosen person.
     * - being able to traverse the graph (most likely via recursion), removing duplicate info and calculating root and leaf nodes, to be structured in the appropriate manner when displayed.
     */
    public function displayFamilyTree(Request $request){
        //initialises query by ensuring results will be displayed in order of DOB
        $requestedPerson = Person::query()->orderBy('birth_date');
        //sets variables desiredUserId and desiredName to take user's input
        $desiredUserId = $request->input('desiredUserId');
        $desiredName = $request->input('desiredName');
    
        if ($desiredUserId) { //retrieves person based on ID
            $requestedPerson->where('id', $desiredUserId);
        }
        elseif ($desiredName) { //retrieves people based on name(s)
            $requestedPerson->where('name', 'like', '%' . $desiredName . '%');
        }
        
        //retrieves people fitting the query criteria
        $persons = $requestedPerson->get();

        //retrieves all mother-child, father-child and spouse relationships from respective Models (DB)
        $motherAndChildRelationships = MotherAndChild::all();
        $fatherAndChildRelationships = FatherAndChild::all();
        $marriages = Spouse::all();

        //initialises family tree structure
        $familyTree = [];

        //iterates through each person creating nodes for them
        foreach ($persons as $person){
            $familyTree[$person->id] = [
                'name' => $person->name,
                'children' => [],
                'spouses' => [],
                'parents' => [],
            ];
        }
        //iterates through spouse relationships and adds spouse data to the nodes
        foreach ($marriages as $marriage){
            $familyTree[$marriage['first_spouse_id']]['spouses'][] = $marriage['second_spouse_id'];
            $familyTree[$marriage['second_spouse_id']]['spouses'][] = $marriage['first_spouse_id'];
        }

        //iterates through parent-child relationships and adds parent and children information to nodes
        foreach ($motherAndChildRelationships as $motherAndChild){
            $familyTree[$motherAndChild['mother_id']]['children'][] = $motherAndChild['child_id'];
            $familyTree[$motherAndChild['child_id']]['parents'][] = $motherAndChild['mother_id'];
        }
        foreach ($fatherAndChildRelationships as $fatherAndChild){
            $familyTree[$fatherAndChild['father_id']]['children'][] = $fatherAndChild['child_id'];
            $familyTree[$fatherAndChild['child_id']]['parents'][] = $fatherAndChild['father_id'];
        }
        
        //prints a display of the structure for debugging purposes - will remove later
        print_r($familyTree);
        //returns the appropriate View, passing the data necessary for it
        return view('tree.index', compact('persons', 'familyTree', 'desiredName', 'desiredUserId'));
    }
}
