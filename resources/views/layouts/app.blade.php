<!DOCTYPE html>
<html>
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    @yield('css')
</head>
<body>
<div class="container">
    @yield('content')
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
<script src="{{ url('js/bootstrap.min.js') }}"></script>
@yield('script')
</body>
</html>