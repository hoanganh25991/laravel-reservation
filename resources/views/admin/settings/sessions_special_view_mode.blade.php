@php
    $special_sessions = isset($special_sessions) ? $special_sessions :'special_sessions';
    $v_for            = "(item, index) in $special_sessions";
@endphp
<table class="table table-striped">
    <thead>
    <tr>
        <th>Name</th>
        <th>On</th>
    </tr>
    </thead>
    <tbody>
    <template v-for="{{ $v_for }}">
        @verbatim
        <tr class="small-label">
            <td>{{ item.session_name }}</td>
            <td>{{ item.one_off_date }}</td>
        </tr>
        <tr>
            <td colspan="8">
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
                    <template v-for="timing in item.timings">
                        <tr>
                            <td>
                                <label class="switch">
                                    <input type="checkbox"
                                           :checked="(timing.disabled == 1) ? 'checked' : false">
                                    <div class="slider round"></div>
                                </label>
                            </td>
                            <td>{{ timing.timing_name }}</td>
                            <td>{{ timing.firt_arrival_time }}</td>
                            <td>{{ timing.last_arrival_time }}</td>
                            <td>{{ timing.interval_minutes }}</td>
                            <td>{{ timing.capacity_1 }}</td>
                            <td>{{ timing.capacity_2 }}</td>
                            <td>{{ timing.capacity_3_4 }}</td>
                            <td>{{ timing.capacity_5_6 }}</td>
                            <td>{{ timing.capacity_8_x }}</td>
                            <td>{{ timing.max_pax }}</td>
                            <td>
                                <input type="checkbox" :id="'children_allowed_' + timing.id"
                                       :checked="(timing.children_allowed == 1) ? 'checked' : false"/>
                                <label :for="'children_allowed_' + timing.id">
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