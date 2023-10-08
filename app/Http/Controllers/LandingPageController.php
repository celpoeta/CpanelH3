<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\UtilityFacades;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use App\Models\FooterSetting;
use App\Models\HeaderSetting;
use App\Models\PageSetting;
use App\Models\settings;
use Google\Service\Blogger\Resource\Blogs;

class LandingPageController extends Controller
{
    private function updateSettings($input)
    {
        foreach ($input as $key => $value) {
            settings::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function landingpagesetting(Request $request)
    {
        if (\Auth::user()->can('manage-landing-page')) {
            return view('landingpage.app_setting');
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function appsettingstore(Request $request)
    {
        if ($request->apps_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'apps_name' => 'required',
                'apps_bold_name' => 'required',
                'app_detail' => 'required',
                'apps_image' => 'image|mimes:png,jpg,jpeg',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }

            if ($request->apps_multiple_image != '') {
                $data = [];
                if ($request->hasFile('apps_multiple_image')) {
                    $images = $request->file('apps_multiple_image');
                    foreach ($images as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('landingpage/app/', $imageName);
                        $data[] = ['apps_multiple_image' => 'landingpage/app/' . $imageName];
                    }
                }
                $data = json_encode($data);
                settings::updateOrCreate(
                    ['key' => 'apps_multiple_image_setting'],
                    ['value' => $data]
                );
            }
            $data = [
                'apps_setting_enable' => $request->apps_setting_enable == 'on' ? 'on' : 'off',
                'apps_name' => $request->apps_name,
                'apps_bold_name' => $request->apps_bold_name,
                'app_detail' => $request->app_detail,
            ];
            if ($request->apps_image) {
                Storage::delete(UtilityFacades::getsettings('apps_image'));
                $image_name = 'app.' . $request->apps_image->extension();
                $request->apps_image->storeAs('landingpage/app/', $image_name);
                $data['apps_image'] = 'landingpage/app/' . $image_name;
            }
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('App setting updated successfully.'));
        } else {
            $data = [
                'apps_setting_enable' => 'off',
            ];
            $arrEnv = [
                'apps_setting_enable' => 'off',
            ];

            $this->updateSettings($data);
            return redirect()->back()->with('success', __('App setting disabled.'));
        }
    }

    public function menusetting(Request $request)
    {
        $setting_data = [
            "menu_setting" => UtilityFacades::getsettings('menu_setting'),
        ];
        $settings = $setting_data;
        $menu_settings = json_decode($settings['menu_setting'], true) ?? [];
        return view('landingpage.menu.index', compact('menu_settings'));
    }
    public function menusettingsection1store(Request $request)
    {
        if ($request->menu_setting_section1_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'menu_name_section1' => 'required',
                'menu_bold_name_section1' => 'required',
                'menu_detail_section1' => 'required',
                'menu_image_section1' => 'image|mimes:png,jpg,jpeg',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'menu_setting_section1_enable' => $request->menu_setting_section1_enable == 'on' ? 'on' : 'off',
                'menu_name_section1' => $request->menu_name_section1,
                'menu_bold_name_section1' => $request->menu_bold_name_section1,
                'menu_detail_section1' => $request->menu_detail_section1,
            ];
            if ($request->menu_image_section1) {
                Storage::delete(UtilityFacades::getsettings('menu_image_section1'));
                $image_name = 'menusection1.' . $request->menu_image_section1->extension();
                $request->menu_image_section1->storeAs('landingpage/menu/', $image_name);
                $data['menu_image_section1'] = 'landingpage/menu/' . $image_name;
            }
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Menu setting updated successfully.'));
        } else {
            $data = [
                'menu_setting_section1_enable' => 'off',
            ];
            $arrEnv = [
                'menu_setting_section1_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('App setting disabled.'));
        }
    }

    public function menusettingsection2store(Request $request)
    {
        if ($request->menu_setting_section2_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'menu_name_section2' => 'required',
                'menu_bold_name_section2' => 'required',
                'menu_detail_section2' => 'required',
                'menu_image_section2' => 'image|mimes:png,jpg,jpeg',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'menu_setting_section2_enable' => $request->menu_setting_section2_enable == 'on' ? 'on' : 'off',
                'menu_name_section2' => $request->menu_name_section2,
                'menu_bold_name_section2' => $request->menu_bold_name_section2,
                'menu_detail_section2' => $request->menu_detail_section2,
            ];
            if ($request->menu_image_section2) {
                Storage::delete(UtilityFacades::getsettings('menu_image_section2'));
                $image_name = 'menusection12.' . $request->menu_image_section2->extension();
                $request->menu_image_section2->storeAs('landingpage/menu/', $image_name);
                $data['menu_image_section2'] = 'landingpage/menu/' . $image_name;
            }
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Menu setting updated successfully.'));
        } else {
            $data = [
                'menu_setting_section2_enable' => 'off',
            ];
            $arrEnv = [
                'menu_setting_section2_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('App setting disabled.'));
        }
    }

    public function menusettingsection3store(Request $request)
    {
        if ($request->menu_setting_section3_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'menu_name_section3' => 'required',
                'menu_bold_name_section3' => 'required',
                'menu_detail_section3' => 'required',
                'menu_image_section3' => 'image|mimes:png,jpg,jpeg',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'menu_setting_section3_enable' => $request->menu_setting_section3_enable == 'on' ? 'on' : 'off',
                'menu_name_section3' => $request->menu_name_section3,
                'menu_bold_name_section3' => $request->menu_bold_name_section3,
                'menu_detail_section3' => $request->menu_detail_section3,
            ];
            if ($request->menu_image_section3) {
                Storage::delete(UtilityFacades::getsettings('menu_image_section3'));
                $image_name = 'menusection13.' . $request->menu_image_section3->extension();
                $request->menu_image_section3->storeAs('landingpage/menu/', $image_name);
                $data['menu_image_section3'] = 'landingpage/menu/' . $image_name;
            }
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Menu setting updated successfully.'));
        } else {
            $data = [
                'menu_setting_section3_enable' => 'off',
            ];
            $arrEnv = [
                'menu_setting_section3_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('App setting disabled.'));
        }
    }

    public function faqsetting(Request $request)
    {
        return view('landingpage.faq_setting');
    }

    public function faqsettingstore(Request $request)
    {
        if ($request->faq_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'faq_name' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'faq_setting_enable' => $request->faq_setting_enable == 'on' ? 'on' : 'off',
                'faq_name' => $request->faq_name,
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Faq setting updated successfully.'));
        } else {
            $data = [
                'faq_setting_enable' => 'off',
            ];
            $arrEnv = [
                'faq_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Faq setting disabled.'));
        }
    }

    public function featuresetting(Request $request)
    {
        $setting_data = [
            "feature_setting" => UtilityFacades::getsettings('feature_setting'),
        ];
        $settings = $setting_data;
        $feature_settings = json_decode($settings['feature_setting'], true) ?? [];
        return view('landingpage.feature.index', compact('feature_settings'));
    }

    public function featuresettingstore(Request $request)
    {
        if ($request->feature_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'feature_name' => 'required',
                'feature_bold_name' => 'required',
                'feature_detail' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'feature_setting_enable' => $request->feature_setting_enable == 'on' ? 'on' : 'off',
                'feature_name' => $request->feature_name,
                'feature_bold_name' => $request->feature_bold_name,
                'feature_detail' => $request->feature_detail,
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Feature setting updated successfully.'));
        } else {
            $data = [
                'feature_setting_enable' => 'off',
            ];
            $arrEnv = [
                'feature_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Feature setting disabled.'));
        }
    }

