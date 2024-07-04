<?php
/** Controller for managing functionalities related to uploading and parsing GEDCOM files.
 * Contains methods which are processed when requested, interacting and obtaining any necessary data from Models if required
 * and returning the result to any relevant Views.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GedcomParser;
use Gedcom\Parser; 

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
        ]);

        //retrieves file path (location), creates new instance of the parser and parses the file
        $filePath = $request->file('gedcom_file')->getPathname();
        $parser = new GedcomParser();
        $parser->parse($filePath); 

        //redirects providing success message if completed with no issues
        return redirect()->back()->with('success', 'GEDCOM file imported successfully.');
    }
}
