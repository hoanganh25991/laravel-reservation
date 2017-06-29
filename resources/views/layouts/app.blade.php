<!DOCTYPE html>
<html >
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url('css/flat-ui.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url('css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url_mix('css/reservation.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @stack('css')
</head>
<body>

<div class="container">
    @yield('content')
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
{{--<script src="{{ url('js/bootstrap.min.js') }}"></script>--}}
<script src="{{ url('js/flat-ui.min.js') }}"></script>
<script src="{{ url('js/moment.min.js') }}"></script>
<script src="{{ url('js/redux.min.js') }}"></script>
@stack('script')
</body>
</html>