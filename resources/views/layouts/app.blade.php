<!DOCTYPE html>
<html >
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url('css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url(substr(mix('css/reservation.css'), 1)) }}" rel="stylesheet">
    @stack('css')
</head>
<body style="width: 468px; height: 670px; overflow: hidden;">

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