<div class="tile" style="background-color: rgba(255,255,255,0.4);" >
    <div id="reservation-details" class="content legend">
        <h6 v-show="reservation.payment_status == 25" class="r-title">Reservation Summary</h6>
        <p v-show="reservation.payment_status == 25" class="r-title">
            <label class="text-danger" style="float: none;">A payment authorization is required<br/>before confirming your reservation.</label>
        </p>

        <h6 v-show="reservation.payment_status != 25" class="r-title">Reservation No. <strong>@{{ reservation.confirm_id }}</strong></h6>
        <p v-show="reservation.payment_status != 25" class="r-title">
            <label style="float: none;">An SMS has been sent to your mobile phone</label>
        </p>
        <table id="r-rsrve-info">
            <tbody>
            <tr>
                <td><label>Outlet</label></td>
                <td>@{{ selected_outlet.outlet_name }}<br/>@{{ selected_outlet.address }}</td>
            </tr>
            <tr>
                <td style="width: 40%;"><label>Date &amp; Time</label></td>
                <td v-if="reservation.date">@{{ reservation.date.format('D MMM Y') }} at @{{ reservation.time }}</td>
                <td v-if="!reservation.date"></td>
            </tr>
            <tr>
                <td><label>Pax</label></td>
                <td>@{{ reservation.adult_pax }} Adults, @{{ reservation.children_pax }} Children</td>
            </tr>
            <tr>
                <td><label>Name</label></td>
                <td>@{{ reservation.first_name }} @{{ reservation.last_name }}</td>
            </tr>
            <tr>
                <td><label>Contact Number</label></td>
                <td>@{{ reservation.phone_country_code }} @{{ reservation.phone }}</td>
            </tr>
            <tr>
                <td><label>Email</label></td>
                <td>@{{ reservation.email }}</td>
            </tr>
            <tr v-show="reservation.customer_remarks != ''">
                <td><label>Special Request</label></td>
                <td>@{{ reservation.customer_remarks }}</td>
            </tr>
            <tr v-show="reservation.payment_status == 100">
                <td><label>Deposit Paid</label></td>
                <td>
                    <label class="h5">$@{{ reservation.deposit }}</label><br/>
                    Your deposit will be returned when you arrive for your reservation
                </td>
            </tr>
            <tr v-show="reservation.payment_status == 25">
                <td><label>Deposit Required</label></td>
                <td>
                    <label class="h5 text-danger">$@{{ reservation.deposit }}</label>
                    <label class="text-danger">Tap on the PayPal button to authorize the payment.
                        Note that you will only be charged in the event of no-show.</label>
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
                <button class="btn btn-primary"
                        v-show="reservation.status <= 100"
                        v-on:click="_confirmReservation">Confirm</button>
            @endif
        </div>
    </div>
</div>

