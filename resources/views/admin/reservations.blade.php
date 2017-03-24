@extends('layouts.admin')
@section('content')
    <div id='app'>
        @include('admin.navigator')
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @verbatim
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="h1">Reservations</span>
                        <button class="btn bg-info pull-right">Save</button>
                    </div>
                    <div class="modal-body">
                        <div style="box-shadow: rgba(0, 0, 0, 0.5) 0px 5px 15px;">
                            <table class="table table-hover table-condensed table-bordered">
                                <thead>
                                <tr class="bg-info">
                                    <th></th>
                                    <th>Customer Info</th>
                                    <th>Time</th>
                                    <th>Pax Size</th>
                                    <th>Table Name</th>
                                    <th>Customer Remarks</th>
                                    <th>Staff Remarks</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="(reservation, reservation_index) in reservations">
                                    <tr :class="reservation.staff_read_state ? '' : 'active'"
                                        :id="'reservation_' + reservation.id"
                                        :reservation-index="reservation_index"
                                        v-on:click="_reservationDetailDialog"
                                    >
                                        <td>{{ reservation_index + 1 }}</td>
                                        <td>
                                            <p style="margin: 0">{{ reservation.salutation }} {{ reservation.first_name }} {{ reservation.last_name }}</p>
                                            <p style="margin: 0">{{ reservation.full_phone_number }}</p>
                                            <p style="margin: 0">{{ reservation.email }}</p>
                                        </td>
                                        <td style="width: 150px">{{ reservation.reservation_timestamp }}</td>
                                        <td style="width: 100px">{{ reservation.adult_pax }}
                                            +{{ reservation.children_pax }}</td>
                                        <td>
                                            <input
                                                    type="text" style="width: 100px"
                                                    v-model="reservation.table_name"
                                                    :value="reservation.table_name"
                                            >
                                        </td>
                                        <td>
                                    <textarea
                                            rows="2" col="20" style="height: 50px"
                                            v-model="reservation.customer_remarks"
                                            :value="reservation.customer_remarks"
                                            placeholder="Customer Remarks"
                                    ></textarea>
                                        </td>
                                        <td>
                                    <textarea
                                            rows="2" col="20" style="height: 50px"
                                            v-model="reservation.staff_remarks"
                                            :value="reservation.staff_remarks"
                                            placeholder="Staff Remarks"
                                    ></textarea>
                                        </td>
                                        <td>
                                            <select v-model="reservation.status">
                                                <option value="300" class="bg-success">Confirmation</option>
                                                <option value="200" class="bg-info">Reminder Sent</option>
                                                <option value="100" class="bg-info">Reserved</option>
                                                <option value="-100" class="bg-info">User cancelled</option>
                                                <option value="-200" class="bg-warning">Staff cancelled</option>
                                                <option value="-300" class="bg-danger">No show</option>
                                            </select>
                                        </td>
                                    </tr>
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
                        <button class="btn bg-info">Save</button>
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