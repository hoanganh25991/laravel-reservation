<tr :class="_getReservationRowClass(reservation)"
    :id="'reservation_' + reservation.id"
    :reservation-id="reservation.id"
    v-on:click="_reservationDetailDialog"
>
    <td>@{{ reservation_index + 1 }}</td>
    <td>
        <input type="checkbox"
               v-model="reservation.staff_read_state"
               style="position: relative; width: 20px; left: 0px;"
               v-on:change="_autoSave(reservation, 'staff_read_state')"
        >
    </td>
    <td>@{{ reservation.confirm_id }}</td>
    <td style="width: 200px">
        <p style="margin: 0">@{{ reservation.salutation }} @{{ reservation.first_name }} @{{ reservation.last_name }}</p>
        <p style="margin: 0">@{{ reservation.phone_country_code }}@{{ reservation.phone }}</p>
        <p style="margin: 0">@{{ reservation.email }}</p>
    </td>
    <td style="width: 200px">@{{ reservation.reservation_timestamp }}
      <br/>
      <p style="font-size: 0.8em;">created at: @{{ reservation.created_timestamp }}</p>
    </td>
    <td style="width: 100px">@{{ reservation.adult_pax }}+@{{ reservation.children_pax }}</td>
    <td>
        <input
                type="text" style="width: 70px"
                v-model="reservation.table_name"
                :value="reservation.table_name"
                v-on:change="_autoSave(reservation, 'table_name')"
        >
    </td>
    <td>
        <textarea
                rows="2" col="20" style="height: 50px; width: 100px"
                v-model="reservation.customer_remarks"
                :value="reservation.customer_remarks"
                placeholder="Customer Remarks"
                v-on:change="_autoSave(reservation, 'customer_remarks')"
        ></textarea>
    </td>
    <td>
        <textarea
                rows="2" col="20" style="height: 50px"
                v-model="reservation.staff_remarks"
                :value="reservation.staff_remarks"
                placeholder="Staff Remarks"
                v-on:change="_autoSave(reservation, 'staff_remarks')"
        ></textarea>
    </td>
    <td>
        @include('admin.reservations.status')
      </select>
    </td>
    <td>
      <div class="alignCenter">
        <button class="btn" v-on:click="_sendReminderSMS(reservation)"
                style="width: 75px; margin: 0 auto">Send</button>
      </div>
    </td>
    <td>
        <div v-show="reservation.payment_status > 25">
            <p>@{{ reservation.payment_amount }} @{{ reservation.payment_currency }} [
                <strong>@{{ reservation.payment_status == 50 ? 'VOID'
                          : reservation.payment_status == 100 ? 'AUTHORIZED'
                          : reservation.payment_status == 200 ? 'CHARGE' : ''}}</strong>]
            </p>
        </div>
        <!--Ok only administrator can see this action
        Be cross-checked on server -->
        <div v-show="reservation.payment_status == 100 && (user.permission_level == 10 || user.permission_level == 5)">
            <button action="refund" :reservation-index='reservation_index'  class="bg-info" style="width: 100%"
                    v-on:click="_updateReservationPayment($event, PAYMENT_REFUNDED)"
            >Void</button>
            <button action="charge" :reservation-index='reservation_index' class="bg-danger" style="width: 100%"
                    v-on:click="_updateReservationPayment($event, PAYMENT_CHARGED)"
            >Charge</button>
        </div>
    </td>
</tr>