    public function feature_create(Request $request)
    {
        return view('landingpage.feature.create');
    }

    public function feature_store(Request $request)
    {
        $setting_data = [
            "feature_setting" => UtilityFacades::getsettings('feature_setting'),
        ];
        $settings = $setting_data;
        $data = json_decode($settings['feature_setting'], true);
        if ($request->feature_image) {
            $allowedfileExtension = ['svg'];
            $feature_image = time() . "-feature_image." . $request->feature_image->getClientOriginalExtension();
            $extension =  $request->feature_image->extension();
            $check = in_array($extension, $allowedfileExtension);
            if ($check) {
                $image_name = $feature_image;
                $request->feature_image->storeAs('landingpage/feature', $image_name);
                $datas['feature_image'] = 'landingpage/feature/' . $image_name;
            } else {
                return redirect()->back()->with('failed', __('File Type Not Valid. Please Upload Svg File'));
            }
        }
        $datas['feature_name'] = $request->feature_name;
        $datas['feature_bold_name'] = $request->feature_bold_name;
        $datas['feature_detail'] = $request->feature_detail;
        $data[] = $datas;
        $data = json_encode($data);
        settings::updateOrCreate(
            ['key' => 'feature_setting'],
            ['value' => $data]
        );
        return redirect()->back()->with(['success' => 'Feature setting created successfully.']);
    }

