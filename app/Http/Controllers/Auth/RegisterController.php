<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
            'phone' => ['required', 'unique:users'],
        ]);
    }

    public function index($lang = '')
    {
        $roles = Role::whereNotIn('name', ['Super Admin', 'Admin'])->pluck('name', 'name')->all();
        if ($lang == '') {
            $lang = UtilityFacades::getValByName('default_language');
        }
        \App::setLocale($lang);
        return view('auth.register', compact('roles', 'lang'));
    }

    protected function create(array $data)
    {
        $countries = \App\Core\Data::getCountriesList();
        $country_code = $countries[$data['country_code']]['phone_code'];
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'country_code' => $country_code,
            'phone' => $data['phone'],
            'active_status' => 1,
            'email_verified_at' => (UtilityFacades::getsettings('email_verification') == '1') ? null : Carbon::now(),
            'phone_verified_at' => (UtilityFacades::getsettings('sms_verification') == '1') ? null : Carbon::now(),
            'type' => UtilityFacades::getsettings('roles'),
            'lang' => 'en',
        ]);
        $user->assignRole(UtilityFacades::getsettings('roles'));

        return $user;

    }

    public function showRegistrationForm()
    {
        $roles = Role::where('id', '!=', 1)->pluck('name', 'id');
        $roles->prepend(__('Select Role'), 0);
        return view('auth.register', compact('roles'));
    }
}
