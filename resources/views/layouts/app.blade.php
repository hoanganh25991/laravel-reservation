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
@stack('script')
<div id="footer">
    Reservations powered by OUS - <a href="https://originallyus.sg" target="_blank">originallyus.sg</a><br><em>© 2017 OUS, All rights reserved. <a href="https://originallyus.sg" target="_blank">Terms and Conditions</a></em>
</div>
</body>
</html>