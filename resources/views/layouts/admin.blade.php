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
                    <li><a href="#reservations" data-toggle="tab">Home</a></li>
                    <li><a href="#customers" data-toggle="tab">Profile</a></li>
                    <li><a href="#settings" data-toggle="tab">Messages</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row bhoechie-tab-container">
        <div class="col-md-2 bhoechie-tab-menu">
            <div class="list-group">
                <a href="#" class="list-group-item active text-center">
                    <h4 class="glyphicon glyphicon-plane"></h4><br/>Flight
                </a>
                <a href="#" class="list-group-item text-center">
                    <h4 class="glyphicon glyphicon-road"></h4><br/>Train
                </a>
                <a href="#" class="list-group-item text-center">
                    <h4 class="glyphicon glyphicon-home"></h4><br/>Hotel
                </a>
                <a href="#" class="list-group-item text-center">
                    <h4 class="glyphicon glyphicon-cutlery"></h4><br/>Restaurant
                </a>
                <a href="#" class="list-group-item text-center">
                    <h4 class="glyphicon glyphicon-credit-card"></h4><br/>Credit Card
                </a>
            </div>
        </div>
        <div class="col-md-10 bhoechie-tab">
            <!-- flight section -->
            <div class="bhoechie-tab-content active">
                <center>
                    <h1 class="glyphicon glyphicon-plane" style="font-size:14em;color:#55518a"></h1>
                    <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                    <h3 style="margin-top: 0;color:#55518a">Flight Reservation</h3>
                </center>
            </div>
            <!-- train section -->
            <div class="bhoechie-tab-content">
                <center>
                    <h1 class="glyphicon glyphicon-road" style="font-size:12em;color:#55518a"></h1>
                    <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                    <h3 style="margin-top: 0;color:#55518a">Train Reservation</h3>
                </center>
            </div>

            <!-- hotel search -->
            <div class="bhoechie-tab-content">
                <center>
                    <h1 class="glyphicon glyphicon-home" style="font-size:12em;color:#55518a"></h1>
                    <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                    <h3 style="margin-top: 0;color:#55518a">Hotel Directory</h3>
                </center>
            </div>
            <div class="bhoechie-tab-content">
                <center>
                    <h1 class="glyphicon glyphicon-cutlery" style="font-size:12em;color:#55518a"></h1>
                    <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                    <h3 style="margin-top: 0;color:#55518a">Restaurant Diirectory</h3>
                </center>
            </div>
            <div class="bhoechie-tab-content">
                <center>
                    <h1 class="glyphicon glyphicon-credit-card" style="font-size:12em;color:#55518a"></h1>
                    <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                    <h3 style="margin-top: 0;color:#55518a">Credit Card</h3>
                </center>
            </div>
        </div>
    </div>
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
<script src="{{ url('js/bootstrap.min.js') }}"></script>
<script src="{{ url('js/moment.min.js') }}"></script>
@stack('script')
<script type="text/javascript">
    $(document).ready(function(){
        $("div.bhoechie-tab-menu>div.list-group>a").click(function(e){
            e.preventDefault();
            $(this).siblings('a.active').removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
            $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
        });
    });
</script>
</body>
</html>