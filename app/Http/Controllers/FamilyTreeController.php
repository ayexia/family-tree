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

    /**
    * Converts family tree data to accepted JSON format to send as response to frontend (suitable for React D3 Tree) - tree structure with separate spouses.
    * This structure hierarchically goes through each individual, their spouses and children before proceeding to their siblings (or another family member).
    *
    * @param Node $person - Root node of family tree.
    * @param int|null $generations - Number of generations to include - all generations included if this is null.
    * @param int $currentGeneration - Current generation being processed - used to recursively go through each generation and prevents exceeding the specified number of generations by the user.
    * @return array $personData - Returns array to be converted to JSON structure of family tree.
    */
    private function convertToJsonTree(Node $person, $generations = null, $currentGeneration = 1) {

     //if generations is not null (max number reached) and current generation exceeds this, returns null, preventing further recursion
      if ($generations !== null && $currentGeneration > $generations) { 
          return null;
      }

      //array for family members' data in JSON format, including arrays of parents, spouses and children
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
              'parents' => array_map(function($parent) { //iterates through person's parents (if they have any), storing them into an array which retruns their ID, name and gender
                  return ['id' => $parent->id, 'name' => $parent->name, 'gender' => $parent->gender];
              }, $person->getParents() ?? []), //if getParents() returns null, provides empty array
          ],
          'children' => [],
          'spouses' => []
      ];
      //array for family members' spouse data in JSON format, including array of their parents
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
                'is_current' => $person->isCurrentSpouse($spouse) //method to check whether the spouse is current or not
              ];
              $personData['spouses'][] = $spouseData; //stores the data in person's spouses array 
          }
      }
      //iterates over each person's children and recursively calls convertToJsonTree method, making the child the current node
      foreach ($person->getChildren() as $child) {
          $childData = $this->convertToJsonTree($child, $generations, $currentGeneration + 1); //current generation increases by 1
          if ($childData) { //if the data for the child is available and not null (if the child did not exceed current number of generations)
              $personData['children'][] = $childData; //stores child's data into person's children array
          }
      }
      return $personData;
  }

  /**
    * Converts family tree data to accepted JSON format to send as response to frontend (suitable for React Flow) - graph structure with nodes and edges which iterates over every individual.
    * This structure treats each individual as an independent node connected by edges.
    *
    * @param array $familyTree - Array containing each person in family tree.
    * @param int|null $generations - Number of generations to include - all generations included if this is null.
    * @return array - Array containing nodes and edges for graph structure.
    */
    private function convertToJsonGraph(array $familyTree, $generations = null)
    {

    //arrays initialising the nodes (people) and edges (relationships) of the graph
    $nodes = [];
    $edges = [];

    //iterates through each person in family tree and checks whether they should be included based on the number of generations the user selects
    foreach ($familyTree as $id => $person) {
      if ($generations !== null && !$this->isWithinGenerations($person, $generations)) {
        //if they are not within the specified number of generations they are skipped
        continue;
    }
      //extracts the birth and death years from person's DOB and DOD (through extracting the first 4 characters)
      $birthYear = substr($person->birth_date, 0, 4);
      $deathYear = substr($person->death_date, 0, 4);
      //creates label for the node (used to display their details) which includes a concatenation of their name followed by birth and death years after a new line (e.g. John Doe \n(1950 - 2000))
      $label = $person->name . "\n(" . $birthYear . " - " . $deathYear . ")";

      //adds family member as a node in the graph
        $nodes[] = [
            'id' => (string)$id, //person's ID converted to string
            'type' => 'custom', //type of node - custom means they will be custom styled in the frontend
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
            'position' => ['x' => 0, 'y' => 0], //initial position of the node (this is set to 0 by default as the frontend will handle the positioning)
        ];
        //iterates through the spouses of each person to add edges indicating marriage
        foreach ($person->getSpouses() as $spouse) {
            $edges[] = [
                'id' => 'e' . $id . '-' . $spouse->id, //ID for edge connecting spouses
                'source' => (string)$id, //the current person is the source
                'target' => (string)$spouse->id, //the target is their spouse
                'type' => 'straight', //line type is straight (styling the edge)
                'label' => 'Spouse', //the edge will be labelled with 'Spouse'
                'is_current' => $person->isCurrentSpouse($spouse) //checks whether the spouse is current or not (ultimately determining further customisations to the edge in the frontend)
            ];
        }
        //iterates through each person's children to add edges indicating parent-child relations
        foreach ($person->getChildren() as $child) {
            $edges[] = [
                'id' => 'e' . $id . '-' . $child->id,
                'source' => (string)$id,
                'target' => (string)$child->id,
                'type' => 'smoothstep', //curved line going down from parent to child
                'label' => 'Child'
            ];
        }
    }
    //returns all family members and their relationships to be placed on graph
    return [
        'nodes' => $nodes,
        'edges' => $edges
      ];
    }

    /**
     * Checks if a person and their ancestors are within user-specified number of generations from the family tree's root, used for graph structure.
     *
     * @param Node $person - Current person to check.
     * @param int $generations - Maximum number of generations to include.
     * @param int $currentGeneration - Current generation being checked. The default for this is 1.
     * @return bool - If the person and all their ancestors are within specified number of generations, return true. Otherwise, return false.
     */
    private function isWithinGenerations(Node $person, $generations, $currentGeneration = 1)
    {
        //checks if the current generation level exceeds the specified maximum number of generations. if so, returns false
        if ($currentGeneration > $generations) {
            return false;
        }
        //iterates through each parent of current person
        foreach ($person->getParents() as $parent) {
            //recursively checks if each parent and their own parents/ancestors are within the generation constraints
            //if any parents or ancestors are not within the specified maximum returns false
            if (!$this->isWithinGenerations($parent, $generations, $currentGeneration + 1)) {
                return false;
            }
        }

        return true; //if all ancestors fit within specified generation limits, return true
    }

    /**
     * Recursively builds family tree starting from given person.
     *
     * @param Node $person - Root node - the person where to start building the tree from.
     * @param array &$visited - This references the visited array used in displayFamilyTree method which is keeping track of visited person IDs to avoid revisiting/duplication.
     * @param string $prefix - String used as a prefix to add to each person's name in the tree - the indentation indicates the generation level.
     * @return array - Array returned is comprised of the family tree structure with spouse names concatenated with "&".
     */
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
     * Displays form to edit a family member's details.
     *
     * @param int $id - ID of family member to edit.
     * @return \Illuminate\View\View - Returns edit view along with family member's data for editing.
     */
    public function edit($id)
    {
      //retrieves the family member with the given ID from DB, otherwise gives 404 error if fails
      $person = Person::findOrFail($id);
      //checks if the family member to edit is within the current user's family tree, if not aborts action with 403 error and message
      if ($person->familyTree->user_id !== Auth::id()) {
        abort(403, 'Unauthorised action.');
      }    
      //returns edit view containing family member's data for editing
     return view('edit', compact('person'));
    }

    /**
     * Updates a family member's details.
     *
     * @param \Illuminate\Http\Request $request - HTTP request containing update data.
     * @param int $id - ID of family member to update.
     * @return \Illuminate\Http\RedirectResponse - If update successful redirects back to previous page with success message.
     */
    public function updateDetails(Request $request, $id)
    {
        //validates the incoming request data - checks if the update details are within the defined constraints
        $data = $request->validate([
            'name' => 'required|string|max:255', //name is required and must be a string with max. 255 chars
            'birth_date' => 'nullable|date', //DOB is optional and must be in date format
            'death_date' => 'nullable|date', //DOD is optional and must be in date format
            // marriages.* denotes it is applied to each marriage in marriages array
            'marriages.*.id' => 'nullable|exists:spouses,id', //IDs are optional however must exist in spouses table
            'marriages.*.marriage_date' => 'nullable|date', //marriage dates are optional and must be in date format
            'marriages.*.divorce_date' => 'nullable|date', //divorce dates are optional and must be in date format
            'marriages.*.first_spouse_id' => 'nullable|exists:people,id', //first spouse ID is optional however must exist in people table
            'marriages.*.second_spouse_id' => 'nullable|exists:people,id', //second spouse ID is optional however must exist in people table
        ]);
    
        //retrieves the family member with the given ID from DB, otherwise gives 404 error if fails
        $person = Person::findOrFail($id);
        $person->name = $data['name']; //updates person's name with new name from update request data 
        $person->birth_date = !empty($data['birth_date']) ? $data['birth_date'] : null; //updates DOB with update request data, if empty sets to null
        $person->death_date = !empty($data['death_date']) ? $data['death_date'] : null; //updates DOD with update request data, if empty sets to null
        $person->save(); //saves details
    
        $marriages = $request->input('marriages', []); //retrieves marriages data from request
        foreach ($marriages as $marriage) { //iterates through each marriage
            if (isset($marriage['id'])) { //if marriage has an id
                $spouse = Spouse::find($marriage['id']); //identifies the spouse record through this ID
    
                if ($spouse) { //if the spouse exists
                    $spouse->marriage_date = !empty($marriage['marriage_date']) ? $marriage['marriage_date'] : null; //updates marriage dates with update request data, if empty sets to null
                    $spouse->divorce_date = !empty($marriage['divorce_date']) ? $marriage['divorce_date'] : null; //updates divorce dates with update request data, if empty sets to null
                    $spouse->save(); //saves spouse data
                }
            }
        }
    
        return redirect()->back()->with('success', 'Family member details updated successfully.'); //returns to previous page with success message
    }    
}