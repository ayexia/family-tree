<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create()
    {
        return view('feedback');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to submit feedback.');
        }

        $validatedData = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        try {
            Feedback::create([
                'user_id' => Auth::id(),
                'content' => $validatedData['content'],
            ]);

            return redirect()->route('home')->with('success', 'Thank you for your feedback!');
        } catch (\Exception $e) {
            return back()->with('error', 'Sorry, we couldn\'t save your feedback. Please try again later.');
        }
    }
}