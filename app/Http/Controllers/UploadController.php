<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'id' => 'required|exists:people,id',
        ]);

        $person = Person::find($request->input('id'));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('images', 'public'); //stores image in public directory
            $fullPath = asset('storage/' . $path); //creates whole link path for image
            $person->update(['image' => $fullPath]); //stores image path in DB for associated person
        }

        return response()->json(['imagePath' => $fullPath]); //returns image path to be displayed in React frontend
    }

}
