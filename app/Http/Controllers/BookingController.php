<?php

namespace App\Http\Controllers;

use App\DataTables\BookingDataTable;
use App\Facades\Utility;
use App\Facades\UtilityFacades;
use App\Mail\BookingSubmitEmail;
use App\Mail\BookingThanksmail;
use App\Models\Booking;
use App\Models\BookingValue;
use App\Models\ImagesWiseBooking;
use App\Models\NotificationsSetting;
use App\Models\SeatWiseBooking;
use App\Models\TimeWiseBooking;
use App\Models\User;
use App\Notifications\NewBookingSurveyDetails;
use Carbon\Carbon;
use Exception;
use Google\Service\Reseller\Seats;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\MailTemplates\Models\MailTemplate;
use Stripe\Charge;
use Stripe\Stripe as StripeStripe;

class BookingController extends Controller
{
    public function index(BookingDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-booking')) {
            return $dataTable->render('bookings.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-booking')) {
            $bookingSlots[''] = __('Select Booking Slots');
            $bookingSlots['time_wise_booking'] = 'Time Wise Booking';
            $bookingSlots['seats_wise_booking'] = 'Seats Wise Booking';
            return view('bookings.create', compact('bookingSlots'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-booking')) {
            $validator = \Validator::make($request->all(),  [
                'business_name' => 'required',
                'business_email' => 'required|email|unique:bookings,business_email',
                'business_website' => 'required',
                'business_address' => 'required',
                'business_number' => 'required',
                'business_phone' => 'required',
                'booking_slots' => 'required',
                'business_logo' => 'required|mimes:jpeg,jpg,png',
            ]);

            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('failed', $messages->first());
            }
            $file_name = '';
            if ($request->file('business_logo')) {
                $file = $request->file('business_logo');
                $file_name  =  $file->store('booking');
            }
            Booking::create([
                'business_name' => $request->business_name,
                'business_email' => $request->business_email,
                'business_website' => $request->business_website,
                'business_address' => $request->business_address,
                'business_number' => $request->business_number,
                'business_logo' => $file_name,
                'business_phone' => $request->business_phone,
                'booking_slots' => $request->booking_slots,
            ]);
            return redirect()->route('bookings.index')->with('success', __('Booking created succesfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
    public function edit($id)
    {
        if (\Auth::user()->can('edit-booking')) {
            $bookingSlots[''] = __('Select Booking Slots');
            $bookingSlots['time_wise_booking'] = 'Time Wise Booking';
            $bookingSlots['seats_wise_booking'] = 'Seats Wise Booking';
            $booking = Booking::find($id);
            return view('bookings.edit', compact('booking', 'bookingSlots'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-booking')) {
            $validator = \Validator::make($request->all(),  [
                'business_name' => 'required',
                'business_email' => 'required|email|unique:bookings,business_email,' . $id,
                'business_website' => 'required',
                'business_address' => 'required',
                'business_number' => 'required',
                'business_phone' => 'required',
                'booking_slots' => 'required',
                'business_logo' => 'mimes:jpeg,jpg,png',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('failed', $messages->first());
            }
            $booking = Booking::find($id);
            if ($request->hasfile('business_logo')) {
                $file = $request->file('business_logo');
                $file_name  =  $file->store('booking');
                $booking->business_logo = $file_name;
            }
            $booking->business_name = $request->business_name;
            $booking->business_email = $request->business_email;
            $booking->business_website = $request->business_website;
            $booking->business_address = $request->business_address;
            $booking->business_number = $request->business_number;
            $booking->business_phone = $request->business_phone;
            $booking->booking_slots = $request->booking_slots;
            $booking->save();
            return redirect()->route('bookings.index')->with('success', __('Booking updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-booking')) {
            $booking = Booking::find($id);
            if (File::exists(Storage::path($booking->business_logo))) {
                Storage::delete($booking->business_logo);
            }
            $booking->delete();
            return redirect()->back()->with('success', __('Booking deleted succesfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function design($id)
    {
        if (\Auth::user()->can('design-booking')) {
            $booking = Booking::find($id);
            return view('bookings.design', compact('booking'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function designUpdate(Request $request, $id)
    {
        if (\Auth::user()->can('design-booking')) {
            $booking = Booking::find($id);
            $booking->json = $request->json;
            $booking->save();
            return redirect()->route('booking.slots.setting', $booking->id)->with('success', __('Booking design updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function slotsSetting($id)
    {
        $booking = Booking::find($id);

         if ($booking->booking_slots == 'time_wise_booking') {
            $weekTimes['sunday']    = 'Sunday';
            $weekTimes['monday']    = 'Monday';
            $weekTimes['tuesday']   = 'Tuesday';
            $weekTimes['wednesday'] = 'Wednesday';
            $weekTimes['thursday']  = 'Thursday';
            $weekTimes['friday']    = 'Friday';
            $weekTimes['saturday']  = 'Saturday';
            $timewisebooking = TimeWiseBooking::where('booking_id', $booking->id)->first();
            $selectedweektime = [];
            if ($timewisebooking) {
                $selectedweektime = explode(',', $timewisebooking->week_time);
            }
            $payment_type = UtilityFacades::getformpaymenttypes();
            return view('bookings.slots.time_wise', compact('booking', 'weekTimes', 'selectedweektime', 'timewisebooking', 'payment_type'));
        } else if ($booking->booking_slots == 'seats_wise_booking') {
            $weekTimes['sunday']    = 'Sunday';
            $weekTimes['monday']    = 'Monday';
            $weekTimes['tuesday']   = 'Tuesday';
            $weekTimes['wednesday'] = 'Wednesday';
            $weekTimes['thursday']  = 'Thursday';
            $weekTimes['friday']    = 'Friday';
            $weekTimes['saturday']  = 'Saturday';
            $seatwisebooking = SeatWiseBooking::where('booking_id', $booking->id)->first();
            $selectedweektime = [];
            if ($seatwisebooking) {
                $selectedweektime = explode(',', $seatwisebooking->week_time);
            }
            return view('bookings.slots.seat_wise', compact('booking', 'weekTimes', 'selectedweektime', 'seatwisebooking'));
        }
    }

    public function slotsSettingupdate(Request $request, $id)
    {
        $booking = Booking::find($id);
        if ($booking->booking_slots == 'time_wise_booking') {
            $validator = \Validator::make($request->all(), [
                'slot_duration' => 'required|string',
                'services'      => 'required|string',
                'week_time'     => 'required|array',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('failed', $messages->first());
            }
            if ($request->slot_duration == 'custom_min') {
                $slottype     = $request->slot_duration;
                $slotduration = $request->slot_duration_minutes;
            } else {
                $slottype     = 'no-custom';
                $slotduration = $request->slot_duration;
            }
            TimeWiseBooking::updateorcreate(
                ['booking_id' => $id],
                [
                    'slot_duration' => $slottype,
                    'slot_duration_minutes' => $slotduration,
                    'payment_status'   => ($request->payment_status) ? 1 : 0,
                    'payment_type'   => ($request->payment_type) ? $request->payment_type : null,
                    'services' => $request->services,
                    'week_time' => ($request->week_time) ? implode(',', $request->week_time) : null,
                    'intervals_time_status' => ($request->intervals_time) ? 1 : 0,
                    'intervals_time_json' => ($request->interval_times) ?  json_encode($request->interval_times) : null,
                    'limit_time_status' => ($request->limit_time_status) ? 1 : 0,
                    'start_date' => ($request->start_date) ? Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d') : null,
                    'end_date' => ($request->end_date) ? Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d') : null,
                    'rolling_days_status' => ($request->rolling_days_status) ? 1 : 0,
                    'rolling_days' => ($request->rolling_days) ? $request->rolling_days : null,
                    'maximum_limit_status' => ($request->maximum_limit_status) ? 1 : 0,
                    'maximum_limit' => ($request->maximum_limit) ? $request->maximum_limit : null,
                    'multiple_booking' => ($request->multiple_booking) ? 1 : 0,
                    'email_notification' => ($request->email_notification) ? 1 : 0,
                    'time_zone' => $request->time_zone,
                    'date_format' => $request->date_format,
                    'time_format' => $request->time_format,
                    'enable_note' => ($request->enable_note) ? 1 : 0,
                    'note' => ($request->note) ? $request->note : null,
                ]
            );
        } elseif ($booking->booking_slots == 'seats_wise_booking') {
            $validator = \Validator::make($request->all(), [
                'seat_booking' => 'required',
                'services' => 'required|string',
                'week_time' => 'required|array',
            ]);
            if ($validator->fails()) {
                $messages = $validator->errors();
                return redirect()->back()->with('failed', $messages->first());
            }
            SeatWiseBooking::updateorcreate(
                ['booking_id' => $id],
                [
                    'seat_booking_json' => json_encode($request->seat_booking),
                    'services' => $request->services,
                    'week_time' => ($request->week_time) ? implode(',', $request->week_time) : null,
                    'session_time_status' => ($request->session_time) ? 1 : 0,
                    'session_time_json' => ($request->session_times) ?  json_encode($request->session_times) : null,
                    'limit_time_status' => ($request->limit_time_status) ? 1 : 0,
                    'start_date' => $request->start_date ? Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d') : null,
                    'end_date' => $request->end_date ? Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d') : null,
                    'rolling_days_status' => ($request->rolling_days_status) ? 1 : 0,
                    'rolling_days' => ($request->rolling_days) ? $request->rolling_days : null,
                    'maximum_limit_status' => ($request->maximum_limit_status) ? 1 : 0,
                    'maximum_limit' => ($request->maximum_limit) ? $request->maximum_limit : null,
                    'multiple_booking' => ($request->multiple_booking) ? 1 : 0,
                    'email_notification' => ($request->email_notification) ? 1 : 0,
                    'time_zone' => $request->time_zone,
                    'date_format' => $request->date_format,
                    'time_format' => $request->time_format,
                    'enable_note' => ($request->enable_note) ? 1 : 0,
                    'note' => ($request->note) ? $request->note : null,
                ]
            );
        }
        return redirect()->route('bookings.index')->with('success', __('Images wise booking updated successfully.'));
    }

    public function appoinmentTime(Request $request, $id)
    {
        $booking = Booking::find($id);
        $timewisebooking = TimeWiseBooking::where('booking_id', $booking->id)->first();
        if ($timewisebooking) {
            $timeZone = $timewisebooking->time_zone;
            $currdate = Carbon::now($timeZone);
            $booking_start_date = $currdate->format('Y-m-d');
            $booking_end_date = $currdate->copy()->endOfYear()->format('Y-m-d');
            if ($timewisebooking->limit_time_status == 1) {
                $booking_start_date = $timewisebooking->start_date;
                $booking_end_date = $timewisebooking->end_date;
            }
            if ($timewisebooking->rolling_days_status == 1) {
                $booking_start_date = $currdate->format('Y-m-d');
                $booking_end_date = $currdate->copy()->addDays($timewisebooking->rolling_days)->format('Y-m-d');
            }
            $dayMapping = [
                'sunday' => 0,
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6,
            ];
            $daysArray = explode(',', $timewisebooking->week_time);
            $selectedDays = array_map(function ($day) use ($dayMapping) {
                return $dayMapping[strtolower(trim($day))];
            }, $daysArray);
            $array = json_decode($booking->json);
            return view('bookings.appoinment_time', compact('booking', 'timewisebooking', 'array', 'selectedDays', 'booking_start_date', 'booking_end_date', 'currdate'));
        } else {
            return redirect()->back()->with('errors', __('Please complete your booking proccess.'));
        }
    }

    public function getappoinmentSlot(Request $request, $id)
    {
        $booking = Booking::find($id);
        $timewisebooking = TimeWiseBooking::where('booking_id', $booking->id)->first();
        $timeZone = $timewisebooking->time_zone;
        $currdate = Carbon::now($timeZone);
        $bookingValues = BookingValue::where('booking_id', $booking->id)->where('booking_slots_date', $request->date)->select('booking_slots_date', 'booking_slots')->get()->toArray();
        $timeBookinglimt = 1;
        if ($timewisebooking->maximum_limit_status == 1) {
            $timeBookinglimt = $timewisebooking->maximum_limit;
        }
        $timeArrays = [];
        $timepartitions = [];
        $booking_start_date = $currdate->format('Y-m-d');
        $booking_end_date = $currdate->copy()->endOfYear()->format('Y-m-d');
        if ($timewisebooking->limit_time_status == 1) {
            $booking_start_date = $timewisebooking->start_date;
            $booking_end_date = $timewisebooking->end_date;
        }
        if ($timewisebooking->rolling_days_status == 1) {
            $booking_start_date = $currdate->format('Y-m-d');
            $booking_end_date = $currdate->copy()->addDays($timewisebooking->rolling_days)->format('Y-m-d');
        }
        if ($request->date >= $booking_start_date && $request->date <= $booking_end_date) {
            $timeArrays[0]['start_time'] =  Carbon::parse($request->date . ' 00:00', $timeZone);
            $timeArrays[0]['end_time']   = Carbon::parse($request->date . ' 24:00', $timeZone);
            if ($timewisebooking->intervals_time_status == 1) {
                $timeIntervals =  json_decode($timewisebooking->intervals_time_json, true);
                foreach ($timeIntervals as $timeIntervalkey => $timeInterval) {
                    $timeArrays[$timeIntervalkey]['start_time'] = Carbon::parse($request->date . ' ' . $timeInterval['start_time'], $timeZone);
                    $timeArrays[$timeIntervalkey]['end_time'] = Carbon::parse($request->date . ' ' . $timeInterval['end_time'], $timeZone);
                }
            }
        }

        foreach ($timeArrays as $timeArray) {
            while ($timeArray['start_time']->lt($timeArray['end_time'])) {
                $endTimeSlot = $timeArray['start_time']->copy()->addMinutes($timewisebooking->slot_duration_minutes);
                $timepartitions[] = [
                    'start' => $timeArray['start_time'],
                    'end' => $endTimeSlot,
                ];
                $timeArray['start_time'] = $endTimeSlot;
            }
        }
        $html = '';
        $nearestSlotKey = null;
        foreach ($timepartitions as $timepartitionkey => $timepartition) {
            $Attribute = '';
            $class = '';
            if ($timepartition['start'] < $currdate) {
                $Attribute = 'disabled';
                $class = 'disabled';
            } elseif (Self::isSlotBooked($bookingValues, $timeBookinglimt, $timepartition["start"]->format('H:i') . '-' . $timepartition["end"]->format('H:i'))) {
                $Attribute = 'disabled';
                $class = 'disabled';
            } else {
                if ($nearestSlotKey === null || $timepartition['start']->diffInMinutes($currdate) < $timepartitions[$nearestSlotKey]['start']->diffInMinutes($currdate)) {
                    $nearestSlotKey = $timepartitionkey;
                }
            }
            if (!$request->slots && !$request->selecteddate) {
                if ($nearestSlotKey === $timepartitionkey) {
                    $Attribute = 'checked';
                }
            } else {
                $slots = explode(',', $request->slots);
                if (in_array($timepartition["start"]->format('H:i') . '-' . $timepartition["end"]->format('H:i'), $slots) && $request->selecteddate >= $currdate->format('Y-m-d')) {
                    $Attribute = 'checked';
                }
            }
            $time_format_value = $timepartition["start"]->format('H:i') . '-' . $timepartition["end"]->format('H:i');
            if ($timewisebooking->time_format  == '24_hour') {
                $time_format_label = $timepartition['start']->format('H:i') . ' to ' . $timepartition['end']->format('H:i');
            } else {
                $time_format_label =  $timepartition['start']->format('h:i') . ' ' . $timepartition['start']->format('A') . ' to ' . $timepartition['end']->format('h:i') . ' ' . $timepartition['end']->format('A');
            }

            $inputType = 'radio';
            $inputName = 'slot';
            if ($timewisebooking->multiple_booking == 1) {
                $inputType = 'checkbox';
                $inputName = 'slot[]';
            }
            $html .= '<input class="btn-check ' . $class . '" id="slot_' . $timepartitionkey . '" name="' . $inputName . '" type="' . $inputType . '" value="' . $time_format_value . '" ' . $Attribute . '>
            <label for="slot_' . $timepartitionkey . '" class="my-2 btn btn-outline-primary">' . $time_format_label . '</label>';
        }
        return response()->json(['is_success' => 1, 'html' => $html]);
    }

    function isSlotBooked($bookingValues, $limitPerSlot, $slot)
    {
        $bookedCount = 0;
        foreach ($bookingValues as $bookingValue) {
            $bookedSlots = explode(',', $bookingValue['booking_slots']);
            if (in_array($slot, $bookedSlots)) {
                $bookedCount++;
                if ($bookedCount >= $limitPerSlot) {
                    return true;
                }
            }
        }
        return false;
    }

    public function appoinmentSeat(Request $request, $id)
    {
        $booking = Booking::find($id);
        $seatwisebooking = SeatWiseBooking::where('booking_id', $booking->id)->first();
        if ($seatwisebooking) {
            $timeZone = $seatwisebooking->time_zone;
            $currdate = Carbon::now($timeZone);
            $array = json_decode($booking->json);
            $seatsessionJsons = [];
            if ($seatwisebooking->session_time_status == 1) {
                $seatsessionJsons = json_decode($seatwisebooking->session_time_json, true);
            }
            $sessionhtml = '';
            $oldStartTime = null;
            $nearestSlotKey = null;
            $booking_start_date = $currdate->format('Y-m-d');
            $booking_end_date = $currdate->copy()->endOfYear()->format('Y-m-d');
            if ($seatwisebooking->limit_time_status == 1) {
                $booking_start_date = $seatwisebooking->start_date;
                $booking_end_date = $seatwisebooking->end_date;
            }
            if ($seatwisebooking->rolling_days_status == 1) {
                $booking_start_date = $currdate->format('Y-m-d');
                $booking_end_date = $currdate->copy()->addDays($seatwisebooking->rolling_days)->format('Y-m-d');
            }
            if ($booking_start_date <= $currdate->format('Y-m-d') && $booking_end_date >= $currdate->format('Y-m-d')) {
                foreach ($seatsessionJsons as $seatsessionJsonkey => $seatsessionJson) {
                    $start_time = Carbon::parse($seatsessionJson['start_time'], $timeZone);
                    $end_time = Carbon::parse($seatsessionJson['end_time'], $timeZone);
                    $Attribute = '';
                    $class = '';
                    if ($start_time < $currdate) {
                        $Attribute = 'disabled';
                        $class = 'disabled';
                    } else {
                        if ($nearestSlotKey === null || ($oldStartTime != null && $start_time->diffInMinutes($currdate) < $oldStartTime->diffInMinutes($currdate))) {
                            $nearestSlotKey = $seatsessionJsonkey;
                        }
                    }
                    if ($nearestSlotKey === $seatsessionJsonkey) {
                        $Attribute = 'checked';
                    }
                    $oldStartTime = $start_time;
                    $time_format_value = $start_time->format('H:i') . '-' . $end_time->format('H:i');
                    if ($seatwisebooking->time_format  == '24_hour') {
                        $time_format_label = $start_time->format('H:i') . ' to ' . $end_time->format('H:i');
                    } else {
                        $time_format_label =  $start_time->format('h:i') . ' ' . $start_time->format('A') . ' to ' . $end_time->format('h:i') . ' ' . $end_time->format('A');
                    }
                    $sessionhtml .= '<input class="btn-check ' . $class . '" id="session_' . $time_format_value . '" name="session_time" ' . $Attribute . '
                                                type="radio" value="' . $time_format_value . '">
                                            <label for="session_' . $time_format_value . '"
                                                class="my-2 btn btn-outline-primary">' . $time_format_label . '</label>';
                }
            }
            return view('bookings.appoinment_seat', compact('booking', 'seatwisebooking', 'array', 'sessionhtml'));
        } else {
            return redirect()->back()->with('errors', __('Please complete your booking proccess.'));
        }
    }

    public function getappoinmentSeat(Request $request, $id)
    {
        $booking = Booking::find($id);
        $seatwisebooking = SeatWiseBooking::where('booking_id', $booking->id)->first();
        $seatbookingJsons = json_decode($seatwisebooking->seat_booking_json, true);
        $bookingValues = BookingValue::where('booking_id', $booking->id)->where('booking_seats_session', $request->session)->select('booking_seats', 'booking_seats_date', 'booking_seats_session')->get()->toArray();
        $inputType = 'radio';
        $inputName = 'seat';
        if ($seatwisebooking->multiple_booking == 1) {
            $inputType = 'checkbox';
            $inputName = 'seat[]';
        }
        $seatBookinglimt = 1;
        if ($seatwisebooking->maximum_limit_status == 1) {
            $seatBookinglimt = $seatwisebooking->maximum_limit;
        }
        $html = '';
        foreach ($seatbookingJsons as $seatbookingJson) {
            $html .= '<tr><td>';
            for ($i = 1; $i <= $seatbookingJson['column']; $i++) {
                $Attribute = '';
                $class = '';
                if (Self::isSeatBooked($bookingValues, $seatBookinglimt, $seatbookingJson['row'] . $i)) {
                    $Attribute = 'disabled';
                    $class = 'disabled';
                }
                if ($request->selectedseat) {
                    $seats = explode(',', $request->selectedseat);
                    if (in_array($seatbookingJson['row'] . $i, $seats)) {
                        $Attribute = 'checked';
                    }
                }
                $html .= '<div class="seat">
                        <input class="btn-check ' . $class . '" ' . $Attribute . '
                            id="seat_' . $seatbookingJson['row'] . $i . '"
                            type="' . $inputType . '" name="' . $inputName . '"
                            value="' . $seatbookingJson['row'] . $i . '">
                        <label for="seat_' . $seatbookingJson['row'] . $i . '"
                            class="btn btn-outline-primary">' . $seatbookingJson['row'] . $i . '</label>
                    </div>';
            }
            $html .= '</td></tr>';
        }
        return response()->json(['is_success' => 1, 'html' => $html]);
    }

    function isSeatBooked($bookingValues, $limitPerSlot, $seat)
    {
        $bookedCount = 0;
        foreach ($bookingValues as $bookingValue) {
            $bookedSeats = explode(',', $bookingValue['booking_seats']);
            if (in_array($seat, $bookedSeats)) {
                $bookedCount++;
                if ($bookedCount >= $limitPerSlot) {
                    return true;
                }
            }
        }
        return false;
    }

    public function bookingpaymentIntegration(Request $request, $id)
    {
        $booking = Booking::find($id);
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
        if (UtilityFacades::getsettings('coingatesetting') == 'on') {
            $payment_type['coingate'] = 'Coingate';
        }
        if (UtilityFacades::getsettings('mercadosetting') == 'on') {
            $payment_type['mercado'] = 'Mercado';
        }
        return view('bookings.payment', compact('booking', 'payment_type'));
    }

    public function bookingpaymentIntegrationstore(Request $request, $id)
    {
        $booking = Booking::find($id);
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
        $booking->payment_status   = ($request->payment == 'on') ? '1' : '0';
        $booking->amount           = ($request->amount == '') ? '0' : $request->amount;
        $booking->currency_symbol  = $request->currency_symbol;
        $booking->currency_name    = $request->currency_name;
        $booking->payment_type     = $request->payment_type;
        $booking->save();
        return redirect()->route('bookings.index')->with('success', __('Form payment integration succesfully.'));
    }

    public function publicTimeFill($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $booking = Booking::find($id);
            $timewisebooking = TimeWiseBooking::where('booking_id', $booking->id)->first();
            if ($timewisebooking) {
                $timeZone = $timewisebooking->time_zone;
                $currdate = Carbon::now($timeZone);
                $booking_start_date = $currdate->format('Y-m-d');
                $booking_end_date = $currdate->copy()->endOfYear()->format('Y-m-d');
                if ($timewisebooking->limit_time_status == 1) {
                    $booking_start_date = $timewisebooking->start_date;
                    $booking_end_date = $timewisebooking->end_date;
                }
                if ($timewisebooking->rolling_days_status == 1) {
                    $booking_start_date = $currdate->format('Y-m-d');
                    $booking_end_date = $currdate->copy()->addDays($timewisebooking->rolling_days)->format('Y-m-d');
                }
                $dayMapping = [
                    'sunday' => 0,
                    'monday' => 1,
                    'tuesday' => 2,
                    'wednesday' => 3,
                    'thursday' => 4,
                    'friday' => 5,
                    'saturday' => 6,
                ];
                $daysArray = explode(',', $timewisebooking->week_time);
                $selectedDays = array_map(function ($day) use ($dayMapping) {
                    return $dayMapping[strtolower(trim($day))];
                }, $daysArray);
                $booking_value = null;
                if ($booking) {
                    $array = $booking->getFormArray();
                    return view('bookings.public_fill.time', compact('booking', 'booking_value', 'array', 'timewisebooking', 'booking_start_date', 'booking_end_date', 'selectedDays', 'currdate'));
                } else {
                    return redirect()->back()->with('failed', __('Form not found.'));
                }
            } else {
                return redirect()->back()->with('errors', __('Please complete your booking proccess.'));
            }
        } else {
            abort(404);
        }
    }

    public function publicSeatFill($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        if ($id) {
            $booking = Booking::find($id);
            $seatwisebooking = SeatWiseBooking::where('booking_id', $booking->id)->first();
            if ($seatwisebooking) {
                $timeZone = $seatwisebooking->time_zone;
                $currdate = Carbon::now($timeZone);
                $array = json_decode($booking->json);
                $seatsessionJsons = [];
                if ($seatwisebooking->session_time_status == 1) {
                    $seatsessionJsons = json_decode($seatwisebooking->session_time_json, true);
                }
                $sessionhtml = '';
                $oldStartTime = null;
                $nearestSlotKey = null;
                $booking_start_date = $currdate->format('Y-m-d');
                $booking_end_date = $currdate->copy()->endOfYear()->format('Y-m-d');
                if ($seatwisebooking->limit_time_status == 1) {
                    $booking_start_date = $seatwisebooking->start_date;
                    $booking_end_date = $seatwisebooking->end_date;
                }
                if ($seatwisebooking->rolling_days_status == 1) {
                    $booking_start_date = $currdate->format('Y-m-d');
                    $booking_end_date = $currdate->copy()->addDays($seatwisebooking->rolling_days)->format('Y-m-d');
                }
                if ($booking_start_date <= $currdate->format('Y-m-d') && $booking_end_date >= $currdate->format('Y-m-d')) {
                    foreach ($seatsessionJsons as $seatsessionJsonkey => $seatsessionJson) {
                        $start_time = Carbon::parse($seatsessionJson['start_time'], $timeZone);
                        $end_time = Carbon::parse($seatsessionJson['end_time'], $timeZone);
                        $Attribute = '';
                        $class = '';
                        if ($start_time < $currdate) {
                            $Attribute = 'disabled';
                            $class = 'disabled';
                        } else {
                            if ($nearestSlotKey === null || ($oldStartTime != null && $start_time->diffInMinutes($currdate) < $oldStartTime->diffInMinutes($currdate))) {
                                $nearestSlotKey = $seatsessionJsonkey;
                            }
                        }
                        if ($nearestSlotKey === $seatsessionJsonkey) {
                            $Attribute = 'checked';
                        }
                        $oldStartTime = $start_time;

                        $time_format_value = $start_time->format('H:i') . '-' . $end_time->format('H:i');
                        if ($seatwisebooking->time_format  == '24_hour') {
                            $time_format_label = $start_time->format('H:i') . ' to ' . $end_time->format('H:i');
                        } else {
                            $time_format_label =  $start_time->format('h:i') . ' ' . $start_time->format('A') . ' to ' . $end_time->format('h:i') . ' ' . $end_time->format('A');
                        }
                        $sessionhtml .= '<input class="btn-check ' . $class . '" id="session_' . $time_format_value . '" name="session_time" ' . $Attribute . '
                                                    type="radio" value="' . $time_format_value . '">
                                                <label for="session_' . $time_format_value . '"
                                                    class="my-2 btn btn-outline-primary">' . $time_format_label . '</label>';
                    }
                }
                $booking_value = null;
                if ($booking) {
                    $array = $booking->getFormArray();
                    return view('bookings.public_fill.seats', compact('booking', 'booking_value', 'array', 'seatwisebooking', 'sessionhtml'));
                } else {
                    return redirect()->back()->with('failed', __('Form not found.'));
                }
            } else {
                return redirect()->back()->with('errors', __('Please complete your booking proccess.'));
            }
        } else {
            abort(404);
        }
    }

    public function qrCode($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $booking = Booking::find($id);
        $view =   view('bookings.public_fill_qr', compact('booking'));
        return ['html' => $view->render()];
    }

    public function fillStore(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (UtilityFacades::getsettings('captcha_enable') == 'on') {
            if (UtilityFacades::keysettings('captcha') == 'hcaptcha') {
                if (empty($_POST['h-captcha-response'])) {
                    if (isset($request->ajax)) {
                        return response()->json(['is_success' => false, 'message' => __('Please check hcaptcha.')], 200);
                    } else {
                        return redirect()->back()->with('failed', __('Please check hcaptcha.'));
                    }
                }
            }
            if (UtilityFacades::keysettings('captcha') == 'recaptcha') {
                if (empty($_POST['g-recaptcha-response'])) {
                    if (isset($request->ajax)) {
                        return response()->json(['is_success' => false, 'message' => __('Please check recaptcha.')], 200);
                    } else {
                        return redirect()->back()->with('failed', __('Please check recaptcha.'));
                    }
                }
            }
        }
        if ($booking) {
            $client_emails = [];
            if ($request->form_value_id) {
                $booking_value = BookingValue::find($request->form_value_id);
                $array = json_decode($booking_value->json);
            } else {
                $array = $booking->getFormArray();
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
                                                    $file->move(storage_path() . '/app/booking_values/' . $booking->id, $name);
                                                    $values[] = 'booking_values/' . $booking->id . '/' . $name;
                                                } else {
                                                    $path = Storage::path("booking_values/$booking->id");
                                                    $file_name = $file->store('booking_values/' . $booking->id);
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
                                                $file->move(storage_path() . '/app/booking_values/' . $booking->id, $name);
                                                $values = 'booking_values/' . $booking->id . '/' . $name;
                                                chmod("$values", 0777);
                                            } else {
                                                $path = Storage::path("booking_values/$booking->id");
                                                $file_name = $file->store('booking_values/' . $booking->id);
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
                    } elseif ($row->type == 'date' || $row->type == 'number' || $row->type == 'textarea' || $row->type == 'spinner') {
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
                                    $path = "booking_values/$booking->id/" . rand(1, 1000) . '.png';
                                    $filePath = Storage::path($path);
                                }
                                $imageContent = file_get_contents($img_url);
                                $file = file_put_contents($filePath, $imageContent);
                            }
                            $row->value = $path;
                        } else {
                            if ($request->{$row->name} != null) {
                                if (!file_exists(Storage::path("booking_values/$booking->id"))) {
                                    mkdir(Storage::path("booking_values/$booking->id"), 0777, true);
                                    chmod(Storage::path("booking_values/$booking->id"), 0777);
                                }
                                $filepath     = "booking_values/$booking->id/" . rand(1, 1000) . '.png';
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
                        $folderPath = "booking_selfie/";

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
                $booking_value->json = json_encode($array);
                $booking_value->save();
            } else {
                if (\Auth::user()) {
                    $user_id = \Auth::user()->id;
                } else {
                    $user_id = NULL;
                }
                $currdate = Carbon::now();
                $data = [];
                if ($booking->payment_status == 1) {
                    if ($booking->payment_type == 'stripe') {
                        StripeStripe::setApiKey(UtilityFacades::getsettings('stripe_secret', $booking->created_by));
                        try {
                            $charge = \Stripe\PaymentIntent::create([
                                'description' => "Payment from " . config('app.name'),
                                'amount' => $booking->amount * 100,
                                'currency' => $booking->currency_name,
                                'payment_method_types' => ['card'],
                                'metadata' => [
                                    'order_id' => '12345',
                                ],
                            ]);
                        } catch (Exception $e) {
                            return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
                        }
                        if ($charge) {
                            $data['transaction_id']  = $charge->id;
                            $data['currency_symbol'] = $booking->currency_symbol;
                            $data['currency_name']   = $booking->currency_name;
                            $data['amount']          = $booking->amount;
                            $data['status']          = 'successfull';
                            $data['payment_type']    = 'Stripe';
                        }
                    } else if ($booking->payment_type == 'razorpay') {
                        $data['transaction_id']  = $request->payment_id;
                        $data['currency_symbol'] = $booking->currency_symbol;
                        $data['currency_name']   = $booking->currency_name;
                        $data['amount']          = $booking->amount;
                        $data['status']          = 'successfull';
                        $data['payment_type']    = 'Razorpay';
                    } else if ($booking->payment_type == 'paypal') {
                        $data['transaction_id']  = $request->payment_id;
                        $data['currency_symbol'] = $booking->currency_symbol;
                        $data['currency_name']   = $booking->currency_name;
                        $data['amount']          = $booking->amount;
                        $data['status']          = 'successfull';
                        $data['payment_type']    = 'Paypal';
                    } else if ($booking->payment_type == 'flutterwave') {
                        $data['transaction_id']  = $request->payment_id;
                        $data['currency_symbol'] = $booking->currency_symbol;
                        $data['currency_name']   = $booking->currency_name;
                        $data['amount']          = $booking->amount;
                        $data['status']          = 'successfull';
                        $data['payment_type'] = 'Flutterwave';
                    } else if ($booking->payment_type == 'paytm') {
                        $data['transaction_id']  = $request->payment_id;
                        $data['currency_symbol'] = $booking->currency_symbol;
                        $data['currency_name']   = $booking->currency_name;
                        $data['amount']          = $booking->amount;
                        $data['status']          = 'pending';
                        $data['payment_type']    = 'Paytm';
                    } else if ($booking->payment_type == 'paystack') {
                        $data['transaction_id']   = $request->payment_id;
                        $data['currency_symbol']  = $booking->currency_symbol;
                        $data['currency_name']    = $booking->currency_name;
                        $data['amount']           = $booking->amount;
                        $data['status']           = 'successfull';
                        $data['payment_type'] = 'Paystack';
                    } else if ($booking->payment_type == 'coingate') {
                        $data['status'] = 'pending';
                    } else if ($booking->payment_type == 'mercado') {
                        $data['status'] = 'pending';
                    }
                } else {
                    $data['status'] = 'free';
                }
                $data['booking_id'] = $booking->id;
                $data['user_id'] = $user_id;
                $data['booking_slots_date'] = ($request->booking_date) ? $request->booking_date : null;
                $data['booking_slots'] = ($request->slot) ? (is_array($request->slot) ? implode(',', $request->slot) : $request->slot) : null;
                $data['booking_seats_date'] = $currdate->format('Y-m-d');
                $data['booking_seats_session'] = ($request->session_time) ? $request->session_time : null;
                $data['booking_seats'] = ($request->seat) ? (is_array($request->seat) ? implode(',', $request->seat) : $request->seat) : null;
                $data['json']       = json_encode($array);
                $booking_value      = BookingValue::create($data);
            }
            $booking_valuearray = json_decode($booking_value->json);
            $emails = explode(',', $booking->business_email);
            if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                if ($emails) {
                    try {
                        Mail::to($emails)->send(new BookingSubmitEmail($booking_value, $booking_valuearray));
                    } catch (\Exception $e) {
                    }
                }
                foreach ($client_emails as $client_email) {
                    try {
                        Mail::to($client_email)->send(new BookingThanksmail($booking_value));
                    } catch (\Exception $e) {
                    }
                }
            }
            $user = User::where('type', 'Admin')->first();
            $notifications_setting = NotificationsSetting::where('title', 'new booking survey details')->first();
            if (isset($notifications_setting)) {
                if ($notifications_setting->notify == '1') {
                    $user->notify(new NewBookingSurveyDetails($booking));
                } elseif ($notifications_setting->email_notification == '1') {
                    if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                        if (MailTemplate::where('mailable', BookingSubmitEmail::class)->first()) {
                            try {
                                Mail::to($booking_value->email)->send(new BookingSubmitEmail($booking_value, $booking_valuearray));
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }
            if ($booking->payment_type != 'coingate' && $booking->payment_type != 'mercado') {
                $success_msg = strip_tags('Thank You');
            }
            if ($request->form_value_id) {
                $success_msg = strip_tags('Thank You');
            }
            $hashids = new Hashids('', 20);
            $id = $hashids->encodeHex($booking_value->id);
            $route = route('appointment.edit', $id);
            if (isset($request->ajax)) {
                return response()->json(['is_success' => true, 'message' => $success_msg, 'redirect' => $route], 200);
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

    public function bookingCalendar()
    {
        $arrayJson = [];
        $bookingValues = BookingValue::all();
        foreach ($bookingValues as $bookingValue) {
            if ($bookingValue->booking_seats_date) {
                $slots = explode(',', $bookingValue->booking_slots);
                foreach ($slots as $slot) {
                    $date = Carbon::parse($bookingValue->booking_seats_date);
                    $arrayJson[] = [
                        "id"         => $bookingValue->id,
                        "title"      => $bookingValue->Booking->business_name . ' ' . $slot,
                        "start"      => $bookingValue->booking_slots_date,
                        "end"        => $date->format('Y-m-d H:i:s'),
                        "className"  => 'event-primary',
                        'url'        => route('timing.bookingvalues.show', $bookingValue->id),
                        "allDay"     => true,
                    ];
                }
            }
        }
        $data = json_encode($arrayJson);
        return view('bookings.calender', compact('data'));
    }
}
