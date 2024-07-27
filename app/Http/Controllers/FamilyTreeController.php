<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\FatherAndChild;
use App\Models\MotherAndChild;
use App\Models\Spouse;
use App\Services\Node;

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
     * - searching should allow parents to be shown of the queried person in their tree? how to indent the current node when retrieving parents -- might have a separate search function for this
     * - searching should show all relationships, not just children and spouse -- can make multiple search functions perhaps showing different relationships (searched person as a leaf or a root), hyperlink and traverse this way building the hierarchy
     * - how to avoid skipping nodes not fully visited? as there is missing relationship information -- duplicates may be necessary, may be useful for listing different families especially as contents for PDF book with hyperlinks
     * - root nodes displaying below their children, is this fine? will formatting make this clearer? -- most likely
     * - another way of displaying relationships with multiple spouses? -- may be easier when using library for visualisation
     */
    
     public function displayFamilyTree(Request $request){
      //initialises query by ensuring results will be displayed in order of DOB, null values first
      $requestedPerson = Person::query()->orderByRaw('birth_date IS NULL, birth_date ASC'); 
  
      $desiredName = $request->input('desiredName');
      $desiredSurname = $request->input('desiredSurname');
  
      if ($desiredName) { //retrieves people based on name(s)
          $requestedPerson->where('name', 'like', '%' . $desiredName . '%');
      }
      if ($desiredSurname) { 
        $requestedPerson->where('surname', 'like', '%' . $desiredSurname . '%');
    }

      //retrieves people fitting the query criteria
      $allPersons = $requestedPerson->get();
  
      $allPersonsIds = $allPersons->pluck('id'); //extracts IDs of the queried people
  
      //retrieves all mother-child, father-child and spouse relationships of queried people from respective Models (DB)
      $motherAndChildRelationships = MotherAndChild::whereIn('mother_id', $allPersonsIds)->orWhereIn('child_id', $allPersonsIds)->get();
      $fatherAndChildRelationships = FatherAndChild::whereIn('father_id', $allPersonsIds)->orWhereIn('child_id', $allPersonsIds)->get();
      $marriages = Spouse::whereIn('first_spouse_id', $allPersonsIds)->orWhereIn('second_spouse_id', $allPersonsIds)->get();
  
      //extracts IDs of relatives and merges with IDs of queried people to form a list of all relatives
      $relativeIds = $allPersonsIds
          ->merge($motherAndChildRelationships->pluck('mother_id'))
          ->merge($motherAndChildRelationships->pluck('child_id'))
          ->merge($fatherAndChildRelationships->pluck('father_id'))
          ->merge($fatherAndChildRelationships->pluck('child_id'))
          ->merge($marriages->pluck('first_spouse_id'))
          ->merge($marriages->pluck('second_spouse_id'));
  
      //retrieves all people whose IDs are in the list of all relatives formed
      $relatives = Person::whereIn('id', $relativeIds)->get();
  
      //initialises "familyTree" array, containing all information of an individual and their relationships
      $familyTree = [];
  
      //iterates through each person creating Node objects for them, then assigned to familyTree array using their ID as key
      foreach ($relatives as $relative){
        $familyTree[$relative->id] = new Node($relative->id, $relative->name, $relative->surname, $relative->birth_date, $relative->death_date, $relative->gender, $relative->father_id, $relative->mother_id, $relative->image);
    }
  
      //iterates through spouse relationships, checks if both spouses exist in the familyTree array
      foreach ($marriages as $marriage){
          if (isset($familyTree[$marriage['first_spouse_id']]) && isset($familyTree[$marriage['second_spouse_id']])) {
              // adds spouse data to the nodes' list of spouses
              $familyTree[$marriage['first_spouse_id']]->
              addSpouse($familyTree[$marriage['second_spouse_id']], 
              $marriage->marriage_date,
              $marriage->divorce_date);

              $familyTree[$marriage['second_spouse_id']]->
              addSpouse($familyTree[$marriage['first_spouse_id']],
              $marriage->marriage_date,
              $marriage->divorce_date);
          }
      }
  
      //iterates through parent-child relationships, checks if both parent and child exist in familyTree array and adds their data to nodes' list of parents and children
      foreach ($motherAndChildRelationships as $motherAndChild){
          if (isset($familyTree[$motherAndChild['mother_id']]) && isset($familyTree[$motherAndChild['child_id']])) {
              $familyTree[$motherAndChild['mother_id']]->addChild($familyTree[$motherAndChild['child_id']]);
              $familyTree[$motherAndChild['child_id']]->addParent($familyTree[$motherAndChild['mother_id']]);
          }
      }
  
      foreach ($fatherAndChildRelationships as $fatherAndChild){
          if (isset($familyTree[$fatherAndChild['father_id']]) && isset($familyTree[$fatherAndChild['child_id']])) {
              $familyTree[$fatherAndChild['father_id']]->addChild($familyTree[$fatherAndChild['child_id']]);
              $familyTree[$fatherAndChild['child_id']]->addParent($familyTree[$fatherAndChild['father_id']]);
          }
      }        
      //initialises "trees" array, which will store the complete family tree for each person
      $trees = [];
      //initialises "visited" array which keeps track of nodes that have been visited, to avoid duplicate information
      $visited = [];
      foreach ($allPersons as $person) {
        //iterates through all people searched, checks if the node for the person has been visited
        if(!in_array($person->id, $visited)){
          //if they have not been visited, retrieves the Node and builds the family tree for the individual, tracking who has been visited to avoid duplication, then stores family tree in trees array
          $trees[] = $this->buildFamilyTree($familyTree[$person->id], $visited);
      }
    }
     //if request end route contains "api", convert the family tree data to an acceptable JSON format by iterating through all people and building nested arrays in a recursive manner, and store in jsonResponse array
      if ($request->is('api/*')) {
        $jsonResponse = [];
        foreach ($allPersons as $person) {
            $jsonResponse[] = $this->convertToJsonTree($familyTree[$person->id]);
        }
          return response()->json($jsonResponse);
      }
      //otherwise returns the appropriate View, passing the data necessary for it
      return view('tree.index', compact('allPersons', 'familyTree', 'desiredName', 'relatives', 'trees'));
  }
    
  //Converts family tree data to accepted JSON format to send as response to frontend - tree structure with spouses in one node
  private function convertToJsonTree(Node $person) {
    $personData = [
        'id' => $person->id,
        'name' => $person->name,
        'attributes' => [
            'gender' => $person->gender,
            'DOB' => $person->birth_date,
            'DOD' => $person->death_date,
            'image' => $person->image,
        ],
        'children' => [],
        'spouses' => []
    ];

    foreach ($person->getSpouses() as $spouse) {
        if ($spouse) {
            $spouseData = [
                'id' => $spouse['node']->id,
                'name' => $spouse['node']->name,
                'attributes' => [
                    'gender' => $spouse['node']->gender,
                    'DOB' => $spouse['node']->birth_date,
                    'DOD' => $spouse['node']->death_date,
                    'marriage' => $spouse['marriage_date'],
                    'divorce' => $spouse['divorce_date'],
                    'image' => $spouse['node']->image,
                ],
            ];
            $personData['spouses'][] = $spouseData;
        }
    }

    foreach ($person->getChildren() as $child) {
        $childData = $this->convertToJsonTree($child);
        if ($childData) {
            $personData['children'][] = $childData;
        }
    }

    return $personData;
}
  //TODO: method to convert to JSON for graph structure and testing graph packages

  private function buildFamilyTree(Node $person, &$visited, $prefix = ""){
      $visited[] = $person->id; //adds current person to visited array to mark them as visited
      $partners = [$person->name]; //retrieves current person's name and adds to "partners" array
      foreach ($person->getSpouses() as $spouse){ //retrieves all spouses for current person
        $partners[] = $spouse['node']->name; //adds spouse's name to partners array
        if(!in_array($spouse['node']->id, $visited)){ //if the spouse has not been visited add them to visited
        $visited[] = $spouse['node']->id;
    }
  }
      $tree = [$prefix . implode(" & ", $partners)]; //creates the tree, adding spouses from partners array and concatenating their names using "&"
      foreach ($person->getChildren() as $child) { //iterates through each child of the current person
      /*if the child has not been visited, pass the child's data to buildFamilyTree function to build their family tree recursively, adding a prefix "----" as an indentation to indicate that they are a child
        this is merged with the current person's tree in a recursive manner */   
        if(!in_array($child->id, $visited)){ 
             $tree = array_merge($tree, $this->buildFamilyTree($child, $visited, $prefix . "----"));
      }
    }
      return $tree; //returns the family tree for the current person
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
