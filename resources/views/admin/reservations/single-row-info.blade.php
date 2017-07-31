<tr :class="_getReservationRowClass(reservation)"
    :id="'reservation_' + reservation.id"
    :reservation-id="reservation.id"
    v-on:click="_reservationDetailDialog($event, reservation)"
>
    <td>@{{ reservation_index + 1 }}</td>
    <td>
        <input type="checkbox"
               v-model="reservation.staff_read_state"
               style="position: relative; width: 20px; left: 0px;"
               v-on:change="_autoSave(reservation, 'staff_read_state')"
        >
    </td>
    <td>
        <p class="noMargin">@{{ reservation.confirm_id }}</p>
        <p class="noMargin"><span class="glyphicon contactIcon"></span> @{{ reservation.adult_pax }}+@{{ reservation.children_pax }}</p>
        <div v-if="reservation.table_name != '' && reservation.table_name != null && reservation.table_name != undefined">
            <span class="glyphicon tableIcon"></span> @{{ reservation.table_name }}
        </div>
    </td>
    <td>
        <p class="noMargin"><span class="glyphicon contactIcon"></span> @{{ reservation.full_name}}</p>
        <p class="noMargin"><span class="glyphicon phoneIcon"></span> (@{{ reservation.phone_country_code }}) @{{ reservation.phone }}</p>
        <p class="noMargin"><span class="glyphicon emailIcon"></span> @{{ reservation.email }}</p>
        <p class="noMargin" style="margin-top: 3px">
            <span class="glyphicon timeIcon"></span> <b>@{{ moment(reservation.reservation_timestamp).format('ddd, Do MMM YYYY HH:mmA') }}</b></p>
    </td>
    <td class="textAlignCenter">
        <textarea
            v-model="reservation.customer_remarks"
            :value="reservation.customer_remarks"
            placeholder="Customer Remarks"
            v-on:change="_autoSave(reservation, 'customer_remarks')"
        ></textarea>
    </td>
    <td class="textAlignCenter">
        <textarea
            v-model="reservation.staff_remarks"
            :value="reservation.staff_remarks"
            placeholder="Staff Remarks"
            v-on:change="_autoSave(reservation, 'staff_remarks')"
        ></textarea>
    </td>
    <td style="width: 150px;">
        @include('admin.reservations.status')
        <button class="btn btn-default marginTop20" v-on:click="_sendReminderSMS(reservation)" style="width: 100%;"
                :disabled="_isDisableSendReminderSMS(reservation)"
                v-if="reservation.status >= 100"
                style="">Send Reminder SMS</button>
      </select>
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
            <div>
                <button action="refund" :reservation-index='reservation_index'  class="btn btn-primary" style="width: 100%;"
                        v-on:click="_updateReservationPayment($event, PAYMENT_REFUNDED, reservation)"
                >Void</button>
            </div>
            <div>
                <button action="charge" :reservation-index='reservation_index' class="btn btn-danger marginTop20" style="width: 100%;"
                        v-on:click="_updateReservationPayment($event, PAYMENT_CHARGED, reservation)"
                >Charge</button>
            </div>
        </div>
        <!--In case of payment required, show resend SMS Msg      -->
        <div v-show="reservation.status == 50">
          <button class="btn btn-default marginTop20" v-on:click="_resendPaymentRequiredAuthorization(reservation)" style="width: 100%;"
                  style="">Re send payment <br/>authorization request SMS</button>
        </div>
    </td>
</tr>