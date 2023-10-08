<div class="col-md-6">
    <div id="pc-datepicker"></div>
    {!! Form::hidden('booking_date', null, []) !!}
</div>
<div class="col-md-6">
    @if ($timewisebooking->enable_note == 1)
        <div class="alert alert-warning" role="alert">
            <strong>{{ __('Note : ') }}</strong> {{ $timewisebooking->note }}
        </div>
    @endif
    <div class="form-group appointmentSlot">

    </div>
</div>
