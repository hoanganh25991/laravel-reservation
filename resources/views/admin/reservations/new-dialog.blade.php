@verbatim
<div class="row">
    <div class="col-md-7" style="padding: 0">
        <div class="form-group">
            <label class="col-md-3 text-right">Phone</label>
            <input
              type="text" style="width: 30px"
              v-model="new_reservation.phone_country_code"
            />
            <input
              type="text" style="display: inline-block; max-width: 200px"
              v-model="new_reservation.phone"
              v-on:change="_findCustomerByPhone"
            />
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Name</label>
            <select
                    v-model="new_reservation.salutation"
            >
                <option value="Mr.">Mr.</option>
                <option value="Ms.">Ms.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Mdm.">Mdm.</option>
            </select>
            <input
                    type="text" style="width: 100px"
                    v-model="new_reservation.first_name"
            />
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="new_reservation.last_name"
            />
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Email</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="new_reservation.email"
            />
        </div>

        <div class="row">
            <div class="col-md-3">
                <label class="pull-right switch">
                    <input type="checkbox"
                           v-model="new_reservation.send_sms_confirmation"
                    >
                    <div class="slider round"></div>
                </label>
            </div>
            <p>Remind customer of this reservation {{ outlet.hour_before_reservation_time_to_send_confirm }} hours before</p>
        </div>

        <hr>

        <div class="row">
            <label class="col-md-3 text-right">Adult Pax</label>
            <select
                    v-model="new_reservation.adult_pax"
                    v-on:change="_alertOutOfRange"
            >
                <!-- This is hard code range of selectable pax -->
                <option value="0">0</option>
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
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
            </select>
        </div>

        <div class="row">
            <label class="col-md-3 text-right">Children Pax</label>
            <select
                    v-model="new_reservation.children_pax"
                    v-on:change="_alertOutOfRange"
            >
                <!-- This is hard code range of selectable pax -->
                <option value="0">0</option>
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
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
            </select>
        </div>

        <div class="row">
            <label class="col-md-3"></label>
            <div  class="col-md-9" style="padding: 0">
                <div class="small text-muted">Overall Pax, allowed range, min: {{ outlet.overall_min_pax }}, max: {{ outlet.overall_max_pax  }}.</div>
            </div>
        </div>

        <div class="row">
            <label class="col-md-3 text-right">Date</label>
            <div  class="col-md-9" style="padding: 0">
                <div style="position: relative">
                    <span style="position: absolute; left: 0; padding: 5px;">{{ moment(new_reservation.date_str, 'YYYY-MM-DD').format('DD/MM/YYYY') }}</span>
                    <input type="date" style="width: 135px; height: 30px; text-indent: -9999px;"
                           v-on:change="_updateNewReservationDate($event.target.value)"
                    />
                </div>
                <div class="small text-muted">Max days in advance: {{ outlet.max_days_in_advance }}.
                    So, only available before {{ moment().add(+outlet.max_days_in_advance + 1, 'days').format('YYYY-MM-DD') }}</div>
            </div>

        </div>

        <div class="row">
            <label class="col-md-3 text-right">Time</label>
            <button class="btn btn-default"
                v-on:click="_searchAvailableTime"
            >Search available</button>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9" style="padding: 0">
                <ul style="list-style-type: none; margin: 0; padding: 0; max-height: 350px; overflow-y: auto;">
                    <li v-if="is_calling_ajax == AJAX_SEARCH_AVAILABLE_TIME" class="bg-info">
                        Searching...
                    </li>
                    <template v-for="(chunk, chunk_index) in new_reservation.available_time">
                        <li class="LI_1"
                            :class="chunk.time == new_reservation.time_str ? 'selected-time' : ''"
                            v-on:click="_pickTime(chunk.time)"
                        >
                            <p class="P_2">
                                {{ outlet.outlet_name }}
                            </p>
                            <h2 class="H2_3">
                                {{ chunk.time }}
                            </h2>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <div>
            <hr/>
            <div class="row">
                <div class="col-md-3">
                    <label class="switch  pull-right">
                        <input v-on:click="_togglePaymentRequired"
                               :class="+new_reservation.payment_required ? 'switchOn' : ''" />
                        <div class="slider round"></div>
                    </label>
                </div>
                <div class="col-md-9" style="padding: 0">
                    <div>Required  Credit Card Authorization</div>
                    <small class="text-muted">System will automatically SMS the customer with credit card authorization link. <br/>Customer must authorize credit card before the reservation is confirmed.</small>

                </div>
            </div>

            <div class="row">
                <label class="col-md-3 text-right">Amount</label>
                <div class="input-group">
                    <span class="input-group-addon">{{new_reservation.payment_currency}}</span>
                    <input type="text" v-model="new_reservation.payment_amount" :disabled="!new_reservation.payment_required"/>
                </div>
            </div>
        </div>
    </div>
    <!-- Customer & staf remark -->
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">Customer Remarks</div>
            <div class="panel-body">
                    <textarea
                            rows="3" class="form-control"
                            v-model="new_reservation.customer_remarks"
                    >
                    </textarea>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Staff Remarks</div>
            <div class="panel-body">
                    <textarea
                            rows="3" class="form-control"
                            v-model="new_reservation.staff_remarks"
                    >
                    </textarea>
            </div>
        </div>
    </div>
</div>
@endverbatim
