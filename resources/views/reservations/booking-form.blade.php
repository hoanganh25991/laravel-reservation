@extends('layouts.app')

@section('content')
    <div id="form-step-container">
            <div class="container">
                <div class="box form-step" id="form-step-1">
                    <div id="check-availability" class="content">
                        <div class="rid-select">
                            <label for="outlet_id">Select an outlet</label>
                            <select name="outlet_id" id="rid" title="spize" class="form-control"
                                    v-model="selected_outlet_id" v-on:change="_updateSelectedOutlet">
                                <template v-for="(outlet, outlet_index) in outlets">
                                    <option :value="outlet.id">@{{ outlet.outlet_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="selectors cf">
                            <div id="adults-wrap">
                                <label for="adults">Adults</label>
                                <select name="adult_pax" class="form-control" v-model="reservation.adult_pax" >
                                    <template v-for="n in 20">
                                        <option :value="n">@{{ n }}</option>
                                    </template>
                                </select>
                            </div>
                            <div id="children-wrap">
                                <label for="children">Children</label>
                                <select name="children_pax" class="form-control" v-model="reservation.children_pax">
                                    <template v-for="n in 20">
                                        <option :value="n">@{{ n }}</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="datetime cf">
                            <div class="clear"></div>
                            <div id="calendar-box" align="center"></div>
                            <div id="dt-choice" class="cf">
                                <label>Booking time on @{{ reservation.date.format('DD MMM Y') }}</label>
                                <select v-model="reservation.time" class="form-control">
                                    <template v-for="(time, time_index) in available_time_on_reservation_date">
                                        <option :value="time.time">@{{ time.session_name }} @{{ time.time }}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="agree-box cf">
                                <div class="checkbox cf" style="padding-left: 5px;">
                                    <label for="agree_box">I acknowledge that this is a waitlisted reservation and is subjected to the restaurant's confirmation.
                                        I understand that the restaurant will hold my table for a maximum of 15 minutes.</label>
                                    <input id="agree_box" type="checkbox" v-model="reservation.agree_term_condition" class="form-control agree-check">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions cf bottom_room">
                            <button class="btn-form-next btn btn-block btn-primary" destination="form-step-2"
                                    :disabled="not_allowed_move_to_form_step_2()">Next</button>
                        </div>
                    </div>
                    @include('reservations.footer')
                </div>{{--box--}}
                <div class="box form-step" id="form-step-2">
                    <div id="confirm-details" class="content">
                        <div class="form-groups login-form">
                            <div class="form-group">
                                <select class="form-control login-field" v-model="reservation.salutation">
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Mdm.">Mdm.</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control d-name name_check login-field" name="firstname"
                                       v-model="reservation.first_name" placeholder="First Name" title="First Name">
                                <label class="login-field-icon fa fa-user" style="top: 12px;" for="first_name"></label>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control d-name name_check login-field" name="lastname"
                                       v-model="reservation.last_name" placeholder="Last Name" title="Last Name">
                                <label class="login-field-icon fa fa-user" style="top: 12px;" for="last_name"></label>
                            </div>

                            <div class="form-group">
                                <input type="email" class="form-control login-field" name="email" id="booking-email"
                                       v-model="reservation.email" placeholder="Email Address">
                                <label class="login-field-icon fa fa-envelope" style="top: 12px;" for="booking-email"></label>
                            </div>

                            <div class="form-group">
                                <input type="text" id="phone-area" class="form-control login-field" name="phone_country_code"
                                       v-model="reservation.phone_country_code" placeholder="+65" title="Country Code">
                                <input type="tel" class="form-control login-field" name="phone" id="telephone"
                                       v-model="reservation.phone" placeholder="Mobile Number" title="Mobile Number">
                                <label class="login-field-icon fa fa-phone" style="top: 12px;" for="telephone"></label>
                            </div>

                            <div class="form-group">
                            <textarea class="form-control login-field" placeholder="Special Requests" name="customer_remarks"
                                      v-model="reservation.customer_remarks"></textarea>
                                <p class="note">Special requests are not guaranteed and are subject to availability and restaurant discretion.</p>
                            </div>
                        </div>

                        <div class="form-actions cf bottom_room" style="width:100%">
                            <button class="btn-form-next btn btn-block btn-primary pull-left" style="width:48%; margin: 0;" destination="form-step-1">Back</button>
                            <button class="btn-form-next btn btn-block btn-primary pull-right" style="width:48%; margin: 0;" destination="form-step-3"
                                    :disabled="not_allowed_move_to_form_step_3()">Next</button>
                        </div>
                    </div>
                    @include('reservations.footer')
                </div><!-- /box -->
                <div class="box form-step" id="form-step-3">
                    @include('reservations.booking-summary')
                </div>
            </div>
            {{--dialog modal--}}
            <div class="modal fade" id="ajax-dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body center">
                            <div style="width: 140px; display: inline-block; margin-top: 85%;">
                                <img src="{{ url('images/ring.svg') }}">
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
    </div>
    @include('debug.redux-state')
@endsection

@push('script')
<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state, JSON_NUMERIC_CHECK);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url(substr(mix('js/calendar.js'), 1)) }}"></script>
<script src="{{ url(substr(mix('js/booking-form.js'), 1)) }}"></script>
@endpush