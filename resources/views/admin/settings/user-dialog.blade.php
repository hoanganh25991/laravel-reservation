@verbatim
<div class="row">
    <div class="col-md-5">
        <div class="form-group">
            <label class="col-md-4 text-right">User name</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="user_dialog_content.user_name"
            >
        </div>

        <div class="form-group">
            <label class="col-md-4 text-right">Password</label>
            <input
                    type="password" style="display: inline-block; max-width: 200px"
                    v-model="user_dialog_content.password"
                    v-on:click="_wantToChangePassword"
            >
            <span v-show="user_dialog_content.password_error">Password at least 6 character</span>
        </div>

        <div class="form-group" v-show="user_dialog_content.reset_password">
            <label class="col-md-4 text-right">Confirm</label>
            <input
                    type="password" style="display: inline-block; max-width: 200px"
                    v-model="user_dialog_content.confirm_password"
            >
            <span v-show="user_dialog_content.password_mismatch"
                  class="bg-danger"
            >Please retype password, mismatch!</span>
        </div>

        <div class="form-group">
            <label class="col-md-4 text-right">Email</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="user_dialog_content.email"
            >

        </div>

        <div class="form-group">
            <label class="col-md-4 text-right">Display name</label>
            <input
                    type="text" style="display: inline-block; max-width: 200px"
                    v-model="user_dialog_content.display_name"
            >
        </div>
    </div>
    <div class="col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">Role</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-4 text-right">Permission level</label>
                    <select v-model="user_dialog_content.permission_level"
                    >
                        <option value="0">Reservations</option>
                        <option value="10">Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right">Assign Restaurant</label>
                    <select multiple class="multiple-select"
                            v-model="user_dialog_content.outlet_ids"
                    >
                        <template v-for="(outlet, outlet_index) in outlets">
                            <option :value="outlet.id">{{ outlet.outlet_name }}</option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@endverbatim
