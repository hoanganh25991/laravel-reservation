@extends('layouts.app')
@section('css')
    <link href="{{ url('css/reservation.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container">
    <div class="box">
        @component('reservations.header')
            @slot('title')
                <span class="r-name"><a href="{{ url('') }}" target="_blank">Spize (Bedok)</a></span>
                <p class="sub">Your reservation has been made! <br>A confirmation email has been sent.</p>
            @endslot
        @endcomponent
        <div id="reservation-details" class="content legend">

            <h6 class="r-title">Your Reservation Information</h6>
            <table id="r-rsrve-info">
                <tbody>

                <tr>
                    <td><label>Date &amp; Time:</label></td>
                    <td>7 March 2017, 12:00pm</td>
                </tr>
                <tr>
                    <td><label>People:</label></td>
                    <td>5 Adults</td>
                </tr>

                <tr>
                    <td><label>Confirmation ID:</label></td>
                    <td>17TPY</td>
                </tr>

                </tbody>
            </table>

            <table id="r-dnr-info">
                <tbody>
                <tr>
                    <td><label>Name:</label></td>
                    <td>mwle4s4i mwle4s4i</td>
                </tr>
                <tr>
                    <td><label>Phone Number:</label></td>
                    <td>+65657 903865657</td>
                </tr>
                <tr>
                    <td><label>Email:</label></td>
                    <td>lehoanganh25991@gmail.com</td>
                </tr>
                <tr>
                    <td><label>Special Request:</label></td>
                    <td><p>mwle4s4i mwle4s4i mwle4s4i </p></td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection