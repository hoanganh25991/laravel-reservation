@verbatim
<div class="row">
    <div class="col-md-5">
        <div class="form-group">
            <label class="col-md-4 text-right">User name</label>
            <input
                    type="text" style="width: 100px"
                    v-model="user_dialog_content.user_name"
            >
        </div>

        <div class="form-group">
            <label class="col-md-4 text-right">Password</label>
            <input
                    type="text" style="width: 30px"
                    v-model="user_dialog_content.password"
            >
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
                    <select
                    >
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
                    </select>
                </div>

                <div class="form-group">
                    <label class="col-md-4 text-right">Assign Restaurant</label>
                    <select
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
