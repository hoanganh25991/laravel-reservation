@verbatim
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
            >
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="reservation_dialog_content.last_name"
            >
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Phone Number</label>
            <input
                    type="text" style="width: 30px"
                    v-model="reservation_dialog_content.phone_country_code"
            >
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="reservation_dialog_content.phone"
            >
        </div>

        <div class="form-group">
            <label class="col-md-3 text-right">Email</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="reservation_dialog_content.email"
            >
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
            <p>Send SMS to reminder confirmation</p>
        </div>

        <hr>
        <div class="form-group">
            <label class="col-md-3 text-right">Adult Pax</label>
            <select
                    v-model="reservation_dialog_content.adult_pax"
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
                    v-model="reservation_dialog_content.children_pax"
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
@endverbatim
