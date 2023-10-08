<?php

namespace App\Http\Controllers;

use App\DataTables\PollDataTable;
use App\Facades\UtilityFacades;
use App\Models\Comments;
use App\Models\CommentsReply;
use App\Models\DashboardWidget;
use App\Models\ImagePoll;
use App\Models\MeetingPoll;
use App\Models\MultipleChoice;
use App\Models\Poll;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PollController extends Controller
{
    public function index(PollDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-poll')) {
            return $dataTable->render('poll.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-poll')) {
            return view('poll.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-poll')) {
            $this->validate($request, [
                'title' => 'required',
            ]);
            if ($request->voting_type == 'Multiple_choice') {
                $this->validate($request, [
                    'multiple_answer_options.*.answer_options' => 'required',
                ]);
            }elseif ($request->voting_type == 'Image_poll') {
                $this->validate($request, [
                    'image_answer_options.*.optional_name' => 'required',
                ]);
            }else{
                $this->validate($request, [
                    'meeting_answer_options.*.datetime' => 'required',
                ]);
            }
            if ($request->voting_type == 'Multiple_choice') {
                $multiple_answer['multiple_answer_options'] =  $request->multiple_answer_options;
                $poll_answer['title'] =  $request->title;
                $poll_answer['description'] =  $request->description;
                $poll_answer['voting_type'] =  $request->voting_type;
                $poll_answer['multiple_answer_options'] =  json_encode($multiple_answer);
                $poll_answer['require_participants_names'] =  ($request->require_participants_names == 'on') ? 1 : 0;
                $poll_answer['voting_restrictions'] =  $request->voting_restrictions;
                $poll_answer['set_end_date'] =  ($request->set_end_date == 'on') ? 1 : 0;
                $poll_answer['allow_comments'] =  ($request->allow_comments == 'on') ? 1 : 0;
                $poll_answer['hide_participants_from_each_other'] =  ($request->hide_participants_from_each_other == 'on') ? 1 : 0;
                $poll_answer['results_visibility'] =  $request->results_visibility;
                $poll_answer['set_end_date_time'] =  Carbon::parse($request['set_end_date_time'])->toDateTimeString();
                $poll_answer = Poll::create($poll_answer);
            } else if ($request->voting_type == 'Image_poll') {
                $images = $request->image_answer_options;
                $abc = [];
                foreach ($images as $key => $img) {
                    $allowedfileExtension = ['jpeg', 'jpg', 'png'];
                    $file = $img['image'];
                    $extension = $file->getClientOriginalExtension();
                    $check = in_array($extension, $allowedfileExtension);
                    $filename = $file->store('polls');
                    $abc['image_answer_options'][] =  [
                        'optional_name' => $img['optional_name'],
                        'image' => $filename
                    ];
                }
                $image_poll_answer['title'] =  $request->title;
                $image_poll_answer['description'] =  $request->description;
                $image_poll_answer['voting_type'] =  $request->voting_type;
                $image_poll_answer['image_answer_options'] =  json_encode($abc);
                $image_poll_answer['image_require_participants_names'] =  ($request->image_require_participants_names == 'on') ? 1 : 0;
                $image_poll_answer['image_voting_restrictions'] =  $request->image_voting_restrictions;
                $image_poll_answer['image_set_end_date'] =  ($request->image_set_end_date == 'on') ? 1 : 0;
                $image_poll_answer['image_set_end_date_time'] =  Carbon::parse($request['image_set_end_date_time'])->toDateTimeString();
                $image_poll_answer['image_allow_comments'] =  ($request->image_allow_comments == 'on') ? 1 : 0;
                $image_poll_answer['image_hide_participants_from_each_other'] =  ($request->image_hide_participants_from_each_other == 'on') ? 1 : 0;
                $image_poll_answer['image_results_visibility'] =  $request->image_results_visibility;
                $image_poll_answer = Poll::create($image_poll_answer);
            } else {
                $meeting_multiple_answer['meeting_answer_options'] =  $request->meeting_answer_options;
                $i = [];
                foreach ($meeting_multiple_answer as $meeting_multiple) {
                    foreach ($meeting_multiple as $meeting) {
                        $meeting_datetime = Carbon::parse($meeting['datetime'])->toDateTimeString();
                        $i['meeting_answer_options'][] =  [
                            'datetime' => $meeting_datetime
                        ];
                    }
                }
                $meeting_poll_answer['title'] =  $request->title;
                $meeting_poll_answer['description'] =  $request->description;
                $meeting_poll_answer['voting_type'] =  $request->voting_type;
                $meeting_poll_answer['meeting_answer_options'] = json_encode($i);
                $meeting_poll_answer['meeting_fixed_time_zone'] =  ($request->meeting_fixed_time_zone == 'on') ? 1 : 0;
                $meeting_poll_answer['meetings_fixed_time_zone'] =  $request->meetings_fixed_time_zone;
                $meeting_poll_answer['limit_selection_to_one_option_only'] =  ($request->limit_selection_to_one_option_only == 'on') ? 1 : 0;
                $meeting_poll_answer['meeting_set_end_date'] =  ($request->meeting_set_end_date == 'on') ? 1 : 0;
                $meeting_poll_answer['meeting_set_end_date_time'] =  Carbon::parse($request['meeting_set_end_date_time'])->toDateTimeString();
                $meeting_poll_answer['meeting_allow_comments'] =  ($request->meeting_allow_comments == 'on') ? 1 : 0;
                $meeting_poll_answer['meeting_hide_participants_from_each_other'] =  ($request->meeting_hide_participants_from_each_other == 'on') ? 1 : 0;
                $meeting_poll_answer = Poll::create($meeting_poll_answer);
            }
            return redirect()->route('poll.index')->with('success', __('Poll created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }


    public function poll(Request $request, $id)
    {
        if (\Auth::user()->can('vote-poll')) {
            $poll = poll::find($id);
            $commmant = Comments::where('poll_id', $id)->get();
            $options = json_decode($poll->multiple_answer_options);
            return view('poll.multiple_fill', compact('poll', 'options', 'commmant'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function fillStore(Request $request, $id)
    {
        $new_sessid   = \Session::getId();
        $location = \Location::get($request->ip());
        // $location = \Location::get('103.74.73.193');
        $poll = poll::find($id);
        if ($poll->set_end_date == '1' && Carbon::now() >= $poll->set_end_date_time == true) {
            return redirect()->back()->with('failed', __('The date for voting has already expired.'));
        } else {
            if ($poll->voting_restrictions == 'One_vote_per_ip_address') {
                if ($ip_address = MultipleChoice::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'multiple_answer_options' => 'required',
                    ]);
                    MultipleChoice::create([
                        'vote' => $request->multiple_answer_options,
                        'poll_id' => $id,
                        'location' => $location->ip,
                        'session_id' => $new_sessid,
                        'name' => $request->name,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                }
            } else if ($poll->voting_restrictions == 'One_vote_per_browser_session') {
                if ($ip_address = MultipleChoice::where('poll_id', $id)->where('session_id', $new_sessid)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'multiple_answer_options' => 'required',
                    ]);
                    MultipleChoice::create([
                        'vote' => $request->multiple_answer_options,
                        'poll_id' => $id,
                        'location' => $location->ip,
                        'session_id' => $new_sessid,
                        'name' => $request->name,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                }
            } else {
                if (Auth::user()) {
                    request()->validate([
                        'multiple_answer_options' => 'required',
                    ]);
                    MultipleChoice::create([
                        'vote' => $request->multiple_answer_options,
                        'poll_id' => $id,
                        'location' => $location->ip,
                        'session_id' => $new_sessid,
                        'name' => $request->name,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                } else {
                    return redirect()->back()->with('failed', __('User account required. Please sign up or log in to vote.'));
                }
            }
        }
    }

    public function PollResult(Request $request, $id)
    {

        if (\Auth::user()->can('result-poll')) {
            $poll = poll::find($id);
            $votes = MultipleChoice::where('poll_id', $id)->get();
            $chartData = json_decode($poll->multiple_answer_options);
            $options = [];
            foreach ($chartData as $chart) {
                foreach ($chart as $key => $value) {
                    $options['options'][$value->answer_options] = 0;
                }
            }
            foreach ($votes as $value) {
                $options['options'][$value->vote]++;
            }
            return view('poll.multiple_result ', compact('votes', 'poll', 'options', 'chartData'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function PollImageResult(Request $request, $id)
    {
        if (\Auth::user()->can('result-poll')) {
            $poll = poll::find($id);
            $imgs = json_decode($poll->image_answer_options);
            $votes = ImagePoll::where('poll_id', $poll->id)->get();
            $chartData = json_decode($poll->image_answer_options);
            $options = [];
            foreach ($chartData as $chart) {
                foreach ($chart as $key => $value) {
                    $options['options'][$value->optional_name] = 0;
                }
            }
            foreach ($votes as $value) {
                $options['options'][$value->vote]++;
            }
            return view('poll.image_result', compact('poll', 'imgs', 'votes', 'options', 'chartData'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function PollMeetingResult(Request $request, $id)
    {
        if (\Auth::user()->can('result-poll')) {
            $poll = poll::find($id);
            $votes = MeetingPoll::where('poll_id', $id)->get();
            $chartData = json_decode($poll->meeting_answer_options);
            $options = [];
            foreach ($chartData as $chart) {
                foreach ($chart as $key => $value) {
                    $options['options'][UtilityFacades::date_time_format($value->datetime)] = 0;
                }
            }
            foreach ($votes as $value) {
                $options['options'][UtilityFacades::date_time_format($value->vote)]++;
            }
            return view('poll.meeting_result ', compact('poll', 'options', 'chartData', 'votes'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function ImagePoll(Request $request, $id)
    {
        if (\Auth::user()->can('vote-poll')) {
            $poll = poll::find($id);
            $options = json_decode($poll->image_answer_options);
            return view('poll.image_fill', compact('poll', 'options'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function ImageStore(Request $request, $id)
    {
        $location = \Location::get($request->ip());
        // $location = \Location::get('103.74.73.193');
        $poll = poll::find($id);
        $new_sessid   = \Session::getId();
        if ($poll->image_set_end_date == '1' && Carbon::now() >= $poll->image_set_end_date_time == true) {
            return redirect()->back()->with('failed', __('The date for voting has already expired.'));
        } else {
            if ($poll->image_voting_restrictions == 'One_vote_per_ip_address') {
                if ($ip_address = ImagePoll::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'optional_name' => 'required',
                    ]);
                    ImagePoll::create([
                        'vote' => $request->optional_name,
                        'poll_id' => $id,
                        'location' => $location->ip,
                        'name' => $request->name,
                        'session_id' => $new_sessid,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                }
            } else if ($poll->image_voting_restrictions == 'One_vote_per_browser_session') {
                if ($ip_address = ImagePoll::where('poll_id', $id)->where('session_id', $new_sessid)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'optional_name' => 'required',
                    ]);
                    ImagePoll::create([
                        'vote' => $request->optional_name,
                        'poll_id' => $id,
                        'location' => $location->ip,
                        'name' => $request->name,
                        'session_id' => $new_sessid,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                }
            } else {
                if (Auth::user()) {
                    request()->validate([
                        'optional_name' => 'required',
                    ]);
                    ImagePoll::create([
                        'vote' => $request->optional_name,
                        'poll_id' => $id,
                        'location' => $location->ip,
                        'name' => $request->name,
                        'session_id' => $new_sessid,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                } else {
                    return redirect()->back()->with('failed', __('User account required. Please sign up or log in to vote.'));
                }
            }
        }
    }

    public function MeetingPoll(Request $request, $id)
    {
        if (\Auth::user()->can('vote-poll')) {
            $poll = poll::find($id);
            $options = json_decode($poll->meeting_answer_options);
            return view('poll.meeting_fill', compact('poll', 'options'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function MeetingStore(Request $request, $id)
    {
        $location = \Location::get($request->ip());
        // $location = \Location::get('103.74.73.193');
        $new_sessid   = \Session::getId();
        $poll = poll::find($id);
        if ($poll->meeting_set_end_date == '1' && Carbon::now() >= $poll->meeting_set_end_date_time == true) {
            return redirect()->back()->with('failed', __('The date for voting has already expired.'));
        } else {
            request()->validate([
                'meeting_answer_options' => 'required',
                'name' => 'required',
            ]);
            foreach ($request->meeting_answer_options as $meeting_answer) {
                MeetingPoll::create([
                    'vote' => $meeting_answer,
                    'poll_id' => $id,
                    'location' => $location->ip,
                    'name' => $request->name,
                    'session_id' => $new_sessid,

                ]);
            }
            return redirect()->back()->with('success', __('Voting successfully.'));
        }
    }

    public function publicFill(Request $request, $id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $poll = Poll::find($id);
            $form_value = null;
            if ($poll) {
                $array = $poll->getPollArray();
                return view('poll.public_multiple_choice', compact('poll', 'form_value', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function PublicFillResult(Request $request, $id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $new_sessid   = \Session::getId();
        $location = \Location::get($request->ip());
        // $location = \Location::get('103.74.73.193');
        if ($poll->results_visibility == 'public_after_vote') {
            if ($poll->voting_restrictions == 'One_vote_per_ip_address') {
                if ($ip_address = MultipleChoice::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    if ($id) {
                        $form_value = null;
                        $votes = MultipleChoice::where('poll_id', $id)->get();
                        $chartData = json_decode($poll->multiple_answer_options);
                        if ($poll) {
                            $options = [];
                            foreach ($chartData as $chart) {
                                foreach ($chart as $key => $value) {
                                    $options['options'][$value->answer_options] = 0;
                                }
                            }
                            foreach ($votes as $value) {
                                $options['options'][$value->vote]++;
                            }
                            return view('poll.public_multiple_choice_result', compact('poll', 'form_value', 'options', 'chartData', 'votes'));
                        } else {
                            return redirect()->back()->with('failed', __('Form not found.'));
                        }
                    } else {
                        abort(404);
                    }
                } else {
                    return redirect()->back()->with('failed', __('After Vote Results Visibility'));
                }
            } else if ($poll->voting_restrictions == 'One_vote_per_browser_session') {
                if ($ip_address = MultipleChoice::where('poll_id', $id)->where('session_id', $new_sessid)->first()) {
                    if ($id) {
                        $form_value = null;
                        $votes = MultipleChoice::where('poll_id', $id)->get();
                        $chartData = json_decode($poll->multiple_answer_options);
                        if ($poll) {
                            $options = [];
                            foreach ($chartData as $chart) {
                                foreach ($chart as $key => $value) {
                                    $options['options'][$value->answer_options] = 0;
                                }
                            }
                            foreach ($votes as $value) {
                                $options['options'][$value->vote]++;
                            }
                            return view('poll.public_multiple_choice_result', compact('poll', 'form_value', 'options', 'chartData', 'votes'));
                        } else {
                            return redirect()->back()->with('failed', __('Form not found.'));
                        }
                    } else {
                        abort(404);
                    }
                } else {
                    return redirect()->back()->with('failed', __('After vote results visibility'));
                }
            } else {
                return redirect()->back()->with('failed', __('Only vote results visibility in user.'));
            }
        } else {
            if ($id) {
                $form_value = null;
                $votes = MultipleChoice::where('poll_id', $id)->get();
                $chartData = json_decode($poll->multiple_answer_options);
                if ($poll) {
                    $options = [];
                    foreach ($chartData as $chart) {
                        foreach ($chart as $key => $value) {
                            $options['options'][$value->answer_options] = 0;
                        }
                    }
                    foreach ($votes as $value) {
                        $options['options'][$value->vote]++;
                    }
                    return view('poll.public_multiple_choice_result', compact('poll', 'form_value', 'options', 'chartData', 'votes'));
                } else {
                    return redirect()->back()->with('failed', __('Form not found.'));
                }
            } else {
                abort(404);
            }
        }
    }

    public function PublicFillMeeting(Request $request, $id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $poll = Poll::find($id);
            $form_value = null;
            if ($poll) {
                $options = $poll->getMeetingArray();
                return view('poll.public_meeting_poll', compact('poll', 'form_value', 'options'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function PublicFillResultMeeting(Request $request, $id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $poll = Poll::find($id);
            $votes = MeetingPoll::where('poll_id', $id)->get();
            $chartData = json_decode($poll->meeting_answer_options);
            $options = [];
            $form_value = null;
            if ($poll) {
                foreach ($chartData as $chart) {
                    foreach ($chart as $key => $value) {
                        $options['options'][UtilityFacades::date_time_format($value->datetime)] = 0;
                    }
                }
                foreach ($votes as $value) {
                    $options['options'][UtilityFacades::date_time_format($value->vote)]++;
                }
                return view('poll.public_meeting_result', compact('poll', 'form_value', 'options', 'chartData', 'votes'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function PublicFillImage(Request $request, $id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $poll = Poll::find($id);
            $form_value = null;
            if ($poll) {
                $options = $poll->getPollImage();
                return view('poll.public_image_poll', compact('poll', 'form_value', 'options'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function PublicFillResultImage(Request $request, $id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $new_sessid   = \Session::getId();
        $location = \Location::get($request->ip());
        // $location = \Location::get('103.74.73.193');
        try {
        $poll = Poll::find($id);
            if ($poll->image_results_visibility == 'public_after_vote') {
                if ($poll->image_voting_restrictions == 'One_vote_per_ip_address') {
                    if ($ip_address = ImagePoll::where('poll_id', $id)->where('location', $location->ip)->first()) {
                        if ($id) {
                            $imgs = json_decode($poll->image_answer_options);
                            $votes = ImagePoll::where('poll_id', $poll->id)->get();
                            $chartData = json_decode($poll->image_answer_options);
                            $options = [];

                            $form_value = null;
                            if ($poll) {
                                foreach ($chartData as $chart) {
                                    foreach ($chart as $key => $value) {
                                        $options['options'][$value->optional_name] = 0;
                                    }
                                }

                                foreach ($votes as $value) {
                                    $options['options'][$value->vote]++;
                                }
                                return view('poll.public_image_result', compact('poll', 'form_value', 'imgs', 'options', 'votes', 'chartData'));
                            } else {
                                return redirect()->back()->with('failed', __('Form not found.'));
                            }
                        } else {
                            abort(404);
                        }
                    } else {
                        return redirect()->back()->with('failed', __('After vote results visibility'));
                    }
                } else if ($poll->image_voting_restrictions == 'One_vote_per_browser_session') {
                    if ($ip_address = ImagePoll::where('poll_id', $id)->where('session_id', $new_sessid)->first()) {
                        if ($id) {
                            $imgs = json_decode($poll->image_answer_options);
                            $votes = ImagePoll::where('poll_id', $poll->id)->get();
                            $chartData = json_decode($poll->image_answer_options);
                            $options = [];
                            $form_value = null;
                            if ($poll) {
                                foreach ($chartData as $chart) {
                                    foreach ($chart as $key => $value) {
                                        $options['options'][$value->optional_name] = 0;
                                    }
                                }
                                foreach ($votes as $value) {
                                    $options['options'][$value->vote]++;
                                }
                                return view('poll.public_image_result', compact('poll', 'form_value', 'imgs', 'options', 'votes', 'chartData'));
                            } else {
                                return redirect()->back()->with('failed', __('Form not found.'));
                            }
                        } else {
                            abort(404);
                        }
                    } else {
                        return redirect()->back()->with('failed', __('After vote results visibility.'));
                    }
                } else {
                    return redirect()->back()->with('failed', __('Only vote results visibility in user.'));
                }
            } else {
                if ($id) {
                    $imgs = json_decode($poll->image_answer_options);
                    $votes = ImagePoll::where('poll_id', $poll->id)->get();
                    $chartData = json_decode($poll->image_answer_options);
                    $options = [];
                    $form_value = null;
                    if ($poll) {
                        foreach ($chartData as $chart) {
                            foreach ($chart as $key => $value) {
                                $options['options'][$value->optional_name] = 0;
                            }
                        }
                        foreach ($votes as $value) {
                            $options['options'][$value->vote]++;
                        }
                        return view('poll.public_image_result', compact('poll', 'form_value', 'imgs', 'options', 'votes', 'chartData'));
                    } else {
                        return redirect()->back()->with('failed', __('Form not found.'));
                    }
                } else {
                    abort(404);
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('errors', $th->getMessage());
        }
    }

    public function Share($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $view =   view('poll.public_multiple_share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareQr($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $view =   view('poll.public_multiple_share_new', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareQrImage($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $view =   view('poll.public_image_share_new', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareImage($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $view =   view('poll.public_image_share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareMeeting($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $view =   view('poll.public_meeting_share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareQrMeeting($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $poll = Poll::find($id);
        $view =   view('poll.public_meeting_share_new', compact('poll'));
        return ['html' => $view->render()];
    }

    public function Shares($id)
    {
        $poll = Poll::find($id);
        $view =   view('poll.multiple_share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareMeetings($id)
    {
        $poll = Poll::find($id);
        $view =   view('poll.meeting_share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function ShareImages($id)
    {
        $poll = Poll::find($id);
        $view =   view('poll.image_share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-poll')) {
            $poll = Poll::find($id);
            $multiplechoice = MultipleChoice::where('poll_id', $id)->get();
            $meeting_poll = MeetingPoll::where('poll_id', $id)->get();
            $image_poll = ImagePoll::where('poll_id', $id)->get();
            $comments = Comments::where('poll_id', $id)->get();
            $comments_reply = CommentsReply::where('poll_id', $id)->get();
            DashboardWidget::where('poll_id', $id)->delete();
            if ($poll->voting_type == 'Multiple_choice') {
                foreach ($multiplechoice as $value) {
                    $ids = $value->id;
                    $multiple =  MultipleChoice::find($ids);
                    if ($multiple) {
                        $multiple->delete();
                    }
                }
            } elseif ($poll->voting_type == 'Meeting_poll') {
                foreach ($meeting_poll as $meeting_value) {
                    $meeting_value_ids = $meeting_value->id;
                    $meeting =  MeetingPoll::find($meeting_value_ids);
                    if ($meeting) {
                        $meeting->delete();
                    }
                }
            } else {
                $imgs = json_decode($poll->image_answer_options);
                foreach ($imgs->image_answer_options as $key => $img) {
                    $image_name = $img->image;
                    if ($image_name) {
                        Storage::delete($image_name);
                    }
                }
                foreach ($image_poll as $image_poll_value) {
                    $image_poll_value_ids = $image_poll_value->id;
                    $image =  ImagePoll::find($image_poll_value_ids);
                    if ($image) {
                        $image->delete();
                    }
                }
            }
            foreach ($comments as $allcomments) {
                $commentsids = $allcomments->id;
                $commentsall = Comments::find($commentsids);
                if ($commentsall) {
                    $commentsall->delete();
                }
            }
            foreach ($comments_reply as $comments_reply_all) {
                $comments_reply_ids = $comments_reply_all->id;
                $reply =  CommentsReply::find($comments_reply_ids);
                if ($reply) {
                    $reply->delete();
                }
            }
            $poll->delete();
            return redirect()->back()->with('success', __('Poll deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-poll')) {
            $poll = Poll::find($id);
            $next = Poll::where('id', '>', $poll->id)->first();
            $previous = Poll::where('id', '<', $poll->id)->orderBy('id', 'desc')->first();
            $image_answer_options = json_decode($poll->image_answer_options);
            $meeting_answer_options = json_decode($poll->meeting_answer_options);
            $multiple_answer_options = json_decode($poll->multiple_answer_options);
            $multiple = [];
            $meeting_poll = [];
            $image_poll = [];
            if ($poll->voting_type == 'Multiple_choice') {
                foreach ($multiple_answer_options as $value) {
                    foreach ($value as $data) {
                        $multiple[] = [
                            'answer_options' => $data->answer_options
                        ];
                    }
                }
            } else if ($poll->voting_type == 'Meeting_poll') {
                foreach ($meeting_answer_options as $value) {
                    foreach ($value as $data) {
                        $meeting_poll[] = [
                            'datetime' => $data->datetime
                        ];
                    }
                }
            } else {
                foreach ($image_answer_options as $value) {
                    foreach ($value as $data) {
                        $image_poll[] = [
                            'optional_name' => $data->optional_name,
                            'image' => Storage::url($data->image),
                            'old_image' => $data->image
                        ];
                    }
                }
            }
            return view('poll.edit', compact('poll', 'multiple', 'meeting_poll', 'image_poll', 'next', 'previous'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-poll')) {
            $this->validate($request, [
                'title' => 'required',
            ]);
            if ($request->voting_type == 'Image_poll') {
                $this->validate($request, [
                    'image_answer_options.*.optional_name' => 'required',
                ]);
            }
            if ($request->voting_type == 'Multiple choice') {
                $poll_answer = Poll::find($id);
                $multiple_answer['multiple_answer_options'] =  $request->multiple_answer_options;
                $poll_answer['title'] =  $request->title;
                $poll_answer['description'] =  $request->description;
                $poll_answer['voting_type'] =  'Multiple_choice';
                $poll_answer['multiple_answer_options'] =  json_encode($multiple_answer);
                $poll_answer['require_participants_names'] =  ($request->require_participants_names == 'on') ? 1 : 0;
                $poll_answer['voting_restrictions'] =  $request->voting_restrictions;
                $poll_answer['set_end_date'] =  ($request->set_end_date == 'on') ? 1 : 0;
                $poll_answer['allow_comments'] =  ($request->allow_comments == 'on') ? 1 : 0;
                $poll_answer['hide_participants_from_each_other'] =  ($request->hide_participants_from_each_other == 'on') ? 1 : 0;
                $poll_answer['results_visibility'] =  $request->results_visibility;
                $poll_answer['set_end_date_time'] =  Carbon::parse($request['set_end_date_time'])->toDateTimeString();
                $poll_answer->save();
            } else if ($request->voting_type == 'Image poll') {
                $image_poll_answer = Poll::find($id);
                $images = $request->image_answer_options;
                $abc = [];
                foreach ($images as $img) {
                    $allowedfileExtension = ['jpeg', 'jpg', 'png'];
                    if ($img['old_image']) {
                        $abc['image_answer_options'][] =  [
                            'optional_name' => $img['optional_name'],
                            'image' => $img['old_image']
                        ];
                    } else {
                        $file = $img['new_image'];
                        $extension = $file->getClientOriginalExtension();
                        $check = in_array($extension, $allowedfileExtension);
                        $filename = $file->store('polls');
                        $abc['image_answer_options'][] =  [
                            'optional_name' => $img['optional_name'],
                            'image' => $filename
                        ];
                    }
                }
                $image_poll_answer['title'] =  $request->title;
                $image_poll_answer['description'] =  $request->description;
                $image_poll_answer['voting_type'] =  'Image_poll';
                $image_poll_answer['image_answer_options'] =  json_encode($abc);
                $image_poll_answer['image_require_participants_names'] =  ($request->image_require_participants_names == 'on') ? 1 : 0;
                $image_poll_answer['image_voting_restrictions'] =  $request->image_voting_restrictions;
                $image_poll_answer['image_set_end_date'] =  ($request->image_set_end_date == 'on') ? 1 : 0;
                $image_poll_answer['image_set_end_date_time'] =  Carbon::parse($request['image_set_end_date_time'])->toDateTimeString();
                $image_poll_answer['image_allow_comments'] =  ($request->image_allow_comments == 'on') ? 1 : 0;
                $image_poll_answer['image_hide_participants_from_each_other'] =  ($request->image_hide_participants_from_each_other == 'on') ? 1 : 0;
                $image_poll_answer['image_results_visibility'] =  $request->image_results_visibility;
                $image_poll_answer->save();
            } else {
                $meeting_poll_answer = Poll::find($id);
                $meeting_multiple_answer['meeting_answer_options'] =  $request->meeting_answer_options;
                $i = [];
                foreach ($meeting_multiple_answer as $meeting_multiple) {
                    foreach ($meeting_multiple as $meeting) {
                        $meeting_datetime = Carbon::parse($meeting['datetime'])->toDateTimeString();
                        $i['meeting_answer_options'][] =  [
                            'datetime' => $meeting_datetime
                        ];
                    }
                }
                $meeting_poll_answer['title'] =  $request->title;
                $meeting_poll_answer['description'] =  $request->description;
                $meeting_poll_answer['voting_type'] =  'Meeting_poll';
                $meeting_poll_answer['meeting_answer_options'] = json_encode($i);
                $meeting_poll_answer['meeting_fixed_time_zone'] =  ($request->meeting_fixed_time_zone == 'on') ? 1 : 0;
                $meeting_poll_answer['meetings_fixed_time_zone'] =  $request->meetings_fixed_time_zone;
                $meeting_poll_answer['limit_selection_to_one_option_only'] =  ($request->limit_selection_to_one_option_only == 'on') ? 1 : 0;
                $meeting_poll_answer['meeting_set_end_date'] =  ($request->meeting_set_end_date == 'on') ? 1 : 0;
                $meeting_poll_answer['meeting_set_end_date_time'] =  Carbon::parse($request['meeting_set_end_date_time'])->toDateTimeString();
                $meeting_poll_answer['meeting_allow_comments'] =  ($request->meeting_allow_comments == 'on') ? 1 : 0;
                $meeting_poll_answer['meeting_hide_participants_from_each_other'] =  ($request->meeting_hide_participants_from_each_other == 'on') ? 1 : 0;
                $meeting_poll_answer->save();
            }
            return redirect()->route('poll.index')->with('success', __('Poll created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
}
