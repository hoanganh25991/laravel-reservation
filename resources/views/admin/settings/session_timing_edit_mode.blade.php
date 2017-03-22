@php
    $session_group = isset($session_group) ? $session_group : 'weekly_sessions';
    $v_for = "(session, session_index) in $session_group";
@endphp

<table class="table table-striped">
    <thead>
    <tr>
        <th>Session Name</th>
        <th>Mondays</th>
        <th>Tuesdays</th>
        <th>Wednesdays</th>
        <th>Thursdays</th>
        <th>Fridays</th>
        <th>Saturdays</th>
        <th>Sundays</th>
    </tr>
    </thead>
    <tbody>
    <template v-for="{{ $v_for }}">
        @verbatim
        <tr>
            <td>
                <input type="text"
                       style="width: 90px"
                       :id="'session_' + session.id + 'session_name'"
                       :value="session.session_name"
                       v-model="session['session_name']">
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_mondays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_mondays'"
                       v-model="session['on_mondays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_mondays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_tuesdays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_tuesdays'"
                       v-model="session['on_tuesdays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_tuesdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_wednesdays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_wednesdays'"
                       v-model="session['on_wednesdays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_wednesdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_thursdays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_thursdays'"
                       v-model="session['on_thursdays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_thursdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_fridays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_fridays'"
                       v-model="session['on_fridays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_fridays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_satdays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_satdays'"
                       v-model="session['on_satdays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_satdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(session.on_sundays == 1) ? 'checked' : false"
                       :id="'session_' + session.id + 'on_sundays'"
                       v-model="session['on_sundays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_sundays'">
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="8" style="padding-left: 50px">
                <table class="table table-striped sub-level">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>First arrival time</th>
                        <th>Last arrival time</th>
                        <th>Interval time</th>
                        <th>Capacity 1</th>
                        <th>Capacity 2</th>
                        <th>Capacity 3_4</th>
                        <th>Capacity 5_6</th>
                        <th>Capacity 8_x</th>
                        <th>Max pax</th>
                        <th>Children</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(timing, timing_index) in session.timings">

                        <tr>
                            <td>
                                <label class="switch">
                                    <input type="checkbox"
                                           v-model="timing['disabled']"
                                           :checked="timing['disabled']">
                                    <div class="slider round"></div>
                                </label>
                            </td>
                            <td>
                                <input type="text"
                                       style="width: 60px"
                                       :id="'timing_' + timing.id + 'timing_name'"
                                       :value="timing.timing_name"
                                       v-model="timing['timing_name']">
                            </td>
                            <td>
                                <input type="time"
                                       style="width: 65px; height: 20px"
                                       :id="'timing_' + timing.id + 'first_arrival_time'"
                                       :value="timing.first_arrival_time"
                                       v-model="timing['first_arrival_time']">
                            </td>
                            <td>
                                <input type="time"
                                       style="width: 65px; height: 20px"
                                       :id="'timing_' + timing.id + 'last_arrival_time'"
                                       :value="timing.last_arrival_time"
                                       v-model="timing['last_arrival_time']">
                            </td>
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'interval_minutes'"
                                       v-model="timing['interval_minutes']">
                            </td>
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'capacity_1'"
                                       v-model="timing['capacity_1']">
                            </td>
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'capacity_2'"
                                       v-model="timing['capacity_2']">
                            </td>
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'capacity_3_4'"
                                       v-model="timing['capacity_3_4']">
                            </td>
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'capacity_5_6'"
                                       v-model="timing['capacity_5_6']">
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'capacity_7_x'"
                                       v-model="timing['capacity_7_x']">
                            </td>
                            <td>
                                <input type="number"
                                       style="width: 40px"
                                       :id="'timing_' + timing.id + 'max_pax'"
                                       v-model="timing['max_pax']">
                            </td>
                            <td>
                                <input type="checkbox"
                                       :checked="(timing.children_allowed == 1) ? 'checked' : false"
                                       :id="'timing_' + timing.id + 'children_allowed'"
                                       v-model="timing['children_allowed']">
                                <label style="width: 12px"
                                       :for="'timing_' + timing.id + 'children_allowed'">
                                </label>
                            </td>
                            <td>
                                <button class="btn btn-sm"
                                        v-on:click="_deleteTiming"
                                >
                                    <i class="fa fa-trash" aria-hidden="true"
                                       :timing-index="timing_index"
                                       :session-index="session_index"
                                    ></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                        <tr>
                            <td colspan="13" style="background-color: white">
                                <button class="btn bg-info btn-sm pull-right"
                                        :session-index="session_index"
                                        v-on:click="_addTimingToSession"
                                >add timing</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </template>
    </tbody>
</table>
@endverbatim