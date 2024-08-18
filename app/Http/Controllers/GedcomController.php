<?php
/** Controller for managing functionalities related to uploading and parsing GEDCOM files.
 * Contains methods which are processed when requested, interacting and obtaining any necessary data from Models if required
 * and returning the result to any relevant Views.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GedcomParser;
use App\Models\FamilyTree;
use Gedcom\Parser; 
use Illuminate\Support\Facades\Auth;

class GedcomController extends Controller
{
    /**
     * Handles the uploading of GEDCOM files, allowing them to be validated and parsed.
     */
    public function upload(Request $request) 
    {
        //checks and ensures the uploaded file type matches what's required (ged alone does not work and must be accompanied by "txt/text")
        $request->validate([
            'gedcom_file' => 'required|file|mimes:txt,text,ged', 
            'family_tree_id' => 'nullable|exists:family_trees,id',
        ]);

        $familyTreeId = $request->input('family_tree_id');

        $userId = auth()->user()->id;
        if (!$familyTreeId) {
            // Create a new FamilyTree if none exists
            $familyTree = FamilyTree::create([
                'user_id' => $userId,
            ]);
    
            $familyTreeId = $familyTree->id;
        } else {
            $familyTree = FamilyTree::where('id', $familyTreeId)
                ->where('user_id', $userId)
                ->firstOrFail();
        }
        //retrieves file path (location), creates new instance of the parser and parses the file
        $filePath = $request->file('gedcom_file')->getPathname();
        $parser = new GedcomParser();
        $parser->parse($filePath, $familyTreeId); 

        //redirects providing success message if completed with no issues
        return redirect()->back()->with('success', 'GEDCOM file imported successfully.');
}

    public function showUploadForm($familyTreeId = null)
{
    $userId = auth()->user()->id;

    if ($familyTreeId) {
        $familyTree = FamilyTree::where('id', $familyTreeId)
            ->where('user_id', $userId)
            ->first();
    }
    return view('import', ['familyTreeId' => $familyTreeId]);
}

public function index()
    {
    $userId = Auth::id();
    $familyTree = FamilyTree::where('user_id', $userId)->first();
    
    $familyTreeId = null;
    
    if ($familyTree) {
        $familyTreeId = $familyTree->id;
        }     
    return view('homepage', [
        'familyTreeId' => $familyTreeId,
        ]);
    }
}
