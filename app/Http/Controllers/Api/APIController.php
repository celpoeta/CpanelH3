<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\Blog;
use App\Models\Zoo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Facades\Utility;
use DB;
use App\Facades\UtilityFacades;
use App\Mail\RegisterMail;
use App\Models\NotificationsSetting;
use App\Models\Settings;
use App\Models\SocialLogin;
use App\Notifications\RegisterMail as NotificationsRegisterMail;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Lab404\Impersonate\Impersonate;
use Lab404\Impersonate\Services\ImpersonateManager;
use Spatie\MailTemplates\Models\MailTemplate;
use Illuminate\Support\Facades\Storage;

class APIController extends Controller
{

    public function allCategories() {
        $coordenadas = BlogCategory::where('status', 1)->get(['id', 'name', 'icon', 'status', 'created_at']);
        $Categories = [];

        foreach ($coordenadas as $coordenada) {
            $Categories[] = [
                'id' => $coordenada->id,
                'name' => $coordenada->name,
                'icon' => Storage::url($coordenada->icon),
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y')
            ];
        }
        return response()->json($Categories);
    }


    public function allNews() {
        $coordenadas = Blog::get(['id', 'title', 'images', 'short_description', 'description','category_id','slug','created_at']);
        $BlogArray = [];

        foreach ($coordenadas as $coordenada) {
            $BlogArray[] = [
                'id' => $coordenada->id,
                'title' => $coordenada->title,
                'images' => Storage::url($coordenada->images),
                'description' => $coordenada->description,
                'category_id' => $coordenada->category_id,
                'slug' => $coordenada->slug,
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y')
            ];
        }

        return response()->json($BlogArray);
    }

    public function allZoos() {
        $coordenadas  = Zoo::where('status', 1)->get(['id', 'scientific_name', 'common_name', 'description', 'risk',
        'risk_description','distribution','habitat','url_image','url_video','latitud','longitud','category_id','slug',
        'status','created_at']);

        $coordenadasArray = [];

        foreach ($coordenadas as $coordenada) {
            $coordenadasArray[] = [
                'id' => $coordenada->id,
                'scientific_name' => $coordenada->scientific_name,
                'common_name' => $coordenada->common_name,
                'description' => $coordenada->description,
                'risk' => $coordenada->risk,
                'risk_description' => $coordenada->risk_description,
                'distribution' => $coordenada->distribution,
                'habitat' => $coordenada->habitat,
                'url_image' => Storage::url($coordenada->url_image),
                'url_video' => $coordenada->url_video,
                'location' => $coordenada->latitud.', '.$coordenada->longitud,
                'category_id' => $coordenada->category_id,
                'slug' => $coordenada->slug,
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y'),
            ];
        }

        return response()->json($coordenadasArray);

    }

    public function Map() {
        $coordenadas  = Zoo::where('status', 1)->get(['id', 'icon', 'common_name', 'latitud', 'longitud']);

        $coordenadasArray = [];

        foreach ($coordenadas as $coordenada) {
            $coordenadasArray[] = [
                'id' => $coordenada->id,
                'icon' => Storage::url($coordenada->icon),
                'name' => $coordenada->common_name,
                'location' => $coordenada->latitud.', '.$coordenada->longitud
            ];
        }

        return response()->json($coordenadasArray);
    }

    public function ShowZoo($id) {
        $coordenadas  = Zoo::where('id', $id)->get(['id', 'scientific_name', 'common_name', 'description', 'risk',
        'risk_description','distribution','habitat','url_image','url_video','latitud','longitud','category_id','slug',
        'status','created_at']);

        $coordenadasArray = [];

        foreach ($coordenadas as $coordenada) {
            $coordenadasArray[] = [
                'id' => $coordenada->id,
                'scientific_name' => $coordenada->scientific_name,
                'common_name' => $coordenada->common_name,
                'description' => $coordenada->description,
                'risk' => $coordenada->risk,
                'risk_description' => $coordenada->risk_description,
                'distribution' => $coordenada->distribution,
                'habitat' => $coordenada->habitat,
                'url_image' => Storage::url($coordenada->url_image),
                'url_video' => $coordenada->url_video,
                'location' => $coordenada->latitud.', '.$coordenada->longitud,
                'category_id' => $coordenada->category_id,
                'slug' => $coordenada->slug,
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y'),
            ];
        }

        return response()->json($coordenadasArray);

    }


