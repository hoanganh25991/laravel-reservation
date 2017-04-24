@extends('layouts.app')

@section('content')
    <div class="box form-step" id="app">
        @include('reservations.booking-summary', ['is_summary_page' => false])
    </div>
    @include('reservations.ajax-dialog')
    @if(env('APP_ENV') != 'production')
        @include('debug.redux-state')
    @endif
@endsection

@push('script')
<script>@php
    $json_state = json_encode($state);
    echo "window.state = $json_state;"
@endphp</script>
<script src="{{ url('js/vue.min.js') }}"></script>
<script src="{{ url_mix('js/reservation-confirm.js') }}"></script>
@endpush