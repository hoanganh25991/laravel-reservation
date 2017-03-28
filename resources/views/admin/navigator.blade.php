<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{ url('admin') }}">Admin Page</a>
                </div>
            </div>

            <div class="col-md-10">
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('admin/reservations') }}">Reservations</a></li>
                        {{--<li><a href="#customers_content"         >Customers</a></li>--}}
                        <li><a href="{{ url('admin/settings') }}">Settings</a></li>
                    </ul>


                    <ul class="nav navbar-nav navbar-right">
                        {{--For Outlet Select--}}
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-btn fa-sign-out"></i>Outlet</a>

                            <ul class="dropdown-menu" role="menu" id="outlet_select">
                                @verbatim
                                <template v-for="(outlet, outlet_index) in outlets">
                                    <li v-on:click="_switchOutlet"><a :outlet-id="outlet.id">{{ outlet.outlet_name }}</a></li>
                                </template>
                                @endverbatim
                            </ul>
                        </li>
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
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
<script>@php
    $outlets_json = json_encode($outlets);
    $admin_url    = json_encode(url('admin'));
    echo "window.outlets = $outlets_json;";
@endphp</script>
@push('before-body')
    <script>
        /**
         * @warn
         * @warn
         * @warn
         * dangerous code
         */
        (function(){
            new Vue({
                el: '#outlet_select',
                data: outlets,
                methods: {
                    _switchOutlet(e){
                        let a = e.target;
                        if(a.tagName == 'A'){
                            let outlet_id = a.getAttribute('outlet-id');
                            let data = {outlet_id};
                            document.dispatchEvent(new CustomEvent('switch-outlet', {detail: data}));
                        }
                    }
                }
            });
        })();
    </script>
@endpush