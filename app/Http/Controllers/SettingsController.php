<?php

namespace App\Http\Controllers;

use App\Facades\Utility;
use App\Facades\UtilityFacades;
use App\Mail\config;
use App\Models\NotificationsSetting;
use App\Models\settings;
use App\Models\User;
use App\Notifications\TestingPurpose;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Spatie\MailTemplates\Models\MailTemplate;
use Str;

class SettingsController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage-setting')) {
            $alllanguages = UtilityFacades::languages();
            foreach ($alllanguages as  $lang) {
                $languages[$lang] = Str::upper($lang);
            }
            $mail_templates = MailTemplate::all();
            $notifications_settings = NotificationsSetting::all();
            return view('settings.index', compact('languages', 'mail_templates', 'notifications_settings'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function appNameUpdate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'app_name' => 'required|min:4',
            'app_logo' => 'image|max:2048|mimes:png',
            'favicon_logo' => 'image|max:2048|mimes:png',
            'app_dark_logo' => 'image|max:2048|mimes:png',
            'app_small_logo' => 'image|max:2048|mimes:png',
        ], [
            'app_name.regex' =>  __('Invalid entry! the app name only letters and numbers are allowed.'),
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $app_logo = UtilityFacades::getsettings('app_logo');
        $app_dark_logo = UtilityFacades::getsettings('app_dark_logo');
        $app_small_logo = UtilityFacades::getsettings('app_small_logo');
        $favicon_logo = UtilityFacades::getsettings('favicon_logo');
        $data = [
            'app_name' => $request->app_name
        ];
        if ($request->app_logo) {
            $app_logo = 'app-logo' . '.' . 'png';
            $logoPath = "appLogo";
            $image = request()->file('app_logo')->storeAs(
                $logoPath,
                $app_logo,
            );
            $data['app_logo'] = $image;
        }
        if ($request->app_dark_logo) {
            $app_dark_logo = 'app-dark-logo' . '.' . 'png';
            $logoPath = "appLogo";
            $image = request()->file('app_dark_logo')->storeAs(
                $logoPath,
                $app_dark_logo,
            );
            $data['app_dark_logo'] = $image;
        }
        if ($request->app_small_logo) {
            $app_small_logo = 'app-small-logo' . '.' . 'png';
            $logoPath = "appLogo";
            $image = request()->file('app_small_logo')->storeAs(
                $logoPath,
                $app_small_logo,
            );
            $data['app_small_logo'] = $image;
        }
        if ($request->favicon_logo) {
            $favicon_logo = 'app-favicon-logo' . '.' . 'png';
            $logoPath = "appLogo";
            $image = request()->file('favicon_logo')->storeAs(
                $logoPath,
                $favicon_logo,
            );
            $data['favicon_logo'] = $image;
        }
        $arrEnv = [
            'APP_NAME' => $request->app_name,
        ];
        UtilityFacades::setEnvironmentValue($arrEnv);
        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('App setting updated successfully.'));
    }

    public function appLogoUpdate(Request $request)
    {
        $disk = Storage::disk('');
        $validator = \Validator::make($request->all(), [
            'app_logo' => 'required|image|max:2048|mimes:jpeg,bmp,png,jpg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $dark_logo = $request->file('app_logo');
        $app_dark_logo = 'app-logo' . '.' . 'png';
        $logoPath = "appLogo";
        $data = request()->file('app_logo')->storeAs(
            $logoPath,
            $app_dark_logo,
        );
        $dark_logo_url =  $disk->url($data);
        $data = [
            'app_logo' => $dark_logo_url,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('App logo updated successfully.'));
    }

    public function appThemeUpdate(Request $request)
    {
        $this->validate($request, [
            'app_theme' => 'required',
        ]);
        $data = [
            'app_theme' => $request->app_theme,
            'app_sidebar' => $request->app_sidebar,
            'app_navbar' => $request->app_navbar,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('App theme updated successfully.'));
    }

    public function pusherSettingUpdate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'pusher_id' => 'required|regex:/^[0-9]+$/',
            'pusher_key' => 'required|regex:/^[A-Za-z0-9_.,()]+$/',
            'pusher_secret' => 'required|regex:/^[A-Za-z0-9_.,()]+$/',
            'pusher_cluster' => 'required|regex:/^[A-Za-z0-9_.,()]+$/',
        ], [
            'pusher_id.regex' =>  __('Invalid entry! the pusher id only letters, underscore and numbers are allowed.'),
            'pusher_key.regex' =>  __('Invalid entry! the pusher key only letters, underscore and numbers are allowed.'),
            'pusher_secret.regex' =>  __('Invalid entry! the pusher secret only letters, underscore and numbers are allowed.'),
            'pusher_cluster.regex' =>  __('Invalid entry! the pusher cluster only letters, underscore and numbers are allowed.'),
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'pusher_id' => $request->pusher_id,
            'pusher_key' => $request->pusher_key,
            'pusher_secret' => $request->pusher_secret,
            'pusher_cluster' => $request->pusher_cluster,
            'pusher_status' => ($request->pusher_status == 'on') ? 1 : 0,
        ];
        $arrEnv = [
            'PUSHER_APP_ID' => $request->pusher_id,
            'PUSHER_APP_KEY' => $request->pusher_key,
            'PUSHER_APP_SECRET' => $request->pusher_secret,
            'PUSHER_APP_CLUSTER' => $request->pusher_cluster,
        ];
        UtilityFacades::setEnvironmentValue($arrEnv);


        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('Pusher API key updated successfully.'));
    }

    public function testMail()
    {
        return view('settings.test-mail');
    }

    public function wasabiSettingUpdate(Request $request)
    {
        if ($request->storage_type == 's3') {
            $validator = \Validator::make($request->all(),  [
                's3_key' => 'required',
                's3_secret' => 'required',
                's3_region' => 'required',
                's3_bucket' => 'required',
            ], [
                's3_key.regex' =>  __('Invalid entry! the s3 key only letters, underscore and numbers are allowed.'),
                's3_secret.regex' =>  __('Invalid entry! the s3 secret only letters, underscore and numbers are allowed.'),
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                's3_key' => $request->s3_key,
                's3_secret' => $request->s3_secret,
                's3_region' => $request->s3_region,
                's3_bucket' => $request->s3_bucket,
                'storage_type' => $request->storage_type,
            ];
        }
        if ($request->storage_type == 'wasabi') {
            $validator = \Validator::make($request->all(), [
                'wasabi_key' => 'required',
                'wasabi_secret' => 'required',
                'wasabi_region' => 'required',
                'wasabi_bucket' => 'required',
                'wasabi_url' => 'required',
                'wasabi_root' => 'required',

            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }

            $wasabi = [
                'wasabi_key' => $request->wasabi_key,
                'wasabi_secret' => $request->wasabi_secret,
                'wasabi_region' => $request->wasabi_region,
                'wasabi_bucket' => $request->wasabi_bucket,
                'wasabi_url' => $request->wasabi_url,
                'wasabi_root' => $request->wasabi_root,
                'FILESYSTEM_DRIVER' => $request->storage_type,
            ];

            $this->updateSettings($request->all());
            return redirect()->back()->with('success',  __('Wasabi keys updated successfully.'));
        } else {
            $data = [
                'storage_type' => $request->storage_type
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Storage setting updated successfully'));
        }
    }

    public function emailSettingUpdate(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'mail_mailer' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required|email',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ], [
            'mail_mailer.regex' => 'Required entry! the mail mailer not allow empty.',
            'mail_host.regex' => 'Required entry! the mail host not allow empty.',
            'mail_port.regex' => 'Required entry! the mail port not allow empty.',
            'mail_username.regex' => 'Required entry! the username mailer not allow empty.',
            'mail_password.regex' => 'Required entry! the password mailer not allow empty.',
            'mail_encryption.regex' => 'Invalid entry! the mail encryption mailer not allow empty.',
            'mail_from_address.regex' => 'Invalid entry! the mail from address not allow empty.',
            'mail_from_name.regex' => 'Invalid entry! the from name not allow empty.',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'email_setting_enable' => ($request->email_setting_enable) ? 'on' : 'off',
            'mail_mailer' => $request->mail_mailer,
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('Email setting updated successfully.'));
    }

    public function captchaSettingUpdate(Request $request)
    {

        $this->validate($request, [
            'captcha' => 'required|min:1'
        ]);
        if ($request->captcha_enable == 'on') {
            if ($request->captcha == 'hcaptcha') {
                $validator = \Validator::make($request->all(), [
                    'hcaptcha_key' => 'required',
                    'hcaptcha_secret' => 'required',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->errors();
                    return redirect()->back()->with('errors', $messages->first());
                }
            }
            if ($request->captcha == 'recaptcha') {
                $validator = \Validator::make($request->all(), [
                    'recaptcha_key' => 'required',
                    'recaptcha_secret' => 'required',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->errors();
                    return redirect()->back()->with('errors', $messages->first());
                }
            }
            $data = [
                'captcha_enable' => $request->captcha_enable == 'on' ? 'on' : 'off',
                'captcha'  => $request->captcha,
                'captcha_secret' => $request->recaptcha_secret,
                'captcha_sitekey' => $request->recaptcha_key,
                'hcaptcha_secret' => $request->hcaptcha_secret,
                'hcaptcha_sitekey' => $request->hcaptcha_key,
            ];

            $input =  $request->all();
            $this->updateSettings($input);
            return redirect()->back()->with('success',  __('Captcha settings updated successfully.'));
        } else {
            $data = ['captcha_enable' => 'off'];

            $this->updateSettings($data);
            return redirect()->back()->with('success',  __('Captcha settings updated successfully.'));
        }
    }

    public function seoSettingUpdate(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'meta_title' => 'required',
                'meta_keywords' => 'required',
                'meta_description' => 'required',
                'meta_image' => 'mimes:png,jpg,jpeg,image',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'seo_setting' => ($request->seo_setting) ? 'on' : 'off',
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ];
        if ($request->hasFile('meta_image')) {
            $meta_image = 'meta_image' . '.' . $request->meta_image->getClientOriginalExtension();
            $logoPath = "seo_image";
            $image = request()->file('meta_image')->storeAs(
                $logoPath,
                $meta_image,
            );
            $data['meta_image'] = $image;
        }

        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('SEO setting updated successfully.'));
    }

    public function cookieSettingUpdate(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'cookie_title' => 'required',
                'cookie_description' => 'required',
                'strictly_cookie_title' => 'required',
                'strictly_cookie_description' => 'required',
                'more_information_description' => 'required',
                'contactus_url' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'enable_cookie' => ($request->enable_cookie) ? 'on' : 'off',
            'cookie_logging' => ($request->cookie_logging) ? 'on' : 'off',
            'cookie_title'  => $request->cookie_title,
            'cookie_description' => $request->cookie_description,
            'strictly_cookie_title' => $request->strictly_cookie_title,
            'strictly_cookie_description' => $request->strictly_cookie_description,
            'more_information_description' => $request->more_information_description,
            'contactus_url' => $request->contactus_url,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('Cookie setting updated successfully.'));
    }

    public function CookieConsent(Request $request)
    {
        if (UtilityFacades::keysettings('enable_cookie', 1) == "on" && UtilityFacades::keysettings('cookie_logging', 1) == "on") {
            $allowed_levels = ['necessary', 'analytics', 'targeting'];
            $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                return in_array($level, $allowed_levels);
            });
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            // Generate new CSV line
            $browser_name = $whichbrowser->browser->name ?? null;
            $os_name = $whichbrowser->os->name ?? null;
            $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $device_type = Utility::get_device_type($_SERVER['HTTP_USER_AGENT']);

            // $ip = $_SERVER['REMOTE_ADDR'];
            $ip = '49.36.83.154';

            $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));

            $date = (new \DateTime())->format('Y-m-d');
            $time = (new \DateTime())->format('H:i:s') . ' UTC';

            $new_line = implode(',', [
                $ip, $date, $time, json_encode($request['cookie']), $device_type, $browser_language, $browser_name, $os_name,
                isset($query) ? $query['country'] : '', isset($query) ? $query['region'] : '', isset($query) ? $query['regionName'] : '', isset($query) ? $query['city'] : '', isset($query) ? $query['zip'] : '', isset($query) ? $query['lat'] : '', isset($query) ? $query['lon'] : ''
            ]);

            if (!file_exists(Storage::path('seo_image/cookie_data.csv'))) {
                $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name,Country,Region,RegionName,City,Zipcode,Lat,Lon';
                file_put_contents(Storage::path('seo_image/cookie_data.csv'), $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            file_put_contents(Storage::path('seo_image/cookie_data.csv'), $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);

            return response()->json('success');
        }
        return response()->json('error');
    }

    public function socialSettingUpdate(Request $request)
    {
        $this->validate($request, [
            'socialsetting' => 'required|min:1'
        ]);
        $googlestatus = 'off';
        $facebookstatus = 'off';
        $githubstatus = 'off';
        $linkedinstatus = 'off';
        if ($request->socialsetting) {
            if (in_array('google', $request->get('socialsetting'))) {
                $validator = \Validator::make($request->all(), [
                    'google_client_id' => 'required',
                    'google_client_secret' => 'required',
                    'google_redirect' => 'required',
                ], [
                    'google_client_id.regex' => 'Invalid entry! the google key only letters, underscore and numbers are allowed.',
                    'google_client_secret.regex' => 'Invalid entry! the google secret only letters, underscore and numbers are allowed.',
                    'google_redirect.regex' => 'Invalid entry! the google redirect only letters, underscore and numbers are allowed.',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->errors();
                    return redirect()->back()->with('errors', $messages->first());
                }
                $data = [
                    'google_client_id' => $request->google_client_id,
                    'google_client_secret' => $request->google_client_secret,
                    'google_redirect' => $request->google_redirect,
                    'googlesetting' => (!empty($request->googlesetting)) ? 'on' : 'off',
                ];
                $googlestatus = 'on';
            }
            if (in_array('facebook', $request->get('socialsetting'))) {
                $validator = \Validator::make($request->all(), [
                    'facebook_client_id' => 'required',
                    'facebook_client_secret' => 'required',
                    'facebook_redirect' => 'required',
                ], [
                    'facebook_client_id.regex' => 'Invalid entry! the facebook key only letters, underscore and numbers are allowed.',
                    'facebook_client_secret.regex' => 'Invalid entry! the facebook secret only letters, underscore and numbers are allowed.',
                    'facebook_redirect.regex' => 'Invalid entry! the facebook redirect only letters, underscore and numbers are allowed.',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->errors();
                    return redirect()->back()->with('errors', $messages->first());
                }
                $data = [
                    'facebook_client_id' => $request->facebook_client_id,
                    'facebook_client_secret' => $request->facebook_client_secret,
                    'facebook_redirect' => $request->facebook_redirect,
                    'facebooksetting' => (!empty($request->facebooksetting)) ? 'on' : 'off',
                ];
                $facebookstatus = 'on';
            }
            if (in_array('github', $request->get('socialsetting'))) {
                $validator = \Validator::make($request->all(), [
                    'github_client_id' => 'required',
                    'github_client_secret' => 'required',
                    'github_redirect' => 'required',
                ], [
                    'github_client_id.regex' => 'Invalid entry! the github key only letters, underscore and numbers are allowed.',
                    'github_client_secret.regex' => 'Invalid entry! the github secret only letters, underscore and numbers are allowed.',
                    'github_redirect.regex' => 'Invalid entry! the github redirect only letters, underscore and numbers are allowed.',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->errors();
                    return redirect()->back()->with('errors', $messages->first());
                }
                $data = [
                    'github_client_id' => $request->github_client_id,
                    'github_client_secret' => $request->github_client_secret,
                    'github_redirect' => $request->github_redirect,
                    'githubsetting' => (!empty($request->githubsetting)) ? 'on' : 'off',
                ];
                $githubstatus = 'on';
            }
            if (in_array('linkedin', $request->get('socialsetting'))) {

                $validator = \Validator::make($request->all(), [
                    'linkedin_client_id' => 'required',
                    'linkedin_client_secret' => 'required',
                    'linkedin_redirect' => 'required',
                ], [
                    'linkedin_client_id.regex' => 'Invalid entry! the linkedin key only letters, underscore and numbers are allowed.',
                    'linkedin_client_secret.regex' => 'Invalid entry! the linkedin secret only letters, underscore and numbers are allowed.',
                    'linkedin_redirect.regex' => 'Invalid entry! the linkedin redirect only letters, underscore and numbers are allowed.',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->errors();
                    return redirect()->back()->with('errors', $messages->first());
                }
                $data = [
                    'linkedin_client_id' => $request->linkedin_client_id,
                    'linkedin_client_secret' => $request->linkedin_client_secret,
                    'linkedin_redirect' => $request->linkedin_redirect,
                    'linkedinsetting' => (!empty($request->linkedinsetting)) ? 'on' : 'off',
                ];
                $linkedinstatus = 'on';
            }
            $data = [
                'google_client_id' => $request->google_client_id,
                'google_client_secret' => $request->google_client_secret,
                'google_redirect' => $request->google_redirect,
                'facebook_client_id' => $request->facebook_client_id,
                'facebook_client_secret' => $request->facebook_client_secret,
                'facebook_redirect' => $request->facebook_redirect,
                'github_client_id' => $request->github_client_id,
                'github_client_secret' => $request->github_client_secret,
                'github_redirect' => $request->github_redirect,
                'linkedin_client_id' => $request->linkedin_client_id,
                'linkedin_client_secret' => $request->linkedin_client_secret,
                'linkedin_redirect' => $request->linkedin_redirect,
                'googlesetting' => (in_array('google', $request->get('socialsetting'))) ? 'on' : 'off',
                'facebooksetting' => (in_array('facebook', $request->get('socialsetting'))) ? 'on' : 'off',
                'githubsetting' => (in_array('github', $request->get('socialsetting'))) ? 'on' : 'off',
                'linkedinsetting' => (in_array('linkedin', $request->get('socialsetting'))) ? 'on' : 'off',
            ];
        } else {
            $data = [
                'googlesetting' => 'off',
                'facebooksetting' => 'off',
                'githubsetting' => 'off',
                'linkedinsetting' => 'off',
            ];
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Social setting updated successfully.'));
    }

    public function authSettingsUpdate(Request $request)
    {
        $user = \Auth::user();
        if ($request->email_verification == 'on') {
            if (UtilityFacades::getsettings('mail_host') != '') {
                $val = [
                    'email_verification' => ($request->email_verification == 'on') ? '1' : '0',
                ];
                $this->updateSettings($val);
            } else {
                return redirect("/settings#useradd-6")->with('warning', __('Please set email setting.'));
            }
        }
        if ($request->sms_verification == 'on') {
            if (UtilityFacades::getsettings('multisms_setting') == 'on') {
                $val = [
                    'sms_verification' => ($request->sms_verification == 'on') ? '1' : '0',
                ];
                $this->updateSettings($val);
            } else {
                return redirect("/settings#useradd-9")->with('warning', __('Please set sms setting.'));
            }
        }

        $data = [
            'rtl' => ($request->rtl_setting == 'on') ? 1 : 0,
            '2fa' => ($request->two_factor_auth == 'on') ? 1 : 0,
            'register' => ($request->register == 'on') ? 1 : 0,
            'landing_page' => ($request->landing_page == 'on') ? 1 : 0,
            'gtag' => $request->gtag,
            'default_language' => $request->default_language,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'email_verification' => ($request->email_verification == 'on') ? 1 : 0,
            'sms_verification' => ($request->sms_verification == 'on') ? 1 : 0,
            'color' => ($request->color) ? $request->color : UtilityFacades::getsettings('color'),
            'dark_mode' => $request->dark_mode,
            'transparent_layout' => ($request->transparent_layout == 'on') ? 'on' : 'off',
            'roles' => $request->roles
        ];
        $this->updateSettings($data);
        $user->dark_layout = ($request->dark_mode && $request->dark_mode == 'on') ? 1 : 0;
        $user->rtl_layout = ($request->rtl_setting && $request->rtl_setting == 'on') ? 1 : 0;
        $user->transprent_layout = ($request->transparent_layout && $request->transparent_layout == 'on') ? 1 : 0;
        $user->theme_color = ($request->color) ? $request->color : UtilityFacades::getsettings('color');
        $user->save();
        return redirect()->back()->with('success',  __('General setting updated successfully.'));
    }

    public function paymentSettingUpdate(Request $request)
    {
        $this->validate($request, [
            'paymentsetting' => 'required|min:1'
        ]);
        $stripestatus = 'off';
        $paypalstatus = 'off';
        $razorpaystatus = 'off';
        $Offlinestatus = 'off';
        $mercadostatus = 'off';
        $payumoneystatus = 'off';
        $molliestatus = 'off';

        if (in_array('stripe', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'stripe_key' => 'required',
                'stripe_secret' => 'required',
            ], [
                'stripe_key.regex' => 'Invalid entry! the stripe key only letters, underscore and numbers are allowed.',
                'stripe_secret.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'stripe_key' => $request->stripe_key,
                'stripe_secret' => $request->stripe_secret,
                'stripesetting' => (in_array('stripe', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $stripestatus = 'on';
        }
        if (in_array('paypal', $request->paymentsetting)) {
            $validator = \Validator::make($request->all(), [
                'client_id' => 'required',
                'client_secret' => 'required',
            ], [
                'client_id.regex' => 'Invalid entry! the stripe key only letters, underscore and numbers are allowed.',
                'client_secret.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'paypal_sandbox_client_id' => $request->client_id,
                'paypal_sandbox_client_secret' => $request->client_secret,
                'paypal_mode' => $request->paypal_mode,
                'paypalsetting' => (in_array('paypal', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $paypalstatus = 'on';
        }
        if (in_array('razorpay', $request->paymentsetting)) {
            $validator = \Validator::make($request->all(), [
                'razorpay_key' => 'required',
                'razorpay_secret' => 'required',
            ], [
                'razorpay_key.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
                'razorpay_secret.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'razorpay_key' => $request->razorpay_key,
                'razorpay_secret' =>  $request->razorpay_secret,
                'razorpaysetting' => (in_array('razorpay', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $razorpaystatus = 'on';
        }
        if (in_array('mollie', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'mollie_api_key' => 'required',
                'mollie_profile_id' => 'required',
                'mollie_partner_id' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'mollie_api_key' => $request->mollie_api_key,
                'mollie_profile_id' => $request->mollie_profile_id,
                'mollie_partner_id' => $request->mollie_partner_id,
                'molliesetting' => (in_array('mollie', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
        }
        if (in_array('paytm', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'merchant_id' => 'required',
                'merchant_key' => 'required',
                'paytm_environment' => 'required',
            ], [
                'merchant_id.regex' => 'Invalid entry! the stripe key only letters, underscore and numbers are allowed.',
                'merchant_key.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'paytm_merchant_id' => $request->merchant_id,
                'paytm_merchant_key' => $request->merchant_key,
                'paytm_environment' => $request->paytm_environment,
                'paytm_merchant_website' => 'local',
                'paytm_channel' => 'WEB',
                'paytm_indistry_type' => 'local',
                'paytmsetting' => (in_array('paytm', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $paytmstatus = 'on';
        }
        if (in_array('flutterwave', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'flw_public_key' => 'required',
                'flw_secret_key' => 'required',
            ], [
                'flw_public_key.regex' => 'Invalid entry! the stripe key only letters, underscore and numbers are allowed.',
                'flw_secret_key.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'flw_public_key' => $request->flw_public_key,
                'flw_secret_key' => $request->flw_secret_key,
                'flutterwavesetting' => (in_array('flutterwave', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $flutterwavestatus = 'on';
        }
        if (in_array('coingate', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'coingate_auth_token' => 'required',
            ], [
                'coingate_auth_token.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'coingate_environment' => $request->coingate_mode,
                'coingate_auth_token' => $request->coingate_auth_token,
                'coingatesetting' => (in_array('coingate', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $stripestatus = 'on';
        }
        if (in_array('paystack', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'paystack_public_key' => 'required',
                'paystack_secret_key' => 'required',
            ], [
                'paystack_public_key.regex' => 'Invalid entry! the stripe key only letters, underscore and numbers are allowed.',
                'paystack_secret_key.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'paystack_public_key' => $request->paystack_public_key,
                'paystack_secret_key' => $request->paystack_secret_key,
                'paystacksetting' => (in_array('paystack', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $flutterwavestatus = 'on';
        }
        if (in_array('payumoney', $request->get('paymentsetting'))) {
            $validator = \Validator::make($request->all(), [
                'payumoney_merchant_key' => 'required',
                'payumoney_salt_key' => 'required',
            ], [
                'payumoney_merchant_key.regex' => 'Invalid entry! the stripe key only letters, underscore and numbers are allowed.',
                'payumoney_salt_key.regex' => 'Invalid entry! the stripe secret only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'payumoney_merchant_key' => $request->payumoney_merchant_key,
                'payumoney_salt_key' => $request->payumoney_salt_key,
                'payumoneysetting' => (in_array('payumoney', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $payumoneystatus = 'on';
        }
        if (in_array('mercado', $request->paymentsetting)) {
            $validator = \Validator::make($request->all(), [
                'mercado_access_token' => 'required',
            ], [
                'mercado_access_token.regex' => 'Invalid entry! the mercado access token only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'mercado_mode' => $request->mercado_mode,
                'mercado_access_token' => $request->mercado_access_token,
                'mercadosetting' => (in_array('mercado', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $mercadostatus = 'on';
        }

        if (in_array('offline', $request->paymentsetting)) {
            $validator = \Validator::make($request->all(), [
                'payment_mode' => 'required',
            ], [
                'payment_mode.regex' => 'Invalid entry! the payment mode only letters, underscore and numbers are allowed.',
                'payment_details.regex' => 'Invalid entry! the payment details only letters, underscore and numbers are allowed.',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'payment_mode' => $request->payment_mode,
                'payment_details' =>  $request->payment_details,
                'offlinesetting' => (in_array('offline', $request->get('paymentsetting'))) ? 'on' : 'off',
            ];
            $Offlinestatus = 'on';
        }
        $data = [
            'stripe_key' => $request->stripe_key,
            'stripe_secret' => $request->stripe_secret,
            'paypal_sandbox_client_id' => $request->client_id,
            'paypal_sandbox_client_secret' => $request->client_secret,
            'paypal_mode' => $request->paypal_mode,
            'razorpay_key' => $request->razorpay_key,
            'razorpay_secret' =>  $request->razorpay_secret,
            'paytm_merchant_id' => $request->merchant_id,
            'paytm_merchant_key' => $request->merchant_key,
            'paytm_environment' => $request->paytm_environment,
            'paytm_merchant_website' => 'local',
            'paytm_channel' => 'WEB',
            'paytm_indistry_type' => 'local',
            'flw_public_key' => $request->flw_public_key,
            'flw_secret_key' => $request->flw_secret_key,
            'paystack_public_key' => $request->paystack_public_key,
            'paystack_secret_key' => $request->paystack_secret_key,
            'payumoney_merchant_key' => $request->payumoney_merchant_key,
            'payumoney_salt_key' => $request->payumoney_salt_key,
            'mollie_api_key' => $request->mollie_api_key,
            'mollie_profile_id' => $request->mollie_profile_id,
            'mollie_partner_id' => $request->mollie_partner_id,
            'coingate_environment' => $request->coingate_mode,
            'coingate_auth_token' => $request->coingate_auth_token,
            'payment_mode' => $request->payment_mode,
            'payment_details' =>  $request->payment_details,
            'mercado_mode' => $request->mercado_mode,
            'mercado_access_token' => $request->mercado_access_token,
            'mercadosetting' => (in_array('mercado', $request->get('paymentsetting'))) ? 'on' : 'off',
            'coingatesetting' => (in_array('coingate', $request->get('paymentsetting'))) ? 'on' : 'off',
            'stripesetting' => (in_array('stripe', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paypalsetting' => (in_array('paypal', $request->get('paymentsetting'))) ? 'on' : 'off',
            'razorpaysetting' => (in_array('razorpay', $request->get('paymentsetting'))) ? 'on' : 'off',
            'offlinesetting' => (in_array('offline', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paytmsetting' => (in_array('paytm', $request->get('paymentsetting'))) ? 'on' : 'off',
            'flutterwavesetting' => (in_array('flutterwave', $request->get('paymentsetting'))) ? 'on' : 'off',
            'paystacksetting' => (in_array('paystack', $request->get('paymentsetting'))) ? 'on' : 'off',
            'payumoneysetting' => (in_array('payumoney', $request->get('paymentsetting'))) ? 'on' : 'off',
            'molliesetting' => (in_array('mollie', $request->get('paymentsetting'))) ? 'on' : 'off',
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Payment setting updated successfully.'));
    }

    public function smsSettingUpdate(Request $request)
    {
        if ($request->smssetting == 'twilio') {
            $validator = \Validator::make($request->all(), [
                'twilio_sid' => 'required',
                'twilio_auth_token' => 'required',
                'twilio_verify_sid' => 'required',
                'twilio_number' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
        } else if ($request->smssetting == 'nexmo') {
            $validator = \Validator::make($request->all(), [
                'nexmo_key' => 'required',
                'nexmo_secret' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
        }
        $data = [
            'multisms_setting' => ($request->multisms_setting) ? 'on' : 'off',
            'smssetting' => ($request->smssetting),
            'nexmo_key' => $request->nexmo_key,
            'nexmo_secret' => $request->nexmo_secret,
            'twilio_sid' => $request->twilio_sid,
            'twilio_auth_token' => $request->twilio_auth_token,
            'twilio_verify_sid' => $request->twilio_verify_sid,
            'twilio_number' => $request->twilio_number,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('Sms setting updated successfully.'));
    }


    private function updateSettings($input)
    {
        foreach ($input as $key => $value) {
            settings::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function backupFiles()
    {
        Artisan::call('backup:run', ['--only-files' => true]);
        $output = Artisan::output();
        if (Str::contains($output, 'Backup completed!')) {
            return redirect()->back()->with('success',  __('Application files backed-up successfully.'));
        } else {
            return redirect()->back()->with('error',  __('Application files backed-up failed.'));
        }
    }

    public function backupDb()
    {
        Artisan::call('backup:run', ['--only-db' => true]);
        $output = Artisan::output();
        if (Str::contains($output, 'Backup completed!')) {
            return redirect()->back()->with('success',  __('Application database backed-up successfully.'));
        } else {
            return redirect()->back()->with('error',  __('Application database backed-up failed.'));
        }
    }

    private function getBackups()
    {
        $path = storage_path('app/app-backups');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $files = File::allFiles($path);
        $backups = collect([]);
        foreach ($files as $dt) {
            $backups->push([
                'filename' => pathinfo($dt->getFilename(), PATHINFO_FILENAME),
                'extension' => pathinfo($dt->getFilename(), PATHINFO_EXTENSION),
                'path' => $dt->getPath(),
                'size' => $dt->getSize(),
                'time' => $dt->getMTime(),
            ]);
        }
        return $backups;
    }

    public function downloadBackup($name, $ext)
    {
        $path = storage_path('app/app-backups');
        $file = $path . '/' . $name . '.' . $ext;
        $status = Storage::disk('backup')->download($name . '.' . $ext, $name . '.' . $ext);
        return $status;
    }

    public function deleteBackup($name, $ext)
    {
        $path = storage_path('app/app-backups');
        $file = $path . '/' . $name . '.' . $ext;
        $status = File::delete($file);
        if ($status) {
            return redirect()->back()->with('success',  __('Backup deleted successfully.'));
        } else {
            return redirect()->back()->with('error',  __('Oops! an error occured, try again.'));
        }
    }

    public function frontendsetting(Request $request)
    {
        return view('settings.frontend');
    }

    public function frontendsettingstore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'app_setting_status' => ($request->app_setting_status) ? 'on' : 'off',
            'apps_paragraph' => $request->apps_paragraph,
        ];
        if ($request->image) {
            Storage::delete(UtilityFacades::getsettings('image'));
            $image_name = 'image.' . $request->image->extension();
            $request->image->storeAs('landingpage', $image_name);
            $data['image'] = 'landingpage/' . $image_name;
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('App setting updated successfully.'));
    }

    public function menusettingstore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'images1' => 'image|mimes:png,jpg,jpeg',
            'images2' => 'image|mimes:svg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'menu_setting_status' => ($request->menu_setting_status) ? 'on' : 'off',
            'menu_name' => $request->menu_name,
            'menu_title' => $request->menu_title,
            'menu_subtitle' => $request->menu_subtitle,
            'menu_paragraph' => $request->menu_paragraph,
            'submenu_name' => $request->submenu_name,
            'submenu_title' => $request->submenu_title,
            'submenu_subtitle' => $request->submenu_subtitle,
            'submenu_paragraph' => $request->submenu_paragraph,
        ];
        if ($request->images1) {
            Storage::delete(UtilityFacades::getsettings('images1'));
            $images1_name = 'images1.' . $request->images1->extension();
            $request->images1->storeAs('landingpage', $images1_name);
            $data['images1'] = 'landingpage/' . $images1_name;
        }
        if ($request->images2) {
            Storage::delete(UtilityFacades::getsettings('images2'));
            $images2_name = 'images2.' . $request->images2->extension();
            $request->images2->storeAs('landingpage', $images2_name);
            $data['images2'] = 'landingpage/' . $images2_name;
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Menu setting updated successfully.'));
    }

    public function featuresettingstore(Request $request)
    {
        $data = [
            'feature_setting_status' => ($request->feature_setting_status) ? 'on' : 'off',
            'feature_name' => $request->feature_name,
            'feature_paragraph' => $request->feature_paragraph,
            'feature_setting' => json_encode($request->feature_setting),
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Feature setting updated successfully.'));
    }

    public function faqsettingstore(Request $request)
    {
        $data = [
            'faq_setting_status' => ($request->faq_setting_status) ? 'on' : 'off',
            'faq_title' => $request->faq_title,
            'faq_paragraph' => $request->faq_paragraph,
            'faq_page_content' => $request->faq_page_content,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Faq setting updated successfully.'));
    }

    public function testimonialStore(Request $request)
    {
        $data = [
            'testimonial_setting_status' => ($request->testimonial_setting_status) ? 'on' : 'off',
            'testimonial_title' => $request->testimonial_title,
            'testimonial_paragraph' => $request->testimonial_paragraph,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Testimonial setting updated successfully.'));
    }
    public function sidefeaturesettingstore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'image1' => 'image|mimes:png,jpg,jpeg',
            'image2' => 'image|mimes:png,jpg,jpeg',
            'image3' => 'image|mimes:png,jpg,jpeg',
            'image4' => 'image|mimes:png,jpg,jpeg',
            'image5' => 'image|mimes:png,jpg,jpeg',
            'image6' => 'image|mimes:png,jpg,jpeg',
            'image7' => 'image|mimes:png,jpg,jpeg',
            'image8' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'sidefeature_setting_status' => ($request->sidefeature_setting_status) ? 'on' : 'off',
            'sidefeature_name' => $request->sidefeature_name,
            'sidefeature_title' => $request->sidefeature_title,
            'sidefeature_subtitle' => $request->sidefeature_subtitle,
            'sidefeature_paragraph' => $request->sidefeature_paragraph,
        ];
        if ($request->image1) {
            Storage::delete(UtilityFacades::getsettings('image1'));
            $image1_name = 'image1.' . $request->image1->extension();
            $request->image1->storeAs('landingpage', $image1_name);
            $data['image1'] = 'landingpage/' . $image1_name;
        }
        if ($request->image2) {
            Storage::delete(UtilityFacades::getsettings('image2'));
            $image2_name = 'image2.' . $request->image2->extension();
            $request->image2->storeAs('landingpage', $image2_name);
            $data['image2'] = 'landingpage/' . $image2_name;
        }
        if ($request->image3) {
            Storage::delete(UtilityFacades::getsettings('image3'));
            $image3_name = 'image3.' . $request->image3->extension();
            $request->image3->storeAs('landingpage', $image3_name);
            $data['image3'] = 'landingpage/' . $image3_name;
        }
        if ($request->image4) {
            Storage::delete(UtilityFacades::getsettings('image4'));
            $image4_name = 'image4.' . $request->image4->extension();
            $request->image4->storeAs('landingpage', $image4_name);
            $data['image4'] = 'landingpage/' . $image4_name;
        }
        if ($request->image5) {
            Storage::delete(UtilityFacades::getsettings('image5'));
            $image5_name = 'image5.' . $request->image5->extension();
            $request->image5->storeAs('landingpage', $image5_name);
            $data['image5'] = 'landingpage/' . $image5_name;
        }
        if ($request->image6) {
            Storage::delete(UtilityFacades::getsettings('image6'));
            $image6_name = 'image6.' . $request->image6->extension();
            $request->image6->storeAs('landingpage', $image6_name);
            $data['image6'] = 'landingpage/' . $image6_name;
        }
        if ($request->image7) {
            Storage::delete(UtilityFacades::getsettings('image7'));
            $image7_name = 'image7.' . $request->image7->extension();
            $request->image7->storeAs('landingpage', $image7_name);
            $data['image7'] = 'landingpage/' . $image7_name;
        }
        if ($request->image8) {
            Storage::delete(UtilityFacades::getsettings('image8'));
            $image8_name = 'image8.' . $request->image8->extension();
            $request->image8->storeAs('landingpage', $image8_name);
            $data['image8'] = 'landingpage/' . $image8_name;
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Side feature setting updated successfully.'));
    }

    public function privacysettingstore(Request $request)
    {
        $data = [
            'privacy' => $request->privacy,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Privacy setting updated successfully.'));
    }

    public function contactussettingstore(Request $request)
    {
        $data = [
            'footer_page_content' => $request->footer_page_content,
            'contact_us' => $request->contact_us,
            'contact_email' => $request->contact_email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'captcha_status' => ($request->captcha_status == 'on') ? 1 : 0,
            'skype_id' => $request->skype_id,
            'skype_name' => $request->skype_name,
            'technical_support_email' => $request->technical_support_email,
            'custom_projects_email' => $request->custom_projects_email,
        ];
        $captcha_data = [
            'CAPTCHA_SITEKEY' => $request->recaptcha_key,
            'CAPTCHA_SECRET' => $request->recaptcha_secret,
        ];
        // foreach ($captcha_data as $key => $value) {
        //     UtilityFacades::setEnvironmentValue([$key => $value]);
        // }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Contact us setting updated successfully.'));
    }

    public function termconditionsettingstore(Request $request)
    {
        $data = [
            'term_condition' => $request->term_condition,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Term & condition setting updated successfully.'));
    }

    public function loginsettingstore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'login_title' => 'required',
            'login_subtitle' => 'required',
            'login_image' => 'image|mimes:svg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $data = [
            'login_title' => $request->login_title,
            'login_subtitle' => $request->login_subtitle,
        ];
        if ($request->login_image) {
            Storage::delete(UtilityFacades::getsettings('login_image'));
            $login_image_name = 'loginpage.' . $request->login_image->extension();
            $request->login_image->storeAs('loginpage', $login_image_name);
            $data['login_image'] = 'loginpage/' . $login_image_name;
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Frontend page setting updated successfully.'));
    }

    public function recaptchasettingstore(Request $request)
    {
        if ($request->contact_us_recaptcha_status == '1' || $request->login_recaptcha_status == '1') {
            $validator = \Validator::make($request->all(), [
                'recaptcha_key' => 'required',
                'recaptcha_secret' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
        }
        $data = [
            'contact_us_recaptcha_status' => ($request->contact_us_recaptcha_status == 'on') ? '1' : '0',
            'login_recaptcha_status' => ($request->login_recaptcha_status == 'on') ? '1' : '0',
            'recaptcha_key' => $request->recaptcha_key,
            'recaptcha_secret' => $request->recaptcha_secret,
        ];
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Recaptcha setting updated successfully.'));
    }

    function loadsetting($type)
    {
        $alllanguages = UtilityFacades::languages();
        foreach ($alllanguages as  $lang) {
            $languages[$lang] = Str::upper($lang);
        }
        return view('settings.index', compact('languages'));
    }

    public function testSendMail(Request $request)
    {
        $user = User::where('type', 'Admin')->first();
        $email = $request->email;
        $validator = \Validator::make($request->all(), ['email' => 'required|email']);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $notifications_setting = NotificationsSetting::where('title', 'testing purpose')->first();
        if (isset($notifications_setting)) {
            if ($notifications_setting->email_notification == '1') {
                if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                    try {
                        $user->notify(new TestingPurpose($email));
                    } catch (\Exception $e) {
                        $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                        return redirect()->back()->with('error', $smtp_error);
                    }
                } else {
                    return redirect()->back()->with('status', __('Please turn on email enable/disable button.'));
                }
            } else {
                return redirect()->back()->with('status', __('Please turn on Email notification'));
            }
        }

        return redirect()->back()->with('success', __('Email send successfully.'));
    }

    public function GoogleCalender(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'google_calendar_id' => 'required',
            'google_calendar_json_file' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->google_calendar_json_file) {
            $dir = md5(time());
            $path = $dir . '/' . md5(time()) . "." . $request->google_calendar_json_file->getClientOriginalExtension();

            $file = $request->file('google_calendar_json_file');
            $file->storeAs('google_json_file', $path);
            $url = Storage::path($path);
        }

        $data = [
            'google_calendar_enable' => ($request->google_calendar_enable  && $request->google_calendar_enable == 'on')  ? 'on' : 'off',
            'google_calendar_id' => $request->google_calendar_id,
            'google_calendar_json_file' => $path,
        ];
        // dd($data);

        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('Google Calendar API key updated successfully.'));
    }

    public function GoogleMapUpdate(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'google_map_api' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $data = [
            'google_map_enable' => ($request->google_map_enable && $request->google_map_enable == 'on') ? 'on' : 'off',
            'google_map_api' => $request->google_map_api,
        ];

        $this->updateSettings($data);
        return redirect()->back()->with('success',  __('Google map API key updated successfully.'));
    }
}
