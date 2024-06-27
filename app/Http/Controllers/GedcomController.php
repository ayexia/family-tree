<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GedcomParser;
use Gedcom\Parser; 

class GedcomController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'gedcom_file' => 'required|file|mimes:txt,text,ged|max:10240',
        ]);

        
        if ($request->file('gedcom_file')->getClientOriginalExtension() !== 'ged') {
            return redirect()->back()->withErrors(['gedcom_file' => 'The file must be a GEDCOM file with .ged extension.']);
        }
        $filePath = $request->file('gedcom_file')->getPathname();
        $parser = new GedcomParser();
        $parser->parse($filePath); 

        return redirect()->back()->with('success', 'GEDCOM file imported successfully.');
    }
}
