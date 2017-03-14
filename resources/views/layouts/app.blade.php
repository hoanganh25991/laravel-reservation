<!DOCTYPE html>
<html>
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url(substr(mix('css/reservation.css'), 1)) }}" rel="stylesheet">
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

{{--<script src="{{ url(substr(mix('js/vendor.js'), 1)) }}"></script>--}}
{{--<script src="{{ url(substr(mix('js/manifest.js'), 1)) }}"></script>--}}
{{--<script src="{{ url('js/bootstrap.min.js') }}"></script>--}}
@stack('script')

</body>
</html>