<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Utility;
use App\Facades\UtilityFacades;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Google2FA;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        // if (!file_exists(storage_path() . "/installed")) {
        //     header('location:install');
        //     die;
        // }
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm($lang = '')
    {
        if ($lang == '') {
            $lang = UtilityFacades::getValByName('default_language');
        }
        \App::setLocale($lang);
        return view('auth.login', compact('lang'));
    }

    public function login(Request $request)
    {
        if (UtilityFacades::getsettings('login_recaptcha_status') == 1) {
            $validator = \Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
        }
        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            if ($user->active_status == 1) {
                if ($this->attemptLogin($request)) {
                    if ($user->type == 'Admin') {
                        if ($this->attemptLogin($request)) {
                            return $this->sendLoginResponse($request);
                        } else {
                            return redirect()->back()->with('errors', __('Invalid username or password.'));
                        }
                    } else {
                        if ($this->attemptLogin($request)) {
                            if ($user->phone_verified_at == ''  && UtilityFacades::keysettings('sms_verification', 1) == '1') {
                                return redirect()->route('smsindex.noticeverification');
                            } else {
                                return $this->sendLoginResponse($request);
                            }
                        } else {
                            return redirect()->back()->with('errors', __('Invalid username or password.'));
                        }
                    }
                } else {
                    return redirect()->back()->with('errors', __('Invalid username or password.'));
                }
            } else {
                return redirect()->back()->with('errors', __('Please Contact to administrator.'));
            }
        } else {
            return redirect()->back()->with('errors', __('User not found.'));
        }
    }

    public function logout(Request $request)
    {
        if (extension_loaded('imagick') && setting('2fa')) {
            Google2FA::logout();
        }
        Auth::logout();
        return redirect('/');
    }
}
