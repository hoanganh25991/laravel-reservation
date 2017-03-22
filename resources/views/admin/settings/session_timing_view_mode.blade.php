@php
    $weekly_view = isset($weekly_view) ? $weekly_view : 'weekly_view';
    $v_for = "(session_group, group_name) in $weekly_view";
@endphp

<template v-for="{{ $v_for }}">
    @verbatim
    <div style="box-shadow: 0 5px 15px rgba(0,0,0,.5);">
        <h3 class="bg-info">{{ group_name }}</h3>
        <div style="margin-left: 30px">
            <table class="table table-striped">
                <tbody>
                <template v-for="item in session_group">
                    <tr>
                        <td>{{ item.session_name }} {{ item.first_arrival_time }} : {{ item.last_arrival_time }}</td>
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
                                <template v-for="timing in item.timings">
                                    <tr>
                                        <td>
                                            <label class="switch">{{ timing.disabled }}
                                                <input type="checkbox"
                                                       disabled
                                                       :checked="timing['disabled']">
                                                <div class="slider round"></div>
                                            </label>
                                        </td>
                                        <td>{{ timing.timing_name }}</td>
                                        <td>{{ timing.first_arrival_time }}</td>
                                        <td>{{ timing.last_arrival_time }}</td>
                                        <td>{{ timing.interval_minutes }}</td>
                                        <td>{{ timing.capacity_1 }}</td>
                                        <td>{{ timing.capacity_2 }}</td>
                                        <td>{{ timing.capacity_3_4 }}</td>
                                        <td>{{ timing.capacity_5_6 }}</td>
                                        <td>{{ timing.capacity_7_x }}</td>
                                        <td>{{ timing.max_pax }}</td>
                                        <td>
                                            <input type="checkbox"
                                                   :checked="(timing.children_allowed == 1) ? 'checked' : false"/>
                                            <label style="width: 12px"></label>
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
        </div>
    </div>
</template>
@endverbatim