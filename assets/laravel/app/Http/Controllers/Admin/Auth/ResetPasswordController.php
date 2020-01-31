<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\MainController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends MainController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('admin.auth.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }


    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return trans('auth.reset_form.messages');
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param  string $response
     * @return \Illuminate\Http\Response
     */
    protected function sendResetResponse(Request $request, $response)
    {
        flash(trans('auth.reset_form.notification_reset'), 'success');
        return redirect()->route('admin.auth.login');
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request
     * @param  string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        flash(trans($response), 'warning');
        return redirect()->back()
            ->withInput($request->only('email'));
    }
}
