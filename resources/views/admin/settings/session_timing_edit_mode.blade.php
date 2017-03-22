@verbatim
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
    @php
    @endphp
    <template v-for="(item, index) in weekly_sessions">
        <tr>
            <td>{{ item.session_name }}</td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_mondays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_mondays'"
                       v-model="item['on_mondays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_mondays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_tuesdays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_tuesdays'"
                       v-model="item['on_tuesdays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_tuesdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_wednesdays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_wednesdays'"
                       v-model="item['on_wednesdays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_wednesdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_thursdays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_thursdays'"
                       v-model="item['on_thursdays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_thursdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_fridays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_fridays'"
                       v-model="item['on_fridays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_fridays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_satdays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_satdays'"
                       v-model="item['on_satdays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_satdays'">
                </label>
            </td>
            <td>
                <input type="checkbox"
                       :checked="(item.on_sundays == 1) ? 'checked' : false"
                       :id="'session_' + item.id + 'on_sundays'"
                       v-model="item['on_sundays']"/>
                <label style="width: 12px"
                       :for="'session_' + item.id + 'on_sundays'">
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
                    <template v-for="(timing, t_index) in item.timings">
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
                        </tr>
                    </template>
                    </tbody>
                </table>
            </td>
        </tr>
    </template>
    </tbody>
</table>
@endverbatim