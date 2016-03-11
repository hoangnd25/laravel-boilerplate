<?php

namespace App\Http\Controllers\Auth;

use App\Forms\Auth\Type\LoginType;
use App\Forms\Auth\Type\RegisterType;
use App\Model\User;
use Doctrine\ORM\EntityManager;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Ramsey\Uuid\Uuid;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /** @var  EntityManager */
    protected $em;

    /** @var Hasher */
    protected $hasher;

    /**
     * Create a new authentication controller instance.
     *
     * @param EntityManager $em
     * @param Hasher $hasher
     */
    public function __construct(EntityManager $em, Hasher $hasher)
    {
//        $this->middleware('guest', ['except' => 'logout']);
        $this->em = $em;
        $this->hasher = $hasher;
    }

    /**
     * @inheritdoc
     */
    public function getLogin(Request $request)
    {
        return $this->login($request);
    }

    /**
     * @inheritdoc
     */
    public function login(Request $request)
    {
        $loginForm = $this->createForm(LoginType::class, null, array(
            'action' => route('auth.login_check')
        ));
        $loginForm->handleRequest($request);

        if($request->isMethod('post')){
            if($loginForm->isValid()){
                $formData = $loginForm->getData();
                $credentials = array(
                    'email' => $formData['email'],
                    'password' => $formData['plainPassword']
                );

                // If the class is using the ThrottlesLogins trait, we can automatically throttle
                // the login attempts for this application. We'll key this by the username and
                // the IP address of the client making these requests into this application.
                $throttles = $this->isUsingThrottlesLoginsTrait();

                if ($throttles && $this->hasTooManyLoginAttempts($request)) {
                    $seconds = app(RateLimiter::class)->availableIn(
                        $this->getThrottleKey($request)
                    );
                    $request->session()->flash('status', trans('auth.message.login.throttle', ['seconds'=>$seconds]));
                    return redirect()->back();
                }

                if (Auth::guard($this->getGuard())->attempt($credentials, $formData['rememberMe'])) {
                    return $this->handleUserWasAuthenticated($request, $throttles);
                }

                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                if ($throttles) {
                    $this->incrementLoginAttempts($request);
                }

                $request->session()->flash('status', trans('auth.message.login.fail'));
            }
        }

        $view = property_exists($this, 'loginView')
            ? $this->loginView : 'auth.authenticate';

        if (view()->exists($view)) {
            return view($view, array('form' => $loginForm->createView()));
        }

        return view('auth.login', array('form' => $loginForm->createView()));
    }

    /**
     * @inheritdoc
     */
    public function getRegister(Request $request)
    {
        return $this->register($request);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $registerForm = $this->createForm(RegisterType::class, null, array(
            'action' => route('auth.register_check')
        ));
        $registerForm->handleRequest($request);

        if($request->isMethod('post')) {
            if ($registerForm->isValid()) {
                /** @var User $user */
                $user = $registerForm->getData();
                $user->setPassword($this->hasher->make($user->getPlainPassword()));
                $user->setApiToken(Uuid::uuid1());
                $this->em->persist($user);
                $this->em->flush();

                Auth::guard($this->getGuard())->login($user);
                return redirect($this->redirectPath());
            }
        }

        if (property_exists($this, 'registerView')) {
            return view($this->registerView, array('form' => $registerForm->createView()));
        }
        return view('auth.register', array('form' => $registerForm->createView()));
    }

}
