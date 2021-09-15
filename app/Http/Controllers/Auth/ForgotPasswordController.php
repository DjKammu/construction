<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Notifications\SendKundennummerEmailNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['kundennummer' => 'required|exists:users,kundennummer']);
    }
    
    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('kundennummer');
    }

    protected function sendKundennummerEmail(Request $request){

        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where($request->only('email'))->first();

        Notification::send($user,new SendKundennummerEmailNotification($user));
        
        return back()->with('status-kundennummer', 'Kundennummer has been sent to your email address');
    }
    

}
