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
                                <i class="fa fa-btn fa-sign-out"></i><span v-if="selected_outlet">{{ selected_outlet.outlet_name }}</span>
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
<script>
    (function(){
        let vue_state = {
            selected_outlet: null,
            outlet_id: null,
            outlets: [],
            base_url: null,
        };

        // What server give us
        let server_state           = window.navigator_state || {};
        window.navigator_vue_state = Object.assign(vue_state, server_state);

        // Ok, build it
        new Vue({
            el: '#outlet_select',
            data: window.navigator_vue_state,
            mounted(){
                console.log('navigator mounted');
                let self = this;
                document.addEventListener('outlet_id', function(e){
                    let data = e.detail;

                    let {outlet_id} = data;

                    self.outlet_id = outlet_id;
                });
            },
            watch: {
                outlet_id(outlet_id){
                    //if outlet_id resolved, update info
                    let matched_outlets = this.outlets.filter(function(outlet){return outlet.id == outlet_id});
                    let selected_outlet = matched_outlets[0] || {};
                    // Ok update outlet
                    this.selected_outlet= selected_outlet;
                }
            },
            methods: {
                _switchOutlet(outlet_id){
                    // Update new selected outlet_id for it self
                    this.outlet_id = outlet_id;
                    // Build data to notify out
                    let data      = {outlet_id};
                    //notify it out, who catch get data to move on
                    document.dispatchEvent(new CustomEvent('switch-outlet', {detail: data}));
                },

                _goToPage(whichPage){
                    let {base_url} = this;
                    let current_url = window.location.href;
                    let redirect_url = `${base_url}/${whichPage}`;

                    let is_on_admin_page = base_url == current_url;
                    // On admin base self redirect
                    // On reservation or setting page
                    // Let them self redirect
                    if(is_on_admin_page){
                        window.location.href = redirect_url;
                    }
                    // notify it out
                    let data = {redirect_url};
                    document.dispatchEvent(new CustomEvent('go-to-page', {detail: data}));
                }
            }
        });
    })();
</script>
@endpush