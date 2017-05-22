@verbatim
<div class="row">
    <div class="col-md-7">
        <div class="form-group">
            <label class="col-md-3 text-right">XXX</label>
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
            <label class="col-md-3 text-right">Phone</label>
            <input
                    type="text" style="width: 30px"
                    v-model="new_reservation.phone_country_code"
            />
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="new_reservation.phone"
            />
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Email</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="new_reservation.email"
            />
        </div>



        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right switch">
                    <input type="checkbox"
                           v-model="new_reservation.send_sms_confirmation"
                    >
                    <div class="slider round"></div>
                </label>
            </div>
            <p>Send SMS to remind confirmation</p>
        </div>

        <hr>

        <div class="form-group">
            <label class="col-md-3 text-right">Adult Pax</label>
            <select
                    v-model="new_reservation.adult_pax"
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
        <div class="form-group">
            <label class="col-md-3 text-right">Children Pax</label>
            <select
                    v-model="new_reservation.children_pax"
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

        <div class="form-group">
            <label class="col-md-3"></label>
            <div style="display: inline-block">
                <span class="small text-muted">Notice. Min Pax: {{ outlet.overall_min_pax }}. Max Pax: {{ outlet.overall_max_pax  }}</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Date</label>
            <input
                    type="date" style="width: 135px; height: 30px"
                    v-model="new_reservation.date_str"
            >
        </div>
        <div class="form-group">
            <label class="col-md-3 text-right">Time</label>
            <button class="btn btn-default"
                v-on:click="_searchAvailableTime"
            >Search available</button>
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right"></label>
            <div style="display: inline-block">
                <ul style="list-style-type: none; margin: 0; padding: 0;">
                    <li v-if="is_calling_ajax" class="bg-info">
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
    </div>
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