<?php

namespace App\Http\Controllers;

use App\Facades\UtilityFacades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;
use App\Mail\ConatctMail;
use App\Models\Faq;
use App\Models\FooterSetting;
use App\Models\NotificationsSetting;
use App\Models\User;
use App\Notifications\NewEnquiryDetails;

class FrontController extends Controller
{
    public function contactus($lang = 'en')
    {
        \App::setLocale($lang);
        $footer_main_menus = FooterSetting::where('parent_id' , 0)->get();
        return view('contactus', compact('lang' , 'footer_main_menus'));
    }

    public function termsandconditions($lang = 'en')
    {
        \App::setLocale($lang);
        return view('termsandconditions', compact('lang'));
    }

    public function privacypolicy($lang = 'en')
    {
        \App::setLocale($lang);
        return view('privacypolicy', compact('lang'));
    }

    public function faqs($lang = 'en')
    {
        \App::setLocale($lang);
        $faqs = Faq::orderBy('order')->get();
        return view('faqs', compact('lang','faqs'));
    }

    public function contact_mail(Request $request)
    {
        $user = User::where('type', '=', 'Admin')->first();
        $notify = NotificationsSetting::where('title', 'new enquiry details')->first();
        if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            if (isset($notify)) {
                if ($notify->notify = '1') {
                    $user->notify(new NewEnquiryDetails($request));
                }
            }
        }

        if (UtilityFacades::getsettings('contact_us_recaptcha_status') == '1') {
            $validator = \Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('errors', $messages->first());
            }
        }

        if (UtilityFacades::getsettings('email_setting_enable') == 'on' && UtilityFacades::getsettings('contact_email') != '') {
            if (MailTemplate::where('mailable', ConatctMail::class)->first()) {
                try {
                    if ($request) {
                        $details = $request->all();
                        Mail::to(UtilityFacades::getsettings('contact_email'))->send(new ConatctMail($request->all()));
                    } else {
                        return redirect()->back()->with('failed', __('Please check Recaptch.'));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }

                return redirect()->back()->with('success', 'Email sent successfully.');
            }
        }
        else{
            return redirect()->back()->with('status' , __('please turn on email enable button'));
        }

        return redirect()->back()->with('success', __('enquiry details send successfully'));
    }
}
