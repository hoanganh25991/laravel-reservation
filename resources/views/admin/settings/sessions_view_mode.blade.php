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
                <template v-for="session in session_group">
                    <tr>
                        <td>{{ session.session_name }} {{ session.first_arrival_time }} : {{ session.last_arrival_time }}</td>
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
                                @endverbatim
                                @include('admin.settings.timing_view_mode')
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