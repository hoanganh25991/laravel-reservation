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
          <p class="noMargin"><svg xmlns="http://www.w3.org/2000/svg" fill="#000" height="18" viewBox="0 0 24 24" width="18">
            <path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
          </svg> {{ $reservation->adult_pax }}+{{ $reservation->children_pax }}</p>
          <div v-if="$reservation->table_name != '' && $reservation->table_name != null && $reservation->table_name != undefined">
            <img src="{{ url('images/table-black.png') }}"> {{ $reservation->table_name }}
          </div>
        </td>
        <td>
          <p class="noMargin"><svg xmlns="http://www.w3.org/2000/svg" fill="#000" height="18" viewBox="0 0 24 24" width="18">
              <path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/>
              <path d="M0 0h24v24H0z" fill="none"/>
            </svg> {{ $reservation->full_name}}</p>
          <p class="noMargin"><svg xmlns="http://www.w3.org/2000/svg" fill="#000" height="18" viewBox="0 0 24 24" width="18">
              <path d="M0 0h24v24H0z" fill="none"/>
              <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
            </svg> ({{ $reservation->phone_country_code }}) {{ $reservation->phone }}</p>
          <p class="noMargin"><svg xmlns="http://www.w3.org/2000/svg" fill="#000" height="18" viewBox="0 0 24 24" width="18">
              <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
              <path d="M0 0h24v24H0z" fill="none"/>
            </svg> {{ $reservation->email }}</p>
          <p class="noMargin" style="margin-top: 3px"><svg xmlns="http://www.w3.org/2000/svg" fill="#000" height="18" viewBox="0 0 24 24" width="18">
              <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
              <path d="M0 0h24v24H0z" fill="none"/>
              <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg> <b>{{ $reservation->date->format('D, dS M Y H:iA') }}</b></p>
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