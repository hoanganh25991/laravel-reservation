<style>
    .open > .dropdown-menu {
        visibility: visible;
        opacity: 1;
        height: auto;
        margin-top: 9px;
    }

    .dropdown-menu {
        display: block;
        visibility: hidden;
        opacity: 0;
        margin-top: 0;
        transition: 0.25s;
    }
</style>
<nav class="navbar navbar-default" id="admin-navigator">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{ url('admin') }}">Admin Page</a>
                </div>
            </div>

            <div class="col-md-10" id="outlet_select">
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a style="cursor: hand;cursor: pointer;" v-on:click="_goToPage('reservations')">Reservations</a></li>
                        <li><a style="cursor: hand;cursor: pointer;" v-on:click="_goToPage('settings')">Settings</a></li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        {{--For Outlet Select--}}
                        @verbatim
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="homeIcon"></span><span v-if="selected_outlet">{{ selected_outlet.outlet_name }}</span>
                            </a>

                            <ul class="dropdown-menu">
                                <template v-for="(outlet, outlet_index) in outlets">
                                    <li>
                                        <a  class="dropdown-toggle" data-toggle="dropdown"
                                            v-on:click="_switchOutlet(outlet.id)">{{ outlet.outlet_name }}</a>
                                    </li>
                                </template>
                            </ul>
                        </li>
                        @endverbatim
                         <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div><!--/.container-fluid -->
</nav>
@push('before-script')
<script>@php
        $state_json = json_encode($navigator_state);
        echo "window.navigator_state = $state_json;";
    @endphp</script>
<!--suppress JSUnresolvedVariable window.naviagtor_state -->
<script src="{{ url_mix('js/admin-navigator.js') }}"></script>
@endpush