    public function feature_edit($key)
    {
        $setting_data = [
            "feature_setting" => UtilityFacades::getsettings('feature_setting'),
        ];
        $settings = $setting_data;
        $features = json_decode($settings['feature_setting'], true);
        $feature = $features[$key];
        return view('landingpage.feature.edit', compact('feature', 'key'));
    }

    public function feature_update(Request $request, $key)
    {
        $setting_data = [
            "feature_setting" => UtilityFacades::getsettings('feature_setting'),
        ];
        $settings = $setting_data;
        $data = json_decode($settings['feature_setting'], true);
        if ($request->feature_image) {
            $allowedfileExtension = ['svg'];
            $feature_image = time() . "-feature_image." . $request->feature_image->getClientOriginalExtension();
            $extension =  $request->feature_image->extension();
            $check = in_array($extension, $allowedfileExtension);
            if ($check) {
                $image_name = $feature_image;
                $request->feature_image->storeAs('landingpage/feature', $image_name);
                $data[$key]['feature_image'] = 'landingpage/feature/' . $image_name;
            } else {
                return redirect()->back()->with('failed', __('File type not valid.'));
            }
        }
        $data[$key]['feature_name'] = $request->feature_name;
        $data[$key]['feature_bold_name'] = $request->feature_bold_name;
        $data[$key]['feature_detail'] = $request->feature_detail;
        $data = json_encode($data);
        settings::updateOrCreate(
            ['key' => 'feature_setting'],
            ['value' => $data]
        );
        return redirect()->back()->with(['success' => 'Feature setting updated successfully.']);
    }

    public function feature_delete($key)
    {
        $setting_data = [
            "feature_setting" => UtilityFacades::getsettings('feature_setting'),
        ];
        $pages = json_decode($setting_data['feature_setting'], true);
        unset($pages[$key]);
        settings::updateOrCreate(['key' =>  'feature_setting'], ['value' => $pages]);
        return redirect()->back()->with(['success' => 'Feature setting deleted successfully']);
    }

    public function startviewsetting(Request $request)
    {
        return view('landingpage.start_view_setting');
    }

