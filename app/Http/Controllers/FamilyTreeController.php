<?php
/**
* Controller method for handling functionalities dealing with constructing the family tree after fetching the queried people and assorting them.
* These are then processed as specific data structures needed to be passed over to the frontend to be visualised (tree/graph).
* Additionally handles editing people details and provides corresponding view. 
*/
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\FatherAndChild;
use App\Models\MotherAndChild;
use App\Models\Spouse;
use App\Models\FamilyTree;
use App\Services\Node;
use Illuminate\Support\Facades\Auth;

class FamilyTreeController extends Controller
{
    /**
    * Displays family tree based on user input, involving searching and filtering based on name, surname and generations.
    * Additionally manages construction of family tree and conversion of data structures to JSON format for API responses.
    *
    * @param \Illuminate\Http\Request $request - HTTP request object containing user's input parameters.
    * @return \Illuminate\Http\Response|\Illuminate\View\View - Returns appropriate JSON response for API requests or alternatively a view for web requests.
    */

     public function displayFamilyTree(Request $request){

      $userId = auth()->id(); //obtains current user's ID

      $familyTreeId = FamilyTree::where('user_id', $userId)->value('id'); //finds family tree containing retrieved user's ID and fetches its ID
     
      //initialises query to retrieve relevant people of family tree in order of root first (no parents) and order of birth date
      $requestedPerson = Person::query() //queries Person model (to obtain from people table)
      ->join('family_trees', 'people.family_tree_id', '=', 'family_trees.id') // join family_trees table with people on family tree id ensuring only people of specific family tree are obtained
      ->leftJoin('father_and_children as fac', 'people.id', '=', 'fac.child_id') // left join father_and_children, ensuring all attributes of people table are included. if any person is listed as a child in father_and_children the relationship will be included as well
      ->leftJoin('mother_and_children as mac', 'people.id', '=', 'mac.child_id') // left join mother_and_children, ensuring all attributes of people table are included. if any person is listed as a child in mother_and_children the relationship will be included as well
      ->where('family_trees.id', $familyTreeId) // filter by family_tree_id
      ->where('family_trees.user_id', $userId) // filter by user_id of the corresponding family tree
      ->select('people.*') //select all columns from people table 
      ->selectRaw('CASE WHEN fac.father_id IS NULL AND mac.mother_id IS NULL THEN 0 ELSE 1 END as has_parents') //additional column added to check if a person has parents
      ->orderByRaw('has_parents ASC') //orders so people with null values for parents appear first (insinuating they are the roots)
      ->orderByRaw('CASE WHEN people.birth_date IS NULL THEN 1 ELSE 0 END') //order by birth date (nulls are provided first)
      ->orderBy('people.birth_date', 'ASC'); //order by birth date (previous birthdates appear prior)
    
      //takes user input for name(s) and/or surname
      $desiredName = $request->input('desiredName');
      $desiredSurname = $request->input('desiredSurname');
  
      if ($desiredName) { //retrieves people based on name(s)
          $requestedPerson->where('name', 'like', '%' . $desiredName . '%');
      }
      if ($desiredSurname) { //retrieves people based on surname
        $requestedPerson->where('surname', 'like', '%' . $desiredSurname . '%');
    }
      //retrieves number of generations for display based on user input (null otherwise)  
      $generations = $request->input('generations', null);
     
      $allPersons = $requestedPerson->get();  //retrieves all people fitting the query criteria
  
      $allPersonsIds = $allPersons->pluck('id'); //extracts IDs of the queried people
  
      //retrieves all mother-child, father-child and spouse relationships from respective Models (DB)
      $marriages = Spouse::join('family_trees', 'spouses.family_tree_id', '=', 'family_trees.id') //joins spouses with family_tree on family_tree_id
      ->where('family_trees.user_id', $userId) //filter by user ID (corresponding to family tree)
      ->where('spouses.family_tree_id', $familyTreeId) //filter by family tree ID 
      ->get();

    $motherAndChildRelationships = MotherAndChild::where('family_tree_id', $familyTreeId)->get();
    $fatherAndChildRelationships = FatherAndChild::where('family_tree_id', $familyTreeId)->get();
    
      //extracts IDs of relatives and merges with IDs of queried people to form a list of all relatives
      $relativeIds = $allPersonsIds //obtains all queried people's IDs
          ->merge($motherAndChildRelationships->pluck('mother_id')) //adds mother IDs
          ->merge($motherAndChildRelationships->pluck('child_id')) //adds child IDs from mother-child relationships
          ->merge($fatherAndChildRelationships->pluck('father_id')) //adds father IDs
          ->merge($fatherAndChildRelationships->pluck('child_id')) //adds child IDs from father-child relationships
          ->merge($marriages->pluck('first_spouse_id')) //adds first spouse IDs
          ->merge($marriages->pluck('second_spouse_id')) //adds second spouse IDs
          ->unique(); //prevents duplicates
  
      //retrieves all people whose IDs are in the list of all relatives formed
      $relatives = Person::whereIn('id', $relativeIds) //obtain all people whose IDs are within relativeIds
      ->where('family_tree_id', $familyTreeId) //filter to ensure they belong to correct family tree
      ->get();
  
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
              $familyTree[$marriage['first_spouse_id']]->addSpouse($familyTree[$marriage['second_spouse_id']]);
              $familyTree[$marriage['first_spouse_id']]->setMarriageDates($marriage['marriage_date'], $marriage['divorce_date']);

              $familyTree[$marriage['second_spouse_id']]->addSpouse($familyTree[$marriage['first_spouse_id']]);
              $familyTree[$marriage['second_spouse_id']]->setMarriageDates($marriage['marriage_date'], $marriage['divorce_date']);
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
     //if request end route contains corresponding links, convert the family tree data to an acceptable JSON format by iterating through all people and building nested arrays in a recursive manner, and store in jsonResponse array
      if ($request->is('api/family-tree-json')) {
        $jsonResponse = []; //initialises JSON response
        foreach ($allPersons as $person) { //iterates through all people and converts to tree structure via convertToJsonTree method
            $jsonResponse[] = $this->convertToJsonTree($familyTree[$person->id], $generations);
        }
        //returns JSON response with family tree data
          return response()->json($jsonResponse);
      }
      if ($request->is('api/family-graph-json')) { //this version iterates through all people and converts to graph structure instead via convertToJsonGraph method
        $graphData = $this->convertToJsonGraph($familyTree, $generations);
       //returns JSON response with graph data
        return response()->json($graphData);
       }
      //otherwise returns the appropriate View, passing the data necessary for it
      return view('tree.index', compact('allPersons', 'familyTree', 'desiredName', 'relatives', 'trees', 'familyTreeId'));
  }
    
