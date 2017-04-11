@extends('layouts.app')

@section('content')
    {{--<form id="booking-form">--}}
    <div id="form-step-container">
        <div class="container">
            <div class="box form-step" id="form-step-1">
                @component('reservations.header')
                @slot('title')
                Reservation at <span class="r-name"> <a href="{{ url('') }}" target="_blank"
                                                               id="reservation_title">@{{ outlet.name }}</a></span>
                @endslot
                @endcomponent
                <div id="check-availability" class="content">
                    <div class="rid-select">
                        <label for="outlet_id">Select an outlet</label>
                        <select name="outlet_id" id="rid" title="spize" class="form-control" :value="outlet.id">
                            {{--@foreach($outlets as $outlet)--}}
                                {{--<option value="{{ $outlet->id }}">{{ $outlet->name }}</option>--}}
                            {{--@endforeach--}}
                            <template v-for="(outlet, outlet_index) in outlets">
                                <option :value="outlet.id">@{{ outlet.outlet_name }}</option>
                            </template>
                        </select>
                    </div>
                    @verbatim
                    <div class="selectors cf" :style="'display: ' + pax_over">
                        <div id="adults-wrap">
                            <label for="adults">Adults</label>
                            <select name="adult_pax" class="form-control" :value="pax.adult">
                                <template v-for="n in overall_max_pax + 1">
                                    <option :value="n - 1">{{ n - 1 }}</option>
                                </template>
                            </select>
                        </div>
                        <div id="children-wrap">
                            <label for="children">Children</label>
                            <select name="children_pax" class="form-control" :value="pax.children">
                                <template v-for="n in overall_max_pax + 1">
                                    <option :value="n - 1">{{ n - 1 }}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    @endverbatim
                    <div class="datetime cf">
                        <div class="clear"></div>
                        <div id="calendar-box" align="center"></div>
                        <div id="dt-choice" class="cf">
                            <label id="reservation_date">Select a booking time on @{{ reservation.date.format('DD MMM Y') }}</label>
                            <input type="hidden" name="reservation_date" value="">
                            <select name="reservation_time" class="form-control">
                                <option>N/A</option>
                            </select>
                        </div>
                        <div class="agree-box cf">
                            <div class="checkbox cf">
                                <label for="agree_box">I acknowledge that this is a waitlisted reservation and is
                                    subjected to the restaurant's confirmation. I understand that the restaurant will hold my table for a maximum of 15 minutes.</label>
                                <input id="agree_box" type="checkbox" name="agree_box" v-model="reservation.agree_term_condition"
                                       class="form-control agree-check">
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
                @component('reservations.header')
                @slot('title')
                Confirm Diner Details
                <p class="sub">
                    We have a table for you at <br>
                    <span class="field" name="outlet_name">@{{ outlet.name }}</span> for <span class="field bloc"
                                                                                               name="pax_size">@{{ Number(pax.adult) + Number(pax.children) }}
                        people</span>
                    <br> at <span class="field  bloc" name="time">@{{ reservation.time }}</span> on <span
                            class="field  bloc" name="date">@{{ reservation.date.format('MMM D Y') }}</span>
                </p>
                @endslot
                @endcomponent
                <div id="confirm-details" class="content">
                    <div class="form-groups login-form">
                        <div class="form-group">
                            <select id="d-title" class="form-control login-field" name="salutation" :value="customer.salutation">
                                <option value="Mr.">Mr.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Mdm.">Mdm.</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control d-name name_check login-field" name="first_name"
                                :value="customer.first_name" placeholder="First Name" title="First Name">
                            <label class="login-field-icon fa fa-user" style="top: 12px;" for="first_name"></label>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control d-name name_check login-field" name="last_name"
                                :value="customer.last_name" placeholder="Last Name" title="Last Name">
                            <label class="login-field-icon fa fa-user" style="top: 12px;" for="last_name"></label>
                        </div>

                        <div class="form-group">
                            <input type="email" class="form-control login-field" name="email" id="booking-email"
                                   :value="customer.email" placeholder="Email Address">
                            <label class="login-field-icon fa fa-envelope" style="top: 12px;" for="booking-email"></label>
                        </div>

                        <div class="form-group">
                            <input type="text" id="phone-area" class="form-control contry_check" name="phone_country_code"
                                :value="customer.phone_country_code" placeholder="+65" title="Area Code">
                            <input type="tel" class="form-control login-field mobile_check" name="phone" id="telephone"
                                   :value="customer.phone" placeholder="Phone Number" title="Phone Number">
                            <label class="login-field-icon fa fa-mobile" style="top: 12px;" for="telephone"></label>
                        </div>

                        <div class="form-group">
                            <textarea class="form-control login-field" placeholder="Special Requests"
                                        name="remarks" id="booking-remarks" :value="customer.remarks"></textarea>
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
        {{--modal--}}
        <div class="modal fade" id="ajax-dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header" style="display: none;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Checking reservation...</h4>
                    </div>
                    <div class="modal-body center">
                        {{--<div style="width: 100px; display: inline-block">--}}
                        <div style="width: 184px; display: inline-block">
                            {{--<img src="{{ url('images/gears.svg') }}">--}}
                            {{--<img src="{{ url('images/hourglass.svg') }}">--}}
                            <img src="{{ url('images/gear.svg') }}">
                        </div>
                    </div>
                    <div class="modal-footer" style="display: none;">
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        {{--modal--}}
        <div class="modal fade" id="paypal-dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Deposit Payment Require</h4>
                    </div>
                    @verbatim
                    <div class="modal-body center">
                        <p><span class="h4">Amount of money</span> {{ reservation.deposit }}</p>
                    </div>
                    @endverbatim
                    <div class="modal-footer">
                        <hr>
                        {{--@include('paypal.authorize')--}}
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