<div class="col-md-6">
    <div class="form-group">
        {!! $sessionhtml !!}
    </div>
</div>
<div class="col-md-6">
    @if ($seatwisebooking->enable_note == 1)
        <div class="alert alert-warning" role="alert">
            <strong>{{ __('Note : ') }}</strong> {{ $seatwisebooking->note }}
        </div>
    @endif
    <table id="seat-table">
        <tbody>

        </tbody>
    </table>
</div>