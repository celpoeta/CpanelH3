<?php

namespace App\Mail;

use App\Models\FormValue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmitEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $form_value;
    protected $form_valuearray;

    public function __construct(FormValue $form_value, $form_valuearray)
    {
        $this->form_value = $form_value;
        $this->form_valuearray = $form_valuearray;
    }

    public function build()
    {
        return $this->markdown('emails.form_submit')->with(['form_value' => $this->form_value, 'form_valuearray' => $this->form_valuearray])->subject('New survey Submited - ' . $this->form_value->Form->title);
    }
}
