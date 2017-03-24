<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">Admin Page</a>
                </div>
            </div>

            <div class="col-md-10">
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('admin/reservations') }}">Reservations</a></li>
                        {{--<li><a href="#customers_content"         >Customers</a></li>--}}
                        <li><a href="{{ url('admin/settings') }}">Settings</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

    </div><!--/.container-fluid -->
</nav>