    public function filterNews($id) {
        $coordenadas = Blog::where('category_id', $id)->get(['id', 'title', 'images', 'short_description', 'description','category_id','slug','created_at']);
        $BlogArray = [];

        foreach ($coordenadas as $coordenada) {
            $BlogArray[] = [
                'id' => $coordenada->id,
                'title' => $coordenada->title,
                'images' => Storage::url($coordenada->images),
                'description' => $coordenada->description,
                'category_id' => $coordenada->category_id,
                'slug' => $coordenada->slug,
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y')
            ];
        }

        return response()->json($BlogArray);

    }


    public function filterZoo($id) {
        $coordenadas  = Zoo::where('category_id', $id)->get(['id', 'scientific_name', 'common_name', 'description', 'risk',
        'risk_description','distribution','habitat','url_image','url_video','latitud','longitud','category_id','slug',
        'status','created_at']);

        $coordenadasArray = [];

        foreach ($coordenadas as $coordenada) {
            $coordenadasArray[] = [
                'id' => $coordenada->id,
                'scientific_name' => $coordenada->scientific_name,
                'common_name' => $coordenada->common_name,
                'description' => $coordenada->description,
                'risk' => $coordenada->risk,
                'risk_description' => $coordenada->risk_description,
                'distribution' => $coordenada->distribution,
                'habitat' => $coordenada->habitat,
                'url_image' => Storage::url($coordenada->url_image),
                'url_video' => $coordenada->url_video,
                'location' => $coordenada->latitud.', '.$coordenada->longitud,
                'category_id' => $coordenada->category_id,
                'slug' => $coordenada->slug,
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y'),
            ];
        }

        return response()->json($coordenadasArray);
    }



    public function addUser(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response(["response"=> $messages->first() ],Response::HTTP_UNAUTHORIZED);
        }

        $input = $request->all();
        $input['type'] =  "User";
        $input['password'] = Hash::make($input['password']);
        $input['lang'] = "es";
        $input['active_status'] = '1';
        $input['country_code'] = '';
        $input['created_by'] = 1;
        $input['email_verified_at'] = (UtilityFacades::getsettings('email_verification') == '1') ? null : Carbon::now()->toDateTimeString();
        $input['phone_verified_at'] = (UtilityFacades::getsettings('sms_verification') == '1') ? null : Carbon::now()->toDateTimeString();
        $user = User::create($input);
        $user->assignRole('User');

        return response(["response"=> 'Usuario registrado con exito.' ],Response::HTTP_UNAUTHORIZED);

    }

    public function updateUser(Request $request)
    {
        $input = $request->all();
        $id= $input['id'];

        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response(["response"=> $messages->first() ],Response::HTTP_UNAUTHORIZED);
        }

        if (!isset($input['password']) || $input['password'] != '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $input['country_code'] = '';
        $input['type'] = 'User';
        $user = User::find($id);
        $user->update($input);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole('User');

        return response(["response"=> 'Usuario actualizado con exito.' ],Response::HTTP_UNAUTHORIZED);

    }


    public function profileUser($id)
    {
        $user = User::find($id);

        $coordenadas = User::where('id', $id)->get(['id', 'name', 'email', 'avatar', 'country', 'phone','created_at']);
        $profile = [];

        foreach ($coordenadas as $coordenada) {
            $profile[] = [
                'id' => $coordenada->id,
                'name' => $coordenada->name,
                'email' => $coordenada->email,
                'avatar' => Storage::url($coordenada->avatar),
                'country' => $coordenada->country,
                'phone' => $coordenada->phone,
                'created_at' => Carbon::parse($coordenada->created_at)->format('m/d/Y')
            ];
        }
        return response()->json($profile);


    }

}
