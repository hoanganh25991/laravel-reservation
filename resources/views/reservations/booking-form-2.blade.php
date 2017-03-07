@extends('layouts.app')

@section('content')
<div class="container">
    <div class="box">
        @component('reservations.header')
            @slot('title')
                Confirm Diner Details
                <p class="sub">
                    We have a table for you at <br>
                    <span class="field">{{ $reservation_info['outlet_name'] }}</span> for <span class="field bloc">{{ $reservation_info['pax_size'] }} people</span>
                    <br> at <span class="field  bloc">{{ $reservation_info['reservation_time'] }}</span> on <span class="field  bloc">{{ $reservation_info['date'] }}</span>
                </p>
            @endslot
        @endcomponent
        <div id="confirm-details" class="content con_mob_style">
            <form id="creatForm" method="POST" action="" class="form-horizontal">
                <input type="hidden" name="step" value="booking-form-2">
                <fieldset>
                    <div class="form-groups">
                        <select id="d-title" class="form-control" name="salutation">
                            <option value="Mr.">Mr.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Mdm.">Mdm.</option>
                        </select>
                        <input type="text" id="forename" class="form-control d-name name_check" name="first_name" value=""
                               placeholder="First Name" title="First Name">&nbsp;
                        <input type="text" id="surname" class="form-control d-name name_check" name="last_name" value=""
                               placeholder="Surname" title="Surname">
                    </div>
                    <br>
                    <div class="form-groups">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" name="email" value=""
                               placeholder="Enter Email Address" title="Enter Email Address"><br>
                        <label for="telephone">Mobile Phone</label>
                        <input type="text" id="phone-area" class="form-control contry_check" name="phone_country_code"
                               value="+65" placeholder="+65" title="Area Code">
                        <input type="tel" id="telephone" class="form-control mobile_check" name="phone" value=""
                               placeholder="Phone Number" title="Phone Number"><br>
                        <label for="notes" class="textarea">Special Requests</label><textarea id="notes"
                                                                                              class="form-control"
                                                                                              name="customer_remarks"
                                                                                              placeholder="Message (Maximum 85 characters.)"
                                                                                              maxlength="85"></textarea>
                        <p class="note">Special requests are not guaranteed and are subject to availability and
                            restaurant discretion.</p></div>

                    <div class="form-actions cf legend">
                        <button id="btn_confirm" class="btn btn-primary" style="float:right">Confirm</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div><!-- /box -->
</div> <!-- /container -->
@endsection