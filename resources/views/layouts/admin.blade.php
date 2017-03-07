<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    @stack('css')
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-2">
            <div id="main-navigator">
                <ul class="nav nav-tabs">
                    <li><a href="#reservations" data-toggle='tab'>Reservations</a></li>
                    <li><a href="#customers"    data-toggle='tab'>Customers</a></li>
                    <li><a href="#settings"     data-toggle='tab'>Settings</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="row tab-pane" id="reservations">Reservations</div>
        <div class="row tab-pane" id="customers">Customers</div>
        <div class="row tab-pane active" id="settings">Settings</div>
    </div>
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
<script src="{{ url('js/bootstrap.min.js') }}"></script>
<script src="{{ url('js/moment.min.js') }}"></script>
@stack('script')
<script type="text/javascript">
</script>
</body>
</html>