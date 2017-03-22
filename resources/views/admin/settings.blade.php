@extends('layouts.app')

@section('content')
    {{--current id = 5--}}
    <!-- Static navbar -->
    @include('admin.navigator')

    <!-- Main component for a primary marketing message or call to action -->
    <div id='app' style="position: relative; height: calc(100vh - 100px)">
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
                        <h2>Striped Rows</h2>
                        <p>The .table-striped class adds zebra-stripes to a table:</p>

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mondays</th>
                                <th>Tuesdays</th>
                                <th>Wednesdays</th>
                                <th>Thursdays</th>
                                <th>Fridays</th>
                                <th>Saturdays</th>
                                <th>Sundays</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="item in weekly_sessions">
                                <tr class="small-label">
                                    <td>{{ item.session_name }}</td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_mondays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_tuesdays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_wednesdays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_thursdays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_fridays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_satdays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                    <td>
                                        <input type="checkbox" :id="'session_' + item.id"
                                               :checked="(item.on_sundays == 1) ? 'checked' : false"/>
                                        <label :for="'session_' + item.id">
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <table class="table table-striped sub-level">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Name</th>
                                                <th>First arrival time</th>
                                                <th>Last arrival time</th>
                                                <th>Interval time</th>
                                                <th>Capacity 1</th>
                                                <th>Capacity 2</th>
                                                <th>Capacity 3_4</th>
                                                <th>Capacity 5_6</th>
                                                <th>Capacity 7_x</th>
                                                <th>Max pax</th>
                                                <th>Children</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <template v-for="timing in item.timings">
                                                <tr>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" :checked="(timing.disabled == 1) ? 'checked' : false">
                                                            <div class="slider round"></div>
                                                        </label>
                                                    </td>
                                                    <td>{{ timing.timing_name }}</td>
                                                    <td>{{ timing.first_arrival_time }}</td>
                                                    <td>{{ timing.last_arrival_time }}</td>
                                                    <td>{{ timing.interval_minutes }}</td>
                                                    <td>{{ timing.capacity_1 }}</td>
                                                    <td>{{ timing.capacity_2 }}</td>
                                                    <td>{{ timing.capacity_3_4 }}</td>
                                                    <td>{{ timing.capacity_5_6 }}</td>
                                                    <td>{{ timing.capacity_7_x }}</td>
                                                    <td>{{ timing.max_pax }}</td>
                                                    <td>
                                                        <input type="checkbox" :id="'children_allowed_' + timing.id"
                                                               :checked="(timing.children_allowed == 1) ? 'checked' : false"/>
                                                        <label :for="'children_allowed_' + timing.id">
                                                        </label>
                                                    </td>
                                                </tr>
                                            </template>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="modal-footer">
                    </div>
                </div>
                <div id="special_sessions" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Special Sessions</h1>
                    </div>
                    <div class="modal-body">
                        <h2>Striped Rows</h2>
                        <p>The .table-striped class adds zebra-stripes to a table:</p>

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>On</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="item in special_sessions">
                                <tr class="small-label">
                                    <td>{{ item.session_name }}</td>
                                    <td>{{ item.one_off_date }}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <table class="table table-striped sub-level">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Name</th>
                                                <th>First arrival time</th>
                                                <th>Last arrival time</th>
                                                <th>Interval time</th>
                                                <th>Capacity 1</th>
                                                <th>Capacity 2</th>
                                                <th>Capacity 3_4</th>
                                                <th>Capacity 5_6</th>
                                                <th>Capacity 7_x</th>
                                                <th>Max pax</th>
                                                <th>Children</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <template v-for="timing in item.timings">
                                                <tr>
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" :checked="(timing.disabled == 1) ? 'checked' : false">
                                                            <div class="slider round"></div>
                                                        </label>
                                                    </td>
                                                    <td>{{ timing.timing_name }}</td>
                                                    <td>{{ timing.firt_arrival_time }}</td>
                                                    <td>{{ timing.last_arrival_time }}</td>
                                                    <td>{{ timing.interval_minutes }}</td>
                                                    <td>{{ timing.capacity_1 }}</td>
                                                    <td>{{ timing.capacity_2 }}</td>
                                                    <td>{{ timing.capacity_3_4 }}</td>
                                                    <td>{{ timing.capacity_5_6 }}</td>
                                                    <td>{{ timing.capacity_7_x }}</td>
                                                    <td>{{ timing.max_pax }}</td>
                                                    <td>
                                                        <input type="checkbox" :id="'children_allowed_' + timing.id"
                                                               :checked="(timing.children_allowed == 1) ? 'checked' : false"/>
                                                        <label :for="'children_allowed_' + timing.id">
                                                        </label>
                                                    </td>
                                                </tr>
                                            </template>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
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
                                <input class="form-control" type="number" :value="buffer.MIN_HOURS_IN_ADVANCE_SLOT_TIME"
                                       id="2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="3" class="col-md-3">Min hours in advance prior to a session</label>
                            <div class="col-md-4">
                                <input class="form-control" type="number"
                                       :value="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME" id="3">
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