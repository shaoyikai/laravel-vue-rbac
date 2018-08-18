<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // --------------------------------------------------------
    // 以下为后来添加
    // --------------------------------------------------------

    public function username()
    {
        return 'account';
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('account', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/admin');
        }
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if(Auth::attempt(['name'=>$request->account,'password'=>$request->password],$request->remember) ||
            Auth::attempt(['email'=>$request->account,'password'=>$request->password],$request->remember) ||
            Auth::attempt(['phone'=>$request->account,'password'=>$request->password],$request->remember)){

            // 如果此用户已被禁用
            $user = Auth::user();
            if($user->usable !== 1){
                $this->logout($request);

                Session::put('has_been_disabled','yes');
                Session::save();
            }else{
                return $this->sendLoginResponse($request);
            }
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|max:16',
            'account' => 'required|string|min:2|max:16',
        ],[
            'account.required' => ':attribute不能为空',
            'account.min' => ':attribute最少为:min位',
            'account.max' => ':attribute最多为:max位',

            'password.required' => ':attribute不能为空',
            'password.min' => ':attribute最少为:min位',
            'password.max' => ':attribute最多为:max位',
        ],[
            'account' => '账号',
            'password' => '密码',
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(url()->previous());
    }
}