  //Converts family tree data to accepted JSON format to send as response to frontend - tree structure with separate spouses
    private function convertToJsonTree(Node $person, $generations = null, $currentGeneration = 1) {

      if ($generations !== null && $currentGeneration > $generations) {
          return null;
      }

      $personData = [
          'id' => $person->id,
          'name' => $person->name,
          'attributes' => [
              'gender' => $person->gender,
              'DOB' => $person->birth_date,
              'DOD' => $person->death_date,
              'marriage_dates' => $person->marriage_dates,
              'divorce_dates' => $person->divorce_dates,
              'image' => $person->image,
              'parents' => array_map(function($parent) {
                  return ['id' => $parent->id, 'name' => $parent->name, 'gender' => $parent->gender];
              }, $person->getParents() ?? []),
          ],
          'children' => [],
          'spouses' => []
      ];

      foreach ($person->getSpouses() as $spouse) {
          if ($spouse) {
              $spouseData = [
                  'id' => $spouse->id,
                  'name' => $spouse->name,
                  'attributes' => [
                      'gender' => $spouse->gender,
                      'DOB' => $spouse->birth_date,
                      'DOD' => $spouse->death_date,
                      'marriage_dates' => $spouse->marriage_dates,
                      'divorce_dates' => $spouse->divorce_dates,
                      'image' => $spouse->image,
                      'parents' => array_map(function($parent) {
                          return ['id' => $parent->id, 'name' => $parent->name, 'gender' => $parent->gender];
                      }, $spouse->getParents() ?? []),
                  ],
                'is_current' => $person->isCurrentSpouse($spouse)
              ];
              $personData['spouses'][] = $spouseData;
          }
      }
      foreach ($person->getChildren() as $child) {
          $childData = $this->convertToJsonTree($child, $generations, $currentGeneration + 1);
          if ($childData) {
              $personData['children'][] = $childData;
          }
      }
      return $personData;
  }

