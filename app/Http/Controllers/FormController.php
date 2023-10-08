<?php

namespace App\Http\Controllers;

use App\DataTables\FormsDataTable;
use App\Facades\UtilityFacades;
use App\Mail\FormSubmitEmail;
use App\Mail\Thanksmail;
use App\Models\AssignFormsRoles;
use App\Models\AssignFormsUsers;
use App\Models\DashboardWidget;
use App\Models\Form;
use App\Models\FormComments;
use App\Models\FormCommentsReply;
use App\Models\FormIntegrationSetting;
use App\Models\FormValue;
use App\Models\NotificationsSetting;
use App\Models\settings;
use App\Models\User;
use App\Models\UserForm;
use App\Notifications\CreateForm;
use App\Notifications\NewSurveyDetails;
use App\Notifications\SlackNotification;
use App\Rules\CommaSeparatedEmails;
use Aws\Sns\SnsClient;
use BulkGate\Message\Connection;
use BulkGate\Sms\Message;
use BulkGate\Sms\Sender;
use Carbon\Carbon;
use Exception;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Stripe\Charge;
use Stripe\Stripe as StripeStripe;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use mediaburst\ClockworkSMS\Clockwork;
use Spatie\MailTemplates\Models\MailTemplate;
use Twilio\Rest\Client;

