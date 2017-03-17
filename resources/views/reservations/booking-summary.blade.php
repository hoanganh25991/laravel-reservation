@component('reservations.header')
@slot('title')
<span class="r-name"><a href="{{ url('') }}" target="_blank">@{{ outlet.name }}</a></span>
<p class="sub">Your reservation has been made! <br>A confirmation SMS has been sent.</p>
@endslot
@endcomponent
<div id="reservation-details" class="content legend">

    <h6 class="r-title">Your Reservation Information</h6>
    <table id="r-rsrve-info">
        <tbody>
        <tr>
            <td><label>Date &amp; Time:</label></td>
            <td>@{{ reservation.date.format('MMM D Y') }}, @{{ reservation.time }}</td>
        </tr>
        <tr>
            <td><label>People:</label></td>
            <td>@{{ pax.adult }} Adults</td>
            <td>@{{ pax.children }} Children</td>
        </tr>
        <tr>
            <td><label>Confirmation ID:</label></td>
            <td>@{{ reservation.confirm_id }}</td>
        </tr>
        </tbody>
    </table>

    <table id="r-dnr-info">
        <tbody>
        <tr>
            <td><label>Name:</label></td>
            <td>@{{ customer.first_name }} @{{ customer.last_name }}</td>
        </tr>
        <tr>
            <td><label>Phone Number:</label></td>
            <td>@{{ customer.phone_country_code }} @{{ customer.phone }}</td>
        </tr>
        <tr>
            <td><label>Email:</label></td>
            <td>@{{ customer.email }}</td>
        </tr>
        <tr>
            <td><label>Special Request:</label></td>
            <td><p>@{{ customer.remarks }}</p></td>
        </tr>
        </tbody>
    </table>
    <div class="form-actions cf bottom_room">
        <button class="btn-form-next btn btn-primary pull-left" destination="form-step-2">Back</button>
        <a href="{{ url('') }}" type="button" class="btn btn-primary pull-right">Home</a>
    </div>
</div>