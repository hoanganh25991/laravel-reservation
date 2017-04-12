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

            <div class="col-md-10">
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('admin/reservations') }}">Reservations</a></li>
                        {{--<li><a href="#customers_content"         >Customers</a></li>--}}
                        <li><a href="{{ url('admin/settings') }}">Settings</a></li>
                    </ul>


                    <ul class="nav navbar-nav navbar-right">
                        {{--For Outlet Select--}}
                        @verbatim
                        <li class="dropdown" id="outlet_select">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-btn fa-sign-out"></i>{{ outlet.outlet_name }}
                            </a>

                            <ul class="dropdown-menu">
                                <template v-for="(outlet, outlet_index) in outlets">
                                    <li>
                                        <a  class="dropdown-toggle" data-toggle="dropdown"
                                            :outlet-id="outlet.id"
                                            v-on:click="_switchOutlet">{{ outlet.outlet_name }}</a>
                                    </li>
                                </template>
                            </ul>
                        </li>
                        @endverbatim
                         <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            {{--<li><a href="{{ url('/register') }}">Register</a></li>--}}
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
<script>@php
        $state_json = json_encode($navigator_state);
        echo "window.navigator_state = $state_json;";
    @endphp</script>
@push('before-body')
<!--suppress JSUnresolvedVariable window.naviagtor_state -->
<script>
    (function(){
        //let outlets = window.outlets;
        //console.log(window.navigator_state);
        new Vue({
            el: '#outlet_select',
            data: window.navigator_state,
            mounted(){
                //try to resolve when init
                this._updateOutletName();
                //please don't fire vue-mounted here
                //current there are 2 vue on page
                //you, the navigator
                //other in main page
            },
            methods: {
                _switchOutlet(e){
                    //console.log(e);
                    let a = e.target;
                    //Only handle when it is A element clicked
                    if(a.tagName == 'A'){
                        let outlet_id = a.getAttribute('outlet-id');
                        let data      = {outlet_id};
                        //notify it out, who catch get data to move on
                        document.dispatchEvent(new CustomEvent('switch-outlet', {detail: data}));
                        //update selected outlet_id
                        Object.assign(window.navigator_state, {outlet_id});
                        //update outlet info
                        this._updateOutletName();
                    }


                },

                _updateOutletName(){
                    let outlet_id = this.outlet_id;
                    //if outlet_id resolved, update info
                    let matched_outlets = this.outlets.filter(function(outlet){return outlet.id == outlet_id});
                    //self pick on the first match
                    let outlet = {};

                    if(matched_outlets.length > 0){
                        outlet = matched_outlets[0];
                    }
                    //assign this back to vue
                    Object.assign(window.navigator_state, {outlet});
                }
            }
        });
    })();
</script>
@endpush