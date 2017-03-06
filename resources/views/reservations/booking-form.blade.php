@extends('layouts.app')
@section('css')
    <link href="{{ url('css/bootstrap.calendar.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="box">
        <div class="hdr-wrapper">
            <div class="header cf">

                <div class="logo">
                    <a href="https://www.chope.co/singapore-restaurants" target="_blank"><img src="https://d2jzxcrnybzkkt.cloudfront.net/static/widget_v4/img/logo.png?date=201702171400"></a>
                </div>

                <h5 class="title">
                    Make a Reservation at									 <span class="r-name"> <a href="https://www.chope.co/singapore-restaurants/categories/restaurant/spize-bedok" target="_blank">Spize (Bedok)</a></span>

                </h5>

            </div>
        </div>
        <div id="check-availability" class="content">
            <div class="rid-select">
                <input name="switch_hn1" id="switch_hn1" value="0" type="hidden">
                <input name="switch_select_location" id="switch_select_location" value="0" type="hidden">
                <select name="rid" id="rid" title="spize" class="form-control">
                    <option value="select_location">Select Location</option>
                    <option selected="" value="spizebedok1504spb">Spize (Bedok)</option>
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
                   <select name="adults" id="adults" class="form-control" title="No of Adults">
                      <option value="5" selected="">5</option>
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
                      <option value="16">16</option>
                      <option value="17">17</option>
                      <option value="18">18</option>
                      <option value="19">19</option>
                      <option value="20">20</option>
                   </select>
                </span>
                <span id="children-wrap">
                   <label for="children">Children</label>
                   <select name="children" id="children" class="form-control" title="">
                      <option value="0" selected="">0</option>
                      <option value="1">1</option>
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
                </span>
            </div>
            <div class="datetime cf">
                <div class="clear"></div>
                <div id="calendar-box" align="center"></div>
                <div id="dt-choice" class="cf">
                    <input name="date" type="hidden" id="datepicker_value" value="3-3-2017" readonly="">
                    <input name="datepicker_date_value" type="hidden" id="datepicker_date_value" value="3" readonly="">
                    <label id="sel-date">  Mar 03 2017</label>
                    <input id="sel-date_2" type="hidden" value="03 3 2017">
                    <input id="sel-date_3" type="hidden" value="03 3 2017">
                    <select id="time" name="time" class="form-control" title="Choose a Time">
                        <option>N/A</option>
                    </select>
                    <input id="wait_list" name="wait_list" value="0" type="hidden">
                    <input id="reservation_charge" name="reservation_charge" value="0" type="hidden">
                </div>
                <div class="agree-box hidden cf">
                    <div class="checkbox cf">
                        <input id="check-required0" type="checkbox" class="form-control agree-check" name="agree_box" value="1">
                        <label for="check-required0">I acknowledge that this is a waitlisted reservation and is subjected to the restaurant's confirmation.</label>
                    </div>
                </div>
            </div>
            <div class="form-actions cf" id="bottom_room">
                <button type="button" id="back_button" class="btn" style="float:left">Previous</button>
                <button type="submit" id="index_submit_button_id" class="btn btn-primary" style="float:right" disabled="disabled">Next</button>
            </div>
        </div>
    </div>
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
    <script src="{{ url(substr(mix('js/bootstrap.calendar.js'), 1)) }}"></script>
    <script>
        let c = $('#calendar-box').Calendar();
    </script>
@endsection