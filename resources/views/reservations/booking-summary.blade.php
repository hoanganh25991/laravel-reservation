@component('reservations.header')
@slot('title')
<span class="r-name"><a href="{{ url('') }}" target="_blank">@{{ outlet.name }}</a></span>
<p class="sub">Your reservation has been made! <br>A confirmation SMS has been sent.</p>
@endslot
@endcomponent
<div id="reservation-details" class="content legend">
    <h6 class="r-title">Reservation Summary</h6>
    <p>
        <label>An SMS has been sent to your mobile phone</label>
    </p>
    <table id="r-rsrve-info">
        <tbody>
        <tr>
            <td><label>Date &amp; Time</label></td>
            <td>@{{ reservation.date.format('MMM D Y') }}, @{{ reservation.time }}</td>
        </tr>
        <tr>
            <td><label>People</label></td>
            <td>@{{ pax.adult }} Adults</td>
            <td>@{{ pax.children }} Children</td>
        </tr>
        <tr>
            <td><label>Confirmation ID</label></td>
            <td>@{{ reservation.confirm_id }}</td>
        </tr>
        </tbody>
    </table>

    <table id="r-dnr-info">
        <tbody>
        <tr>
            <td><label>Name</label></td>
            <td>@{{ customer.first_name }} @{{ customer.last_name }}</td>
        </tr>
        <tr>
            <td><label>Phone Number</label></td>
            <td>@{{ customer.phone_country_code }} @{{ customer.phone }}</td>
        </tr>
        <tr>
            <td><label>Email</label></td>
            <td>@{{ customer.email }}</td>
        </tr>
        <tr>
            <td><label>Special Request</label></td>
            <td>@{{ customer.remarks }}</td>
        </tr>
        <tr v-show="reservation.payment_status == 100">
            <td><label>Deposit Paid</label></td>
            <td>
                <p class="h4">$ @{{ reservation.deposit }}</p>
                <p class="h5"><strong>Your deposit will be returned when you arrive for your reservation</strong></p>
            </td>
        </tr>
        <tr v-show="reservation.payment_status == 25">
            <td><label>Deposit Required</label></td>
            <td>
                <p class="h4 text-danger">$ @{{ reservation.deposit }}</p>
                @include('paypal.authorize')
            </td>
        </tr>
        </tbody>
    </table>
    <div class="form-actions cf bottom_room">
        @php
            $is_summary_page = isset($is_summary_page) ? $is_summary_page : true;
        @endphp
        @if($is_summary_page && env('APP_ENV') != 'production')
            <button class="btn-form-next btn btn-primary pull-left" destination="form-step-2">Back</button>
            <a href="{{ url('') }}" type="button" class="btn btn-primary pull-right">Home</a>
        @endif

        @if(!$is_summary_page)
            <form method="POST">
                <button class="btn btn-primary">Confirm</button>
            </form>
        @endif
    </div>
</div>

