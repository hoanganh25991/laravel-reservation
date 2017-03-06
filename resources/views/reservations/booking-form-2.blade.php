@extends('layouts.app')
@push('css')
    <link href="{{ url('css/reservation.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="container">
    <div class="box">
        @component('reservations.header')
            @slot('title')
                Confirm Diner Details
                <p class="sub">
                    We have a table for you at <br><span class="field">Spize (Bedok)</span> for <span
                            class="field bloc">5 people</span><br> at<span class="field  bloc"> 1:00 pm</span> on <span
                            class="field  bloc">7 March 2017</span>
                </p>
            @endslot
        @endcomponent
        <div id="confirm-details" class="content con_mob_style">
            <form class="form-horizontal" role="form" id="creatForm" method="post"
                  action="/booking/do_confirm?lang=en_US">
                <fieldset>
                    <div class="form-groups">
                        <select id="d-title" class="form-control" name="title">
                            <option value="Mr.">Mr.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Mdm.">Mdm.</option>
                        </select>
                        <input type="text" id="forename" class="form-control d-name name_check" name="forename" value=""
                               placeholder="First Name" title="First Name">&nbsp;
                        <input type="text" id="surname" class="form-control d-name name_check" name="surname" value=""
                               placeholder="Surname" title="Surname">
                    </div>
                    <br>
                    <div class="form-groups">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" name="email" value=""
                               placeholder="Enter Email Address" title="Enter Email Address"><br>
                        <label for="telephone">Mobile Phone</label>
                        <input type="text" id="phone-area" class="form-control contry_check" name="phone_ccode"
                               value="+65" placeholder="+65" title="Area Code">
                        <input type="tel" id="telephone" class="form-control mobile_check" name="mobile" value=""
                               placeholder="Phone Number" title="Phone Number"><br>
                        <label for="notes" class="textarea">Special Requests</label><textarea id="notes"
                                                                                              class="form-control"
                                                                                              name="notes"
                                                                                              placeholder="Message (Maximum 85 characters.)"
                                                                                              maxlength="85"></textarea>
                        <p class="note">Special requests are not guaranteed and are subject to availability and
                            restaurant discretion.</p></div>

                    <div class="form-actions cf legend">
                        <button id="btn_confirm" type="button" class="btn btn-primary" style="float:right">Confirm</button>
                    </div>
                </fieldset>
            </form>
            <input type="hidden" id="capturebtn" value="Continue">
        </div>
    </div><!-- /box -->
</div> <!-- /container -->
@endsection