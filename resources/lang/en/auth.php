<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'form' => array(
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Repeat password',
        'registration' => array(
            'submit' => 'Create new account'
        ),
        'login' => array(
            'remember_me' => 'Remember me',
            'submit' => 'Login'
        ),
        'reset_password_request' => array(
            'submit' => 'Submit'
        ),
        'reset_password' => array(
            'submit' => 'Update password'
        )
    ),

    'error' => array(
        'registration' => array(
            'password_mismatch' => 'Repeated password must match password'
        ),
        'reset_password' => array(
            'password_mismatch' => 'Repeated password must match new password'
        ),
        'reset_password_request' => array(
            'email' => 'Could not find any user with the email address'
        )
    ),

    'message' => array(
        'login' => array(
            'fail' => 'These credentials do not match our records.'
        ),
        'reset_password_request' => array(
            'success' => 'An email has sent to you with instructions to reset your password',
            'fail' => 'Cannot send email'
        ),
        'reset_password' => array(
            'success' => 'Password reset successfully',
            'invalid_token' => 'Your reset password link either expired or invalid',
            'invalid_email' => 'Your reset password link either expired or invalid',
            'fail' => 'Password reset failed'
        )
    ),

    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
];
