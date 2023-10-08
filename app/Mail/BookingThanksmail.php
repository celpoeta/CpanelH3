<?php

namespace App\Mail;

use App\Models\BookingValue;
use Hashids\Hashids;
use Spatie\MailTemplates\TemplateMailable;

class BookingThanksmail extends TemplateMailable
{
    public $title;
    public $thanks_msg;
    public $image;
    public $link;

    public function __construct(BookingValue $booking_value)
    {
        $this->title = $booking_value->Booking->business_name;
        if (!empty($booking_value->Booking->business_logo)) {
            $this->image = asset('storage/app/' . $booking_value->Booking->business_logo);
        }
        $this->thanks_msg = strip_tags('Your booking is successfully completed!');
        $hashids = new Hashids('', 20);
        $id = $hashids->encodeHex($booking_value->id);
        $route = route('appointment.edit', $id);
        $this->link = $route;
    }

    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
