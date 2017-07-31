<div>
  <select v-model="reservation.status" style="width: 100%; padding: 6px 12px"
          v-on:change="_autoSave(reservation, 'status')"
          v-on:click="_getLastStatus(reservation.status)"
  >
    <option value="400" :disabled="reservation.status == 50" class="bg-success hoiH4">Arrived</option>
    <option value="300" :disabled="reservation.status == 50" class="bg-success hoiH4">Confirmed</option>
    <option value="200" :disabled="reservation.status == 50" class="bg-info hoiH4">Reminder Sent</option>
    <option value="100" :disabled="reservation.status == 50" class="bg-info hoiH4">Reserved</option>
    <option value="75" disabled class="hoiH4">Amended</option>
    <option value="50" disabled class="hoiH4">Payment Required</option>
    <option value="-100" disabled class="hoiH4">User cancelled</option>
    <option value="-200" class="bg-warning hoiH4">Staff cancelled</option>
    <option value="-300" :disabled="reservation.status == 50" class="bg-danger hoiH4">No show</option>
  </select>
</div>
