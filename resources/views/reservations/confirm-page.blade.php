@extends('layouts.app')

@section('content')
    <div class="box form-step" id="app">
        @include('reservations.booking-summary', ['is_summary_page' => false]);
    </div>
    {{--modal--}}
    <div class="modal fade" id="ajax-dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Searching for available time</h4>
                </div>
                <div class="modal-body center">
                    {{--<div style="width: 100px; display: inline-block">--}}
                    <div style="width: 184px; display: inline-block">
                        {{--<img src="{{ url('images/gears.svg') }}">--}}
                        {{--<img src="{{ url('images/hourglass.svg') }}">--}}
                        <img src="{{ url('images/gear.svg') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <hr>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    @include('debug.redux-state')
@endsection

@push('script')
<script>@php
    $json_state = json_encode($state);
    echo "window.state = $json_state;"
@endphp</script>
<script src="{{ url('js/vue.min.js') }}"></script>
<script src="{{ url_mix('js/reservation-confirm.js') }}"></script>
@endpush