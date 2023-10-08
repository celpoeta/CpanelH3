<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Zoo;
use Spatie\Permission\Models\Role;
use App\DataTables\ZoosDataTable;
use App\Facades\UtilityFacades;
use App\Mail\RegisterMail;
use App\Models\NotificationsSetting;
use App\Models\Settings;
use App\Models\SocialLogin;
use App\Notifications\RegisterMail as NotificationsRegisterMail;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Lab404\Impersonate\Impersonate;
use Lab404\Impersonate\Services\ImpersonateManager;
use Spatie\MailTemplates\Models\MailTemplate;
use App\Models\BlogCategory;

class ZooController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage-user|create-user|edit-user|delete-user', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }

    public function index(ZoosDataTable $dataTable)
    {
        return $dataTable->render('zoos.index');
    }

    public function create()
    {
        $category = BlogCategory::where('status', 1)->get();
        $categories = [];
        $categories[''] = "Seleccione una Categoria";
        foreach ($category as $value) {
            $categories[$value->id] = $value->name;
        }
        $view =  view('zoos.create', compact('categories'));
        return ['html' => $view->render()];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'common_name' => 'required',
            'description' => 'required',
            'url_image' => 'required',
            'category_id' => 'required',
            'habitat' => 'required',
        ]);
        $input = $request->all();
        $input['created_by'] = \Auth::user()->id;
        Zoo::create($input);
        // $zoo = Zoo::create([
        //     'scientific_name' => $request->title,
        //     'common_name' => $request->title,
        //     'risk' => $request->risk,
        //     'risk_description' => $request->risk_description,
        //     'distribution' => $request->distribution,
        //     'description' => $request->description,
        //     'category_id' => $request->category_id,
        //     'url_image' => $request->url_image,
        //     'habitat' => $request->habitat,
        //     'created_by' => \Auth::user()->id,
        // ]);
        return redirect()->route('zoos.index')
            ->with('success',  __('Se ha creado el registro con exito.'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $role = Role::all();
        $roles = [];
        $roles[''] = __('Select role');
        foreach ($role as $value) {
            $roles[$value->name] = $value->name;
        }
        $userRole = $user->roles->pluck('name', 'name')->all();
        $view =   view('users.edit', compact('user', 'roles', 'userRole'));
        return ['html' => $view->render()];
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
            'phone' => 'required|unique:users,phone,' . $id,
        ]);
        $countries = \App\Core\Data::getCountriesList();
        $country_code = $countries[$request->country_code]['phone_code'];
        $input = $request->all();
        if (!isset($input['password']) || $input['password'] != '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $input['country_code'] = $country_code;
        $input['phone'] = $input['phone'];
        $input['type'] = $input['roles'];
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success',  __('User updated successfully.'));
    }


    public function destroy($id)
    {
        if ($id != 1) {
            $user = User::find($id);
            $social_login = SocialLogin::where('user_id', $id)->get();
            foreach ($social_login as $value) {
                if ($user->type != 'Admin') {
                    if ($value) {
                        $value->delete();
                    }
                }
            }
            $user->delete();
            return redirect()->back()->with('success', __('User deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function zooStatus(Request $request, $id)
    {
        $zoos = Zoo::find($id);
        $input = ($request->value == "true") ? 1 : 0;
        if ($zoos) {
            $zoos->status = '0';
            var_dump($zoos->status);
            $zoos->save();
        }
        return response()->json(['is_success' => true, 'message' => __('User status changed successfully.')]);
    }
}
