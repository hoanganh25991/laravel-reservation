<div class="row">
    <div class="col-md-7">
        <div class="form-group">
            <label class="col-md-3 text-right">Name</label>
            <select
                    v-model="reservation_dialog_content.salutation"
            >
                <option value="Mr.">Mr.</option>
                <option value="Ms.">Ms.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Mdm.">Mdm.</option>
            </select>
            <input
                    type="text" style="width: 100px"
                    v-model="reservation_dialog_content.first_name"
            />
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="reservation_dialog_content.last_name"
            />
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Phone</label>
            <input
                    type="text" style="width: 30px"
                    v-model="reservation_dialog_content.phone_country_code"
            />
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="reservation_dialog_content.phone"
            />
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Email</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="reservation_dialog_content.email"
            />
        </div>


        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right switch">
                    <input type="checkbox"
                           v-model="reservation_dialog_content.send_sms_confirmation"
                    >
                    <div class="slider round"></div>
                </label>
            </div>
            <p>Remind customer of this reservation @{{ outlet.hour_before_reservation_time_to_send_confirm }} hours before</p>
        </div>

        <hr>
        <div class="form-group">
            <label class="col-md-3 text-right">Adult Pax</label>
            <select
                    v-model="reservation_dialog_content.adult_pax"
            >
              @include('admin.reservations.select-pax')
            </select>
        </div>
        <div class="form-group">
            <label class="col-md-3 text-right">Children Pax</label>
            <select
                    v-model="reservation_dialog_content.children_pax"
            >
              @include('admin.reservations.select-pax')
            </select>
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Table Name</label>
            <input type="text" style="wi: 135px;" v-model="reservation_dialog_content.table_name" />
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Date</label>
            <input
                    type="date" style="width: 135px; height: 30px"
                    v-model="reservation_dialog_content.date_str"
            >
        </div>
        <div class="form-group">
            <label class="col-md-3 text-right">Time</label>
            <input
                    type="time" style="width: 80px; height: 30px"
                    v-model="reservation_dialog_content.time_str"
            >
        </div>
    </div>
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">Customer Remarks</div>
            <div class="panel-body">
                    <textarea
                            rows="3" class="form-control"
                            v-model="reservation_dialog_content.customer_remarks"
                    >
                    </textarea>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Staff Remarks</div>
            <div class="panel-body">
                    <textarea
                            rows="3" class="form-control"
                            v-model="reservation_dialog_content.staff_remarks"
                    >
                    </textarea>
            </div>
        </div>
    </div>
</div>