  private function convertToJsonGraph(array $familyTree, $generations = null)
{
    $nodes = [];
    $edges = [];

    foreach ($familyTree as $id => $person) {
      if ($generations !== null && !$this->isWithinGenerations($person, $generations)) {
        continue;
    }

      $birthYear = substr($person->birth_date, 0, 4);
      $deathYear = substr($person->death_date, 0, 4);
      $label = $person->name . "\n(" . $birthYear . " - " . $deathYear . ")";

        $nodes[] = [
            'id' => (string)$id,
            'type' => 'custom',
            'data' => [
                'label' => $label,
                'name' => $person->name,
                'gender' => $person->gender,
                'birth_date' => $person->birth_date,
                'death_date' => $person->death_date,
                'image' => $person->image,
                'marriage_dates' => $person->marriage_dates,
                'divorce_dates' => $person->divorce_dates,
                'parents' => array_map(function($parent) {
                 return ['id' => $parent->id, 'name' => $parent->name, 'gender' => $parent->gender];
              }, $person->getParents() ?? []),
            ],
            'position' => ['x' => 0, 'y' => 0],
        ];

        foreach ($person->getSpouses() as $spouse) {
            $edges[] = [
                'id' => 'e' . $id . '-' . $spouse->id,
                'source' => (string)$id,
                'target' => (string)$spouse->id,
                'type' => 'straight',
                'label' => 'Spouse',
                'is_current' => $person->isCurrentSpouse($spouse)
            ];
        }

        foreach ($person->getChildren() as $child) {
            $edges[] = [
                'id' => 'e' . $id . '-' . $child->id,
                'source' => (string)$id,
                'target' => (string)$child->id,
                'type' => 'smoothstep',
                'label' => 'Child'
            ];
        }
    }
    return [
        'nodes' => $nodes,
        'edges' => $edges
    ];
}

    private function isWithinGenerations(Node $person, $generations, $currentGeneration = 1)
    {
        if ($currentGeneration > $generations) {
            return false;
        }

        foreach ($person->getParents() as $parent) {
            if (!$this->isWithinGenerations($parent, $generations, $currentGeneration + 1)) {
                return false;
            }
        }

        return true;
    }

  private function buildFamilyTree(Node $person, &$visited, $prefix = ""){
      $visited[] = $person->id; //adds current person to visited array to mark them as visited
      $partners = [$person->name]; //retrieves current person's name and adds to "partners" array
      foreach ($person->getSpouses() as $spouse){ //retrieves all spouses for current person
        $partners[] = $spouse->name; //adds spouse's name to partners array
        if(!in_array($spouse->id, $visited)){ //if the spouse has not been visited add them to visited
        $visited[] = $spouse->id;
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
     * Updates family member details.
     */

    public function edit($id)
    {
      $person = Person::findOrFail($id);
      if ($person->familyTree->user_id !== Auth::id()) {
        abort(403, 'Unauthorised action.');
      }    
     return view('edit', compact('person'));
    }

    public function updateDetails(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'death_date' => 'nullable|date',
            'marriages.*.id' => 'nullable|exists:spouses,id',
            'marriages.*.marriage_date' => 'nullable|date',
            'marriages.*.divorce_date' => 'nullable|date',
            'marriages.*.first_spouse_id' => 'nullable|exists:people,id',
            'marriages.*.second_spouse_id' => 'nullable|exists:people,id',
        ]);
    
        $person = Person::findOrFail($id);
        $person->name = $data['name'];
        $person->birth_date = !empty($data['birth_date']) ? $data['birth_date'] : null;
        $person->death_date = !empty($data['death_date']) ? $data['death_date'] : null;
        $person->save();
    
        $marriages = $request->input('marriages', []);
        foreach ($marriages as $marriage) {
            if (isset($marriage['id'])) {
                $spouse = Spouse::find($marriage['id']);
    
                if ($spouse) {
                    $spouse->marriage_date = !empty($marriage['marriage_date']) ? $marriage['marriage_date'] : null;
                    $spouse->divorce_date = !empty($marriage['divorce_date']) ? $marriage['divorce_date'] : null;
                    $spouse->save();
                }
            }
        }
    
        return redirect()->back()->with('success', 'Family member details updated successfully.');
    }    
}