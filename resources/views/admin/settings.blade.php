@extends('layouts.app')

@section('content')
    {{--current id = 5--}}
    <!-- Static navbar -->
    @include('admin.navigator')

    <!-- Main component for a primary marketing message or call to action -->
    <div id ='app' style="position: relative; height: calc(100vh - 100px)">
        <div id="settings_content" class="row">
            <div id="reservations_content" class="col-md-2">
                <div class="navbar navbar-default">
                    <ul class="nav navbar-nav" id="go-container">
                        <li><a destination="#weekly_sessions" class="btn go">Weekly Sessions</a></li>
                        <li><a destination="#special_sessions" class="btn go">Special Sessions</a></li>
                        <li><a destination="#buffer" class="btn go">Buffer</a></li>
                        <li><a destination="#notification" class="btn go">Notification</a></li>
                        <li><a destination="#settings" class="btn go">Settings</a></li>
                    </ul>
                </div>
            </div>
            @verbatim
            <div class="col-md-10" id="admin-step-container">
                <div id="weekly_sessions" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Weekly Sessions</h1>
                    </div>
                    <div class="modal-body">
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button class="btn btn-success">Save</button>
                    </div>
                </div>
                <div id="special_sessions" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Special Sessions</h1>
                    </div>
                    <div class="modal-body">
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button class="btn btn-success">Save</button>
                    </div>
                </div>
                <div id="buffer" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Buffer</h1>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="1" class="col-md-3">Max number of days in advance</label>
                            <div class="col-md-4">
                                <input class="form-control" type="number" :value="buffer.MAX_DAYS_IN_ADVANCE" id="1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="2" class="col-md-3">Min hours in advance prior to a reservation time</label>
                            <div class="col-md-4">
                                <input class="form-control" type="number" :value="buffer.MIN_HOURS_IN_ADVANCE_SLOT_TIME" id="2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="3" class="col-md-3">Min hours in advance prior to a session</label>
                            <div class="col-md-4">
                                <input class="form-control" type="number" :value="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME" id="3">
                            </div>
                        </div>

                        <div class="bg-info">
                            <p class="text-muted">Max number of days in advance – The maximum number of days in
                                advance a customer may make a reservation. Min hours in advance prior to a
                                reservation time – The minimum number of hours buffer before a reservation timing is
                                no longer available for booking.</p>
                            <p class="text-muted">Min hours in advance prior to a session – The minimum number of
                                hours buffer before a reservation session is no longer available for booking.</p>
                            <p class="text-muted">The session’s start timing is defined as the earliest time within
                                that session.</p>
                        </div>
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button class="btn btn-success btn_save">Save</button>
                    </div>
                </div>
                <div id="notification" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Notification</h1>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="4" class="col-md-3">Send SMS on booking</label>
                            <!-- Rounded switch -->
                            <label class="switch">
                                <input type="checkbox" id="4">
                                <div class="slider round"></div>
                            </label>
                        </div>

                        <div class="form-group row">
                            <label for="5" class="col-md-3">Send SMS to confirm reservation</label>
                            <!-- Rounded switch -->
                            <label class="switch">
                                <input type="checkbox" id="5">
                                <div class="slider round"></div>
                            </label>
                        </div>

                        <div class="form-group row">
                            <label for="6" class="col-md-3">Hours before reservation time to send confirmation
                                SMS</label>
                            <div class="col-md-4">
                                <input class="form-control" type="number" value="2" id="6">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="modal-footer">
                        <button class="btn btn-success">Save</button>
                    </div>
                </div>
                <div id="settings" class="modal-content admin-step"></div>
            </div>
            @endverbatim
        </div>
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
<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url_mix('js/admin-settings.js') }}"></script>
<script></script>
@endpush