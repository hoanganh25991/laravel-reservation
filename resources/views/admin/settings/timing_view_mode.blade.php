@verbatim
<template v-for="timing in session.timings">
    <tr>
        <td>
            <label class="switch">{{ timing.disabled }}
                <input type="checkbox"
                       disabled
                       :checked="!timing['disabled']">
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
@endverbatim