@php
    $session_group = isset($session_group) ? $session_group : 'weekly_sessions';
    $v_for = "(session, session_index) in $session_group";
@endphp
<template v-for="{{ $v_for }}">
    <div style="box-shadow: 0 5px 15px rgba(0,0,0,.5);">
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
            <th>
                <button class="pull-right"
                        v-on:click="_deleteSession"
                >
                    <i class="fa fa-trash"
                       :session-index="session_index"
                    ></i>
                </button>
            </th>
        </tr>
        </thead>
        <tbody>
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
                       :id="'session_' + session.id + 'on_saturdays'"
                       v-model="session['on_saturdays']"/>
                <label style="width: 12px"
                       :for="'session_' + session.id + 'on_saturdays'">
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
            <td>

            </td>
        </tr>
        <tr>
            <td colspan="9" style="padding-left: 50px">
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
                    @include('admin.settings.timing_edit_mode')
                    <tr>
                        <td colspan="13" style="background-color: white">
                            <button class="btn bg-info pull-right"
                                    :session-index="session_index"
                                    v-on:click="_addTimingToSession"
                            >add timing
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    </div>
</template>