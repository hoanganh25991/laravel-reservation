@extends('layouts.admin')

@section('content')
    <div id="app">
        @verbatim
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <span class="h4 ">Outlet</span>
                    </div>
                    <div class="modal-body">
                        <select v-model="selected_outlet" style="margin: auto">
                            <option value="null" disabled>Please select an outlet</option>
                            <template v-for="(outlet, outlet_index) in outlets">
                                <option :value="outlet.id">{{ outlet.outlet_name }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endverbatim
    </div>
@endsection

@push('script')
<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script>
    const AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';
    const AJAX_UPDATE_SCOPE_OUTLET_ID_ERROR = 'AJAX_UPDATE_SCOPE_OUTLET_ID_ERROR';

    new Vue({
        el: '#app',
        data: state,
        updated(){
            this._goToAdminReservations();
        },
        methods: {
            _goToAdminReservations(){
                let url = this._url('admin');
                let data = {
                    outlet_id: this.selected_outlet
                };

                let vue = this;

                $.ajax({
                    url,
                    method: 'POST',
                    data: JSON.stringify(data),
                    success(res){
                        if(res.statusMsg == AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS){
                            window.location.href = vue._url('admin/reservations');
                        }

                        if(res.statusMsg == AJAX_UPDATE_SCOPE_OUTLET_ID_ERROR){

                        }
                    },
                    complete(res){
                        console.log(res);
                    }
                });
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