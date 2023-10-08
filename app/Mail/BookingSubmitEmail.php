<?php

namespace App\Mail;

use App\Models\FormValue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingSubmitEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $booking_value;
    protected $booking_valuearray;

    public function __construct($booking_value, $booking_valuearray)
    {
        $this->booking_value = $booking_value;
        $this->booking_valuearray = $booking_valuearray;
    }

    public function build()
    {
        return $this->markdown('emails.booking_submit')->with(['booking_value' => $this->booking_value, 'booking_valuearray' => $this->booking_valuearray])->subject('New survey Submited - ' . $this->booking_value->Booking->business_name);
    }
}
