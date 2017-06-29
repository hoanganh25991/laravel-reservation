@extends('layouts.empty')

@push('head')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Print Page</title>
@endpush

@push('css')
@include('icon')
<style>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  table, tr, td, th, tbody, thead, tfoot {
    page-break-before: avoid !important;
    page-break-inside: avoid !important;
    page-break-after: avoid !important;
  }
  th {
    background-color: #9E9E9E;
  }
  p {
    margin: 0;
    padding: 0 5px;
  }
  .flexRow {
    display: flex;
  }
  .flexColumn {
    display: flex;
    flex-direction: column;
  }
  .justifyContentCenter {
    justify-content: center;
  }
  .flex1 {
    flex: 1;
  }
  @media print {
    .hidden-print {
      display: none !important;
    }
  }
</style>
@endpush

@section('content')
  <div class="flexRow hidden-print">
    <div class="flex1"></div>
    <button onclick="window.print()">Print</button>
  </div>
  <table style="width: 100%">
    <tr>
      <th></th>
      <th>No.</th>
      <th>Customer Info</th>
      <th>Customer Remarks</th>
      <th>Staff Remarks</th>
    </tr>
    @foreach($reservations as $index => $reservation)
      <tr>
        <td>
          {{ $index + 1 }}
        </td>
        <td>
          <p class="noMargin">{{ $reservation->confirm_id }}</p>
          <p class="noMargin"><span class="glyphicon contactIcon"></span> {{ $reservation->adult_pax }}+{{ $reservation->children_pax }}</p>
          <div v-if="$reservation->table_name != '' && $reservation->table_name != null && $reservation->table_name != undefined">
            <span class="glyphicon tableIcon"></span> {{ $reservation->table_name }}
          </div>
        </td>
        <td>
          <p class="noMargin"><span class="glyphicon contactIcon"></span> {{ $reservation->full_name}}</p>
          <p class="noMargin"><span class="glyphicon phoneIcon"></span> ({{ $reservation->phone_country_code }}) {{ $reservation->phone }}</p>
          <p class="noMargin"><span class="glyphicon emailIcon"></span> {{ $reservation->email }}</p>
          <p class="noMargin" style="margin-top: 3px">
            <span class="glyphicon timeIcon"></span> <b>{{ $reservation->date->format('D, dS M Y H:iA') }}</b></p>
        </td>
        <td>
          <p>{{ $reservation->customer_remarks }}</p>
        </td>
        <td>
          <p>{{ $reservation->staff_remarks }}</p>
        </td>
      </tr>
    @endforeach
  </table>
@endsection