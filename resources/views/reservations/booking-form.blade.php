@extends('layouts.app')
@section('css')
    <link href="{{ url('css/reservation.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="container">
        <div class="box">
            @component('reservations.header')
            @slot('title')
            Make a Reservation at <span class="r-name"> <a href="{{ url('') }}" target="_blank">Spize (Bedok)</a></span>
            <p class="sub"></p>
            @endslot
            @endcomponent
            <div id="check-availability" class="content">
                <div class="rid-select">
                    <select name="rid" id="rid" title="spize" class="form-control">
                        <option value="spizebedok1504spb" selected>Spize (Bedok)</option>
                        <option value="spizerivervalley1505srv">Spize (River Valley)</option>
                        <option value="spizeriflerange1510srr">Spize (Rifle Range)</option>
                    </select>
                    <input id="source" name="source" type="hidden" value="spize">
                    <input name="hf" id="switch_hh" type="hidden" value="0">
                    <input name="hh" id="switch_hf" type="hidden" value="0">
                    <input name="res_country_code" id="res_country_code" type="hidden" value="SG">
                    <input name="setting" id="setting" type="hidden" value="">
                    <input name="membership_id" id="membership_id" type="hidden" value="">
                </div>
                <div class="selectors cf">
                <span id="adults-wrap">
                   <label for="adults">Adults</label>
                   <select name="adult_pax" class="form-control">
                      <option value="5" selected>5</option>
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
                </span>
                <span id="children-wrap">
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
                </span>
                </div>
                <div class="datetime cf">
                    <div class="clear"></div>
                    <div id="calendar-box" align="center"></div>
                    <div id="dt-choice" class="cf">
                        <label id="reservation_time">Mar 03 2017</label>
                        <select name="time" class="form-control">
                            <option>N/A</option>
                        </select>
                    </div>
                    <div class="agree-box cf">
                        <div class="checkbox cf">
                            <label for="agree_box">I acknowledge that this is a waitlisted reservation and is subjected
                                to
                                the restaurant's confirmation.</label>
                            <input id="agree_box" type="checkbox" name="agree_box" value="1"
                                   class="form-control agree-check" required>
                        </div>
                    </div>
                </div>
                <div class="form-actions cf" id="bottom_room">
                    <button id="btn_next" class="btn btn-primary" style="float:right">Next</button>
                </div>
            </div>
        </div>{{--box--}}
    </div>{{--container--}}
    <style>
        #bottom_room {
            text-align: center;
            margin: 20px auto;
            border: 0;
            width: 330px;
            bottom: 0;
        }
    </style>
@endsection
@section('script')
    <script src="{{ url(substr(mix('js/calendar.js'), 1)) }}"></script>
    <script>
        let c = $('#calendar-box').Calendar();
    </script>
@endsection