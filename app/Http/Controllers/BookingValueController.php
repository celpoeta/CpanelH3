<?php

namespace App\Http\Controllers;

use App\DataTables\BookingValueDataTable;
use App\Models\Booking;
use App\Models\BookingValue;
use App\Models\SeatWiseBooking;
use App\Models\TimeWiseBooking;
use Carbon\Carbon;
use Google\Service\Books;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingValueController extends Controller
{
    public function showBookingsForms($booking_id, BookingValueDataTable $dataTable)
    {
        $bookings = Booking::get();
        $bookings_details = Booking::find($booking_id);
        return $dataTable->with('booking_id', $booking_id)->render('booking_value.index', compact('bookings', 'bookings_details'));
    }

    public function timingBookingvaluesShow($id)
    {
        try {
            $booking_value = BookingValue::find($id);
            $array = json_decode($booking_value->json);
        } catch (\Throwable $th) {
            return redirect()->back()->with('errors', $th->getMessage());
        }
        return view('booking_value.timing_view', compact('booking_value', 'array'));
    }

    public function seatsBookingvaluesShow($id)
    {
        try {
            $booking_value = BookingValue::find($id);
            $array = json_decode($booking_value->json);
        } catch (\Throwable $th) {
            return redirect()->back()->with('errors', $th->getMessage());
        }
        return view('booking_value.seats_view', compact('booking_value', 'array'));
    }

    public function destroy($id)
    {
        BookingValue::find($id)->delete();
        return redirect()->back()->with('success',  __('Booking deleted successfully.'));
    }

    public function timeBookingEdit($id)
    {
        $user = \Auth::user();
        $user_role = $user->roles->first()->id;
        $booking_value = BookingValue::find($id);
        $booking = Booking::find($booking_value->booking_id);
        $timewisebooking = TimeWiseBooking::where('booking_id', $booking->id)->first();
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
        $array = json_decode($booking_value->json);
        return view('bookings.appoinment_time', compact('booking', 'booking_value', 'timewisebooking', 'array', 'selectedDays', 'booking_start_date', 'booking_end_date', 'currdate'));
    }

    public function seatsBookingEdit($id)
    {
        $booking_value = BookingValue::find($id);
        $booking = Booking::find($booking_value->booking_id);
        $seatwisebooking = SeatWiseBooking::where('booking_id', $booking->id)->first();
        $timeZone = $seatwisebooking->time_zone;
        $currdate = Carbon::now($timeZone);
        $array = json_decode($booking_value->json);
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
        return view('bookings.appoinment_seat', compact('booking', 'seatwisebooking', 'booking_value', 'array', 'sessionhtml'));
    }

    public function editappoinment($id)
    {
        $hashids = new Hashids('', 20);
        $id = $hashids->decodeHex($id);
        $booking_value = BookingValue::find($id);
        $booking = Booking::find($booking_value->booking_id);
        $array = json_decode($booking_value->json);
        if ($booking->booking_slots == 'seats_wise_booking') {
            $seatwisebooking = SeatWiseBooking::where('booking_id', $booking->id)->first();
            $timeZone = $seatwisebooking->time_zone;
            $currdate = Carbon::now($timeZone);
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
                    if ($booking_value->booking_seats_date == $currdate->format('Y-m-d')) {
                        if ($booking_value->booking_seats_session == $start_time->format('H:i') . '-' . $end_time->format('H:i')) {
                            $Attribute = 'checked';
                        }
                    } else {
                        if ($nearestSlotKey === $seatsessionJsonkey) {
                            $Attribute = 'checked';
                        }
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
            return view('booking_value.appointment.seat', compact('booking', 'booking_value', 'seatwisebooking', 'array', 'sessionhtml'));
        } elseif ($booking->booking_slots == 'time_wise_booking') {
            $timewisebooking = TimeWiseBooking::where('booking_id', $booking->id)->first();
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
            return view('booking_value.appointment.time', compact('booking', 'booking_value', 'array', 'timewisebooking', 'booking_start_date', 'booking_end_date', 'selectedDays', 'currdate'));
        }
    }

    public function SlotCancel($id)
    {
        $booking_value = BookingValue::find($id);
        $booking_value->booking_slots_date = null;
        $booking_value->booking_slots = null;
        $booking_value->booking_status = 0;
        $booking_value->save();
        return redirect()->back()->with('success', __('Slots cancelled successfully.'));
    }

    public function SeatCancel($id)
    {
        $booking_value = BookingValue::find($id);
        $booking_value->booking_seats_date = null;
        $booking_value->booking_seats_session = null;
        $booking_value->booking_seats = null;
        $booking_value->booking_status = 0;
        $booking_value->save();
        return redirect()->back()->with('success', __('Seats cancelled successfully.'));
    }
}
