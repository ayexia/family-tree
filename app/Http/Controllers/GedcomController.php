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
     * 
     * @param Request $request - HTTP request object containing the uploaded file along with any other data.
     * @return \Illuminate\Http\RedirectResponse - Redirects back with success message if the uploading and parsing are complete without issues.
     */
    public function upload(Request $request) 
    {
        //checks and ensures the uploaded file type matches what's required (ged alone does not work and must be accompanied by "txt/text")
        $request->validate([
            'gedcom_file' => 'required|file|mimes:txt,text,ged', 
        ]);

        $userId = auth()->user()->id;
            // Create a new FamilyTree if none exists
            $familyTree = FamilyTree::firstOrCreate([
                'user_id' => $userId,
            ]);

        //retrieves file path (location), creates new instance of the parser and parses the file
        $filePath = $request->file('gedcom_file')->getPathname();
        $parser = new GedcomParser();
        $parser->parse($filePath, $familyTree->id); 

        //redirects providing success message if completed with no issues
        return redirect()->back()->with('success', 'GEDCOM file imported successfully.');
}

    public function showUploadForm()
{
    return view('import');
}

public function index()
    {
    $userId = Auth::id();
    $familyTree = FamilyTree::where('user_id', $userId)->first();
    
    return view('homepage', [
        'familyTreeId' => $familyTree ? $familyTree->id : null,
        ]);
    }
}
