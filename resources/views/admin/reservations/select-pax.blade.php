<template v-for="n in _totalSelectPax()">
  <!-- This is hard code range of selectable pax -->
  <option :value="n">@{{ n }}</option>
</template>