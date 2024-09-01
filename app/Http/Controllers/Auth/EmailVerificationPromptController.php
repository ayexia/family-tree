<?php
/**
 * Controller handling email verification requests - provided by Laravel Breeze.
 */
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home', absolute: false)) //ensures if email is verified directed to homepage otherwise taken to verify email page
                    : view('auth.verify-email');
    }
}
