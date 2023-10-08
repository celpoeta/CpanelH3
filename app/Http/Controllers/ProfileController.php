<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use PragmaRX\Countries\Package\Countries;
use App\Google2fa as TwoFactor;
use Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $country;

    public function __construct(Countries $country)
    {
        if (setting('email_verification')) {
            $this->middleware(['verified']);
        }
        $this->middleware(['auth', 'web']);
        $this->countries = $country->all()->sortBy('name.common')->pluck('name.common');
    }

    public function index()
    {
        if (!setting('2fa')) {
            $user = auth()->user();
            $role = $user->roles->first();
            $countries = $this->countries;
            foreach ($countries as  $countrie) {
                $allcountries[$countrie] = $countrie;
            }
            return view('profile.index', [
                'user' => $user,
                'role' => $role,
                'countries' => $allcountries,
            ]);
        }
        return $this->activeTwoFactor();
    }

    private function activeTwoFactor()
    {
        $user = Auth::user();
        $google2fa_url = "";
        $secret_key = "";
        if ($user->loginSecurity()->exists()) {
            $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
            $google2fa_url = $google2fa->getQRCodeInline(
                @setting('app_name'),
                $user->name,
                $user->loginSecurity->google2fa_secret
            );
            $secret_key = $user->loginSecurity->google2fa_secret;
        }
        $user = auth()->user();
        $role = $user->roles->first();
        $countries = $this->countries;
        foreach ($countries as  $countrie) {
            $allcountries[$countrie] = $countrie;
        }
        $data = array(
            'user' => $user,
            'secret' => $secret_key,
            'google2fa_url' => $google2fa_url,
            'countries' => $countries
        );
        return view('profile.index', [
            'user' => $user,
            'role' => $role,
            'secret' => $secret_key,
            'google2fa_url' => $google2fa_url,
            'countries' => $allcountries
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $this->validate($request, [
            'fullname' => 'required|regex:/^[A-Za-z0-9_.,() ]+$/|max:255',
            'address' => 'nullable|regex:/^[A-Za-z0-9_.,() ]+$/|string',
            'country' => 'nullable|string',
            'phone' => 'nullable|string',
        ], [
            'fullname.regex' =>  __('Invalid entry! the fullname only letter and numbers are allowed.'),
            'address.regex' =>  __('Invalid entry! the address only letter and numbers are allowed.'),
        ]);
        $user->name = $request->fullname;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->phone = $request->phone;
        $user->save();
        return redirect()->back()->with('success',  __('Account details updated successfully.'));
    }

    public function updateAvatar(Request $request, $id)
    {
        $disk = Storage::disk();
        $user = User::find($id);
        $this->validate($request, [
            'avatar' => 'required|',
        ]);
        $image = $request->avatar;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imagename = time() . '.' . 'png';
        $imagepath = "avatar/" . $imagename;
        $disk->put($imagepath, base64_decode($image));
        $user->avatar = $imagepath;

        if ($user->save()) {
            return __("Avatar updated successfully.");
        }
        return __("Avatar updated failed.");
    }

    public function updateLogin(Request $request, $id)
    {
        $user = User::find($id);
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:5|confirmed',
            'password_confirmation' => 'same:password',
        ], [
            'regex' =>  __('Invalid entry! the username only letter and numbers are allowed.'),
        ]);
        $user->email = $request->email;
        if (!is_null($request->password)) {
            $user->password = bcrypt($request->password);
        }
        $user->save();
        return redirect()->back()->with('success',  __('Login details updated successfully.'));
    }

    private function generateCode()
    {
        $google2fa = app('pragmarx.google2fa');
        $generated = $google2fa->getQRCodeInline(
            config('app.name'),
            auth()->user()->name,
            auth()->user()->google2fa->google2fa_secret
        );
        return $generated;
    }

    public function activate()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $google2fa = $google2fa->generateSecretKey();
        TwoFactor::create([
            'user_id' => $user->id,
            'google2fa_enable' => 0,
            'google2fa_secret' => $google2fa
        ]);
        return redirect()->back()->with('success',  __('2-factor activated successfully.'));
    }

    public function profileStatus()
    {
        $user = User::find(Auth::user()->id);
        $user->active_status = 0;
        $user->save();
        auth()->logout();
        return redirect()->route('home');
    }

    public function enable(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $verified = $google2fa->verifyKey($user->google2fa->google2fa_secret, $request->code);
        if ($verified) {
            $user->google2fa->google2fa_enable = 1;
            $user->google2fa->save();
            return redirect()->back()->with('success',  __('2-factor enabled successfully.'));
        }
        return redirect()->back()->with('fail',  __('Verification code is invalid.'));
    }

    public function disable(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'password' => 'required',
        ]);
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        if (Hash::check($request->password, $user->password)) {
            $verified = $google2fa->verifyKey($user->google2fa->google2fa_secret, $request->code);
            if ($verified) {
                $user->google2fa->delete();
                return redirect()->back()->with('success',  __('2-factor disabled'));
            }
            return redirect()->back()->with('fail',  __('Verification code is invalid.'));
        } else {
            return redirect()->back()->with('fail',  __('Invalid password! check password and try again.'));
        }
    }

    public function verify()
    {
        return redirect(URL()->previous());
    }

    public function instruction()
    {
        return view('google2fa.instruction');
    }
}
