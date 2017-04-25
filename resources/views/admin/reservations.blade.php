@extends('layouts.admin')
@push('css')
    <link href="{{ url_mix('css/animate.css') }}" rel="stylesheet"/>
@endpush
@section('content')
    <div id='app'>
        {{--@include('admin.navigator')--}}
        <div class="row">
            <div class="col-md-11 col-md-offset-1">
                @verbatim
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="h1">Reservations</span>
                        <button class="btn bg-info pull-right"
                            v-on:click="_updateReservations"
                        >Save</button>
                    </div>
                    <!-- This div used to filterd reservations -->
                    <div class="modal-body">
                        <div style="box-shadow: rgba(0, 0, 0, 0.5) 0px 5px 15px;">
                            <div style="width: 100%; height: 38px; text-align: right">
                                <span class="h3 text-muted">Filter reservations</span>
                                <span  class="fa fa-filter btn bg-info"
                                       v-on:click="filtered = !filtered"
                                ></span>
                            </div>
                            <div style="width: 100%; text-align: right; margin-bottom: 20px; ">
                                <transition name="slide">
                                    <div  v-if="filtered" class="btn-group">
                                        <button class="btn btn-sm btn-default" v-on:click="_filter(TODAY)"       >Today</button>
                                        <button class="btn btn-sm btn-default" v-on:click="_filter(TOMORROW)"    >Tomorrow</button>
                                        <button class="btn btn-sm btn-default" v-on:click="_filter(NEXT_3_DAYS)" >Next 3 days</button>
                                        <button class="btn btn-sm btn-default" v-on:click="_filter(NEXT_7_DAYS)" >Next 7 days</button>
                                        <button class="btn btn-sm btn-default" v-on:click="_filter(NEXT_30_DAYS)">Next 30 days</button>
                                        <button class="btn btn-sm btn-default" v-on:click="filter_date_picker = !filter_date_picker">Pick a day</button>
                                        <button class="btn btn-sm btn-default" v-on:click="_clearSearch"       ><span class="fa fa-times"></span>Clear</button>
                                    </div>
                                </transition>
                            </div>
                            <transition name="slide">
                                <div v-if="filter_date_picker & filtered" style="width: 100%; text-align: right; margin-bottom: 20px;">
                                    <input type="date" style="width: 135px; height: 30px; border-radius: 3px"
                                           v-model="custom_pick_day" v-on:change="_filter(CUSTOM)">
                                </div>
                            </transition>
                            <transition name="slide">
                                <table  v-if="filtered" class="table table-hover table-condensed table-bordered">
                                    <thead>
                                    <tr class="bg-info">
                                        <th></th>
                                        <th>Read</th>
                                        <th>No.</th>
                                        <th>Customer Info</th>
                                        <th>Time</th>
                                        <th>Pax Size</th>
                                        <th>Table Name</th>
                                        <th>Customer Remarks</th>
                                        <th>Staff Remarks</th>
                                        <th>Status</th>
                                        <th>Payment Authorization</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(reservation, reservation_index) in filtered_reservations">
                                        @endverbatim
                                        @include('admin.reservations.single-row-info')
                                        @verbatim
                                    </template>
                                    </tbody>
                                </table>
                            </transition>
                        </div>
                    </div>

                    <div v-if="!filtered" class="modal-body">
                        <div style="box-shadow: rgba(0, 0, 0, 0.5) 0px 5px 15px;">
                            <table class="table table-hover table-condensed table-bordered">
                                <thead>
                                <tr class="bg-info">
                                    <th></th>
                                    <th>Read</th>
                                    <th>No.</th>
                                    <th>Customer Info</th>
                                    <th>Time</th>
                                    <th>Pax Size</th>
                                    <th>Table Name</th>
                                    <th>Customer Remarks</th>
                                    <th>Staff Remarks</th>
                                    <th>Status</th>
                                    <th>Payment Authorization</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="(reservation, reservation_index) in reservations">
                                    @endverbatim
                                    @include('admin.reservations.single-row-info')
                                    @verbatim
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="reservation-dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Reservation {{ reservation_dialog_content.confirm_id }}</h4>
                    </div>
                    <div class="modal-body">
                        @endverbatim
                        @include('admin.reservations.detail-dialog')
                        @verbatim
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                        <button
                                class="btn bg-info"
                                v-on:click="_updateSingleReservation"
                        >Save</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        @endverbatim
        @include('partial.toast')
    </div>
    @include('debug.redux-state')
@endsection

@push('script')

<script src="{{ url('js/vue.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url_mix('js/admin-reservations.js') }}"></script>
@endpush