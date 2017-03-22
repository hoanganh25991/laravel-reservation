@extends('layouts.app')

@section('content')
    {{--current id = 5--}}
    <!-- Static navbar -->
    @include('admin.navigator')

    <!-- Main component for a primary marketing message or call to action -->
    <div id='app' style="position: relative; height: calc(100vh - 100px); overflow-y: scroll; overflow-x: hidden;">
        <div id="settings_content" class="row">
            <div id="reservations_content" class="col-md-2">
                <div class="navbar navbar-default">
                    <ul class="nav navbar-nav" id="go-container">
                        <li><a destination="#weekly_sessions_view" class="btn go">Weekly Sessions View</a></li>
                        <li><a destination="#weekly_sessions" class="btn go">Weekly Sessions</a></li>
                        <li><a destination="#special_sessions" class="btn go">Special Sessions</a></li>
                        <li><a destination="#buffer" class="btn go">Buffer</a></li>
                        <li><a destination="#notification" class="btn go">Notification</a></li>
                        <li><a destination="#settings" class="btn go">Settings</a></li>
                        <li><a id="xxx" href="#test-footer">Test foolter</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10" id="admin-step-container">
                <div id="weekly_sessions_view" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Weekly Sessions View</h1>
                    </div>
                    <div class="modal-body">
                        @include('admin.settings.session_timing_view_mode')
                    </div>
                </div>
                <div id="weekly_sessions" class="modal-content admin-step">
                    <div class="modal-header">
                        <h1>Weekly Sessions</h1>
                    </div>
                    <div class="modal-body">
                        @include('admin.settings.session_timing_edit_mode')
                    </div>
                    <hr>
                    <div class="modal-footer">
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{--modal--}}
    <div class="modal fade" id="ajax-dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Searching for available time</h4>
                </div>
                <div class="modal-body center">
                    <div style="width: 184px; display: inline-block">
                        <img src="{{ url('images/gear.svg') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <hr>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    @include('debug.redux-state')
    @include('partial.toast')
@endsection

@push('script')
<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url_mix('js/admin-settings.js') }}"></script>
<script></script>
@endpush