@extends('layouts.admin')
@section('content')
    <div id='app'>
        <div>
            <div id="settings_content" class="row">
                <div id="reservations_content" class="col-md-2">
                    <div class="navbar navbar-default" style="max-width: 200px;">
                        <ul class="nav navbar-nav" id="go-container">
                            <li style="float: none"><a destination="weekly_sessions_view"  class="btn go">Weekly Sessions</a></li>
                            <li style="float: none"><a destination="special_sessions_view" class="btn go">Special Sessions</a></li>
                            <li style="float: none"><a destination="buffer"                class="btn go">Buffer</a></li>
                            <li style="float: none"><a destination="notification"          class="btn go">Notification</a></li>
                            <li style="float: none"><a destination="settings"              class="btn go">Settings</a></li>
                            <li style="float: none"><a destination="deposit"               class="btn go">Payment Authorization</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-10" id="admin-step-container" style="position: relative; height: 100vh; overflow-y: scroll; overflow-x: hidden;">
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
                                           id="buffer_MAX_DAYS_IN_ADVANCE">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="buffer_MIN_HOURS_IN_ADVANCE_SLOT_TIME" class="col-md-3">
                                    Min hours in advance to allow new bookings prior to a reservation time</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           v-model="buffer.MIN_HOURS_IN_ADVANCE_SLOT_TIME"
                                           id="buffer_MIN_HOURS_IN_ADVANCE_SLOT_TIME">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME" class="col-md-3">
                                    Min hours in advance to allow new bookings prior to a session</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           v-model="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME"
                                           id="buffer.MIN_HOURS_IN_ADVANCE_SESSION_TIME">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="buffer.MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME"
                                       class="col-md-3">Min hours in advance to allow cancellation/amendment prior to a reservation time</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           :value="buffer.MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME"
                                           v-on:change="_updateBufferMinHoursAllowCancellationOrAmendment($event.target.value)"
                                           id="buffer.MIN_HOURS_IN_ADVANCE_TO_ALLOW_CANCELLATION_AMENDMENT_PRIOR_TO_RESERVATION_TIME">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="buffer.MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT"
                                       class="col-md-3">Max pax for self cancellation/amenment</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number"
                                           v-model="buffer.MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT"
                                           id="buffer.MAX_PAX_FOR_SELF_CANCELLATION_AMENDMENT">
                                </div>
                            </div>

                            <div class="bg-info">
                                <p class="text-muted">Max number of days in advance: The maximum number of days in
                                    advance a customer may make a reservation.</p>
                                <p class="text-muted">Min hours in advance prior to a
                                    reservation time: The minimum number of hours buffer before a reservation timing is
                                    no longer available for booking.</p>
                                <p class="text-muted">Min hours in advance prior to a session: The minimum number of
                                    hours buffer before a reservation session is no longer available for booking. The session’s start timing is defined as the earliest time within that session. Set value to -1000 to <strong>ignore checks</strong> for minimum hours in advance prior to session.</p>
                                <p class="text-muted">The max pax for self cancellation/amendment after which customer is not allowed to make their own cancellation/amendment. e.g. if max pax = 16. If reservation has 17 or more pax, customer cannot make changes to reservation themselves. Set 0 if <strong>don't allow</strong> customer to modify their own reservation, set 1000 if <strong>allow all</strong> reservations to be modified by customers</p>
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
                                        <label class="col-md-4">Send SMS on booking</label>
                                        <!-- Rounded switch -->
                                        <label class="switch">
                                            <input v-on:click="notification.SEND_SMS_ON_BOOKING = !+notification.SEND_SMS_ON_BOOKING"
                                                   :class="+notification.SEND_SMS_ON_BOOKING ? 'switchOn' : ''">
                                            <div class="slider round"></div>
                                        </label>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-4">Send Email on booking</label>
                                        <!-- Rounded switch -->
                                        <label class="switch">
                                            <input v-on:click="notification.SEND_EMAIL_ON_BOOKING = !+notification.SEND_EMAIL_ON_BOOKING"
                                                   :class="+notification.SEND_EMAIL_ON_BOOKING ? 'switchOn' : ''">
                                            <div class="slider round"></div>
                                        </label>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-4">Send SMS to confirm reservation</label>
                                        <!-- Rounded switch -->
                                        <label class="switch">
                                            <input v-on:click="notification.SEND_SMS_CONFIRMATION = !+notification.SEND_SMS_CONFIRMATION"
                                                   :class="+notification.SEND_SMS_CONFIRMATION ? 'switchOn' : ''">
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
                                <div class="col-md-5">
                                    <div style="border: 1px solid #e5e5e5; border-radius: 3px; padding: 20px">
                                        <div class="row">
                                            <p>
                                                <span class="h3 pull-left" style="margin: 0">SMSes</span>
                                                <span class="h3 pull-right" style="margin: 0"><span class="bg-info">
                                                        {{ notification.sms_credit_balance }}</span> credits remaining
                                                </span>
                                            </p>
                                        </div>
                                        <div class="row" v-show="notification.sms_credit_balance < 200">
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
                                    <div class="form-group">
                                        <label for="settings_SMS_SENDER_NAME" class="col-md-4 text-right">SMS sender name</label>
                                            <input type="text" class="form-control" style="width: 200px;display: inline-block;"
                                                   spellcheck="false"
                                                   v-model="settings.SMS_SENDER_NAME"
                                                   :value="settings.SMS_SENDER_NAME"
                                                   id="settings_SMS_SENDER_NAME">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="settings_OVERALL_MIN_PAX" class="col-md-4 text-right">Overall min pax</label>
                                        <input type="number" class="form-control" style="width: 100px;display: inline-block;"
                                               spellcheck="false"
                                               v-model="settings.OVERALL_MIN_PAX"
                                               :value="settings.OVERALL_MIN_PAX"
                                               id="settings_OVERALL_MIN_PAX">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="settings_OVERALL_MAX_PAX" class="col-md-4 text-right">Overall max pax</label>
                                        <input type="number" class="form-control" style="width: 100px;display: inline-block;"
                                               spellcheck="false"
                                               v-model="settings.OVERALL_MAX_PAX"
                                               :value="settings.OVERALL_MAX_PAX"
                                               id="settings_OVERALL_MAX_PAX">
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
                                            <th>Display name</th>
                                            <th>User name</th>
                                            <th>Email</th>
                                            <th>Assigned Outlet</th>
                                            <th>Role</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-for="(user, user_index) in settings.users">
                                            <tr     :user-index='user_index'
                                                    v-on:click="_updateUserDialog">
                                                <td>{{ user.display_name }}</td>
                                                <td>{{ user.user_name }}</td>
                                                <td>{{ user.email }}</td>
                                                <td>
                                                    <select multiple v-model="user.outlet_ids"
                                                            class="multiple-select"

                                                    >
                                                        <template v-for="(outlet, outlet_index) in outlets">
                                                            <option :value="outlet.id">{{ outlet.outlet_name }}</option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select v-model="user.permission_level">
                                                        <option value="0">Reservations</option>
                                                        <option value="5">Master Reservations</option>
                                                        <option value="10">Administrator</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </template>
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
                            <h1>Payment Authorization</h1>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div style="padding: 0 15px">
                                    <label class="switch" style="transform: translateY(5px)">
                                        <input v-on:click="deposit.REQUIRE_DEPOSIT = !+deposit.REQUIRE_DEPOSIT"
                                               :class="+deposit.REQUIRE_DEPOSIT ? 'switchOn' : ''"
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
                                        <h4>Payment Authorization Calculation</h4>
                                        <div class="input-group col-md-6">
                                            <span class="input-group-addon">{{ deposit.PAYPAL_CURRENCY }}</span>
                                            <input type="number" class="form-control" style="min-width: 60px;"
                                                   placeholder="$5"
                                                   v-model="deposit.DEPOSIT_VALUE"
                                                   :value="deposit.DEPOSIT_VALUE"
                                                   id="deposit_DEPOSIT_VALUE">
                                            <div class="input-group-addon">
                                                <select v-model="deposit.DEPOSIT_TYPE" class="input-group">
                                                    <option disabled>Please select deposit type</option>
                                                    <option value="0">Fixed Sum</option>
                                                    <option value="1">Per Pax</option>
                                                </select>
                                            </div>
                                        </div>
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
                                    <br/>
                                    <div class="form-group">
                                        <h4>Paypal Token</h4>
                                        <input type="text" spellcheck="false"
                                               v-model="deposit.PAYPAL_TOKEN"
                                               id="deposit_PAYPAL_TOKEN"
                                               class="form-control" style="width: 200px; display: inline-block;">
                                    </div>
                                    <div class="form-group">
                                        <h4>Paypal Currency</h4>
                                        <select v-model="deposit.PAYPAL_CURRENCY" class="input-group">
                                            <option disabled>Please select paypal currency</option>
                                            <template v-for="(value, key) in deposit.SUPPORTED_PAYPAL_CURRENCY">
                                                <option :value="key">{{ value }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="form-group bg-danger" style="padding: 1px 15px;">
                                        <h4>Notice</h4>
                                        <p>Please submit Paypal currency match what the paypal merchant account accepted.
                                            If not execute transaction from customer fail.</p>
                                    </div>
                                </div>
                                <div class="col-md-5">

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
        <div class="modal fade" id="user-dialog" style="margin-left: 175px;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">User Info</h4>
                    </div>
                    <div class="modal-body">
                        @include('admin.settings.user-dialog')
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                        <button
                                class="btn bg-info"
                                v-on:click="_updateSingleUser"
                        >Save</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
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