    public function startviewsettingstore(Request $request)
    {
        if ($request->start_view_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'start_view_name' => 'required',
                'start_view_detail' => 'required',
                'start_view_link_name' => 'required',
                'start_view_link' => 'required',
                'start_view_image' => 'image|mimes:png,jpg,jpeg',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'start_view_setting_enable' => $request->start_view_setting_enable == 'on' ? 'on' : 'off',
                'start_view_name' => $request->start_view_name,
                'start_view_detail' => $request->start_view_detail,
                'start_view_link_name' => $request->start_view_link_name,
                'start_view_link' => $request->start_view_link,
            ];
            if ($request->start_view_image) {
                Storage::delete(UtilityFacades::getsettings('start_view_image'));
                $image_name = 'startview.' . $request->start_view_image->extension();
                $request->start_view_image->storeAs('landingpage', $image_name);
                $data['start_view_image'] = 'landingpage/' . $image_name;
            }
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Start view setting updated successfully.'));
        } else {
            $data = [
                'start_view_setting_enable' => 'off',
            ];
            $arrEnv = [
                'start_view_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Start view setting disabled.'));
        }
    }

    public function businessgrowthsetting(Request $request)
    {
        $setting_data = [
            "business_growth_setting" => UtilityFacades::getsettings('business_growth_setting'),
            "business_growth_view_setting" => UtilityFacades::getsettings('business_growth_view_setting'),
        ];
        $settings = $setting_data;
        $business_growth_settings = json_decode($settings['business_growth_setting'], true) ?? [];
        $business_growth_view_settings = json_decode($settings['business_growth_view_setting'], true);
        return view('landingpage.business_growth.index', compact('business_growth_settings', 'business_growth_view_settings'));
    }

    public function businessgrowthsettingstore(Request $request)
    {
        if ($request->business_growth_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'business_growth_name' => 'required',
                'business_growth_bold_name' => 'required',
                'business_growth_detail' => 'required',
                'business_growth_video' => 'mimes:mp4,avi,wmv,mov,webm',
                'business_growth_front_image' => 'image|mimes:png,jpg,jpeg',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'business_growth_setting_enable' => $request->business_growth_setting_enable == 'on' ? 'on' : 'off',
                'business_growth_name' => $request->business_growth_name,
                'business_growth_bold_name' => $request->business_growth_bold_name,
                'business_growth_detail' => $request->business_growth_detail,
            ];
            if ($request->business_growth_front_image) {
                Storage::delete(UtilityFacades::getsettings('business_growth_front_image'));
                $image_name = '10.' . $request->business_growth_front_image->extension();
                $request->business_growth_front_image->storeAs('landingpage/businessgrowth/', $image_name);
                $data['business_growth_front_image'] = 'landingpage/businessgrowth/' . $image_name;
            }
            if ($request->business_growth_video) {
                Storage::delete(UtilityFacades::getsettings('business_growth_video'));
                $filename = 'vedio.' . $request->business_growth_video->extension();
                $request->business_growth_video->storeAs('landingpage/businessgrowth/', $filename);
                $data['business_growth_video'] = $request->business_growth_video->storeAs('landingpage/businessgrowth/', $filename);
            }
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Business growth updated successfully.'));
        } else {
            $data = [
                'business_growth_setting_enable' => 'off',
            ];
            $arrEnv = [
                'business_growth_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Business growth disabled.'));
        }
    }

    public function business_growth_create(Request $request)
    {
        return view('landingpage.business_growth.create');
    }

    public function business_growth_store(Request $request)
    {
        $setting_data = [
            "business_growth_setting" => UtilityFacades::getsettings('business_growth_setting'),
        ];
        $settings = $setting_data;
        $data = json_decode($settings['business_growth_setting'], true);

        $datas['business_growth_title'] = $request->business_growth_title;
        $data[] = $datas;
        $data = json_encode($data);
        settings::updateOrCreate(
            ['key' => 'business_growth_setting'],
            ['value' => $data]
        );
        return redirect()->back()->with(['success' => 'Business growth setting created successfully.']);
    }

    public function business_growth_edit($key)
    {
        $setting_data = [
            "business_growth_setting" => UtilityFacades::getsettings('business_growth_setting'),
        ];
        $settings = $setting_data;
        $business_growth_settings = json_decode($settings['business_growth_setting'], true);
        $business_growth_setting = $business_growth_settings[$key];
        return view('landingpage.business_growth.edit', compact('business_growth_setting', 'key'));
    }

    public function business_growth_update(Request $request, $key)
    {
        $setting_data = [
            "business_growth_setting" => UtilityFacades::getsettings('business_growth_setting'),
        ];
        $settings = $setting_data;
        $data = json_decode($settings['business_growth_setting'], true);

        $data[$key]['business_growth_title'] = $request->business_growth_title;
        $data = json_encode($data);
        settings::updateOrCreate(
            ['key' => 'business_growth_setting'],
            ['value' => $data]
        );
        return redirect()->back()->with(['success' => 'Business growth setting updated successfully.']);
    }

    public function business_growth_delete($key)
    {
        $setting_data = [
            "business_growth_setting" => UtilityFacades::getsettings('business_growth_setting'),
        ];
        $pages = json_decode($setting_data['business_growth_setting'], true);
        unset($pages[$key]);
        settings::updateOrCreate(['key' =>  'business_growth_setting'], ['value' => $pages]);
        return redirect()->back()->with(['success' => 'Business growth setting deleted successfully']);
    }

    public function business_growth_view_create(Request $request)
    {
        return view('landingpage.business_growth.business_growth_view_create');
    }

    public function business_growth_view_store(Request $request)
    {
        $setting_data = [
            "business_growth_view_setting" => UtilityFacades::getsettings('business_growth_view_setting'),
        ];
        $settings = $setting_data;
        $data = json_decode($settings['business_growth_view_setting'], true);

        $datas['business_growth_view_name'] = $request->business_growth_view_name;
        $datas['business_growth_view_amount'] = $request->business_growth_view_amount;
        $data[] = $datas;
        $data = json_encode($data);
        settings::updateOrCreate(
            ['key' => 'business_growth_view_setting'],
            ['value' => $data]
        );
        return redirect()->back()->with(['success' => 'Business growth view setting created successfully.']);
    }

    public function business_growth_view_edit($key)
    {
        $setting_data = [
            "business_growth_view_setting" => UtilityFacades::getsettings('business_growth_view_setting'),
        ];
        $settings = $setting_data;
        $business_growth_view_settings = json_decode($settings['business_growth_view_setting'], true);
        $business_growth_view_setting = $business_growth_view_settings[$key];
        return view('landingpage.business_growth.business_growth_view_edit', compact('business_growth_view_setting', 'key'));
    }

    public function business_growth_view_update(Request $request, $key)
    {
        $setting_data = [
            "business_growth_view_setting" => UtilityFacades::getsettings('business_growth_view_setting'),
        ];
        $settings = $setting_data;
        $data = json_decode($settings['business_growth_view_setting'], true);

        $data[$key]['business_growth_view_name'] = $request->business_growth_view_name;
        $data[$key]['business_growth_view_amount'] = $request->business_growth_view_amount;
        $data = json_encode($data);
        settings::updateOrCreate(
            ['key' => 'business_growth_view_setting'],
            ['value' => $data]
        );
        return redirect()->back()->with(['success' => 'Business growth view setting updated successfully.']);
    }

    public function business_growth_view_delete($key)
    {
        $setting_data = [
            "business_growth_view_setting" => UtilityFacades::getsettings('business_growth_view_setting'),
        ];
        $pages = json_decode($setting_data['business_growth_view_setting'], true);
        unset($pages[$key]);
        settings::updateOrCreate(['key' =>  'business_growth_view_setting'], ['value' => $pages]);
        return redirect()->back()->with(['success' => 'Business growth view setting deleted successfully']);
    }

    public function contactussetting(Request $request)
    {
        return view('landingpage.contactus_setting');
    }

    public function contactussettingstore(Request $request)
    {
        if ($request->contactus_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'contact_email' => 'required|email',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }

            $data = [
                'contactus_setting_enable' => $request->contactus_setting_enable == 'on' ? 'on' : 'off',
                'contact_email' => $request->contact_email,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Contactus setting updated successfully.'));
        } else {
            $data = [
                'contactus_setting_enable' => 'off',
            ];
            $arrEnv = [
                'contactus_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Contactus setting disabled.'));
        }
    }

    public function formsetting(Request $request)
    {
        return view('landingpage.form_setting');
    }

    public function formsettingstore(Request $request)
    {
        if ($request->form_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'form_name' => 'required',
                'form_detail' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }

            $data = [
                'form_setting_enable' => $request->form_setting_enable == 'on' ? 'on' : 'off',
                'form_name' => $request->form_name,
                'form_detail' => $request->form_detail,
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('form setting updated successfully.'));
        } else {
            $data = [
                'form_setting_enable' => 'off',
            ];
            $arrEnv = [
                'form_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('failed', __('form setting disabled.'));
        }
    }

    public function blogsetting(Request $request)
    {
        return view('landingpage.blog_setting');
    }

    public function blogsettingstore(Request $request)
    {
        if ($request->blog_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'blog_name' => 'required',
                'blog_detail' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }

            $data = [
                'blog_setting_enable' => $request->blog_setting_enable == 'on' ? 'on' : 'off',
                'blog_name' => $request->blog_name,
                'blog_detail' => $request->blog_detail,
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('blog setting updated successfully.'));
        } else {
            $data = [
                'blog_setting_enable' => 'off',
            ];
            $arrEnv = [
                'blog_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('failed', __('blog setting disabled.'));
        }
    }

    public function headerSetting()
    {
        $header_settings = HeaderSetting::all();
        return view('landingpage.header.index', compact('header_settings'));
    }

    public function headerMenuCreate()
    {
        $pages = PageSetting::pluck('title', 'id');
        return view('landingpage.header.create', compact('pages'));
    }

    public function headerMenuStore(Request $request)
    {
        $page = PageSetting::where('id', $request->page_id)->first();
        $validator = \Validator::make($request->all(), [
            'page_id' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }

        $headerMenu               = new HeaderSetting();
        $headerMenu->menu         = $page->title;
        $headerMenu->page_id      = $request->page_id;
        $headerMenu->save();
        return redirect()->back()->with('success', 'Header Menu created successfully');
    }

    public function headerMenuEdit($id)
    {
        $headerMenuEdit = HeaderSetting::find($id);
        $pages = PageSetting::pluck('title', 'id');
        return view('landingpage.header.edit', compact('headerMenuEdit', 'pages'));
    }


    public function headerMenuUpdate(Request $request, $id)
    {
        $page = PageSetting::where('id', $request->page_id)->first();
        $validator = \Validator::make($request->all(), [
            'page_id' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $headerMenu = HeaderSetting::where('id', $id)->first();
        $headerMenu->menu = $page->title;
        $headerMenu->page_id      = $request->page_id;
        $headerMenu->update();
        return redirect()->back()->with('success', 'Header Menu updated successfully');
    }

    public function headerMenuDelete(Request $request, $id)
    {

        $headerMenu = HeaderSetting::where('id', $id)->first();
        $headerMenu->delete();
        return redirect()->back()->with('success', 'Footer Menu Updated Successfully');
    }




    public function footersetting(Request $request)
    {

        $footer_main_menus = FooterSetting::where('parent_id', 0)->get();
        $footer_sub_menus = FooterSetting::where('parent_id', '!=', 0)->get();
        return view('landingpage.footer.index', compact('footer_main_menus', 'footer_sub_menus'));
    }

    public function footersettingstore(Request $request)
    {
        if ($request->footer_setting_enable == 'on') {
            $validator = \Validator::make($request->all(), [
                'footer_description' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
            $data = [
                'footer_setting_enable' => $request->footer_setting_enable == 'on' ? 'on' : 'off',
                'footer_description' => $request->footer_description,
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Footer setting updated successfully.'));
        } else {
            $data = [
                'footer_setting_enable' => 'off',
            ];
            $arrEnv = [
                'footer_setting_enable' => 'off',
            ];
            $this->updateSettings($data);
            return redirect()->back()->with('success', __('Footer setting disabled.'));
        }
    }

    public function footer_main_menu_create()
    {
        return view('landingpage.footer.create');
    }

    public function footer_main_menu_store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'menu' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }

        $footer_main_menu           = new FooterSetting();
        $footer_main_menu->menu = $request->menu;
        $footer_main_menu->parent_id     = 0;
        $footer_main_menu->save();

        return redirect()->back()->with('success', 'Footer Main Menu created successfully');
    }

    public function footer_main_menu_edit($id)
    {
        $footer_main_menu_edit = FooterSetting::where('id', $id)->first();
        return view('landingpage.footer.edit', compact('footer_main_menu_edit'));
    }

    public function footer_main_menu_update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'menu' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }

        $footer_main_menu = FooterSetting::where('id', $id)->first();
        $footer_main_menu->menu = $request->menu;
        $footer_main_menu->parent_id = 0;
        $footer_main_menu->save();

        return redirect()->back()->with('success', 'Footer Main Menu updated successfully');
    }

    public function footer_main_menu_delete($id)
    {
        $footer_main_menu = FooterSetting::where('id', $id)->first();
        $footer_main_menu->delete();
        return redirect()->back()->with('success', 'Footer Menu Updated Successfully');
    }

    public function footer_sub_menu_create()
    {
        $pages = PageSetting::pluck('title', 'id');
        $footers = FooterSetting::where('parent_id', 0)->pluck('menu', 'id');
        return view('landingpage.footer.create_sub_menu', compact('pages', 'footers'));
    }

    public function footer_sub_menu_store(Request $request)
    {
        $pages = PageSetting::where('id', $request->page_id)->first();
        // $validator = \Validator::make($request->all(), [
        //     'type'=>'required',
        // ]);
        // if ($validator->fails()) {
        //     $messages = $validator->errors();
        //     return redirect()->back()->with('errors', $messages->first());
        // }
        $footer_sub_menu             = new FooterSetting();
        $footer_sub_menu->menu       = $pages->title;
        $footer_sub_menu->page_id    = $request->page_id;
        $footer_sub_menu->parent_id  = $request->parent_id;
        $footer_sub_menu->save();
        return redirect()->route('landing.footer.index')->with('success', 'Footer sub menu created successfully');
    }

    public function footer_sub_menu_edit($id)
    {
        $footer_page = FooterSetting::find($id);
        $pages = PageSetting::pluck('title', 'id');
        $footer = FooterSetting::where('parent_id', 0)->pluck('menu', 'id');
        $footer_menu = FooterSetting::where('id', $footer_page->parent_id)->pluck('menu', 'id');
        return view('landingpage.footer.edit_sub_menu', compact('pages', 'footer_page', 'footer', 'footer_menu'));
    }

    public function footer_sub_menu_update(Request $request, $id)
    {
        $pages = PageSetting::where('id', $request->page_id)->first();
        $validator = \Validator::make($request->all(), [
            //'type'=>'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        $footer_sub_menu = FooterSetting::where('id', $id)->first();
        $footer_sub_menu->menu = $pages->title;
        $footer_sub_menu->page_id    = $request->page_id;
        $footer_sub_menu->parent_id = $request->parent_id;
        $footer_sub_menu->save();
        return redirect()->route('landing.footer.index')->with('success', 'Footer sub menu updated successfully');
    }

    public function footer_sub_menu_delete($id)
    {
        $footer_sub_menu = FooterSetting::where('id', $id)->first();
        $footer_sub_menu->delete();
        return redirect()->back()->with('success', 'Footer Sub Menu Updated Successfully');
    }

    public function pagesView($slug, $lang = 'en')
    {
        \App::setLocale($lang);
        $page_footer = FooterSetting::where('slug', $slug)->first();
        $footer_main_menus = FooterSetting::where('parent_id', 0)->get();
        return view('landingpage.footer.pagesView', compact('page_footer', 'footer_main_menus', 'lang', 'slug'));
    }

    public function loginSetting()
    {
        return view('landingpage.login_page_setting');
    }

    public function loginSettingStore(Request $request)
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
            $request->login_image->storeAs('landingpage', $login_image_name);
            $data['login_image'] = 'landingpage/' . $login_image_name;
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Login page setting updated successfully.'));
    }

    public function captchaSetting()
    {
        return view('landingpage.captcha_setting');
    }

    public function captchaSettingStore(Request $request)
    {
        if ($request->contact_us_recaptcha_status == 'on' || $request->login_recaptcha_status == 'on') {
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

    public function pageBackground(Request $request)
    {
        return view('landingpage.background_image');
    }

    public function pageBackgroundstore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'background_image' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return redirect()->back()->with('errors', $messages->first());
        }
        if ($request->background_image) {
            Storage::delete(UtilityFacades::keysettings('background_image', 1));
            $image_name = 'background.' . $request->background_image->extension();
            $request->background_image->storeAs('landingpage/', $image_name);
            $data['background_image'] = 'landingpage/' . $image_name;
        }
        $this->updateSettings($data);
        return redirect()->back()->with('success', __('Background setting updated successfully.'));
    }
}
