<!--
When should disable change on status

  1. When reservation not complete payment
     Any change on this reservation issss dangerous, make it from uncomplete > free complete
     Dont have to pay anything

  2. When status pump into "No Show", "User Cancelled", "Staff Cancelled" dont allow any change
-->

<div>
  <select v-model="reservation.status" style="width: 100%; padding: 6px 12px"
          v-on:change="_autoSave(reservation, 'status')"
          v-on:click="_getLastStatus(reservation.status)"
  >
    <option value="400"  :disabled="_shouldDisabledEditStatus(reservation,  400)" class="bg-success hoiH4">Arrived</option>
    <option value="300"  :disabled="_shouldDisabledEditStatus(reservation,  300)" class="bg-success hoiH4">Confirmed</option>
    <option value="200"  :disabled="_shouldDisabledEditStatus(reservation,  200)" class="bg-info hoiH4">Reminder Sent</option>
    <option value="100"  :disabled="_shouldDisabledEditStatus(reservation,  100)" class="bg-info hoiH4">Reserved</option>
    <option value="75"   :disabled="_shouldDisabledEditStatus(reservation,   75)" class="hoiH4">Amended</option>
    <option value="50"   :disabled="_shouldDisabledEditStatus(reservation,   50)" class="hoiH4">Payment Required</option>
    <option value="-100" :disabled="_shouldDisabledEditStatus(reservation, -100)" class="hoiH4">User cancelled</option>
    <option value="-200" :disabled="_shouldDisabledEditStatus(reservation, -200)" class="bg-warning hoiH4">Staff cancelled</option>
    <option value="-300" :disabled="_shouldDisabledEditStatus(reservation, -300)" class="bg-danger hoiH4">No show</option>
  </select>
</div>
