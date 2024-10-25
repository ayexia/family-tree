<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
 { 
    //check if logged in user has admin access
    private function checkAdminAccess()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorised action.');
        }
    } 
    
    //display admin dashboard with users and feedback
     public function dashboard()
    {
        $this->checkAdminAccess(); //checks for admin access

        $users = User::all(); //retrieve all users
        $feedback = Feedback::with('user')->get(); //get all feedback with user info
        return view('admin', compact('users', 'feedback')); //return admin view with data
    } 
   
    public function toggleAdmin(User $user) //toggle admin status of user
    {
        $this->checkAdminAccess(); //checks for admin access

        $user->is_admin = !$user->is_admin; //toggle admin status
        $user->save(); //save updated user info
        return back()->with('success', 'User admin status updated.'); //return back with success message
    }

    public function deleteUser(User $user)
    {
        $this->checkAdminAccess(); //checks for admin access

        $user->delete(); //deletes user account
        return back()->with('success', 'User deleted successfully.'); //return back with success message
    }
}