@extends('layouts.admin')
@push('css')
    <link href="{{ url_mix('css/animate.css') }}" rel="stylesheet"/>
    <link href="{{ url('css/flatpickr_material_blue.css') }}" rel="stylesheet"/>
@endpush
@section('content')
    <div id='app'>
        {{--@include('admin.navigator')--}}
        <div class="row" style="margin: 0">
            <div>
                @verbatim
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="h1">Reservations</span>
                        <button class="btn bg-info pull-right" v-on:click="_refreshOutletData">
                            @endverbatim
                            <img src="{{ url('images/ring.svg') }}" height="25" v-show="auto_refresh_status == REFRESHING">Refresh
                            @verbatim
                        </button>
                    </div>
                    <!-- This div used to filterd reservations -->
                    <div class="modal-body">
                        <div style="width: 100%; height: 38px; display: flex; flex-direction: row">
                            <button class="btn btn-default"
                                v-on:click="_openNewReservationDialog()"
                            >New reservation</button>
                            <div style="display: flex; flex: 1"></div>
                            <span class="text-muted" style="font-size: 1.67em">Filter reservations</span>
                        </div>
                        <div style="width: 100%; text-align: right; margin-bottom: 20px; ">
                            <transition name="slide">
                                <div  v-if="filter_panel" class="btn-group">
                                    <button :class="(TODAY == filter_day? 'active' : '') + ' ' + 'btn btn-default'"
                                            v-on:click="_fetchReservationsByDay(TODAY)"       >Today</button>

                                    <button :class="(NEXT_3_HOURS == filter_day? 'active' : '') + ' ' +  'btn btn-default'"
                                            v-on:click="_fetchReservationsByDay(NEXT_3_HOURS)" >Next 3 hours</button>

                                    <button :class="(TOMORROW == filter_day? 'active' : '') + ' ' +  'btn btn-default'"
                                            v-on:click="_fetchReservationsByDay(TOMORROW)"    >Tomorrow</button>

                                    <button :class="(NEXT_3_DAYS == filter_day? 'active' : '') + ' ' +  'btn btn-default'"
                                            v-on:click="_fetchReservationsByDay(NEXT_3_DAYS)" >Next 3 days</button>

                                    <button :class="(NEXT_7_DAYS == filter_day? 'active' : '') + ' ' +  'btn btn-default'"
                                            v-on:click="_fetchReservationsByDay(NEXT_7_DAYS)" >Next 7 days</button>

                                    <button :class="(NEXT_30_DAYS == filter_day? 'active' : '') + ' ' +  'btn btn-default'"
                                            v-on:click="_fetchReservationsByDay(NEXT_30_DAYS)">Next 30 days</button>

                                    <button :class="(CUSTOM == filter_day ? 'active' : '') + ' ' + 'btn btn-default'"
                                            v-on:click="filter_date_picker = !filter_date_picker"
                                    >Pick a day</button>

                                    <!-- <button class="btn bg-info"
                                            v-on:click="_clearFilterByDay"
                                    ><i class="fa fa-times"></i>Clear</button> -->
                                </div>
                            </transition>
                        </div>

                        <div v-if="filter_date_picker & filter_panel" style="width: 100%; text-align: right; margin-bottom: 20px;">
                            <input id="flatpickr" class="flatpickr flatpickr-input" type="text" placeholder="Select Date.." data-id="inline" readonly="readonly"
                                   style="width: 135px; height: 30px; border-radius: 3px"
                                   v-model="custom_pick_day" v-on:change="_fetchReservationsByDay(CUSTOM, $event.target.value)">
                        </div>

                        <div  v-if="filter_panel"  style="width: 100%; text-align: right; margin-bottom: 20px;">
                            <div  v-if="filter_panel" class="btn-group">
                                <button :class="(filter_statuses.includes(RESERVATION_ARRIVED) ? 'active' : '') + ' ' +'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_ARRIVED, $event)"
                                >Arrived</button>

                                <button :class="(filter_statuses.includes(RESERVATION_CONFIRMATION) ? 'active' : '') + ' ' + 'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_CONFIRMATION, $event)"
                                >Confirmed</button>

                                <button :class="(filter_statuses.includes(RESERVATION_REMINDER_SENT) ? 'active' : '') + ' ' + 'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_REMINDER_SENT, $event)"
                                >Reminder Sent</button>

                                <button :class="(filter_statuses.includes(RESERVATION_RESERVED) ? 'active' : '') +  ' ' +'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_RESERVED, $event)"
                                >Reserved</button>

                                <button :class="(filter_statuses.includes(RESERVATION_USER_CANCELLED) ? 'active' : '') +  ' ' +'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_USER_CANCELLED, $event)"
                                >User cancelled</button>

                                <button :class="(filter_statuses.includes(RESERVATION_STAFF_CANCELLED) ? 'active' : '') + ' ' + 'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_STAFF_CANCELLED, $event)"
                                >Staff cancelled</button>

                                <button :class="(filter_statuses.includes(RESERVATION_NO_SHOW) ? 'active' : '') + ' ' + 'btn btn-default'"
                                        v-on:click="_toggleFilterStatus(RESERVATION_NO_SHOW, $event)"
                                >No show</button>

                                <button class="btn bg-info" v-on:click="_clearFilterByStatus"><i class="fa fa-times"></i>Clear</button>
                            </div>
                        </div>
                        <div style="width: 100%; text-align: right; /** margin-bottom: 20px; */">
                            <div class="btn-group">
                                <button class="btn" style="padding: 1px;">
                                    <input type="text" class="form-control" placeholder="CONFIRM ID, name, phone or email"
                                           style="height: 30px; width: 275px"
                                           v-model="filter_confirm_id"
                                           v-on:keyup.enter="_addFilterByConfirmId"
                                    >
                                </button>
                                <button class="btn bg-info"
                                    v-on:click="_addFilterByConfirmId"
                                ><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- This div used to SHOW filterd reservations -->
                    <div class="modal-body">
                        <div class="flexRow justifyContentCenter" style="padding-bottom: 15px;">
                            <button class="btn btn-default"
                                    v-on:click="_goToPrintPage"
                            >Print this list</button>
                        </div>
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
                                <th>Send Reminder SMS</th>
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
        <div class="modal fade" id="new-reservation-dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Reservation</h4>
                    </div>
                    <div class="modal-body">
                        @endverbatim
                        @include('admin.reservations.new-dialog')
                        @verbatim
                    </div>
                    <!-- Footer when normal -->
                    <div v-if="is_calling_ajax != AJAX_CREATE_NEW_RESERVATION"
                         class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                        <button class="btn bg-info"
                                v-on:click="_createNewReservation"
                        >Save</button>
                        <button class="btn bg-info"
                                v-on:click="_createNewReservation({sms_message_on_reserved: true})"
                        >Save & SMS Customer</button>
                    </div>
                    <!-- Footer when click on button waiting for server response -->
                    @endverbatim
                    <div  v-if="is_calling_ajax == AJAX_CREATE_NEW_RESERVATION"
                          class="modal-footer" style="border-top: 1px solid #e5e5e5;">
                        <button v-if="new_reservation.sms_message_on_reserved != true"
                                class="btn bg-info" disabled >
                            <img src="{{ url('images/ring.svg') }}" height="25" />
                            Saving
                        </button>
                        <button v-if="new_reservation.sms_message_on_reserved == true"
                                class="btn bg-info" disabled>
                            <img src="{{ url('images/ring.svg') }}" height="25" />
                            Saving & SMS Customer
                        </button>
                    </div>
                    @verbatim
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        @endverbatim
        @include('partial.toast')
    </div>
    @include('debug.redux-state')
@endsection

@push('script')
<script src="{{ url('js/flatpickr.min.js') }}"></script>
<script src="{{ url('js/hashids.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url_mix('js/admin-reservations.js') }}"></script>
@endpush