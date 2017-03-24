<!DOCTYPE html>
<html>
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url('css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url(substr(mix('css/reservation.css'), 1)) }}" rel="stylesheet">
    <style>
        .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
            padding: 2px;
        }
    </style>
    @stack('css')
</head>
<body>

<div class="container">
    @yield('content')
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
<script src="{{ url('js/bootstrap.min.js') }}"></script>
<script src="{{ url('js/moment.min.js') }}"></script>
<script src="{{ url('js/redux.min.js') }}"></script>
@stack('script')
</body>
</html>