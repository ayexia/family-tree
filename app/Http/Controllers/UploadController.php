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
            $path = $file->store('images', 'public');
            $fullPath = asset('storage/' . $path);
            $person->update(['image' => $fullPath]);
        }

        return response()->json(['imagePath' => $fullPath]);
    }

}
