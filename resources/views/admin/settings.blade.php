@extends('layouts.admin')
@section('content')
    <div id='app'>
        <!-- Static navbar -->
        @include('admin.navigator')
         <!-- Main component for a primary marketing message or call to action -->
        <div>
            <div id="settings_content" class="row">
                <div id="reservations_content" class="col-md-2">
                    <div class="navbar navbar-default">
                        <ul class="nav navbar-nav" id="go-container">
                            <li><a destination="weekly_sessions_view"  class="btn go">Weekly Sessions</a></li>
                            <li><a destination="special_sessions_view" class="btn go">Special Sessions</a></li>
                            <li><a destination="buffer"                class="btn go">Buffer</a></li>
                            <li><a destination="notification"          class="btn go">Notification</a></li>
                            <li><a destination="settings"              class="btn go">Settings</a></li>
                            <li><a destination="deposit"               class="btn go">Deposit</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-10" id="admin-step-container" style="position: relative; height: calc(100vh - 100px); overflow-y: scroll; overflow-x: hidden;">
                    <div id="weekly_sessions_view" class="modal-content admin-step">
                        <div class="modal-header">
                            <span class="h1">Weekly Sessions View</span>
                            <button destination="weekly_sessions" class="btn bg-info pull-right go">Edit</button>
                        </div>
                        <div class="modal-body">
                            @include('admin.settings.sessions_view_mode')
                        </div>
                    </div>
                    <div id="weekly_sessions" class="modal-content admin-step">
                        <div class="modal-header">
                            <span class="h1">Weekly Sessions</span>
                            <button v-on:click="_addWeeklySession"
                                    class="btn bg-info pull-right"
                                    style="border-radius: 20px">Add Session</button>
                        </div>
                        <div class="modal-body">
                            @include('admin.settings.sessions_edit_mode')
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                            <button v-on:click="_updateWeeklySessions"
                                    class="btn bg-info">Save</button>
                        </div>
                    </div>
                    <div id="special_sessions_view" class="modal-content admin-step">
                        <div class="modal-header">
                            <span class="h1">Special Sessions View</span>
                            <button destination="special_sessions" class="btn bg-info pull-right go">Edit</button>
                        </div>
                        <div class="modal-body">
                            @include('admin.settings.sessions_special_view_mode')
                        </div>
                    </div>
                    <div id="special_sessions" class="modal-content admin-step">
                        <div class="modal-header">
                            <span class="h1">Special Sessions</span>
                            <button v-on:click="_addSpecialSession"
                                    class="btn bg-info pull-right" style="border-radius: 20px">Add Session</button>
                        </div>
                        <div class="modal-body">
                            @include('admin.settings.sessions_special_edit_mode')
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                            <button v-on:click="_updateSpecialSession"
                                    class="btn bg-info">Save</button>
                        </div>
                    </div>
                    <div id="buffer" class="modal-content admin-step">
                        <div class="modal-header">
                            <h1>Buffer</h1>
                        </div>
                        <div class="modal-body">
                            @verbatim
                            <div class="form-group row">
                                <label for="buffer_MAX_DAYS_IN_ADVANCE" class="col-md-3">Max number of days in advance</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           v-model="buffer.MAX_DAYS_IN_ADVANCE"
                                           :value="buffer.MAX_DAYS_IN_ADVANCE"
                                           id="buffer_MAX_DAYS_IN_ADVANCE">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="buffer_MIN_HOURS_IN_ADVANCE_SLOT_TIME" class="col-md-3">Min hours in advance prior to a reservation time</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           v-model="buffer.MIN_HOURS_IN_ADVANCE_SLOT_TIME"
                                           :value="buffer.MIN_HOURS_IN_ADVANCE_SLOT_TIME"
                                           id="buffer_MIN_HOURS_IN_ADVANCE_SLOT_TIME">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME" class="col-md-3">Min hours in advance prior to a session</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           v-model="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME"
                                           :value="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME"
                                           id="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME">
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
                            @endverbatim
                        </div>
                        <hr>
                        <div class="modal-footer">
                            <button v-on:click="_updateBuffer"
                                    class="btn bg-info">Save</button>
                        </div>
                    </div>
                    <div id="notification" class="modal-content admin-step">
                        @verbatim
                        <div class="modal-header">
                            <h1>Notification</h1>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group row">
                                        <label for="notification_SEND_SMS_ON_BOOKING" class="col-md-4">Send SMS on booking</label>
                                        <!-- Rounded switch -->
                                        <label class="switch">
                                            <input type="checkbox"
                                                   v-model="notification.SEND_SMS_ON_BOOKING"
                                                   :value="notification.SEND_SMS_ON_BOOKING"
                                                   id="notification_SEND_SMS_ON_BOOKING">
                                            <div class="slider round"></div>
                                        </label>
                                    </div>

                                    <div class="form-group row">
                                        <label for="notification_SEND_SMS_CONFIRMATION" class="col-md-4">Send SMS to confirm reservation</label>
                                        <!-- Rounded switch -->
                                        <label class="switch">
                                            <input type="checkbox"
                                                   v-model="notification.SEND_SMS_CONFIRMATION"
                                                   :value="notification.SEND_SMS_CONFIRMATION"
                                                   id="notification_SEND_SMS_CONFIRMATION"
                                            >
                                            <div class="slider round"></div>
                                        </label>
                                    </div>

                                    <div class="form-group row">
                                        <label for="notification_HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM" class="col-md-4">Hours before reservation time to send confirmation
                                            SMS</label>
                                        <input type="number" class="form-control" style="width: 80px;display: inline-block;"
                                               v-model="notification.HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM"
                                               :value="notification.HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM"
                                               id="notification_HOURS_BEFORE_RESERVATION_TIME_TO_SEND_CONFIRM">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="border: 1px solid #e5e5e5; border-radius: 3px; padding: 20px">
                                        <div class="row">
                                            <p><span class="h3 pull-left" style="margin: 0">SMSes</span> <span class="h4 pull-right" style="margin: 0"><span>{{ notification.sms_credit_balance }}</span> credits remaining</span></p>
                                        </div>
                                        <div class="row">
                                            <p></p>
                                            <p class="bg-warning">You are running on low SMS credits, please recharge ASAP to prevent service interruption!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                            <button v-on:click="_updateNotification"
                                    class="btn bg-info">Save</button>
                        </div>
                        @endverbatim
                    </div>
                    <div id="settings" class="modal-content admin-step">
                        @verbatim
                        <div class="modal-header">
                            <h1>Settings</h1>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group row">
                                        <label for="settings_SMS_SENDER_NAME" class="col-md-4">SMS sender name</label>
                                            <input type="text" class="form-control" style="width: 200px;display: inline-block;"
                                                   spellcheck="false"
                                                   v-model="settings.SMS_SENDER_NAME"
                                                   :value="settings.SMS_SENDER_NAME"
                                                   id="settings_SMS_SENDER_NAME">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bg-info">User list</h4>
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Firstname</th>
                                            <th>Lastname</th>
                                            <th>Email</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>John</td>
                                            <td>Doe</td>
                                            <td>john@example.com</td>
                                        </tr>
                                        <tr>
                                            <td>Mary</td>
                                            <td>Moe</td>
                                            <td>mary@example.com</td>
                                        </tr>
                                        <tr>
                                            <td>July</td>
                                            <td>Dooley</td>
                                            <td>july@example.com</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                            <button v-on:click="_updateSettings"
                                    class="btn bg-info">Save</button>
                        </div>
                        @endverbatim
                    </div>
                    <div id="deposit" class="modal-content admin-step">
                        @verbatim
                        <div class="modal-header">
                            <h1>Deposit</h1>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div style="padding: 0 15px">
                                    <label class="switch" style="transform: translateY(5px)">
                                        <input type="checkbox"
                                               v-model="deposit.REQUIRE_DEPOSIT"
                                               :value="deposit.REQUIRE_DEPOSIT"
                                               id="deposit_REQUIRE_DEPOSIT"
                                        >
                                        <div class="slider round"></div>
                                    </label>
                                    <span style="margin: 0 15px">Require deposit for reservation when reservation above</span>
                                    <input type="number" class="form-control" style="width: 60px; display: inline-block;"
                                           v-model="deposit.DEPOSIT_THRESHOLD_PAX"
                                           :value="deposit.DEPOSIT_THRESHOLD_PAX"
                                           id="deposit_DEPOSIT_THRESHOLD_PAX">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <!-- div style="padding: 0 15px" -->
                                    <div>
                                        <h4>Deposit Calculation</h4>
                                        <input type="number" class="form-control" style="width: 60px;display: inline-block;"
                                               placeholder="$5"
                                               v-model="deposit.DEPOSIT_VALUE"
                                               :value="deposit.DEPOSIT_VALUE"
                                               id="deposit_DEPOSIT_VALUE">
                                        <select v-model="deposit.DEPOSIT_TYPE" style="display: inline-block">
                                            <option disabled>Please deposit type</option>
                                            <option value="0">Fixed Sum</option>
                                            <option value="1">Per Pax</option>
                                        </select>
                                    </div>
                                    <p></p>
                                    <!-- div style="padding: 0 15px" -->
                                    <div>
                                        <div style="padding: 1px 15px; box-sizing: border-box;" class="bg-info">
                                            <h4>Example</h4>
                                            <p>For a booking of 20 pax, at $5 per pax, a total payment authorization of $100 will be obtained.
                                                If "fixed sum" is selected, then a payment authorization of $5 will be obtained.</p>
                                        </div>
                                    </div>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                            <button v-on:click="_updateDeposit"
                                    class="btn bg-info">Save</button>
                        </div>
                        @endverbatim
                    </div>
                </div>
            </div>
        </div>
        @include('partial.toast')
    </div>
    @include('debug.redux-state')
@endsection

@push('script')
{{--<script src="{{ url('js/hashids.min.js') }}"></script>--}}
<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url_mix('js/admin-settings.js') }}"></script>
<script></script>
@endpush