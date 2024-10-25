<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    //display feedback form
    public function create()
    {
        return view('feedback');
    } 
    //handle submission of feedback
    public function store(Request $request)
    {//check if user's logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to submit feedback.');
        }
    //validate feedback content
        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        try { //in database create new feedback entry
            Feedback::create([
                'user_id' => Auth::id(),
                'content' => $validatedData['content'],
            ]); //redirect home with success message

            return redirect()->route('home')->with('success', 'Thank you for your feedback!');
        } catch (\Exception $e) { //during save process handle any errors
            return back()->with('error', 'Sorry, we couldn\'t save your feedback. Please try again later.');
        }
    }
}