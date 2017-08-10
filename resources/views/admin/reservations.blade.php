@extends('layouts.admin')
@push('css')
    <link href="{{ url_mix('css/animate.css') }}" rel="stylesheet"/>
    <link href="{{ url('css/flatpickr_material_blue.css') }}" rel="stylesheet"/>
    <link href="{{ url('css/jquery.timepicker.min.css') }}" rel="stylesheet"/>
    <link href="{{ url_mix('css/flex.css') }}" rel="stylesheet"/>
    <link href="{{ url_mix('css/admin-reservation.css') }}" rel="stylesheet"/>
    @include('icon')
@endpush
@section('content')
    <div id='app'>
        <div class="row" style="margin: 0">
            <div>
                @verbatim
                <div class="modal-header">
                    <div class="flexRow">
                        <button class="btn btn-default " v-on:click="_refreshOutletData" title="Refresh">
                            @endverbatim <img src="{{ url('images/ring.svg') }}" height="25" v-show="auto_refresh_status == REFRESHING"> @verbatim
                            <span class="glyphicon refreshIcon" v-show="auto_refresh_status != REFRESHING"></span>
                        </button>
                        <button class="btn btn-default marginLeft20" title="Print Page"
                                v-on:click="_goToPrintPage"
                        ><span class="glyphicon printIcon"></span></button>
                        <button class="btn btn-default marginLeft20" title="New reservation"
                                v-on:click="_openNewReservationDialog()"
                        ><span class="glyphicon addIcon"></span></button>
                        <button class="btn btn-default marginLeft20" title="Open Filter"
                                v-on:click="filter_panel = !filter_panel; filter_date_picker = false"
                        ><span class="glyphicon filterIcon"></span></button>
                        <button class="btn btn-danger marginLeft40" title="Close slot"
                                v-on:click="close_slot = !close_slot"
                        ><span class="glyphicon closeSlotIcon"></span></button>
                        <div class="flex1"></div>
                        <div style="text-align: right;">
                            <div class="flexRow height35">
                                <input type="text" class="form-control" placeholder="CONFIRM ID, name, phone or email"
                                       style="width: 275px"
                                       v-model="filter_confirm_id"
                                       v-on:keyup.enter="_addFilterByConfirmId"
                                >
                                <button class="btn bg-info marginLeftMinus2"
                                        v-on:click="_addFilterByConfirmId"
                                ><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- This div used to filterd reservations -->
                <transition name="slide">
                    <div v-if="filter_panel">
                        <div class="flexRow">
                            <div class="flex1"></div>
                            <div  class="btn-group">
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
                                >Pick a date range</button>
                            </div>
                        </div>



                        <div v-if="filter_date_picker" class="flexRow marginTop20">
                            <div class="flex1"></div>
                            <input id="flatpickr" class="flatpickr flatpickr-input" type="text" placeholder="Select Date.." data-id="rangeDisable" readonly="readonly"
                                   style="height: 30px; border-radius: 3px"
                                   v-on:change="_fetchReservationsByRangeDay(CUSTOM, $event.target.value)"/>
                        </div>

                        <div class="flexRow marginTop20 marginBottom20">
                            <div class="flex1"></div>
                            <div  class="btn-group">
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
                    </div>
                </transition>

                <transition name="slide">
                    <div v-if="close_slot">
                        <div class="flexRow">
                            <div class="flex1"></div>
                            <div style="width: 400px;">
                                <div class="divBorder divPadding">
                                    <div class="hoiH3">Disallow any new reservation on</div>
                                    <div class="flexRow">
                                        <label class="flexRow flexStart" style="font-weight: normal">
                                            <input id="special_session_date" type="text" placeholder="Pick date" style="height: 36px;" class="hoiInputBorder"
                                                   v-on:change="_updateSpecialSessionDate($event.target.value)"
                                            />
                                            <div class="calendarIcon" style="margin-left: -36px"></div>
                                        </label>

                                    </div>
                                    <div class="flexRow">
                                        <div class="hoiH5">From </div>
                                        <input class="jonthornton-time hoiInputBorder timingTime"
                                               id="timing_start" type="text" onfocus="blur();"
                                               v-on:$change="_updateTimingTime('first_arrival_time', $event)"/>
                                        <div class="hoiH5" style="margin-left: 10px;">to</div>
                                        <input class="jonthornton-time hoiInputBorder timingTime"
                                               id="timing_end" type="text" onfocus="blur();"
                                               v-on:$change="_updateTimingTime('last_arrival_time', $event)"
                                        />
                                    </div>
                                    <br/>
                                    <button class="btn btn-default btn-block"
                                        v-on:click="_createSpecialSession"
                                    >Confirm</button>
                                    <p class="small">To undo this, please access from Settings > Special Sessions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- This div used to SHOW filterd reservations -->
                <table class="table table-hover table-condensed table-bordered">
                    <thead>
                    <tr class="bg-info">
                        <th></th>
                        <th></th>
                        <th>No.</th>
                        <th>Customer Info</th>
                        <th>Customer Remarks</th>
                        <th>Staff Remarks</th>
                        <th>Status</th>
                        <th>Payment Authorization</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(reservations, key) in filtered_reservations_by_date">
                      <tr>
                        <td colspan="8">
                          <div  style="display: flex; background-color: #BDBDBD; font-weight: bold">
                            <div style="flex: 1">{{moment(key).format('DD MMM YYYY')}}</div>
                            <div style="flex: 1">Total Reservations: {{reservations.length}}</div>
                            <div style="flex: 1">Total Pax: {{_totalPaxInReservations(reservations)}}</div>
                          </div>
                        </td>
                      </tr>
                      <template v-for="(reservation, reservation_index) in reservations">
                        @endverbatim
                        @include('admin.reservations.single-row-info')
                        @verbatim
                      </template>
                    </template>
                    </tbody>
                </table>
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
                        <div style="display: flex">
                            <div class="text-muted">Created At: {{ moment(reservation_dialog_content.created_timestamp).format('DD/MM/YYYY HH:mm:ss') }}</div>
                            <div style="flex: 1"></div>
                            <button
                              class="btn bg-info"
                              v-on:click="_updateSingleReservation"
                            >Save</button>
                        </div>

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
                                v-if="!new_reservation.payment_required"
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
@endsection

@push('script')
<script src="{{ url('js/flatpickr.min.js') }}"></script>
<script src="{{ url('js/hashids.min.js') }}"></script>
<script src="{{ url('js/jquery.timepicker.min.js') }}"></script>
<script>@php
        $state_json = json_encode($state);
        echo "window.state = $state_json;";
    @endphp</script>
<script src="{{ url_mix('js/admin-reservations.js') }}"></script>
@endpush