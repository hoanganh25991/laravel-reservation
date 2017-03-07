<!DOCTYPE html>
<html>
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    @stack('css')
</head>
<body>
<div class="container">
    @yield('content')
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
<script src="{{ url('js/bootstrap.min.js') }}"></script>
<script src="{{ url('js/moment.min.js') }}"></script>
@stack('script')
<div id="footer">
    Reservations powered by Hoi.US - <a href="https://www.hoipos.com/" target="_blank">hoipos.com</a><br><em>© 2017 Hoi.US, All rights reserved. <a href="https://www.hoipos.com/" target="_blank">Terms and Conditions</a></em>
</div>
</body>
</html>