@extends('layouts.admin')

@section('content')
    <div id="app">
        @verbatim
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="h1">Admin</span>
                    </div>
                    <div class="modal-body">
                        <div style="box-shadow: 0 5px 15px rgba(0,0,0,.5);">
                            <h3 class="bg-info">Outlet</h3>
                            <div class="panel-body">
                                <select v-model="selected_outlet_id">
                                    <option value="null" disabled>Please select an outlet</option>
                                    <template v-for="(outlet, outlet_index) in outlets">
                                        <option :value="outlet.id">{{ outlet.outlet_name }}</option>
                                    </template>
                                </select>
                                <div v-show="outlets.length == 0"
                                     style="padding: 10px; width: 300px; margin: 20px 0;"
                                     class="bg-warning"
                                >
                                    <p>Please wait for your administrator assign which outlet you can go</p>
                                </div>
                            </div>

                        </div>

                        <div style="box-shadow: 0 5px 15px rgba(0,0,0,.5);">
                            <h3 class="bg-info">Info</h3>
                            <div class="panel-body">
                                <div style="width: 300px">
                                    <p>Hi, <strong>{{ user.display_name }}</strong></p>
                                    <p>Your role: {{ user.role }}</p>
                                    <p v-show="user.role == 'Logined'" class="bg-danger">At current role, you can't change reservations, settings, etc..</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endverbatim
        {{--@include('partial.toast')--}}
    </div>
@endsection

@push('script')
<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
@endphp</script>
<script>

    let frontend_state = {selected_outlet_id: null};

    // Build vue_state
    window.vue_state = Object.assign({}, frontend_state, state);

    let vue = new Vue({
        el: '#app',
        data: window.vue_state,
        mounted(){
            document.dispatchEvent(new CustomEvent('vue-mounted'));
        },
        updated(){
            this._goToAdminReservations();
        },
        methods: {
            _goToAdminReservations(){
                let vue = this;

                //noinspection JSUnresolvedVariable
                let outlet_id = vue.selected_outlet_id;

                let redirect_url = `admin/reservations?outlet_id=${outlet_id}`;

                window.location.href = vue._url(redirect_url);
            },

            _url(path){
                //noinspection JSUnresolvedVariable
                let base_url = this.base_url || '';

                if(base_url.endsWith('/')){
                    base_url = path.substr(1);
                }

                if(path.startsWith('/')){
                    path = path.substr(1);
                }

                return `${base_url}/${path}`;
            }
        }
    });
</script>
@endpush