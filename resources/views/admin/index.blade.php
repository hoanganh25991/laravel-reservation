@extends('layouts.admin')

@push('css')
<link href="{{ url_mix('css/flex.css') }}" rel="stylesheet" media="screen">
@endpush

@section('content')
    <div id="app" class="padding15LeftRight">
        @verbatim
        <div>
            <div class="hasBorder bg-info">
                <h3 class="noMargin">Outlet</h3>
            </div>
            <div class="hasBorder paddingAll">
                <h4 class="noMargin">Please select an outlet</h4>
                <div class="flexRow flexWrap flexContentSpace">
                    <template v-for="(outlet, outlet_index) in outlets">
                        <button class="btn btn-default width25Per marginTop20"
                                v-on:click="_goToAdminReservations(outlet.id)"
                        >{{ outlet.outlet_name }}</button>
                    </template>
                </div>
                <div v-show="outlets.length == 0"
                     style="padding: 10px; width: 300px; margin: 20px 0;"
                     class="bg-warning"
                >
                    <p>Please wait for your administrator assign which outlet you can go</p>
                </div>
            </div>

        </div>

        <div class="marginTop20">
            <div class="hasBorder bg-info">
                <h3 class="noMargin">Info</h3>
            </div>
            <div class="hasBorder paddingAll">
                <div style="width: 300px">
                    <p>Hi, <strong>{{ user.display_name }}</strong></p>
                    <p>Your role: {{ user.role }}</p>
                    <p v-show="user.role == 'Logined'" class="bg-danger">At current role, you can't change reservations, settings, etc..</p>
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
<script src="{{ url_mix('js/admin-index.js') }}"></script>
@endpush