class FormController extends Controller
{
    public function index(FormsDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-form')) {
            if (\Auth::user()->forms_grid_view == 1) {
                return redirect()->route('grid.form.view', 'view');
            }
            return $dataTable->render('form.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-form')) {
            $users = User::where('id', '!=', 1)->pluck('name', 'id');
            $roles = Role::where('name', '!=', 'Super Admin')->orwhere('name', Auth::user()->type)->pluck('name', 'id');
            return view('form.create', compact('roles', 'users'));
        } else {
            return response()->json(['failed' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-form')) {
            $rules = [
                'title' => 'required',
            ];

            if (isset($request->set_end_date) && $request->set_end_date == 'on') {
                $rules = [
                    'set_end_date' => 'required',
                    'set_end_date_time' => 'required'
                ];
            }
            $ccemails = implode(',', $request->ccemail);
            $bccemails = implode(',', $request->bccemail);
            if ($ccemails) {
                $request->validate([
                    'ccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            if ($bccemails) {
                $request->validate([
                    'bccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            $request->validate([
                'email' => ['nullable', new CommaSeparatedEmails],
            ]);

            $userSchema = User::where('type', '=', 'Admin')->first();
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('failed', $messages->first());
            }
            $filename = '';
            if (request()->file('form_logo')) {
                $allowedfileExtension = ['jpeg', 'jpg', 'png'];
                $file = $request->file('form_logo');
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if ($check) {
                    $filename = $file->store('form_logo');
                } else {
                    return redirect()->route('forms.index')->with('failed', __('File type not valid.'));
                }
            }
            if (isset($request->email) and !empty($request->email)) {
                $emails = implode(',', $request->email);
            }
            if (isset($request->ccemail) and !empty($request->ccemail)) {
                $ccemails = implode(',', $request->ccemail);
            }
            if (isset($request->bccemail) and !empty($request->bccemail)) {
                $bccemails = implode(',', $request->bccemail);
            }
            if (isset($request->set_end_date) && $request->set_end_date == 1) {
                $set_end_date = 1;
            } else {
                $set_end_date = 0;
            }
            if (isset($request->set_end_date_time)) {
                $set_end_date_time = Carbon::parse($request->set_end_date_time)->toDateTimeString();
            } else {
                $set_end_date_time = null;
            }

            $form = new Form();
            $form->title  = $request->title;
            $form->logo  = $filename;
            $form->description  = $request->form_description;
            $form->email  = $emails;
            $form->bccemail  = $bccemails;
            $form->ccemail  = $bccemails;
            $form->allow_comments  = ($request->allow_comments == 'on') ? '1' : '0';
            $form->allow_share_section  = ($request->allow_share_section == 'on') ? '1' : '0';
            $form->json  = '';
            $form->success_msg  = $request->success_msg;
            $form->thanks_msg  = $request->thanks_msg;
            $form->set_end_date = $set_end_date;
            $form->set_end_date_time = $set_end_date_time;
            $form->created_by  = Auth::user()->id;
            $form->assign_type  = $request->assign_type;
            $form->save();
            if ($request->assign_type == 'role') {
                $form->assignRole($request->roles);
            }
            if ($request->assign_type == 'user') {
                $form->assignUser($request->users);
            }
            $form->assignFormRoles($request->roles);


            $notify = NotificationsSetting::where('title', 'From Create')->first();
            if (isset($notify)) {
                if ($notify->notify == 1) {
                    if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                        if (isset($notify)) {
                            if ($notify &&  $notify->notify == '1') {
                                $userSchema->notify(new CreateForm($form));
                            }
                        }
                    }
                }
            }
            return redirect()->route('forms.index')->with('success', __('Form created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $usr = \Auth::user();
        $user_role = $usr->roles->first()->id;
        $formallowededit = UserForm::where('role_id', $user_role)->where('form_id', $id)->count();
        if (\Auth::user()->can('edit-form') && $usr->type == 'Admin') {
            $form = Form::find($id);
            $next = Form::where('id', '>', $form->id)->first();
            $previous = Form::where('id', '<', $form->id)->orderBy('id', 'desc')->first();
            $form_roles = $form->Roles->pluck('id')->toArray();
            $roles = Role::where('name', '!=', 'Super Admin')->pluck('name', 'id');
            $formRole = $form->assignedroles->pluck('id')->toArray();
            $form_role = Role::pluck('name', 'id');
            $formUser =  $form->assignedusers->pluck('id')->toArray();
            $form_user = User::where('id', '!=', 1)->pluck('name', 'id');
            return view('form.edit', compact('form', 'form_roles', 'roles', 'form_user', 'formUser', 'formRole', 'form_role', 'next', 'previous'));
        } else {
            if (\Auth::user()->can('edit-form') && $formallowededit > 0) {
                $form = Form::find($id);
                $next = Form::where('id', '>', $form->id)->first();
                $previous = Form::where('id', '<', $form->id)->orderBy('id', 'desc')->first();
                $form_roles = $form->Roles->pluck('id')->toArray();
                $roles = Role::pluck('name', 'id');
                $formRole = $form->assignedroles->pluck('id')->toArray();
                $form_role = Role::pluck('name', 'id');
                $formUser =  $form->assignedusers->pluck('id')->toArray();
                $form_user = User::where('id', '!=', 1)->pluck('name', 'id');
                return view('form.edit', compact('form', 'form_roles', 'form_role', 'form_user', 'formUser', 'formRole', 'next', 'previous'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        }
    }

    public function update(Request $request, Form $form)
    {
        if (\Auth::user()->can('edit-form')) {
            $rules = [
                'title' => 'required',
            ];
            $ccemails = implode(',', $request->ccemail);
            $bccemails = implode(',', $request->bccemail);
            if ($ccemails) {
                $request->validate([
                    'ccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            if ($bccemails) {
                $request->validate([
                    'bccemail' => ['nullable', new CommaSeparatedEmails],
                ]);
            }
            $validator = Validator::make($request->all(), $rules);
            $request->validate([
                'email' => ['nullable', new CommaSeparatedEmails],
            ]);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('failed', $messages->first());
            }
            $filename = $form->logo;
            $emails = $form->logo;
            if (request()->file('form_logo')) {
                $allowedfileExtension = ['jpeg', 'jpg', 'png'];
                $file = $request->file('form_logo');
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if ($check) {
                    $filename = $file->store('form_logo');
                } else {
                    return redirect()->route('forms.index')->with('failed', __('File type not valid.'));
                }
            }
            if (isset($request->email) and !empty($request->email)) {
                $emails = implode(',', $request->email);
            }
            if (isset($request->ccemail) and !empty($request->ccemail)) {
                $ccemails = implode(',', $request->ccemail);
            }
            if (isset($request->bccemail) and !empty($request->bccemail)) {
                $bccemails = implode(',', $request->bccemail);
            }

            if ($request->set_end_date == 'on') {
                $set_end_date = 1;
            } else {
                $set_end_date = 0;
            }
            if (isset($request->set_end_date_time)) {
                $set_end_date_time = Carbon::parse($request->set_end_date_time)->toDateTimeString();;
            } else {
                $set_end_date_time = null;
            }


            $form->title = $request->title;
            $form->success_msg = $request->success_msg;
            $form->thanks_msg = $request->thanks_msg;
            $form->logo = $filename;
            $form->description  = $request->form_description;
            $form->email = $emails;
            $form->ccemail = $ccemails;
            $form->bccemail = $bccemails;
            $form->allow_comments = ($request->allow_comments == 'on') ? '1' : '0';
            $form->allow_share_section = ($request->allow_share_section == 'on') ? '1' : '0';
            $form->set_end_date = $set_end_date;
            $form->set_end_date_time = $set_end_date_time;
            $form->created_by  = Auth::user()->id;
            $form->assign_type  = $request->assign_type;
            $form->save();
            if ($request->assign_type == 'role') {
                $id = $form->id;
                AssignFormsUsers::where('form_id', $id)->delete();
                $form->assignRole($request->roles);
            }
            if ($request->assign_type == 'user') {
                $id = $form->id;
                AssignFormsRoles::where('form_id', $id)->delete();
                $form->assignUser($request->users);
            }
            if ($request->assign_type == 'public') {
                $id = $form->id;
                AssignFormsRoles::where('form_id', $id)->delete();
                AssignFormsUsers::where('form_id', $id)->delete();
            }
            $form->assignFormRoles($request->roles);
            return redirect()->route('forms.index')->with('success', __('Form updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy(Form $form)
    {
        if (\Auth::user()->can('delete-form')) {
            $id = $form->id;
            $comments = FormComments::where('form_id', $id)->get();
            $comments_reply = FormCommentsReply::where('form_id', $id)->get();
            DashboardWidget::where('form_id', $id)->delete();
            AssignFormsRoles::where('form_id', $id)->delete();
            AssignFormsUsers::where('form_id', $id)->delete();
            foreach ($comments as $allcomments) {
                $commentsids = $allcomments->id;
                $commentsall = FormComments::find($commentsids);
                if ($commentsall) {
                    $commentsall->delete();
                }
            }
            foreach ($comments_reply as $comments_reply_all) {
                $comments_reply_ids = $comments_reply_all->id;
                $reply =  FormCommentsReply::find($comments_reply_ids);
                if ($reply) {
                    $reply->delete();
                }
            }
            $form->delete();
            return redirect()->back()->with('success', __('Form deleted successfully'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function grid_view($slug = '')
    {
        $usr = \Auth::user();
        $usr->forms_grid_view = ($slug) ? 1 : 0;
        $usr->save();
        if ($usr->forms_grid_view == 0) {
            return redirect()->route('forms.index');
        }
        $role_id = $usr->roles->first()->id;
        $user_id = $usr->id;
        if ($usr->type == 'Admin') {
            $forms = Form::all();
        } else {
            $forms = Form::where(function ($query) use ($role_id, $user_id) {
                $query->whereIn('id', function ($query1) use ($role_id) {
                    $query1->select('form_id')->from('assign_forms_roles')->where('role_id', $role_id);
                })->OrWhereIn('id', function ($query1) use ($user_id) {
                    $query1->select('form_id')->from('assign_forms_users')->where('user_id', $user_id);
                });
            })->get();
        }

        return view('form.grid_view', compact('forms'));
    }

    public function design($id)
    {
        if (\Auth::user()->can('design-form')) {
            $form = Form::find($id);
            if ($form) {
                return view('form.design', compact('form'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function designtest($id)
    {
        if (\Auth::user()->can('design-form')) {
            $form = Form::find($id);
            if ($form) {
                return view('form.test_design', compact('form'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function designUpdate(Request $request, $id)
    {
        if (\Auth::user()->can('design-form')) {
            $form = Form::find($id);
            if ($form) {
                $form->json = $request->json;
                $field_name = json_decode($request->json);
                $arr = [];
                foreach ($field_name[0] as $k => $fields) {
                    if ($fields->type == "header" || $fields->type == "paragraph") {
                        $arr[$k] = $fields->type;
                    } else {
                        $arr[$k] = $fields->name;
                    }
                }
                $value = DashboardWidget::where('form_id', $form->id)->pluck('field_name', 'id');
                foreach ($value  as $key => $v) {
                    if (!in_array($v, $arr)) {
                        DashboardWidget::find($key)->delete();
                    }
                }
                $form->save();
                return redirect()->route('forms.index')->with('success', __('Form updated successfully.'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function fill($id)
    {
        if (\Auth::user()->can('fill-form')) {
            $form = Form::find($id);
            $form_value = null;
            if ($form) {
                $array = $form->getFormArray();
                return view('form.fill', compact('form', 'form_value', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Form not found'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function publicFill($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $form = Form::find($id);
            $today_date = Carbon::now()->toDateTimeString();
            $form_value = null;
            if ($form) {
                if ($form->set_end_date != '0') {
                    if ($form->set_end_date_time && $form->set_end_date_time < $today_date) {
                        abort('404');
                    }
                }
                $array = $form->getFormArray();
                return view('form.public_fill', compact('form', 'form_value', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function qrCode($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $form = Form::find($id);
        $view =   view('form.public_fill_qr', compact('form'));
        return ['html' => $view->render()];
    }

    public function fillStore(Request $request, $id)
    {
        // dd($request->all());

        // if (UtilityFacades::getsettings('captcha_enable') == 'on') {
        //     if (UtilityFacades::keysettings('captcha') == 'hcaptcha') {
        //         if (empty($_POST['h-captcha-response'])) {
        //             if (isset($request->ajax)) {
        //                 return response()->json(['is_success' => false, 'message' => __('Please check hcaptcha.')], 200);
        //             }
        //             // else {
        //             //     return redirect()->back()->with('failed', __('Please check hcaptcha.'));
        //             // }
        //         }
        //     }
        //     if (UtilityFacades::keysettings('captcha') == 'recaptcha') {
        //         // if (empty($_POST['g-recaptcha-response'])) {
        //         //     if (isset($request->ajax)) {
        //         //         return response()->json(['is_success' => false, 'message' => __('Please check recaptcha.')], 200);
        //         //     }
        //         //     // else {
        //         //     //     return redirect()->back()->with('failed', __('Please check recaptcha.'));
        //         //      // }
        //         // }

        //         $request->validate([
        //             'g-recaptcha-response' => 'accepted',
        //             // Other validation rules
        //         ], [
        //             'g-recaptcha-response.accepted' => __('Please check the reCAPTCHA.'),
        //             // Other custom error messages
        //         ]);


        //     }
        // }

        if (UtilityFacades::getsettings('captcha_enable') == 'on' && $request->input('g-recaptcha-response') == '') {
            $request->validate([
                'g-recaptcha-response' => 'required',
            ]);
            // return back()->with('failed',  __('Please check recaptcha.'))->withInput();
        } else {
            $form = Form::find($id);
            if ($form) {
                $client_emails = [];
                if ($request->form_value_id) {
                    $form_value = FormValue::find($request->form_value_id);
                    $array = json_decode($form_value->json);
                } else {
                    $array = $form->getFormArray();
                }
                foreach ($array as  &$rows) {
                    foreach ($rows as &$row) {
                        if ($row->type == 'checkbox-group') {
                            foreach ($row->values as &$checkboxvalue) {
                                if (is_array($request->{$row->name}) && in_array($checkboxvalue->value, $request->{$row->name})) {
                                    $checkboxvalue->selected = 1;
                                } else {
                                    if (isset($checkboxvalue->selected)) {
                                        unset($checkboxvalue->selected);
                                    }
                                }
                            }
                        } elseif ($row->type == 'file') {
                            if ($row->subtype == "fineuploader") {
                                $file_size = number_format($row->max_file_size_mb / 1073742848, 2);
                                $file_limit = $row->max_file_size_mb / 1024;
                                if ($file_size < $file_limit) {
                                    $values = [];
                                    $value = explode(',', $request->input($row->name));
                                    foreach ($value as $file) {
                                        $values[] = $file;
                                    }
                                    $row->value = $values;
                                } else {
                                    return response()->json(['is_success' => false, 'message' => __("Please upload maximum $row->max_file_size_mb MB file size.")], 200);
                                }
                            } else {
                                if ($row->file_extention == 'pdf') {
                                    $allowed_file_Extension = ['pdf', 'pdfa', 'fdf', 'xdp', 'xfa', 'pdx', 'pdp', 'pdfxml', 'pdxox'];
                                } else if ($row->file_extention == 'image') {
                                    $allowed_file_Extension = ['jpeg', 'jpg', 'png'];
                                } else if ($row->file_extention == 'excel') {
                                    $allowed_file_Extension = ['xlsx', 'csv', 'xlsm', 'xltx', 'xlsb', 'xltm', 'xlw'];
                                }
                                $requiredextention = implode(',', $allowed_file_Extension);
                                $file_size = number_format($row->max_file_size_mb / 1073742848, 2);
                                $file_limit = $row->max_file_size_mb / 1024;
                                if ($file_size < $file_limit) {
                                    if ($row->multiple) {
                                        if ($request->hasFile($row->name)) {
                                            $values = [];
                                            $files = $request->file($row->name);
                                            foreach ($files as $file) {
                                                $extension = $file->getClientOriginalExtension();
                                                $check = in_array($extension, $allowed_file_Extension);
                                                if ($check) {
                                                    if ($extension == 'csv') {
                                                        $name = \Str::random(40) . '.' . $extension;
                                                        $file->move(storage_path() . '/app/form_values/' . $form->id, $name);
                                                        $values[] = 'form_values/' . $form->id . '/' . $name;
                                                    } else {
                                                        $path = Storage::path("form_values/$form->id");
                                                        $file_name = $file->store('form_values/' . $form->id);
                                                        if (!file_exists($path)) {
                                                            mkdir($path, 0777, true);
                                                            chmod($path, 0777);
                                                        }
                                                        if (!file_exists(Storage::path($file_name))) {
                                                            mkdir(Storage::path($file_name), 0777, true);
                                                            chmod(Storage::path($file_name), 0777);
                                                        }
                                                        $values[] = $file_name;
                                                    }
                                                } else {
                                                    if (isset($request->ajax)) {
                                                        return response()->json(['is_success' => false, 'message' => __("Invalid file type, Please upload $requiredextention files")], 200);
                                                    } else {
                                                        return redirect()->back()->with('failed', __("Invalid file type, please upload $requiredextention files."));
                                                    }
                                                }
                                            }
                                            $row->value = $values;
                                        }
                                    } else {
                                        if ($request->hasFile($row->name)) {
                                            $values = '';
                                            $file = $request->file($row->name);
                                            $extension = $file->getClientOriginalExtension();
                                            $check = in_array($extension, $allowed_file_Extension);
                                            if ($check) {
                                                if ($extension == 'csv') {
                                                    $name = \Str::random(40) . '.' . $extension;
                                                    $file->move(storage_path() . '/app/form_values/' . $form->id, $name);
                                                    $values = 'form_values/' . $form->id . '/' . $name;
                                                    chmod("$values", 0777);
                                                } else {
                                                    $path = Storage::path("form_values/$form->id");
                                                    $file_name = $file->store('form_values/' . $form->id);
                                                    if (!file_exists($path)) {
                                                        mkdir($path, 0777, true);
                                                        chmod($path, 0777);
                                                    }
                                                    if (!file_exists(Storage::path($file_name))) {
                                                        mkdir(Storage::path($file_name), 0777, true);
                                                        chmod(Storage::path($file_name), 0777);
                                                    }
                                                    $values = $file_name;
                                                }
                                            } else {
                                                if (isset($request->ajax)) {
                                                    return response()->json(['is_success' => false, 'message' => __("Invalid file type, Please upload $requiredextention files")], 200);
                                                } else {
                                                    return redirect()->back()->with('failed', __("Invalid file type, please upload $requiredextention files."));
                                                }
                                            }
                                            $row->value = $values;
                                        }
                                    }
                                } else {
                                    return response()->json(['is_success' => false, 'message' => __("Please upload maximum $row->max_file_size_mb MB file size.")], 200);
                                }
                            }
                        } elseif ($row->type == 'radio-group') {
                            foreach ($row->values as &$radiovalue) {
                                if ($radiovalue->value == $request->{$row->name}) {
                                    $radiovalue->selected = 1;
                                } else {
                                    if (isset($radiovalue->selected)) {
                                        unset($radiovalue->selected);
                                    }
                                }
                            }
                        } elseif ($row->type == 'autocomplete') {
                            if (isset($row->multiple)) {
                                foreach ($row->values as &$autocompletevalue) {
                                    if (is_array($request->{$row->name}) && in_array($autocompletevalue->value, $request->{$row->name})) {
                                        $autocompletevalue->selected = 1;
                                    } else {
                                        if (isset($autocompletevalue->selected)) {
                                            unset($autocompletevalue->selected);
                                        }
                                    }
                                }
                            } else {
                                foreach ($row->values as &$autocompletevalue) {
                                    if ($autocompletevalue->value == $request->autocomplete) {
                                        $autocompletevalue->selected = 1;
                                    } else {
                                        if (isset($autocompletevalue->selected)) {
                                            unset($autocompletevalue->selected);
                                        }
                                        $row->value = $request->autocomplete;
                                    }
                                }
                            }
                        } elseif ($row->type == 'select') {
                            if ($row->multiple) {
                                foreach ($row->values as &$selectvalue) {
                                    if (is_array($request->{$row->name}) && in_array($selectvalue->value, $request->{$row->name})) {
                                        $selectvalue->selected = 1;
                                    } else {
                                        if (isset($selectvalue->selected)) {
                                            unset($selectvalue->selected);
                                        }
                                    }
                                }
                            } else {
                                foreach ($row->values as &$selectvalue) {
                                    if ($selectvalue->value == $request->{$row->name}) {
                                        $selectvalue->selected = 1;
                                    } else {
                                        if (isset($selectvalue->selected)) {
                                            unset($selectvalue->selected);
                                        }
                                    }
                                }
                            }
                        } elseif ($row->type == 'date') {
                            $row->value = $request->{$row->name};
                        } elseif ($row->type == 'number') {
                            $row->value = $request->{$row->name};
                        } elseif ($row->type == 'textarea') {
                            $row->value = $request->{$row->name};
                        } elseif ($row->type == 'text') {
                            $client_email = '';
                            if ($row->subtype == 'email') {
                                if (isset($row->is_client_email) && $row->is_client_email) {

                                    $client_emails[] = $request->{$row->name};
                                }
                            }
                            $row->value = $request->{$row->name};
                        } elseif ($row->type == 'starRating') {
                            $row->value = $request->{$row->name};
                        } elseif ($row->type == 'SignaturePad') {
                            if (property_exists($row, 'value')) {
                                $filepath = $row->value;
                                if ($request->{$row->name} == null) {
                                    $url = $row->value;
                                } else {
                                    if (file_exists(Storage::path($request->{$row->name}))) {
                                        $url = $request->{$row->name};
                                        $path = $url;
                                        $img_url = Storage::path($url);
                                        $filePath = $img_url;
                                    } else {
                                        $img_url = $request->{$row->name};
                                        $path = "form_values/$form->id/" . rand(1, 1000) . '.png';
                                        $filePath = Storage::path($path);
                                    }
                                    $imageContent = file_get_contents($img_url);
                                    $file = file_put_contents($filePath, $imageContent);
                                }
                                $row->value = $path;
                            } else {
                                if ($request->{$row->name} != null) {
                                    if (!file_exists(Storage::path("form_values/$form->id"))) {
                                        mkdir(Storage::path("form_values/$form->id"), 0777, true);
                                        chmod(Storage::path("form_values/$form->id"), 0777);
                                    }
                                    $filepath     = "form_values/$form->id/" . rand(1, 1000) . '.png';
                                    $url          = $request->{$row->name};
                                    $imageContent = file_get_contents($url);
                                    $filePath     = Storage::path($filepath);
                                    $file         = file_put_contents($filePath, $imageContent);
                                    $row->value   = $filepath;
                                }
                            }
                        } elseif ($row->type == 'location') {
                            $row->value = $request->location;
                        } elseif ($row->type == 'video') {
                            $validator = \Validator::make($request->all(),  [
                                'media' => 'required',
                            ]);
                            if ($validator->fails()) {
                                $messages = $validator->errors();
                            }

                            $row->value = $request->media;
                        } elseif ($row->type == 'selfie') {
                            $file = '';
                            $img = $request->image;
                            $folderPath = "form_selfie/";

                            $image_parts = explode(";base64,", $img);

                            if ($image_parts[0]) {

                                $image_base64 = base64_decode($image_parts[1]);
                                $fileName = uniqid() . '.png';

                                $file = $folderPath . $fileName;
                                Storage::put($file, $image_base64);
                            }
                            $row->value  = $file;
                        }
                    }
                }

                if ($request->form_value_id) {
                    $form_value->json = json_encode($array);
                    $form_value->save();
                } else {
                    if (\Auth::user()) {
                        $user_id = \Auth::user()->id;
                    } else {
                        $user_id = NULL;
                    }
                    $data = [];
                    if ($form->payment_status == 1) {
                        if ($form->payment_type == 'stripe') {
                            StripeStripe::setApiKey(UtilityFacades::getsettings('STRIPE_SECRET', $form->created_by));
                            try {
                                $charge = Charge::create([
                                    "amount"      => $form->amount * 100,
                                    "currency"    => $form->currency_name,
                                    "description" => "Payment from " . config('app.name'),
                                    "source"      => $request->input('stripeToken')
                                ]);
                            } catch (Exception $e) {
                                return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
                            }
                            if ($charge) {
                                $data['transaction_id']  = $charge->id;
                                $data['currency_symbol'] = $form->currency_symbol;
                                $data['currency_name']   = $form->currency_name;
                                $data['amount']          = $form->amount;
                                $data['status']          = 'successfull';
                                $data['payment_type']    = 'Stripe';
                            }
                        } else if ($form->payment_type == 'razorpay') {
                            $data['transaction_id']  = $request->payment_id;
                            $data['currency_symbol'] = $form->currency_symbol;
                            $data['currency_name']   = $form->currency_name;
                            $data['amount']          = $form->amount;
                            $data['status']          = 'successfull';
                            $data['payment_type']    = 'Razorpay';
                        } else if ($form->payment_type == 'paypal') {
                            $data['transaction_id']  = $request->payment_id;
                            $data['currency_symbol'] = $form->currency_symbol;
                            $data['currency_name']   = $form->currency_name;
                            $data['amount']          = $form->amount;
                            $data['status']          = 'successfull';
                            $data['payment_type']    = 'Paypal';
                        } else if ($form->payment_type == 'flutterwave') {
                            $data['transaction_id']  = $request->payment_id;
                            $data['currency_symbol'] = $form->currency_symbol;
                            $data['currency_name']   = $form->currency_name;
                            $data['amount']          = $form->amount;
                            $data['status']          = 'successfull';
                            $data['payment_type'] = 'Flutterwave';
                        } else if ($form->payment_type == 'paytm') {
                            $data['transaction_id']  = $request->payment_id;
                            $data['currency_symbol'] = $form->currency_symbol;
                            $data['currency_name']   = $form->currency_name;
                            $data['amount']          = $form->amount;
                            $data['status']          = 'pending';
                            $data['payment_type']    = 'Paytm';
                        } else if ($form->payment_type == 'paystack') {
                            $data['transaction_id']   = $request->payment_id;
                            $data['currency_symbol']  = $form->currency_symbol;
                            $data['currency_name']    = $form->currency_name;
                            $data['amount']           = $form->amount;
                            $data['status']           = 'successfull';
                            $data['payment_type'] = 'Paystack';
                        } else if ($form->payment_type == 'payumoney') {
                            $data['transaction_id']   = $request->payment_id;
                            $data['currency_symbol']  = $form->currency_symbol;
                            $data['currency_name']    = $form->currency_name;
                            $data['amount']           = $form->amount;
                            $data['status']           = 'successfull';
                            $data['payment_type'] = 'PayuMoney';
                        } else if ($form->payment_type == 'mollie') {
                            $data['transaction_id']   = $request->payment_id;
                            $data['currency_symbol']  = $form->currency_symbol;
                            $data['currency_name']    = $form->currency_name;
                            $data['amount']           = $form->amount;
                            $data['status']           = 'successfull';
                            $data['payment_type'] = 'Mollie';
                        } else if ($form->payment_type == 'coingate') {
                            $data['status'] = 'pending';
                        } else if ($form->payment_type == 'mercado') {
                            $data['status'] = 'pending';
                        }
                    } else {
                        $data['status'] = 'free';
                    }

                    $data['form_id'] = $form->id;
                    $data['user_id'] = $user_id;
                    $data['json']    = json_encode($array);
                    $form_value      = FormValue::create($data);
                }
                $form_valuearray = json_decode($form_value->json);
                $emails = explode(',', $form->email);
                $ccemails = explode(',', $form->ccemail);
                $bccemails = explode(',', $form->bccemail);
                if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                    if ($form->ccemail && $form->bccemail) {
                        try {
                            Mail::to($form->email)
                                ->cc($form->ccemail)
                                ->bcc($form->bccemail)
                                ->send(new FormSubmitEmail($form_value, $form_valuearray));
                        } catch (\Exception $e) {
                        }
                    } else if ($form->ccemail) {
                        try {
                            Mail::to($emails)
                                ->cc($ccemails)
                                ->send(new FormSubmitEmail($form_value, $form_valuearray));
                        } catch (\Exception $e) {
                        }
                    } else if ($form->bccemail) {
                        try {
                            Mail::to($emails)
                                ->bcc($bccemails)
                                ->send(new FormSubmitEmail($form_value, $form_valuearray));
                        } catch (\Exception $e) {
                        }
                    } else {
                        try {
                            Mail::to($emails)->send(new FormSubmitEmail($form_value, $form_valuearray));
                        } catch (\Exception $e) {
                        }
                    }
                    foreach ($client_emails as $client_email) {
                        try {
                            Mail::to($client_email)->send(new Thanksmail($form_value));
                        } catch (\Exception $e) {
                        }
                    }
                }

                $user = User::where('type', 'Admin')->first();
                $notifications_setting = NotificationsSetting::where('title', 'new survey details')->first();
                if (isset($notifications_setting)) {
                    if ($notifications_setting->notify == '1') {
                        $user->notify(new NewSurveyDetails($form));
                    } elseif ($notifications_setting->email_notification == '1') {
                        if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                            if (MailTemplate::where('mailable', FormSubmitEmail::class)->first()) {
                                try {
                                    Mail::to($form_value->email)->send(new FormSubmitEmail($form_value, $form_valuearray));
                                } catch (\Exception $e) {
                                }
                            }
                        }
                    }
                }
                if ($form->payment_type != 'coingate' && $form->payment_type != 'mercado') {
                    $success_msg = strip_tags($form->success_msg);
                }
                if ($request->form_value_id) {
                    $success_msg = strip_tags($form->success_msg);
                }

                Form::integrationFormData($form, $form_value);

                if (isset($request->ajax)) {
                    return response()->json(['is_success' => true, 'message' => $success_msg, 'redirect' => route('edit.form.values', $form_value->id)], 200);
                } else {
                    return redirect()->back()->with('success', $success_msg);
                }
            } else {
                if (isset($request->ajax)) {
                    return response()->json(['is_success' => false, 'message' => __('Form not found')], 200);
                } else {
                    return redirect()->back()->with('failed', __('Form not found.'));
                }
            }
        }
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $fileName = $request->upload->store('editor');
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = Storage::url($fileName);
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function duplicate(Request $request)
    {
        if (\Auth::user()->can('duplicate-form')) {
            $form = Form::find($request->form_id);
            if ($form) {
                Form::create([
                    'title'           => $form->title . ' (copy)',
                    'logo'            => $form->logo,
                    'email'           => $form->email,
                    'success_msg'     => $form->success_msg,
                    'thanks_msg'      => $form->thanks_msg,
                    'json'            => $form->json,
                    'payment_status'  => $form->payment_status,
                    'amount'          => $form->amount,
                    'currency_symbol' => $form->currency_symbol,
                    'currency_name'   => $form->currency_name,
                    'payment_type'    => $form->payment_type,
                    'created_by'      => $form->created_by,
                    'is_active'       => $form->is_active,
                ]);
                return redirect()->back()->with('success', __('Form duplicate successfully.'));
            } else {
                return redirect()->back()->with('errors', __('Form not found.'));
            }
        } else {
            return redirect()->back()->with('errors', __('Permission denied.'));
        }
    }

    public function ckupload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName         = $request->file('upload')->getClientOriginalName();
            $fileName           = pathinfo($originName, PATHINFO_FILENAME);
            $extension          = $request->file('upload')->getClientOriginalExtension();
            $fileName           = $fileName . '_' . time() . '.' . $extension;
            $request->file('upload')->move(public_path('images'), $fileName);
            $CKEditorFuncNum    = $request->input('CKEditorFuncNum');
            $url                = asset('images/' . $fileName);
            $msg                = __('Image uploaded successfully');
            $response           = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    public function dropzone(Request $request, $id)
    {
        $allowedfileExtension = [];
        $values = '';
        if ($request->file_extention == 'pdf') {
            $allowedfileExtension = ['pdf', 'pdfa', 'fdf', 'xdp', 'xfa', 'pdx', 'pdp', 'pdfxml', 'pdxox'];
        } else if ($request->file_extention == 'image') {
            $allowedfileExtension = ['jpeg', 'jpg', 'png'];
        } else if ($request->file_extention == 'excel') {
            $allowedfileExtension = ['xlsx', 'csv', 'xlsm', 'xltx', 'xlsb', 'xltm', 'xlw'];
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            if (in_array($extension, $allowedfileExtension)) {
                $filename = $file->store('form_values/' . $id);
                $values = $filename;
            } else {
                return response()->json(['errors' => 'Only ' . implode(',', $allowedfileExtension) . ' file allowed']);
            }
            return response()->json(['success' => 'File uploded successfully.', 'filename' => $values]);
        } else {
            return response()->json(['errors' => 'File not found.']);
        }
    }

    public function formStatus(Request $request, $id)
    {
        $form = Form::find($id);
        $input = ($request->value == "true") ? 1 : 0;
        if ($form) {
            $form->is_active = $input;
            $form->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Form status changed successfully.')]);
    }

    public function NewDemo()
    {
        return view('form.new');
    }

    public function formIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        $slackSettings = FormIntegrationSetting::where('key', 'slack_integration')->where('form_id', $form->id)->first();
        $slackJsons = [];
        $slackFieldJsons = [];
        if ($slackSettings) {
            $slackFieldJsons = json_decode($slackSettings->field_json, true);
            $slackJsons = json_decode($slackSettings->json, true);
        }
        $telegramSettings = FormIntegrationSetting::where('key', 'telegram_integration')->where('form_id', $form->id)->first();
        $telegramJsons = [];
        $telegramFieldJsons = [];
        if ($telegramSettings) {
            $telegramFieldJsons = json_decode($telegramSettings->field_json, true);
            $telegramJsons = json_decode($telegramSettings->json, true);
        }
        $mailgunSettings = FormIntegrationSetting::where('key', 'mailgun_integration')->where('form_id', $form->id)->first();
        $mailgunJsons = [];
        $mailgunFieldJsons = [];
        if ($mailgunSettings) {
            $mailgunFieldJsons = json_decode($mailgunSettings->field_json, true);
            $mailgunJsons = json_decode($mailgunSettings->json, true);
        }
        $bulkgateSettings = FormIntegrationSetting::where('key', 'bulkgate_integration')->where('form_id', $form->id)->first();
        $bulkgateJsons = [];
        $bulkgateFieldJsons = [];
        if ($bulkgateSettings) {
            $bulkgateFieldJsons = json_decode($bulkgateSettings->field_json, true);
            $bulkgateJsons = json_decode($bulkgateSettings->json, true);
        }
        $nexmoSettings = FormIntegrationSetting::where('key', 'nexmo_integration')->where('form_id', $form->id)->first();
        $nexmoJsons = [];
        $nexmoFieldJsons = [];
        if ($nexmoSettings) {
            $nexmoFieldJsons = json_decode($nexmoSettings->field_json, true);
            $nexmoJsons = json_decode($nexmoSettings->json, true);
        }
        $fast2smsSettings = FormIntegrationSetting::where('key', 'fast2sms_integration')->where('form_id', $form->id)->first();
        $fast2smsJsons = [];
        $fast2smsFieldJsons = [];
        if ($fast2smsSettings) {
            $fast2smsFieldJsons = json_decode($fast2smsSettings->field_json, true);
            $fast2smsJsons = json_decode($fast2smsSettings->json, true);
        }
        $vonageSettings = FormIntegrationSetting::where('key', 'vonage_integration')->where('form_id', $form->id)->first();
        $vonageJsons = [];
        $vonageFieldJsons = [];
        if ($vonageSettings) {
            $vonageFieldJsons = json_decode($vonageSettings->field_json, true);
            $vonageJsons = json_decode($vonageSettings->json, true);
        }
        $sendgridSettings = FormIntegrationSetting::where('key', 'sendgrid_integration')->where('form_id', $form->id)->first();
        $sendgridJsons = [];
        $sendgridFieldJsons = [];
        if ($sendgridSettings) {
            $sendgridFieldJsons = json_decode($sendgridSettings->field_json, true);
            $sendgridJsons = json_decode($sendgridSettings->json, true);
        }
        $twilioSettings = FormIntegrationSetting::where('key', 'twilio_integration')->where('form_id', $form->id)->first();
        $twilioJsons = [];
        $twilioFieldJsons = [];
        if ($twilioSettings) {
            $twilioFieldJsons = json_decode($twilioSettings->field_json, true);
            $twilioJsons = json_decode($twilioSettings->json, true);
        }
        $textlocalSettings = FormIntegrationSetting::where('key', 'textlocal_integration')->where('form_id', $form->id)->first();
        $textlocalJsons = [];
        $textlocalFieldJsons = [];
        if ($textlocalSettings) {
            $textlocalFieldJsons = json_decode($textlocalSettings->field_json, true);
            $textlocalJsons = json_decode($textlocalSettings->json, true);
        }
        $messenteSettings = FormIntegrationSetting::where('key', 'messente_integration')->where('form_id', $form->id)->first();
        $messenteJsons = [];
        $messenteFieldJsons = [];
        if ($messenteSettings) {
            $messenteFieldJsons = json_decode($messenteSettings->field_json, true);
            $messenteJsons = json_decode($messenteSettings->json, true);
        }
        $smsgatewaySettings = FormIntegrationSetting::where('key', 'smsgateway_integration')->where('form_id', $form->id)->first();
        $smsgatewayJsons = [];
        $smsgatewayFieldJsons = [];
        if ($smsgatewaySettings) {
            $smsgatewayFieldJsons = json_decode($smsgatewaySettings->field_json, true);
            $smsgatewayJsons = json_decode($smsgatewaySettings->json, true);
        }
        $clicktellSettings = FormIntegrationSetting::where('key', 'clicktell_integration')->where('form_id', $form->id)->first();
        $clicktellJsons = [];
        $clicktellFieldJsons = [];
        if ($clicktellSettings) {
            $clicktellFieldJsons = json_decode($clicktellSettings->field_json, true);
            $clicktellJsons = json_decode($clicktellSettings->json, true);
        }
        $clockworkSettings = FormIntegrationSetting::where('key', 'clockwork_integration')->where('form_id', $form->id)->first();
        $clockworkJsons = [];
        $clockworkFieldJsons = [];
        if ($clockworkSettings) {
            $clockworkFieldJsons = json_decode($clockworkSettings->field_json, true);
            $clockworkJsons = json_decode($clockworkSettings->json, true);
        }
        return view('form.integration.index', compact('form', 'slackJsons', 'telegramJsons', 'mailgunJsons', 'bulkgateJsons', 'nexmoJsons', 'fast2smsJsons', 'vonageJsons', 'sendgridJsons', 'twilioJsons', 'textlocalJsons', 'messenteJsons', 'smsgatewayJsons', 'clicktellJsons', 'clockworkJsons', 'formJsons', 'slackFieldJsons', 'telegramFieldJsons', 'mailgunFieldJsons', 'bulkgateFieldJsons', 'nexmoFieldJsons', 'fast2smsFieldJsons', 'vonageFieldJsons', 'sendgridFieldJsons', 'twilioFieldJsons', 'textlocalFieldJsons', 'messenteFieldJsons', 'smsgatewayFieldJsons', 'clicktellFieldJsons', 'clockworkFieldJsons'));
    }

    public function slackIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.slack', compact('form', 'formJsons'));
    }

    public function telegramIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.telegram', compact('form', 'formJsons'));
    }

    public function mailgunIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.mailgun', compact('form', 'formJsons'));
    }

    public function bulkgateIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.bulkgate', compact('form', 'formJsons'));
    }

    public function nexmoIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.nexmo', compact('form', 'formJsons'));
    }

    public function fast2smsIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.fast2sms', compact('form', 'formJsons'));
    }

    public function vonageIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.vonage', compact('form', 'formJsons'));
    }

    public function sendgridIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.sendgrid', compact('form', 'formJsons'));
    }

    public function twilioIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.twilio', compact('form', 'formJsons'));
    }

    public function textlocalIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.textlocal', compact('form', 'formJsons'));
    }

    public function messenteIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.messente', compact('form', 'formJsons'));
    }

    public function smsgatewayIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.smsgateway', compact('form', 'formJsons'));
    }

    public function clicktellIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.clicktell', compact('form', 'formJsons'));
    }

    public function clockworkIntegration($id)
    {
        $form = Form::find($id);
        $formJsons = json_decode($form->json);
        return view('form.integration.clockwork', compact('form', 'formJsons'));
    }

    public function formpaymentIntegration(Request $request, $id)
    {
        $form = Form::find($id);
        $payment_type = [];
        $payment_type[''] = 'Select payment';
        if (UtilityFacades::getsettings('stripesetting') == 'on') {
            $payment_type['stripe'] = 'Stripe';
        }
        if (UtilityFacades::getsettings('paypalsetting') == 'on') {
            $payment_type['paypal'] = 'Paypal';
        }
        if (UtilityFacades::getsettings('razorpaysetting') == 'on') {
            $payment_type['razorpay'] = 'Razorpay';
        }
        if (UtilityFacades::getsettings('paytmsetting') == 'on') {
            $payment_type['paytm'] = 'Paytm';
        }
        if (UtilityFacades::getsettings('flutterwavesetting') == 'on') {
            $payment_type['flutterwave'] = 'Flutterwave';
        }
        if (UtilityFacades::getsettings('paystacksetting') == 'on') {
            $payment_type['paystack'] = 'Paystack';
        }
        if (UtilityFacades::getsettings('payumoneysetting') == 'on') {
            $payment_type['payumoney'] = 'PayuMoney';
        }
        if (UtilityFacades::getsettings('molliesetting') == 'on') {
            $payment_type['mollie'] = 'Mollie';
        }
        if (UtilityFacades::getsettings('coingatesetting') == 'on') {
            $payment_type['coingate'] = 'Coingate';
        }
        if (UtilityFacades::getsettings('mercadosetting') == 'on') {
            $payment_type['mercado'] = 'Mercado';
        }
        return view('form.payment', compact('form', 'payment_type'));
    }

    public function formpaymentIntegrationstore(Request $request, $id)
    {
        $form = Form::find($id);
        if ($request->payment_type == "paystack") {
            if ($request->currency_symbol != '₦' || $request->currency_name != 'NGN') {
                return redirect()->back()->with('failed', __('Currency not suppoted this payment getway. please enter NGN currency and ₦ symbol.'));
            }
        }
        if ($request->payment_type == "paytm") {
            if ($request->currency_symbol != '₹' || $request->currency_name != 'INR') {
                return redirect()->back()->with('failed', __('Currency not suppoted this payment getway. please enter INR currency and ₹ symbol.'));
            }
        }
        $form->payment_status   = ($request->payment == 'on') ? '1' : '0';
        $form->amount           = ($request->amount == '') ? '0' : $request->amount;
        $form->currency_symbol  = $request->currency_symbol;
        $form->currency_name    = $request->currency_name;
        $form->payment_type     = $request->payment_type;
        $form->save();
        return redirect()->back()->with('success', __('Form payment integration succesfully.'));
    }

    public function formIntegrationStore(Request $request, $id)
    {
        $slackdata = [];
        $slackFiledtext = [];
        if ($request->get('slack_webhook_url')) {
            foreach ($request->get('slack_webhook_url') as $slackkey => $slackvalue) {
                $slackdata[$slackkey]['slack_webhook_url'] = $slackvalue;
                $slackField                                = $request->input('slack_field' . $slackkey);
                if ($slackField) {
                    $slackFiledtext[] = implode(',', $slackField);
                }
            }
        }
        $slackJsonData = ($slackdata) ? json_encode($slackdata) : null;
        FormIntegrationSetting::updateOrCreate(
            ['form_id' => $id,  'key' => 'slack_integration'],
            ['status' => ($request->get('slack_webhook_url')) ? 1 : 0, 'field_json' => json_encode($slackFiledtext), 'json' => $slackJsonData]
        );
        $telegramdata = [];
        $telegramFiledtext = [];
        if ($request->get('telegram_access_token') && $request->get('telegram_chat_id')) {
            foreach ($request->get('telegram_access_token') as $telegramkey => $telegramvalue) {
                $telegramdata[$telegramkey]['telegram_access_token'] = $telegramvalue;
                $telegramdata[$telegramkey]['telegram_chat_id']      = $request->get('telegram_chat_id')[$telegramkey];
                $telegramField                                       = $request->input('telegram_field' . $telegramkey);
                if ($telegramField) {
                    $telegramFiledtext[] = implode(',', $telegramField);
                }
            }
        }
        $telegramJsonData = ($telegramdata) ? json_encode($telegramdata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'telegram_integration'],
            ['status' => ($request->get('telegram_access_token') && $request->get('telegram_chat_id')) ? 1 : 0, 'field_json' => json_encode($telegramFiledtext), 'json' => $telegramJsonData]
        );

        $mailgundata = [];
        $mailgunFiledtext = [];
        if ($request->get('mailgun_email') && $request->get('mailgun_domain') && $request->get('mailgun_secret')) {
            foreach ($request->get('mailgun_email') as $mailgunkey => $mailgunvalue) {
                $mailgundata[$mailgunkey]['mailgun_email']       = $mailgunvalue;
                $mailgundata[$mailgunkey]['mailgun_domain']      = $request->get('mailgun_domain')[$mailgunkey];
                $mailgundata[$mailgunkey]['mailgun_secret']      = $request->get('mailgun_secret')[$mailgunkey];
                $mailgunField                                    = $request->input('mailgun_field' . $mailgunkey);
                if ($mailgunField) {
                    $mailgunFiledtext[] = implode(',', $mailgunField);
                }
            }
        }
        $mailgunJsonData = ($mailgundata) ? json_encode($mailgundata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'mailgun_integration'],
            ['status' => ($request->get('mailgun_email') && $request->get('mailgun_domain') && $request->get('mailgun_secret')) ? 1 : 0, 'field_json' => json_encode($mailgunFiledtext), 'json' => $mailgunJsonData]
        );

        $bulkgatedata = [];
        $bulkgateFiledtext = [];
        if ($request->get('bulkgate_number') && $request->get('bulkgate_token') && $request->get('bulkgate_app_id')) {
            foreach ($request->get('bulkgate_number') as $bulkgatekey => $bulkgatevalue) {
                $bulkgatedata[$bulkgatekey]['bulkgate_number']      = $bulkgatevalue;
                $bulkgatedata[$bulkgatekey]['bulkgate_token']       = $request->get('bulkgate_token')[$bulkgatekey];
                $bulkgatedata[$bulkgatekey]['bulkgate_app_id']      = $request->get('bulkgate_app_id')[$bulkgatekey];
                $bulkgateField                                      = $request->input('bulkgate_field' . $bulkgatekey);
                if ($bulkgateField) {
                    $bulkgateFiledtext[] = implode(',', $bulkgateField);
                }
            }
        }
        $bulkgateJsonData = ($bulkgatedata) ? json_encode($bulkgatedata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'bulkgate_integration'],
            ['status' => ($request->get('bulkgate_number') && $request->get('bulkgate_token') && $request->get('bulkgate_app_id')) ? 1 : 0, 'field_json' => json_encode($bulkgateFiledtext), 'json' => $bulkgateJsonData]
        );

        $nexmodata = [];
        $nexmoFiledtext = [];
        if ($request->get('nexmo_number') && $request->get('nexmo_key') && $request->get('nexmo_secret')) {
            foreach ($request->get('nexmo_number') as $nexmokey => $nexmovalue) {
                $nexmodata[$nexmokey]['nexmo_number']   = $nexmovalue;
                $nexmodata[$nexmokey]['nexmo_key']      = $request->get('nexmo_key')[$nexmokey];
                $nexmodata[$nexmokey]['nexmo_secret']   = $request->get('nexmo_secret')[$nexmokey];
                $nexmoField                             = $request->input('nexmo_field' . $nexmokey);
                if ($nexmoField) {
                    $nexmoFiledtext[] = implode(',', $nexmoField);
                }
            }
        }
        $nexmoJsonData = ($nexmodata) ? json_encode($nexmodata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'nexmo_integration'],
            ['status' => ($request->get('nexmo_number') && $request->get('nexmo_key') && $request->get('nexmo_secret')) ? 1 : 0, 'field_json' => json_encode($nexmoFiledtext), 'json' => $nexmoJsonData]
        );
        $fast2smsdata = [];
        $fast2smsFiledtext = [];
        if ($request->get('fast2sms_number') && $request->get('fast2sms_api_key')) {
            foreach ($request->get('fast2sms_number') as $fast2smskey => $fast2smsvalue) {
                $fast2smsdata[$fast2smskey]['fast2sms_number']   = $fast2smsvalue;
                $fast2smsdata[$fast2smskey]['fast2sms_api_key']  = $request->input('fast2sms_api_key')[$fast2smskey];
                $fast2smsField                                   = $request->input('fast2sms_field' . $fast2smskey);
                if ($fast2smsField) {
                    $fast2smsFiledtext[] = implode(',', $fast2smsField);
                }
            }
        }
        $fast2smsJsonData = ($fast2smsdata) ? json_encode($fast2smsdata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'fast2sms_integration'],
            ['status' => ($request->get('fast2sms_number') && $request->get('fast2sms_api_key')) ? 1 : 0, 'field_json' => json_encode($fast2smsFiledtext), 'json' => $fast2smsJsonData]
        );

        $vonagedata = [];
        $vonageFiledtext = [];
        if ($request->get('vonage_number') && $request->get('vonage_key') && $request->get('vonage_secret')) {
            foreach ($request->get('vonage_number') as $vonagekey => $vonagevalue) {
                $vonagedata[$vonagekey]['vonage_number']  = $vonagevalue;
                $vonagedata[$vonagekey]['vonage_key']     = $request->input('vonage_key')[$vonagekey];
                $vonagedata[$vonagekey]['vonage_secret']  = $request->input('vonage_secret')[$vonagekey];
                $vonageField                              = $request->input('vonage_field' . $vonagekey);
                if ($vonageField) {
                    $vonageFiledtext[] = implode(',', $vonageField);
                }
            }
        }
        $vonageJsonData = ($vonagedata) ? json_encode($vonagedata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'vonage_integration'],
            ['status' => ($request->get('vonage_number') && $request->get('vonage_key') && $request->get('vonage_secret')) ? 1 : 0, 'field_json' => json_encode($vonageFiledtext), 'json' => $vonageJsonData]
        );

        $sendgriddata = [];
        $sendgridFiledtext = [];
        if ($request->get('sendgrid_email') && $request->get('sendgrid_host') && $request->get('sendgrid_port') && $request->get('sendgrid_username') && $request->get('sendgrid_password') && $request->get('sendgrid_encryption') && $request->get('sendgrid_from_address') && $request->get('sendgrid_from_name')) {
            foreach ($request->get('sendgrid_email') as $sendgridkey => $sendgridvalue) {
                $sendgriddata[$sendgridkey]['sendgrid_email']           = $sendgridvalue;
                $sendgriddata[$sendgridkey]['sendgrid_host']            = $request->get('sendgrid_host')[$sendgridkey];
                $sendgriddata[$sendgridkey]['sendgrid_port']            = $request->get('sendgrid_port')[$sendgridkey];
                $sendgriddata[$sendgridkey]['sendgrid_username']        = $request->get('sendgrid_username')[$sendgridkey];
                $sendgriddata[$sendgridkey]['sendgrid_password']        = $request->get('sendgrid_password')[$sendgridkey];
                $sendgriddata[$sendgridkey]['sendgrid_encryption']      = $request->get('sendgrid_encryption')[$sendgridkey];
                $sendgriddata[$sendgridkey]['sendgrid_from_address']    = $request->get('sendgrid_from_address')[$sendgridkey];
                $sendgriddata[$sendgridkey]['sendgrid_from_name']       = $request->get('sendgrid_from_name')[$sendgridkey];
                $sendgridField                                          = $request->input('sendgrid_field' . $sendgridkey);
                if ($sendgridField) {
                    $sendgridFiledtext[] = implode(',', $sendgridField);
                }
            }
        }
        $sendgridJsonData = ($sendgriddata) ? json_encode($sendgriddata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'sendgrid_integration'],
            ['status' => ($request->get('sendgrid_email') && $request->get('sendgrid_host') && $request->get('sendgrid_port') && $request->get('sendgrid_username') && $request->get('sendgrid_password') && $request->get('sendgrid_encryption') && $request->get('sendgrid_from_address') && $request->get('sendgrid_from_name')) ? 1 : 0, 'field_json' => json_encode($sendgridFiledtext), 'json' => $sendgridJsonData]
        );

        $twiliodata = [];
        $twilioFiledtext = [];
        if ($request->get('twilio_mobile_number') && $request->get('twilio_sid') && $request->get('twilio_auth_token') && $request->get('twilio_number')) {
            foreach ($request->get('twilio_mobile_number') as $twiliokey => $twiliovalue) {
                $twiliodata[$twiliokey]['twilio_mobile_number']    = $twiliovalue;
                $twiliodata[$twiliokey]['twilio_sid']              = $request->get('twilio_sid')[$twiliokey];
                $twiliodata[$twiliokey]['twilio_auth_token']       = $request->get('twilio_auth_token')[$twiliokey];
                $twiliodata[$twiliokey]['twilio_number']           = $request->get('twilio_number')[$twiliokey];
                $twilioField                                       = $request->input('twilio_field' . $twiliokey);
                if ($twilioField) {
                    $twilioFiledtext[] = implode(',', $twilioField);
                }
            }
        }
        $twilioJsonData = ($twiliodata) ? json_encode($twiliodata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'twilio_integration'],
            ['status' => ($request->get('twilio_mobile_number') && $request->get('twilio_sid') && $request->get('twilio_auth_token') && $request->get('twilio_number')) ? 1 : 0, 'field_json' => json_encode($twilioFiledtext), 'json' => $twilioJsonData]
        );

        $textlocaldata = [];
        $textlocalFiledtext = [];
        if ($request->get('textlocal_number') && $request->get('textlocal_api_key')) {
            foreach ($request->get('textlocal_number') as $textlocalkey => $textlocalvalue) {
                $textlocaldata[$textlocalkey]['textlocal_number']   = $textlocalvalue;
                $textlocaldata[$textlocalkey]['textlocal_api_key']  = $request->input('textlocal_api_key')[$textlocalkey];
                $textlocalField                                   = $request->input('textlocal_field' . $textlocalkey);
                if ($textlocalField) {
                    $textlocalFiledtext[] = implode(',', $textlocalField);
                }
            }
        }
        $textlocalJsonData = ($textlocaldata) ? json_encode($textlocaldata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'textlocal_integration'],
            ['status' => ($request->get('textlocal_number') && $request->get('textlocal_api_key')) ? 1 : 0, 'field_json' => json_encode($textlocalFiledtext), 'json' => $textlocalJsonData]
        );

        $messentedata = [];
        $messenteFiledtext = [];
        if ($request->get('messente_number') && $request->get('messente_api_username') && $request->get('messente_api_password') && $request->get('messente_sender')) {
            foreach ($request->get('messente_number') as $messentekey => $messentevalue) {
                $messentedata[$messentekey]['messente_number']                    = $messentevalue;
                $messentedata[$messentekey]['messente_api_username']              = $request->get('messente_api_username')[$messentekey];
                $messentedata[$messentekey]['messente_api_password']              = $request->get('messente_api_password')[$messentekey];
                $messentedata[$messentekey]['messente_sender']                    = $request->get('messente_sender')[$messentekey];
                $messenteField                                                    = $request->input('messente_field' . $messentekey);
                if ($messenteField) {
                    $messenteFiledtext[] = implode(',', $messenteField);
                }
            }
        }
        $messenteJsonData = ($messentedata) ? json_encode($messentedata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'messente_integration'],
            ['status' => ($request->get('messente_number') && $request->get('messente_api_username') && $request->get('messente_api_password') && $request->get('messente_sender')) ? 1 : 0, 'field_json' => json_encode($messenteFiledtext), 'json' => $messenteJsonData]
        );

        $smsgatewaydata = [];
        $smsgatewayFiledtext = [];
        if ($request->get('smsgateway_number') && $request->get('smsgateway_api_key') && $request->get('smsgateway_user_id') && $request->get('smsgateway_user_password') && $request->get('smsgateway_sender_id')) {
            foreach ($request->get('smsgateway_number') as $smsgatewaykey => $smsgatewayvalue) {
                $smsgatewaydata[$smsgatewaykey]['smsgateway_number']              = $smsgatewayvalue;
                $smsgatewaydata[$smsgatewaykey]['smsgateway_api_key']             = $request->get('smsgateway_api_key')[$smsgatewaykey];
                $smsgatewaydata[$smsgatewaykey]['smsgateway_user_id']             = $request->get('smsgateway_user_id')[$smsgatewaykey];
                $smsgatewaydata[$smsgatewaykey]['smsgateway_user_password']       = $request->get('smsgateway_user_password')[$smsgatewaykey];
                $smsgatewaydata[$smsgatewaykey]['smsgateway_sender_id']           = $request->get('smsgateway_sender_id')[$smsgatewaykey];
                $smsgatewayField                                                  = $request->input('smsgateway_field' . $smsgatewaykey);
                if ($smsgatewayField) {
                    $smsgatewayFiledtext[] = implode(',', $smsgatewayField);
                }
            }
        }
        $smsgatewayJsonData = ($smsgatewaydata) ? json_encode($smsgatewaydata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'smsgateway_integration'],
            ['status' => ($request->get('smsgateway_number') && $request->get('smsgateway_sid') && $request->get('smsgateway_user_id') && $request->get('smsgateway_user_password') && $request->get('smsgateway_sender_id')) ? 1 : 0, 'field_json' => json_encode($smsgatewayFiledtext), 'json' => $smsgatewayJsonData]
        );
        $clicktelldata = [];
        $clicktellFiledtext = [];
        if ($request->get('clicktell_number') && $request->get('clicktell_api_key')) {
            foreach ($request->get('clicktell_number') as $clicktellkey => $clicktellvalue) {
                $clicktelldata[$clicktellkey]['clicktell_number']              = $clicktellvalue;
                $clicktelldata[$clicktellkey]['clicktell_api_key']             = $request->get('clicktell_api_key')[$clicktellkey];
                $clicktellField                                                = $request->input('clicktell_field' . $clicktellkey);
                if ($clicktellField) {
                    $clicktellFiledtext[] = implode(',', $clicktellField);
                }
            }
        }
        $clicktellJsonData = ($clicktelldata) ? json_encode($clicktelldata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'clicktell_integration'],
            ['status' => ($request->get('clicktell_number') && $request->get('clicktell_api_key')) ? 1 : 0, 'field_json' => json_encode($clicktellFiledtext), 'json' => $clicktellJsonData]
        );

        $clockworkdata = [];
        $clockworkFiledtext = [];
        if ($request->get('clockwork_number') && $request->get('clockwork_api_token')) {
            foreach ($request->get('clockwork_number') as $clockworkkey => $clockworkvalue) {
                $clockworkdata[$clockworkkey]['clockwork_number']     = $clockworkvalue;
                $clockworkdata[$clockworkkey]['clockwork_api_token']  = $request->input('clockwork_api_token')[$clockworkkey];
                $clockworkField                                       = $request->input('clockwork_field' . $clockworkkey);
                if ($clockworkField) {
                    $clockworkFiledtext[] = implode(',', $clockworkField);
                }
            }
        }
        $clockworkJsonData = ($clockworkdata) ? json_encode($clockworkdata) : null;
        FormIntegrationSetting::updateorcreate(
            ['form_id' => $id,  'key' => 'clockwork_integration'],
            ['status' => ($request->get('clockwork_number') && $request->get('clockwork_api_token')) ? 1 : 0, 'field_json' => json_encode($clockworkFiledtext), 'json' => $clockworkJsonData]
        );
        return redirect()->back()->with('success', __('Form integration succesfully.'));
    }

    public function formTheme($id)
    {
        $form = Form::find($id);
        return view('form.themes.theme', compact('form'));
    }

    public function formThemeedit(Request $request, $slug, $id)
    {
        $form = Form::find($id);
        return view('form.themes.index', compact('slug', 'form'));
    }

    public function themeChange(Request $request, $id)
    {
        $form = Form::find($id);
        $form->theme = $request->theme;
        $form->save();
        return redirect()->route('forms.index')->with('success', __('Theme successfully changed.'));
    }

    public function formThemeupdate(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'background_image' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors();
            return response()->json(['errors' => $messages->first()]);
        }
        $form = Form::find($id);
        $form->theme = $request->theme;
        $form->theme_color = $request->color;
        if ($request->hasFile('background_image')) {
            $theme_background_image = 'form-background.' . $request->background_image->getClientOriginalExtension();
            $theme_background_imagePath = 'form-themes/theme3/' . $form->id;
            $background_image = $request->file('background_image')->storeAs(
                $theme_background_imagePath,
                $theme_background_image
            );
            $form->theme_background_image = $background_image;
        }
        $form->save();
        return redirect()->route('forms.index')->with('success', __('Form theme selected succesfully.'));
    }

    public function WebcamCapture(Request $request)
    {
        $img = $request->image;
        $folderPath = "form_selfie/";
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.png';
        $file = $folderPath . $fileName;
        Storage::put($file, $image_base64);
        return redirect()->back()->with('success', 'Image Upload succesfully');
    }
}
