<div>
  <select v-model="reservation.status" style="width: 100%; padding: 6px 12px"
          v-on:change="_autoSave(reservation, 'status')"
  >
    <option value="400" class="bg-success">Arrived</option>
    <option value="300" class="bg-success">Confirmed</option>
    <option value="200" class="bg-info">Reminder Sent</option>
    <option value="100" class="bg-info">Reserved</option>
    <option value="75" disabled>Amendmented</option>
    <option value="50" disabled>Payment Required</option>
    <option value="-100" class="bg-info">User cancelled</option>
    <option value="-200" class="bg-warning">Staff cancelled</option>
    <option value="-300" class="bg-danger">No show</option>
  </select>
</div>
