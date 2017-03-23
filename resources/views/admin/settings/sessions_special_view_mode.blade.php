@php
    $sessions = isset($sessions) ? $sessions : 'special_sessions';
    $v_for = "(session, session_index) in $sessions";
@endphp
<template v-for="{{ $v_for }}">
    @verbatim
    <div style="box-shadow: 0 5px 15px rgba(0,0,0,.5);">
        <h3 class="bg-info">{{ session.one_off_date }}</h3>
        <div style="margin-left: 30px">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Session Name</th>
                    <th>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ session.session_name }} {{ session.first_arrival_time }} : {{ session.last_arrival_time }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-left: 50px">
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
                            @endverbatim
                            @include('admin.settings.timing_view_mode')
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>