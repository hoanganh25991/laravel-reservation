@verbatim
<template v-for="(timing, timing_index) in session.timings">
    <tr>
        <td>
            <label class="switch">
                <input type="checkbox"
                       :session-id="session.id"
                       :timing-index="timing_index"
                       v-on:click="_updateTimingDisabled"
                       :checked="!timing['disabled']">
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
            @endverbatim
            @php
                $btn_action = isset($btn_action) ? $btn_action : '_deleteTiming';
            @endphp
            <button class=""
                    v-on:click="{{ $btn_action }}"
            >
                @verbatim
                <i class="fa fa-trash"
                   :timing-index="timing_index"
                   :session-index="session_index"
                ></i>
            </button>
        </td>
    </tr>
</template>
@endverbatim