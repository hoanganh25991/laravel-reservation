@extends('layouts.app')

@section('content')
    {{--<form id="booking-form">--}}
    <div class="container" id="form-step-container">
        <div class="box form-step" id="form-step-1">
            @component('reservations.header')
            @slot('title')
            Make a Reservation at <span class="r-name"> <a href="{{ url('') }}" target="_blank"
                                                           id="reservation_title">{{ $outlets->first()->name }}</a></span>
            <p class="sub"></p>
            @endslot
            @endcomponent
            <div id="check-availability" class="content">
                <div class="rid-select">
                    <input type="hidden" name='outlet_name' value="{{ $outlets->first()->name }}">
                    <select name="outlet_id" id="rid" title="spize" class="form-control">
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                    @if($loop->first)
                                    selected
                                    @endif
                            >{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="selectors cf">
                    <div id="adults-wrap">
                        <label for="adults">Adults</label>
                        <select name="adult_pax" class="form-control">
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                        </select>
                    </div>
                    <div id="children-wrap">
                        <label for="children">Children</label>
                        <select name="children_pax" class="form-control">
                            <option value="0" selected>0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                        </select>
                    </div>
                </div>

                <div class="datetime cf">
                    <div class="clear"></div>
                    <div id="calendar-box" align="center"></div>
                    <div id="dt-choice" class="cf">
                        <label id="reservation_date">{{ date('M d Y') }}</label>
                        <input type="hidden" name="reservation_date" value="">
                        <select name="reservation_time" class="form-control">
                            <option>N/A</option>
                        </select>
                    </div>
                    <div class="agree-box cf">
                        <div class="checkbox cf">
                            <label for="agree_box">I acknowledge that this is a waitlisted reservation and is
                                subjected
                                to
                                the restaurant's confirmation.</label>
                            <input id="agree_box" type="checkbox" name="agree_box" value="1"
                                   class="form-control agree-check" required>
                        </div>
                    </div>
                </div>
                <div class="form-actions cf bottom_room">
                    <button class="btn-form-next btn btn-primary  pull-right" destination="form-step-2">Next</button>
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
                <span class="field" name="outlet_name"></span> for <span class="field bloc" name="pax_size"></span>
                <br> at <span class="field  bloc" name="time"></span> on <span class="field  bloc" name="date"></span>
            </p>
            @endslot
            @endcomponent
            <div id="confirm-details" class="content">
                <div class="form-groups">
                    <select id="d-title" class="form-control" name="salutation">
                        <option value="Mr.">Mr.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Mdm.">Mdm.</option>
                    </select>
                    <input type="text" class="form-control d-name name_check" name="first_name" value=""
                           placeholder="First Name" title="First Name">&nbsp;
                    <input type="text" class="form-control d-name name_check" name="last_name" value=""
                           placeholder="Last Name" title="Last Name">
                </div>
                <br>
                <div class="form-groups">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" value=""
                           placeholder="Enter Email Address" title="Enter Email Address"><br>
                    <label for="telephone">Mobile Phone</label>
                    <input type="text" id="phone-area" class="form-control contry_check" name="phone_country_code"
                           value="+65" placeholder="+65" title="Area Code">
                    <input type="tel" id="telephone" class="form-control mobile_check" name="phone" value=""
                           placeholder="Phone Number" title="Phone Number"><br>
                    <label for="notes" class="textarea">Special Requests</label><textarea class="form-control"
                                                                                          name="remarks"
                                                                                          placeholder="Message (Maximum 85 characters.)"
                                                                                          maxlength="85"></textarea>
                    <p class="note">Special requests are not guaranteed and are subject to availability and
                        restaurant discretion.</p></div>

                <div class="form-actions cf bottom_room">
                    <button class="btn-form-next btn btn-primary pull-right" destination="form-step-3">Confirm</button>
                </div>
            </div>
            @include('reservations.footer')
        </div><!-- /box -->
        <div class="box form-step" id="form-step-3">
            <h1>SUMMARY INFO BEFORE COMEBACK HOME</h1>
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
                        <img src="{{ url('images/hourglass.svg') }}">
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

<script src="{{ url(substr(mix('js/calendar.js'), 1)) }}"></script>
<script src="{{ url(substr(mix('js/syntax-highlight.js'), 1)) }}"></script>
<script src="{{ url(substr(mix('js/booking-form.js'), 1)) }}"></script>
<script>
    //let c = $('#calendar-box').Calendar();
</script>
@endpush