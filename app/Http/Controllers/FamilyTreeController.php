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
     * Stores information of newly added family member - IN PROGRESS
     * TODO: further checks, fixing DB variables and GEDCOM IDs, complete form and allocate arbitrary GEDCOM ID if unavailable
     * CONT: ensure user can select from dropdown list (or manually write if possible but will require creating new individuals if they do not exist)
     * CONT: once DB expands to be more inclusive will require additional checks
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'gedcom_id' => 'nullable',
            'birth_date' => 'nullable|date',
            'death_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'mother_id' => 'nullable|numeric',
            'father_id' => 'nullable|numeric',
            'spouse_id' => 'nullable|numeric',
            'marriage_date' => 'nullable|date',
            'divorce_date' => 'nullable|date',
            'child_id' => 'nullable|numeric',
            'child_number' => 'nullable|numeric',
          ]);
          
          $person = Person::create([
            'name' => $data['name'],
            'gedcom_id' => $data['gedcom_id'],
            'birth_date' => $data['birth_date'],
            'death_date' => $data['death_date'],
            'gender' => $data['gender'],
          ]);

          if($data['mother_id']) {
            $lastChild = MotherAndChild::where('mother_id', $data['mother_id'])->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;

            MotherAndChild::create([
                'mother_id' => $data['mother_id'],
                'child_id' => $person->id,
                'child_number' => $childNumber,
            ]);
          }

          if($data['father_id']) {
            $lastChild = FatherAndChild::where('father_id', $data['father_id'])->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;
            FatherAndChild::create([
                'father_id' => $data['father_id'],
                'child_id' => $person->id,
                'child_number' => $childNumber,
            ]);
          }

          if($data['gender'] === 'M' && $data['spouse_id']){
            Spouse::create([
                'second_spouse_id' => $person->id,
                'first_spouse_id' => $data['spouse_id'],
                'marriage_date' => $data['marriage_date'],
                'divorce_date' => $data['marriage_date'],
            ]);
          }

          if($data['gender'] === 'F' && $data['spouse_id']){
            Spouse::create([
                'second_spouse_id' => $data['spouse_id'],
                'first_spouse_id' => $person->id,
                'marriage_date' => $data['marriage_date'],
                'divorce_date' => $data['marriage_date'],
            ]);
          }

          if($data['father_id'] && $data['mother_id']){
            Spouse::create([
                'second_spouse_id' => $data['father_id'],
                'first_spouse_id' => $data['mother_id'],
                'marriage_date' => $data['marriage_date'],
                'divorce_date' => $data['marriage_date'],
            ]);
          }

          if($data['gender'] === 'F' && $data['child_id']){
            $lastChild = MotherAndChild::where('mother_id', $person->id)->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;
            MotherAndChild::create([
                'mother_id' => $person->id,
                'child_id' => $data['child_id'],
                'child_number' => $childNumber,
            ]);
          }

          
          if($data['gender'] === 'M' && $data['child_id']){
            $lastChild = FatherAndChild::where('father_id', $person->id)->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;
            FatherAndChild::create([
                'father_id' => $person->id,
                'child_id' => $data['child_id'],
                'child_number' => $childNumber,
            ]);
          }

          return redirect()->route('tree.index')
            ->with('success', 'Family member created successfully.');
    }



    /**
     * IN PROGRESS - function to display the family tree in standard pedigree tree format.
     * This involves processing all of the relationships between people (spouse, parent and child), storing them in adjacency list format.
     * This consists of each person being a node, with details such as their name and containing lists of their parents, spouse(s) and children.
     * Currently working on: 
     * - a search function which allows users to search a person in the tree (by name/ID - TBC), and being able to select a level of relationships they wish to view revolving the chosen person.
     * - being able to traverse the graph (most likely via recursion), removing duplicate info and calculating root and leaf nodes, to be structured in the appropriate manner when displayed.
     * - refactoring to use more OOP principles (neater, more organised and understandable code)
     */
    public function displayFamilyTree(Request $request){
        //initialises query by ensuring results will be displayed in order of DOB
        $requestedPerson = Person::query()->orderBy('birth_date');
        $desiredName = $request->input('desiredName');

        //CURRENT PROBLEMS: searches retrieved but all information related to matching names are "Unknown"/blank in tree display, remove timestamp and format date

        if ($desiredName) { //retrieves people based on name(s)
            $requestedPerson->where('name', 'like', '%' . $desiredName . '%');
        }
        
        //retrieves people fitting the query criteria
        $allPersons = $requestedPerson->get();

        $allPersonsIds = $allPersons->pluck('id');

        //retrieves all mother-child, father-child and spouse relationships from respective Models (DB)
        
        $motherAndChildRelationships = MotherAndChild::whereIn('mother_id', $allPersonsIds)->orWhereIn('child_id', $allPersonsIds)->get();
        $fatherAndChildRelationships = FatherAndChild::whereIn('father_id', $allPersonsIds)->orWhereIn('child_id', $allPersonsIds)->get();
        $marriages = Spouse::whereIn('first_spouse_id', $allPersonsIds)->orWhereIn('second_spouse_id', $allPersonsIds)->get();

        $relativeIds = $allPersonsIds
        ->merge($motherAndChildRelationships->pluck('mother_id'))
        ->merge($motherAndChildRelationships->pluck('child_id'))
        ->merge($fatherAndChildRelationships->pluck('father_id'))
        ->merge($fatherAndChildRelationships->pluck('child_id'))
        ->merge($marriages->pluck('first_spouse_id'))
        ->merge($marriages->pluck('second_spouse_id'))
        ->unique();

        $relatives = Person::whereIn('id', $relativeIds)->get();

        //initialises family tree structure
        $familyTree = [];

        //iterates through each person creating nodes for them, using their ID as key and value as an array of details
        foreach ($allPersons as $person){
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
        
        $trees = [];
        foreach ($allPersons as $person) {
            $trees[] = $this->buildFamilyTree($person->id, $familyTree, $relatives);
        }

        //prints a display of the structure for debugging purposes - will remove later
        // print_r($familyTree);
        //returns the appropriate View, passing the data necessary for it
        return view('tree.index', compact('allPersons', 'familyTree', 'desiredName', 'relatives', 'trees'));
    }

    private function buildFamilyTree($id, $familyTree, $relatives, $prefix = ""){
        if (!isset($familyTree[$id])) {
            return [];
        }
    
        $root = $familyTree[$id];
  
        $rootName = $relatives->where('id', $id)->first()->name ?? 'Unknown Person';

        $parents = [$rootName];

        if (isset($root['spouses'])) {
        foreach ($root['spouses'] as $spouse){
          $spouseName = $relatives->where('id', $spouse)->first()->name ?? 'Unknown Spouse';
          $parents[] = $spouseName;
      }
  }
        $tree = [$prefix . implode(" & ", $parents)];
        if (isset($root['children'])) {
        foreach ($root['children'] as $child) {
            $tree = array_merge($tree, $this->buildFamilyTree($child, $familyTree, $relatives, $prefix . "----"));
        }
    }
        return $tree;
    }

    /**
     * Updates family member and all associated relationships - IN PROGRESS
     * TODO: fix depending on other methods, checks, add edit button to frontend page
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'gedcom_id' => 'nullable',
            'birth_date' => 'nullable|date',
            'death_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'mother_id' => 'nullable|numeric',
            'father_id' => 'nullable|numeric',
            'spouse_id' => 'nullable|numeric',
            'marriage_date' => 'nullable|date',
            'divorce_date' => 'nullable|date',
            'child_id' => 'nullable|numeric',
            'child_number' => 'nullable|numeric',
          ]);
          
          $person = Person::findOrFail($id);
          $person->update($data);
          $mother_id = MotherAndChild::where('child_id', $id)->value('mother_id');
          $father_id = FatherAndChild::where('child_id', $id)->value('father_id');
          
          if($data['mother_id']) {
            $lastChild = MotherAndChild::where('mother_id', $data['mother_id'])->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;

            MotherAndChild::updateOrCreate([
                'mother_id' => $data['mother_id'],
                'child_id' => $person->id,
                'child_number' => $childNumber,
            ]);
          } else {
            MotherAndChild::where('child_id', $person->id)->delete();
        }

          if($data['father_id']) {
            $lastChild = FatherAndChild::where('father_id', $data['father_id'])->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;
            FatherAndChild::updateOrCreate([
                'father_id' => $data['father_id'],
                'child_id' => $person->id,
                'child_number' => $childNumber,
            ]);
        } else {
            FatherAndChild::where('child_id', $person->id)->delete();
        }

          if($data['gender'] === 'M' && $data['spouse_id']){
            Spouse::updateOrCreate([
                'second_spouse_id' => $person->id,
                'first_spouse_id' => $data['spouse_id'],
                'marriage_date' => $data['marriage_date'],
                'divorce_date' => $data['marriage_date'],
            ]);
        } else {
            Spouse::where('second_spouse_id', $person->id)->delete();
        }

          if($data['gender'] === 'F' && $data['spouse_id']){
            Spouse::updateOrCreate([
                'second_spouse_id' => $data['spouse_id'],
                'first_spouse_id' => $person->id,
                'marriage_date' => $data['marriage_date'],
                'divorce_date' => $data['marriage_date'],
            ]);
        } else {
            Spouse::where('first_spouse_id', $person->id)->delete();
        }

          if($data['father_id'] && $data['mother_id']){
            Spouse::updateOrCreate([
                'second_spouse_id' => $data['father_id'],
                'first_spouse_id' => $data['mother_id'],
                'marriage_date' => $data['marriage_date'],
                'divorce_date' => $data['marriage_date'],
            ]);
          } else {
            Spouse::where('first_spouse_id', $mother_id)->orWhere('second_spouse_id', $father_id)->delete();}

          if($data['gender'] === 'F' && $data['child_id']){
            $lastChild = MotherAndChild::where('mother_id', $person->id)->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;
            MotherAndChild::updateOrCreate([
                'mother_id' => $person->id,
                'child_id' => $data['child_id'],
                'child_number' => $childNumber,
            ]);
          } else {
            MotherAndChild::where('mother_id', $person->id)->delete();
        }

          
          if($data['gender'] === 'M' && $data['child_id']){
            $lastChild = FatherAndChild::where('father_id', $person->id)->max('child_number');
            $childNumber = $lastChild ? $lastChild + 1 : 1;
            FatherAndChild::updateOrCreate([
                'father_id' => $person->id,
                'child_id' => $data['child_id'],
                'child_number' => $childNumber,
            ]);
          } else {
            FatherAndChild::where('father_id', $person->id)->delete();
        }
          return redirect()->route('tree.index')
            ->with('success', 'Family member updated successfully.');

    }

    /**
     * Deletes family member from family tree and all associated relationships - IN PROGRESS
     * TODO: fix depending on other methods, checks, add delete button to frontend page
     */
    public function destroy($id)
    {
        $person = Person::findOrFail($id);
        $mother_id = MotherAndChild::where('child_id', $id)->value('mother_id');
        $father_id = FatherAndChild::where('child_id', $id)->value('father_id');
    
        MotherAndChild::where('child_id', $id)->delete();
        MotherAndChild::where('mother_id', $id)->delete();
    
        FatherAndChild::where('child_id', $id)->delete();
        FatherAndChild::where('father_id', $id)->delete();
    
        Spouse::where('first_spouse_id', $id)->orWhere('second_spouse_id', $id)->delete();

        Spouse::where('first_spouse_id', $mother_id)->orWhere('second_spouse_id', $father_id)->delete();
    
        $person->delete();
    
        return redirect()->route('tree.index')
            ->with('success', 'Family member deleted successfully.');
    }

    public function show($id){
        //TODO: code for showing individuals, page to display person, modifications if necessary
        $person = Person::findOrFail($id);

        return view('person.show', compact('person'));
    }
}
