<?php

namespace App\Http\Controllers\Auth;

use App\Forms\Auth\Type\PasswordResetRequestType;
use App\Forms\Auth\Type\PasswordResetUpdateType;
use App\Http\Controllers\Controller;
use App\Model\User;
use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Form\FormError;

class PasswordController extends Controller
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
     * Where to redirect users after reset password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /** @var  EntityManager */
    protected $em;

    /** @var Hasher */
    protected $hasher;


    /**
     * Create a new password controller instance.
     *
     */
    public function __construct(EntityManager $em, Hasher $hasher)
    {
//        $this->middleware('guest');
        $this->em = $em;
        $this->hasher = $hasher;
    }

    /**
     * Display the form to request a password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getEmail(Request $request)
    {
        return $this->requestResetPassword($request);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        return $this->requestResetPassword($request);
    }

    /**
     * Handle showing form and sending email to reset password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestResetPassword(Request $request)
    {
        $requestForm = $this->createForm(PasswordResetRequestType::class, null, array(
            'action' => route('auth.reset_password.request_check')
        ));
        $requestForm->handleRequest($request);

        if($request->isMethod('post')){
            if($requestForm->isValid()){
                $email = $requestForm->get('email')->getData();
                $credentials = array('email' => $email);

                $broker = $this->getBroker();

                $response = Password::broker($broker)->sendResetLink($credentials, function (Message $message) {
                    $message->subject($this->getEmailSubject());
                });

                switch ($response) {
                    case Password::RESET_LINK_SENT:
                        $request->session()->flash('status', trans('auth.message.reset_password_request.success'));
                        break;
                    case Password::INVALID_USER:
                        $requestForm->get('email')->addError(new FormError(
                            trans('auth.error.reset_password_request.email')
                        ));
                        break;
                    default:
                        $request->session()->flash('status', trans('auth.message.reset_password_request.fail'));
                        break;
                }
                redirect()->back();
            }
        }

        if (property_exists($this, 'linkRequestView')) {
            return view($this->linkRequestView, ['form' => $requestForm->createView()]);
        }

        if (view()->exists('auth.passwords.email')) {
            return view('auth.passwords.email', ['form' => $requestForm->createView()]);
        }

        return view('auth.password', ['form' => $requestForm->createView()]);
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $email
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function getReset(Request $request, $email = null, $token = null)
    {
        return $this->proceedResetPassword($request, $email, $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        return $this->proceedResetPassword($request);
    }


    /**
     * Handle showing form and update password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $email
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function proceedResetPassword(Request $request, $email = null, $token = null)
    {
        if (is_null($token)) {
            redirect(route('auth.reset_password.request'));
        }

        $resetForm = $this->createForm(PasswordResetUpdateType::class, array(
            'email' => $email,
            'token' => $token
        ), array(
            'action' => route('auth.reset_password.update_check')
        ));
        $resetForm->handleRequest($request);

        if($request->isMethod('post')) {
            if ($resetForm->isValid()) {
                $data = $resetForm->getData();

                $credentials = array(
                    'email' => $data['email'],
                    'password_confirmation' => $data['plainPassword'],
                    'password' => $data['plainPassword'],
                    'token' => $data['token']
                );
                $broker = $this->getBroker();

                // Disable default password validator
                Password::broker($broker)->validator(function(){ return true; });

                $response = Password::broker($broker)
                    ->reset($credentials, function ($user, $password) {
                        $this->resetPassword($user, $password);
                    });

                switch ($response) {
                    case Password::PASSWORD_RESET:
                        return redirect($this->redirectPath())->with('status', trans('auth.message.reset_password.success'));
                    case Password::INVALID_USER:
                        return redirect($this->redirectPath())->with('status', trans('auth.message.reset_password.invalid_email'));
                    case Password::INVALID_TOKEN:
                        return redirect($this->redirectPath())->with('status', trans('auth.message.reset_password.invalid_token'));
                    default:
                        return redirect()->back()->with('status', trans('auth.message.reset_password.fail'));
                }
            }
        }

        if (property_exists($this, 'resetView')) {
            return view($this->resetView, ['form' => $resetForm->createView()]);
        }

        if (view()->exists('auth.passwords.reset')) {
            return view('auth.passwords.reset', ['form' => $resetForm->createView()]);
        }

        return view('auth.reset', ['form' => $resetForm->createView()]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        /** @var $user User */
        $user->setPassword($this->hasher->make($password));
        $this->em->persist($user);
        $this->em->flush();

        Auth::guard($this->getGuard())->login($user);
    }
}
