<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Print Page</title>
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
</head>
<body>
<div class="flexRow hidden-print">
  <div class="flex1"></div>
  <button onclick="window.print()">Print</button>
</div>
<table style="width: 100%">
  <tr>
    <th>No</th>
    <th>Diner Details</th>
    <th>Date Time</th>
    <th>Table</th>
    <th>Diner Remarks</th>
    <th>Staff Remarks</th>
  </tr>
  @foreach($reservations as $index => $reservation)
    <tr>
      <td>
        {{ $index + 1 }}
      </td>
      <td>
        <p><strong>{{ $reservation->full_name }}</strong></p>
        <p>{{ $reservation->full_phone_number }}</p>
        <p>{{ $reservation->email }}</p>
      </td>
      <td>
        <p><strong>{{ $reservation->date->format('h:i a') }}</strong></p>
        <p>{{ $reservation->date->format('dS M Y') }}</p>
        <p><strong>{{ $reservation->pax_size }}</strong> pax</p>
      </td>
      <td>
        <p>{{ $reservation->table_layout_name }} {{ $reservation->table_name }}</p>
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
</body>
</html>