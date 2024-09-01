<?php
/** Controller for managing functionalities related to uploading images of family members.
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{ 
    /**
    * Handles image uploading, associating images with the specified family member through the database.
    * Achieves this by obtaining the image and member's ID from the request, then storing the path to the image in the "image" field of the person in the DB.
    *
    * @param Request $request - HTTP request object containing the uploaded image and person's ID.
    * @return \Illuminate\Http\JsonResponse - Returns JSON response with image path (or error message if upload failed).
    */
    public function uploadImage(Request $request)
    {
         //checks and ensures the uploaded image type matches what's required along with confirming the person associated with the image exists in the People table
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'id' => 'required|exists:people,id',
        ]);
        //retrieves the person based on the ID given in the request
        $person = Person::findOrFail($request->input('id'));
        //ensures the person belongs to the family tree of the user, otherwise gives error message
        if ($person->familyTree->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorised access to person'], 403);
        }
        //if the request contains image file
        if ($request->hasFile('image')) {
            $file = $request->file('image'); //retrieves uploaded image from request
            $path = $file->store('images', 'public'); //stores image in public directory
            $fullPath = asset('storage/' . $path); //creates whole link path for image
            $person->update(['image' => $fullPath]); //stores image path in DB for associated person
        }

        return response()->json(['imagePath' => $fullPath]); //returns image path to be displayed in React frontend
    }

}
