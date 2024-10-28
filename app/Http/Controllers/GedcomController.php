<?php
/** Controller for managing functionalities related to uploading and parsing GEDCOM files.
 * Contains methods which are processed when requested, interacting and obtaining any necessary data from Models if required
 * and returning the result to any relevant Views.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GedcomParser;
use App\Services\GedcomExporter;
use App\Models\FamilyTree;
use Gedcom\Parser;

class GedcomController extends Controller
{
    /**
     * Handles the uploading of GEDCOM files, allowing them to be validated and parsed.
     * 
     * @param Request $request - HTTP request object containing the uploaded GEDCOM file.
     * @return \Illuminate\Http\RedirectResponse - redirects back with success message if the uploading and parsing are complete without issues.
     */
    public function upload(Request $request) 
    {
        //checks and ensures the uploaded file type matches what's required (.ged files)
        $request->validate([
            'gedcom_file' => ['required', 'extensions:ged'], 
        ]);

        //obtains current user's ID
        $userId = auth()->user()->id;
            // Create a new FamilyTree if none exists after searching with corresponding user's ID for reference
            $familyTree = FamilyTree::firstOrCreate([
                'user_id' => $userId,
            ]);

        //retrieves file path (location), creates new instance of the parser and parses the file, allocating a family tree ID to it which then refers to the user
        $filePath = $request->file('gedcom_file')->getPathname();
        $parser = new GedcomParser();
        $parser->parse($filePath, $familyTree->id); 

        //redirects providing success message if completed with no issues
        return redirect()->back()->with('success', 'GEDCOM file imported successfully.');
    }

    /**
     * Displays form for user to upload GEDCOM files.
     * 
     * @return \Illuminate\View\View - Returns view for GEDCOM file upload form.
     */
    public function showUploadForm()
    {
        return view('import'); //view which contains GEDCOM file upload form
    }

    /**
     * Displays homepage for the current user.
     * 
     * @return \Illuminate\View\View - Returns the homepage view and passes the user's family tree ID if it exists (to allow the user to be able to search through their family tree).
     */
    public function index()
    {
        //obtain user ID
        $userId = auth()->user()->id;
        //find family tree corresponding to user ID
        $familyTree = FamilyTree::where('user_id', $userId)->first();
        //returns the view for the homepage and passes the user's family tree ID if it exists to allow for appropriate searching
        return view('homepage', [
            'familyTreeId' => $familyTree ? $familyTree->id : null,
        ]);
    }

    /**
     * Exports the user's family tree as a GEDCOM file.
     * 
     * @return \Symfony\Component\HttpFoundation\Response - returns the GEDCOM file as a downloadable response.
     */
    public function export()
    {
        //obtain user ID
        $userId = auth()->user()->id;
        //find family tree corresponding to user ID
        $familyTree = FamilyTree::where('user_id', $userId)->first();

        //if no family tree is found, redirect back with an error message
        if (!$familyTree) {
            return redirect()->back()->with('error', 'No family tree found for export.');
        }

        //create new instance of GedcomExporter and export the family tree
        $exporter = new GedcomExporter();
        $gedcomContent = $exporter->export($familyTree->id);

        //generate filename for the exported GEDCOM file
        $filename = 'family_tree_' . $userId . '.ged';

        //return the GEDCOM content as a downloadable file
        return response($gedcomContent)
            ->header('Content-Type', 'text/x-gedcom')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}