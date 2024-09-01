<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function checkAdminAccess()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorised action.');
        }
    }

    public function dashboard()
    {
        $this->checkAdminAccess();

        $users = User::all();
        $feedback = Feedback::with('user')->get();
        return view('admin', compact('users', 'feedback'));
    }

    public function toggleAdmin(User $user)
    {
        $this->checkAdminAccess();

        $user->is_admin = !$user->is_admin;
        $user->save();
        return back()->with('success', 'User admin status updated.');
    }

    public function deleteUser(User $user)
    {
        $this->checkAdminAccess();

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}
