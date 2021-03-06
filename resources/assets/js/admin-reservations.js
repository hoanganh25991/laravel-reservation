const INIT_VIEW = 'INIT_VIEW';

const SHOW_RESERVATION_DIALOG_CONTENT = 'SHOW_RESERVATION_DIALOG_CONTENT';
const HIDE_RESERVATION_DIALOG_CONTENT = 'HIDE_RESERVATION_DIALOG_CONTENT';
const UPDATE_SINGLE_RESERVATION         = 'UPDATE_SINGLE_RESERVATION';
const UPDATE_RESERVATIONS = 'UPDATE_RESERVATIONS';

const SYNC_DATA              = 'SYNC_DATA';
const REFETCHING_DATA        = 'REFETCHING_DATA';
// const SYNC_DATA = 'SYNC_DATA';

const TOAST_SHOW = 'TOAST_SHOW';

const REFETCHING_DATA_SUCCESS     = 'REFETCHING_DATA_SUCCESS';
const SEND_SMS_REMINDER_ON_RESERVATION = 'SEND_SMS_REMINDER_ON_RESERVATION'
// AJAX ACTION
const AJAX_UPDATE_RESERVATIONS    = 'AJAX_UPDATE_RESERVATIONS';

const AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';
const AJAX_REFETCHING_DATA        = 'AJAX_REFETCHING_DATA';

//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_SESSIONS_SUCCESS   = 'AJAX_UPDATE_SESSIONS_SUCCESS';
const AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

const AJAX_SUCCESS  = 'AJAX_SUCCESS';
const AJAX_ERROR    = 'AJAX_ERROR';
const AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';
const AJAX_REFETCHING_DATA_SUCCESS = 'AJAX_REFETCHING_DATA_SUCCESS';
const AJAX_SEND_REMINDER_SMS_ON_RESERVATION = 'AJAX_SEND_REMINDER_SMS_ON_RESERVATION';
const AJAX_SEND_REMINDER_SMS_ON_RESERVATION_SUCCESS = 'AJAX_SEND_REMINDER_SMS_ON_RESERVATION_SUCCESS';
const AJAX_SEND_REMINDER_SMS_ON_RESERVATION_FAIL = 'AJAX_SEND_REMINDER_SMS_ON_RESERVATION_FAIL';

//Paypal
const PAYMENT_REFUNDED = 50;
const PAYMENT_PAID     = 100;
const PAYMENT_CHARGED  = 200;

const TODAY        = 'TODAY';
const TOMORROW     = 'TOMORROW';
const NEXT_3_HOURS = 'NEXT_3_HOURS';
const NEXT_3_DAYS  = 'NEXT_3_DAYS';
const NEXT_7_DAYS  = 'NEXT_7_DAYS';
const NEXT_30_DAYS = 'NEXT_30_DAYS';
const CUSTOM       = 'CUSTOM';
const CLEAR        = 'CLEAR';

const MODE_EXACTLY = 'MODE_EXACTLY';
const MODE_BETWEEN = 'MODE_BETWEEN';
const SYNC_VUE_STATE = 'SYNC_VUE_STATE';

const RESERVATION_NO_SHOW          = -300;
const RESERVATION_STAFF_CANCELLED  = -200;
const RESERVATION_USER_CANCELLED   = -100;
const RESERVATION_REQUIRED_PAYMENT = 50;
const RESERVATION_AMENDMENTED      = 75;
const RESERVATION_RESERVED         = 100;
const RESERVATION_REMINDER_SENT    = 200;
const RESERVATION_CONFIRMATION     = 300;
const RESERVATION_ARRIVED          = 400;

const FILTER_TYPE_DAY        =  'FILTER_TYPE_DAY';
const FILTER_TYPE_STATUS     = 'FILTER_TYPE_STATUS';
const FILTER_TYPE_CONFIRM_ID = 'FILTER_TYPE_CONFIRM_ID';

const REFRESH = 'REFRESH';
const REFRESHING = 'REFRESHING';

const CALLING_AJAX = 'CALLING_AJAX';

const FETCH_RESERVATIONS_BY_DAY = 'FETCH_RESERVATIONS_BY_DAY';
const FETCH_RESERVATIONS_BY_RANGE_DAY = 'FETCH_RESERVATIONS_BY_RANGE_DAY';
const FETCH_RESERVATIONS_BY_CONFIRM_ID = 'FETCH_RESERVATIONS_BY_CONFIRM_ID';
const AJAX_FETCH_RESERVATIONS_BY_DAY = 'AJAX_FETCH_RESERVATIONS_BY_DAY';
const AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY = 'AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY';
const AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS = 'AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS';
const AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY_SUCCESS = 'AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY_SUCCESS';
const AJAX_FIND_RESERVATION = 'AJAX_FIND_RESERVATION'
const AJAX_FIND_RESERVATION_SUCCESS = 'AJAX_FIND_RESERVATION_SUCCESS'

const OPEN_NEW_RESERVATION_DIALOG = 'OPEN_NEW_RESERVATION_DIALOG';

const SELF_DISPATCH_THUNK = 'SELF_DISPATCH_THUNK';

const AJAX_SEARCH_AVAILABLE_TIME = 'AJAX_SEARCH_AVAILABLE_TIME';
const AJAX_AVAILABLE_TIME_FOUND  = 'AJAX_AVAILABLE_TIME_FOUND';
const UPDATE_AVAILABLE_TIME      = 'UPDATE_AVAILABLE_TIME';
const CHANGE_NEW_RESERVATION_TIME= 'CHANGE_NEW_RESERVATION_TIME';
const CREATE_NEW_RESERVATION     = 'CREATE_NEW_RESERVATION';
const AJAX_CREATE_NEW_RESERVATION= 'AJAX_CREATE_NEW_RESERVATION';
const AJAX_RESERVATION_SUCCESS_CREATE = 'AJAX_RESERVATION_SUCCESS_CREATE';
const CLOSE_NEW_RESERVATION_DIALOG = 'CLOSE_NEW_RESERVATION_DIALOG';

const AJAX_RESERVATION_REQUIRED_DEPOSIT = 'AJAX_RESERVATION_REQUIRED_DEPOSIT';
const UPDATE_NEW_RESERVATION = 'UPDATE_NEW_RESERVATION';

const CREATE_CLOSE_SLOT = 'CREATE_CLOSE_SLOT';
const EMPTY_SPECIAL_SESSION = 'EMPTY_SPECIAL_SESSION';

const AJAX_CREATE_CLOSE_SLOT = 'AJAX_CREATE_CLOSE_SLOT';
const AJAX_CREATE_CLOSE_SLOT_SUCCESS = 'AJAX_CREATE_CLOSE_SLOT_SUCCESS';
const HIDE_CLOSE_SLOT_EMPTY_SPECIAL_SESSIOn = 'HIDE_CLOSE_SLOT_EMPTY_SPECIAL_SESSIOn';
const UPDATE_LAST_REFRESH_BCS_EXCEPTION = "UPDATE_LAST_REFRESH_BCS_EXCEPTION";
const RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS = "RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS";

const AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS = "AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS";
const AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS_SUCCESS = "AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS_SUCCESS";

const FIND_CUSTOMER_SAME_PHONE = "FIND_CUSTOMER_SAME_PHONE";
const AJAX_FIND_CUSTOMER_SAME_PHONE = "AJAX_FIND_CUSTOMER_SAME_PHONE";
const AJAX_FIND_CUSTOMER_SAME_PHONE_SUCCESS = "AJAX_FIND_CUSTOMER_SAME_PHONE_SUCCESS";
const AJAX_FIND_CUSTOMER_SAME_PHONE_NOT_FOUND = "AJAX_FIND_CUSTOMER_SAME_PHONE_NOT_FOUND";
const AJAX_FIND_CUSTOMER_SAME_PHONE_FAIL = "AJAX_FIND_CUSTOMER_SAME_PHONE_FAIL";



class AdminReservations {
	/** @namespace res.errorMsg */
	/**
	 * @namespace Redux
	 * @namespace moment
	 * @namespace $
	 */
	constructor(){
		this.buildRedux();
		this.buildVue();
		//Hack into these core concept, to get log
		//this.hack_store();
		this.hack_ajax();
	}

	buildRedux(){
		let self = this;
		let default_state = this.defaultState();
		let rootReducer = function(state = default_state, action){
			switch(action.type){
				case INIT_VIEW:
					return Object.assign({}, state, {init_view: true});
				case SHOW_RESERVATION_DIALOG_CONTENT:
				case HIDE_RESERVATION_DIALOG_CONTENT:
					return Object.assign({}, state, {
						reservation_dialog_content: self.reservationDialogContentReducer(state.reservation_dialog_content, action)
					});
				case TOAST_SHOW:
					return Object.assign({}, state, {
						toast: action.toast
					});
				case SYNC_DATA:{
					let new_state = Object.assign({}, state, action.data);

					Object.assign(new_state, {auto_refresh_status: REFRESH});

					return new_state;
				}
				case SYNC_VUE_STATE :{
					return Object.assign({}, state, action.vue_state);
					break;
				}
				case REFETCHING_DATA: {
					return Object.assign({}, state, {auto_refresh_status: REFRESHING});
					break;
				}
				case CALLING_AJAX: {
					let {is_calling_ajax} = action;
					return Object.assign({}, state, {is_calling_ajax});
				}
				case FETCH_RESERVATIONS_BY_DAY:
				case FETCH_RESERVATIONS_BY_RANGE_DAY:{
					let {day: filter_day, day_str: custom_pick_day} = action;

					return Object.assign({}, state, {filter_day, custom_pick_day});
				}
				case FETCH_RESERVATIONS_BY_CONFIRM_ID:{
					let {confirm_id: filter_confirm_id} = action;
					return Object.assign({}, state, {filter_confirm_id});
				}
				case OPEN_NEW_RESERVATION_DIALOG: {
					let new_reservation = self.newReservation();

					return Object.assign({}, state, {new_reservation});
				}
				case CLOSE_NEW_RESERVATION_DIALOG: {
					return state;
				}
				case UPDATE_AVAILABLE_TIME: {
					// This is available_time for whole range of date-range
					let {available_time: whole_range_time} = action;
					// Only get what we need
					let {new_reservation: current_reservation} = state;
					// Consider as default empty array if no thing available
					let {date_str} = current_reservation;
					// Get him out
					let available_time = whole_range_time[date_str] ? whole_range_time[date_str] : [];
					if(available_time.length == 0){
						window.alert('No available time found on your booking conditions');
					}
					// Build new reservation
					let new_reservation = Object.assign({}, current_reservation, {available_time});
					
					return Object.assign({}, state, {new_reservation});
				}
				case CHANGE_NEW_RESERVATION_TIME: {
					let {time_str} = action;

					let {new_reservation: current_reservation} = state;

					let new_reservation = Object.assign({}, current_reservation, {time_str});

					return Object.assign({}, state, {new_reservation});
				}
				case CREATE_NEW_RESERVATION: {
					let {new_reservation: current_reservation} = state;

					let {date_str, time_str} = current_reservation;
					let date_timestamp       = `${date_str} ${time_str}`;
					// Format as YYYY-MM-DD HH:mm
					let date = moment(date_timestamp, 'YYYY-MM-DD HH:mm');
					// Submit reservation_timestamp as str
					let reservation_timestamp = date.format('YYYY-MM-DD HH:mm:ss');

					let new_reservation = Object.assign({}, current_reservation, {reservation_timestamp});

					// Update what sent from action
					let {new_reservation: updated_info} = action;
					Object.assign(new_reservation, updated_info);
					// Test success sent through action
					console.log(new_reservation.sms_message_on_reserved);

					return Object.assign({}, state, {new_reservation});
				}
				case UPDATE_NEW_RESERVATION:{
					let {new_reservation: curr_new_reservation} = state;
					
					let new_reservation = Object.assign({}, curr_new_reservation, action.new_reservation);
					
					return Object.assign({}, state, {new_reservation});
				}
        case SEND_SMS_REMINDER_ON_RESERVATION:{
          let {confirm_id, outlet_id} = action;
          let send_sms_on_reservation = {confirm_id, outlet_id};

          return Object.assign({}, state, {send_sms_on_reservation});
        }
				case HIDE_CLOSE_SLOT_EMPTY_SPECIAL_SESSIOn:{
					let close_slot = false;
					let special_session = {};

					return Object.assign({}, state, {close_slot, special_session});
				}
        case UPDATE_LAST_REFRESH_BCS_EXCEPTION: {
          let {time} = action;
          let lastRefreshBcsException = time;
          return Object.assign({}, state, {lastRefreshBcsException});
        }
        case RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS: {
          let {confirm_id: resend_reservation_confirm_id} = action;
          return Object.assign({}, state, {resend_reservation_confirm_id});
        }
				default:
					return state;
			}
		}

		window.store = Redux.createStore(rootReducer);

		/**
		 * Helper function
		 */
		let store      = window.store;
		let o_dispatch = store.dispatch;

		store.dispatch = function(action){
			//action.type = action.type ? action.type : SELF_DISPATCH_THUNK;
			console.info(action.type ? action.type : SELF_DISPATCH_THUNK);

			if(typeof action == 'function'){
				// Bring dispatch & getState into action
				// as thunk
				action(store.dispatch, store.getState);
			}

			store.prestate = store.getState();
			store.last_action = action.type;
			return o_dispatch(action);
		}

		store.getPrestate = function(){
			return store.prestate;
		}

		store.getLastAction = function(){
			return store.last_action;
		}
	}

	defaultState(){
		let default_state  = window.state || {};

		//let frontend_state = this.getFrontEndState();
		let frontend_state = this.frontEndState();

		return Object.assign({}, frontend_state, default_state);
	}

	frontEndState(){
		return {
			init_view: false,
			base_url: null,
			outlet_id: null,
			outlet: {},
			user: {},
			reservation_dialog_content: {},
			new_reservation: {},
			toast: {
				title: 'Title',
				content: 'Content'
			},
			reservations: [],
			// allow create special session
			// as meaning for close slot
			close_slot: false,
			// Decide show|hide
			filter_panel: false,
			// Manage filterd on reservations
			filtered_reservations: [],
			filtered_reservations_by_date: [],
			// contains all filters
			filter_options: [],
			// manage filter date picker
			filter_date_picker: null,
			// store which day pick by staff
			custom_pick_day: null,
			// support multilple status
			filter_statuses: [50, 75, 100, 200, 300],
			// store which type of filter by day
			filter_day: null,
			// allow search by confirm_id
			filter_confirm_id: null,
			// store search confirm_id search state
			filter_search: null,
			// auto refresh
			auto_refresh_status: null,
      lastRefreshBcsException: 0,
			is_calling_ajax: null,
      // Store the outlet_id, confirm_id
      // When ask for send, send it to server
      // Ask for manual send reminder SMS
      send_sms_on_reservation: {},
			// Handle quick create special session
			special_session: {},
      reservationLastStatus: 0,
      // Store which one need resend
      resend_reservation_confirm_id: null,
      // Store find customer when create new
		};
	}

	newReservation(){
		let store = window.store;
		let {outlet_id} = store.getState();

		let new_reservation = {
			outlet_id,
			salutation:"Mr.",
			first_name:null,
			last_name: null,
			email: null,
			phone_country_code: '+65',
			phone: null,
			status: null,
			adult_pax: 0,
			children_pax: 0,
			reservation_timestamp: null,
			customer_remarks: null,
			is_outdoor:null,
			send_sms_confirmation:true,
			send_email_confirmation:null,
			table_layout_id:null,
			table_layout_name:null,
			table_name:null,
			staff_read_state:null,
			staff_remarks:null,
			payment_required:null,
			payment_authorization_id: null,
			payment_amount: null,
			payment_currency: null,
			payment_status: null,
			payment_timestamp: null,
			created_timestamp: null,
			modified_timestamp: null,
			confirm_id: null,
			send_confirmation_by_timestamp: null,
			// This branch store decision from admin
			// In payment authorization case, ask customer to pay or not
			//payment_required: null,
			deposit: null,
			time: null,
			paypal_currency: null,
			date: null,
			date_str: null,
			showing_date_str: null,
			time_str: null,
			// Show available_time for staff pick
			available_time: [],
			sms_message_on_reserved: null,
		}

		let date     =  moment();
		let date_str = date.format('YYYY-MM-DD');
		// let time_str = date.format('HH:mm');

		Object.assign(new_reservation, {
			reservation_timestamp: date,
			date_str,
			// time_str,
		});

		if(state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')){
			Object.assign(new_reservation, {
				// salutation: 'Mr.',
				// first_name: 'Anh',
				// last_name : 'Le Hoang',
				// email: 'lehoanganh25991@gmail.com',
				// phone_country_code: '+84',
				// phone: '903865657',
				// customer_remarks: 'hello world'
			});
		}

		return new_reservation;
	}

	buildVue(){
		window.vue_state = this.frontEndState();

		let self  = this;

		// Long time before
		let onBuildVue = moment();

		this.vue  = new Vue({
			/** @namespace moment */
			el: '#app',
			data: window.vue_state,
			mounted(){
				document.dispatchEvent(new CustomEvent('vue-mounted'));
				self.event();
				self.view();
				self.listener();
				let store = window.store;
				// Init view
				store.dispatch({type: INIT_VIEW});

				// Start auto refresh interval
				this.startIntervalAutoRefresh();

        // Set default filter as except [user cancelled, staff cancelled, arrived, no-show]
        this._addFilterByStatus(...this.filter_statuses);
			},
			beforeUpdate(){
				let store = window.store;

				store.dispatch({
					type: SYNC_VUE_STATE,
					vue_state: window.vue_state
				});
			},
			updated(){
				let {is_flatpickr_mounted: lastState} = this;
				let is_flatpickr_mounted = document.getElementById('flatpickr');
				if(!lastState && is_flatpickr_mounted){
					let dp = flatpickr('#flatpickr', {mode: "range"});
					dp.open();
				}
				this.is_flatpickr_mounted = is_flatpickr_mounted;


				let sp = flatpickr('#special_session_date');
				let stp = flatpickr('#special_session_time', {
					enableTime: true,
					noCalendar: true,
					enableSeconds: false, // disabled by default
					time_24hr: true, // AM/PM time picker is used by default
					// default format
					dateFormat: "H:i",
					// initial values for time. don't use these to preload a date
					defaultHour: +onBuildVue.format('H'),
					defaultMinute: 0,
					minuteIncrement: 15,
				});
				// Timepicker with jQuery
				$('.jonthornton-time').timepicker({
					step: 30,
					disableTextInput: true,
          scrollDefault: 'now',
          useSelect: true,
				})
        .on('change', function(e){
          let $i = $(this);
          let i  = $i[0];
          let value = $i.val();
          i.dispatchEvent(new CustomEvent('$change', {bubbles: false, detail: {value}}));
        });
			},
			computed:{
        updateFilteredReservationsByDate() {
          // it's only required to reference those properties
          this.reservations;
          this.filter_options;
          // and then return a different value every time
          return new Date(); // or performance.now()
        }
			},
			watch: {
				// Need modify reservations with moment date obj
				// To run compare date easily
				// Any time see reservations changed
				// Ok assign date obj to him
				reservations(reservations){
					// Assign date
					reservations.forEach(reservation => {
						let timestamp = reservation.reservation_timestamp;
						let date      = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
						// Assign date
						reservation.date = date;
					});
				},
        updateFilteredReservationsByDate(){
          let reservations   = this.reservations;

          // console.log(reservations);
          let filter_options = this.filter_options;
          // Loop through each filter_options, run on current reservations
          let filtered_reservations =
            filter_options.reduce((carry, filter) => {
              // aplly current filter
              let _f_reservations = carry.filter(filter);
              // return result for next row call filter on
              return _f_reservations;
            }, reservations);

          let filtered_reservations_by_date = filtered_reservations.reduce((carry, reservation) => {
            let key = reservation.date.format('YYYY-MM-DD');
            carry[key] = carry[key] ? [...carry[key], reservation] : [reservation];

            return carry;
          }, {});

          // console.log(filtered_reservations_by_date);

          this.filtered_reservations_by_date = filtered_reservations_by_date;
        },
				outlet_id(outlet_id){
					let data = {outlet_id};
					document.dispatchEvent(new CustomEvent('outlet_id', {detail: data}));
				}
			},
			methods: {
				_reservationDetailDialog(e, reservation){
					try{
            // Prevent show dialog if click on row
						let tr = this._findTrElement(e);
            if(tr == null){
              return;
            }

            // Prevent show dialog if this reservation
            // No longer allowed to edit
            let {status} = reservation;
            let shouldShow =  this._shouldShowDetailDialog(status);
            if(!shouldShow){
              return;
            }

						//Clone it into reservation dialog content
						let picked_reservation = reservation;
						let dialog_reservation = Object.assign({}, picked_reservation);
						//Diloag need data for other stuff
						//Self update for itself
						let date = moment(dialog_reservation.reservation_timestamp, 'Y-M-D H:m:s');
						dialog_reservation.date_str = date.format('YYYY-MM-DD');
						dialog_reservation.time_str = date.format('HH:mm');

						//Update these info into vue
						Object.assign(window.vue_state, {reservation_dialog_content: dialog_reservation});

						store.dispatch({
							type: SHOW_RESERVATION_DIALOG_CONTENT,
							reservation_dialog_content: dialog_reservation
						});
					}catch(e){}
				},

				_remarksAsStaffRead(tr){
					// try{
					// 	let reservation_index  = tr.getAttribute('reservation-index');
					// 	let picked_reservation = this.reservations[reservation_index];
					// 	//Update reservations staff_read
					// 	picked_reservation.staff_read_state = true;
					// }catch(e){}
          return;
				},

				_findTrElement(e){
					let tr = e.target;

					let path = [tr].concat(e.path);

					let i = 0;
					let found_tr = null;
					let is_click_on_edit_form = false;

					while(i < path.length && !found_tr){
						let tr = path[i];

						/**
						 * Click on input / select to edit info
						 */
						if(!is_click_on_edit_form){
							//try does it click on edit form
							is_click_on_edit_form =
								tr.tagName == 'INPUT'
								|| tr.tagName == 'TEXTAREA'
								|| tr.tagName == 'SELECT'
								|| tr.tagName == 'BUTTON';
						}

						if(tr.tagName == 'TR'){
							found_tr = tr;
						}

						i++;
					}

					if(found_tr){
						//click on edit form, consider as already read it
						//has take action
						if(is_click_on_edit_form){
							this._remarksAsStaffRead(found_tr);
							return null;
						}
					}

					return found_tr;
				},

				_updateSingleReservation(){
					let reservation_dialog_content = this.reservation_dialog_content;
					//Recalculate reservation timestamp from select data
					reservation_dialog_content.reservation_timestamp = `${reservation_dialog_content.date_str} ${reservation_dialog_content.time_str}:00`;

					let reservations = this.reservations;

					/**
					 * Find which reservation need update info
					 * Base on reservation dialog content
					 */
					let i = 0, found = false;
					while(i < reservations.length && !found){
						if(reservations[i].id == reservation_dialog_content.id){
							found = true;
						}

						i++;
					}

					/**
					 * Get him out
					 */
					let need_update_reservation = reservations[i-1];

					/**
					 * Only assign on reservation key
					 * Not all what come from reservation_dialog_content
					 */
					Object
						.keys(need_update_reservation)
						.forEach(key => {
							need_update_reservation[key] = reservation_dialog_content[key];
						});

					// Mark as read
					Object.assign(need_update_reservation, {staff_read_state: 1});

					store.dispatch({
						type: HIDE_RESERVATION_DIALOG_CONTENT
					});

					this._updateReservations();
				},

				_updateReservations(){
					let reservations = this.reservations;
					let action = {
						type: AJAX_UPDATE_RESERVATIONS,
						reservations
					}

					self.ajax_call(action);
				},

				_updateReservationPayment(e, which_payment, reservation){
					//console.log(e);
					let button = e.target;
					if(button.tagName == 'BUTTON'){
						try{
							//let action = button.getAttribute('action');
							let picked_reservation = reservation;

							let payment_status;
							let action;

							switch(which_payment){
								default:
									//payment_status = PAYMENT_PAID;
									break;
								case PAYMENT_REFUNDED:
									payment_status = PAYMENT_REFUNDED;
									action = 'void';
									break;
								case PAYMENT_CHARGED:
									payment_status = PAYMENT_CHARGED;
									action = 'charge';
									break;
							}

							// Admin may touch to this button by accident
							// Last check before execute
							let {payment_amount, payment_currency} = picked_reservation;
							let confirmed = window.confirm(`Are you sure you want to ${action} the authorization of ${payment_amount} ${payment_currency}?`);

							if(confirmed){
								if(payment_status){
									picked_reservation.payment_status = payment_status;
								}

								//Stop bubble event
								//e.stopPropagation();
								//let it touch to tr to resolve as read
								this._updateReservations();
							}

						}
						catch(e){}
					}
				},

				/**
				 * Fitler base on a date
				 * @param date
				 * @param mode
				 *      two mode supported: 'exactly', 'from'
				 * @private
				 */
				_fitlerReservationByDay(date, mode = MODE_EXACTLY){
					let reservations = this.reserved_reservations;
					// Assign reservations with moment date obj
					let reservations_with_date = reservations.map(reservation => {
						if(!reservation.date){
							let timestamp    = reservation.reservation_timestamp;
							reservation.date = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
						}

						return reservation;
					});

					// Update back resersvations
					this.reservations = reservations_with_date;

					let dateQueryFunction = '';
					switch(mode){
						case MODE_EXACTLY:{
							dateQueryFunction = function(reservation){
								return reservation.date.isSame(date, 'day');
							};
							break;
						}
						case MODE_BETWEEN:{
							dateQueryFunction = function(reservation){
								return reservation.date.isBefore(date);
							};
							break;
						}
						default: {
							throw 'No mode is specified';
						}

					}

					let filtered_reservations = reservations_with_date.filter(dateQueryFunction);

					// Update filtered reservations;
					this.filtered_reservations = filtered_reservations;
				},

				_filter(which_case){
					//console.log(which_case);
					switch(which_case){
						case TODAY:{
							console.log('see click today');
							// Find out which date
							let today = moment({hour: 0, minute: 0, seconds: 0});
							let mode  = MODE_EXACTLY;

							this._fitlerReservationByDay(today, mode);
							break;
						}
						case TOMORROW:{
							console.log('see click tomorrow');
							// Find out which date
							let tomorrow = moment({hour: 0, minute: 0, seconds: 0}).add(1, 'days');
							let mode     = MODE_EXACTLY;

							this._fitlerReservationByDay(tomorrow, mode);
							break;
						}
						case NEXT_3_DAYS:{
							// When call next_3_days, means next 3 days from current search
							// if no current search stored > default is today
							if(!this.next_3_days){
								let today        = moment({hour: 0, minute: 0, seconds: 0});
								this.next_3_days = today.clone().add(4, 'days');
							}

							let date = this.next_3_days;
							let mode = MODE_BETWEEN;
							this._fitlerReservationByDay(date, mode);

							break;
						}
						case NEXT_7_DAYS:{
							// When call next_7_days, means next 7 days from current search
							// if no current search stored > default is today
							if(!this.next_7_days){
								let today        = moment({hour: 0, minute: 0, seconds: 0});
								this.next_7_days = today.clone().add(8, 'days');
							}

							let date = this.next_7_days;
							let mode = MODE_BETWEEN;
							this._fitlerReservationByDay(date, mode);

							break;
						}
						case NEXT_30_DAYS:{
							// When call next_7_days, means next 30 days from current search
							// if no current search stored > default is today
							if(!this.next_30_days){
								let today         = moment({hour: 0, minute: 0, seconds: 0});
								this.next_30_days = today.clone().add(31, 'days');
							}

							let date = this.next_30_days;
							let mode = MODE_BETWEEN;
							this._fitlerReservationByDay(date, mode);

							break;
						}
						case CUSTOM:{
							let date_str = this.custom_pick_day;
							console.log(date_str);
							// Luckily, format of date is YYYY-MM-DD
							// Can't change this default
							// Ok, cross platform, parse it
							let date    = moment(date_str, 'YYYY-MM-DD');
							//console.log(date);
							let mode    = MODE_EXACTLY;
							this._fitlerReservationByDay(date, mode);

							break;
						}
						case CLEAR:{break;}
					}
				},

				_clearFilterByDay(){
					// clean date picker
					this.filter_date_picker = null;
					/** @warn annoy code, should improve */
					this.custom_pick_day    = null;
					// clean filter
					let new_filter_day      = null;
					// Update vue state
					this.filter_day         = new_filter_day;
					// Hide filter panel
					//this.filtered_reservations = [];
					this._addFilterByDay(new_filter_day);

				},

        _getLastStatus(status){
          this.reservationLastStatus = status;
        },

				_autoSave(reservation = null, key = null){
					let statusKey = key == 'status';
          let {status} = reservation;
          let statusAsFinishedCase = this._statusAsFinishedCase(status);
          let statusCase = statusKey && statusAsFinishedCase;
          
          if(statusCase){
            let confirm = window.confirm('Are you sure you want to cancel this reservation? This cannot be undone.');
            if(!confirm){
              reservation.status = this.reservationLastStatus;
              return;
            }
          }

          if(reservation && key != 'staff_read_state'){
            reservation.staff_read_state = true;
          }
          // Get out reservations & save it
          let reservations = this.reservations;
          let action = {
            type: AJAX_UPDATE_RESERVATIONS,
            reservations
          }

          self.ajax_call(action);
				},

				/**
				 * What is 'type'
				 * type to classify filter condition in same type can override on each other
				 * ok simple support priority, if current type is same, priority not larger than
				 * be POP OUT, to replace when add new one
				 * @param name
				 * @param filter_function
				 * @param options
				 * type keys:
				 * {
				 *      type: '<name of type'>,
				 *      priority: <number>,
				 *      name: 'name of this filter', //optional
				 * }
				 * @returns {*}
				 * @private
				 */
				_createFilter(filter_function, options){
					// Check required keys of type
					const required_keys = ['type', 'priority'];
					let empty_keys      = required_keys.filter(key => typeof options[key] == 'undefined');

					if(empty_keys.length > 0){
						throw '_createFilter, type lack of required key';
					}

					filter_function.priority = options.priority;
					filter_function.type     = options.type;
					filter_function.toJSON   = () => {return options.name;};

					return filter_function;
				},

				_addNewFilter(new_filter){
					// Push it back to filter_options
					// Filter here is same type & doesn't have higher priority
					// >>> remove it out
					let new_filter_options = this.filter_options.filter(filter => {
						if(filter.type == new_filter.type && filter.priority <= new_filter.priority){
							return false;
						}

						return true;
					});

					new_filter_options.push(new_filter);

					this.filter_options = new_filter_options;
				},

				_addFilterByDay(which_day){
					let start_day = moment({hour: 0, minute: 0, seconds: 0});
					let num_days    = 0;
					switch(which_day){
						case TODAY:{
							// in today, start day is start day of default
							num_days = 1;
							break;
						}
						case TOMORROW:{
							// as tomorrow case, start day is early of tomorrow
							// ok, at one more
							start_day = start_day.add(1, 'days');
							num_days = 1;
							break;
						}
						case NEXT_3_DAYS:{
							// why at 4 in 3_days case
							// bcs we want to reach up to 23:59:59
							// when filter in between as [)
							// equal at first start
							// less than at last end
							num_days = 4;
							break;
						}
						case NEXT_7_DAYS:{
							num_days = 8;
							break;
						}
						case NEXT_30_DAYS:{
							num_days = 31;
							break;
						}
						case CUSTOM:{
							let date_str = this.custom_pick_day;
							// Browser pick day, ONLY RETURN AS YYYY-MM-DD
							// So lucky at this point
							start_day = moment(date_str, 'YYYY-MM-DD');
							num_days  = 1;
							break;
						}
						// When specify as null, means no filter apply
						case null:{
							break;
						}
						default:{
							throw '_addFilterByDay: not support case';
							break;
						}
					}

					let end_day = start_day.clone().add(num_days, 'days');

					// Filter receive a reservation
					// Base on that reservation filter out
					let filter = (reservation) => {
						// Get out date of reservation to compare
						let date = reservation.date;
						// wow the last parameter is [}, [], () compare on equal or not
						return date.isBetween(start_day, end_day, null, '[)');
					};

					// When no filter apply, filter function return true in all cases
					if(which_day == null){
						filter = () => {return true};
					}

					let iFilter = this._createFilter(filter, {name: 'filter reservation by day', type: FILTER_TYPE_DAY, priority: 1});

					this._addNewFilter(iFilter);
				},

				/**
				 * Support multiple status
				 * @param which_status
				 * @private
				 */
				_addFilterByStatus(...which_status){
					// Support case when status as number
					let integer_status = which_status.map(status => Number(status));
					let filter = (reservation) => {
						if(which_status.includes(reservation.status)
							||integer_status.includes(reservation.status)
						){
							return true;
						}

						return false;
					};

					// When no status specify as empty array
					// Which means no filter to apply
					if(which_status.length == 0){
						filter = () => {return true;};
					}

					let iFilter = this._createFilter(filter, {name: 'filter reservation by status', type: FILTER_TYPE_STATUS, priority: 1});

					this._addNewFilter(iFilter);
				},

				_clearFilterByStatus(){
					let new_filter_statuses = [50, 75, 100, 200, 300];
          this._addFilterByStatus(...new_filter_statuses);
					// Update vue state
					this.filter_statuses    = new_filter_statuses;
					// add to filter queue
					this._addFilterByStatus(...new_filter_statuses);
				},

				_toggleFilterStatus(status, $event){
					//console.log(which_status, $event);
					let filter_statuses = this.filter_statuses;
					let current_state   = filter_statuses.includes(status);
					// Toggle state
					current_state       = !current_state;

					let new_filter_statuses;
					// true it means show push
					if(current_state){
						new_filter_statuses = [...filter_statuses, status];
					// should remove
					}else{
						new_filter_statuses = filter_statuses.filter(_status => _status != status);
					}

					// Update vue state
					this.filter_statuses = new_filter_statuses;

					//console.log(new_filter_statuses);
					// Ok, now call search
					this._addFilterByStatus(...new_filter_statuses);
				},

				_toggleFilterByDay(which_day){
					let current_state = this.filter_day == which_day;
					// toggle it
					current_state     = !current_state;
					/**
					 * This is quite ANNOY
					 * But when toggle on custom day
					 * Which only means that we change a pick day
					 * So, still be at CUSTOM
					 * @type {boolean}
					 */
					// which_day == CUSTOM, means when change a pick day
					// still be at CUSTOM, rather than close it
					if(which_day == CUSTOM){
						current_state = true;
					}

					/**
					 * These code should be improve
					 * custom_pick_day store staff pick
					 * but when toggle to other options
					 * it should be remove
					 * to notify when staff repick
					 * as on-change event trigger
					 */
					if(which_day != CUSTOM){
						//clear custom_pick_day
						this.custom_pick_day = null;
						this.filter_date_picker = null;
					}

					let new_filter_day;
					// true it means should push
					if(current_state){
						new_filter_day = which_day;
					}else{
						// Update current filter day
						new_filter_day = null;
					}

					// Update vue state
					this.filter_day = new_filter_day;
					// Call filter
					this._addFilterByDay(new_filter_day);
				},

				_addFilterByConfirmId(){
          let {filter_confirm_id: confirm_id} = this;
          store.dispatch({
						type: FETCH_RESERVATIONS_BY_CONFIRM_ID,
            confirm_id
					});
				},

				_refreshOutletData(){
					//this.startIntervalAutoRefresh();
					store.dispatch({type: REFETCHING_DATA});
				},

				startIntervalAutoRefresh(){

					let self          = this;
					const short_check = 5 * 1000;
					const long_check  = 5 * 60 * 1000;

					let run = function(how_long){
						console.log('run timeout');
						setTimeout(() => {

							if(!self.is_calling_ajax){

								store.dispatch({type: REFETCHING_DATA});

								run(long_check)

							}else{

								run(short_check);

							}
						}, how_long);
					}

					// execute run
					run(long_check);
				},

				_fetchReservationsByDay(day, day_str = null){
					store.dispatch({
						type: FETCH_RESERVATIONS_BY_DAY,
						day,
						day_str
					});
				},

				_fetchReservationsByRangeDay(day, raw_day_str = null){
					console.log('Fetch by range date', raw_day_str)
					if(raw_day_str.length <= 10){
						// in this case, just first day selected
						// not handle here
						return;
					}
					
					let day_str = JSON.stringify( raw_day_str.split(/ to /));
					
					store.dispatch({
						type: FETCH_RESERVATIONS_BY_DAY,
						day,
						day_str
					});
				},

				_openNewReservationDialog(){
					//let store = window.store;
					//store.dispatch({type: OPEN_NEW_RESERVATION_DIALOG});
					// let thunkNewReservation = (dispatch, getState) => {
					// 	dispatch({type: OPEN_NEW_RESERVATION_DIALOG});
					// };
					// Dispatch as thunk, if need can fetch data from here
					// this.pleaseDispatchAction = thunkNewReservation;
					// store.dispatch(thunkNewReservation);

					// reuse normal dispatch
					store.dispatch({type: OPEN_NEW_RESERVATION_DIALOG});
				},

				_searchAvailableTime(){
					let vue_state   = window.vue_state;
					let {outlet_id} = vue_state;

					let {adult_pax, children_pax} = vue_state.new_reservation;

					let action = {
						type: AJAX_SEARCH_AVAILABLE_TIME,
						outlet_id,
						adult_pax,
						children_pax,
					};

					self.ajax_call(action);
				},

				_pickTime(time_str){
					//console.log('_pickTime, see you click');
					store.dispatch({type: CHANGE_NEW_RESERVATION_TIME, time_str});
				},

				_createNewReservation({sms_message_on_reserved}){
					//
					let {new_reservation} = vue_state;
					// Quick check for empty str
					const required_keys = [
						'outlet_id',
						'salutation',
						'first_name',
						'last_name',
						// 'email',
						'phone_country_code',
						'phone',
						'time_str',
					];

					let empty_fields = required_keys.filter(key => {
						let value = new_reservation[key];

						return !value;
					});

					if(empty_fields.length > 0){
						let first_empty_key = empty_fields[0];
						window.alert(`Please fill in all fields. Ex: ${first_empty_key} is empty`);
					}else{
						let new_reservation = {sms_message_on_reserved};
						store.dispatch({type: CREATE_NEW_RESERVATION, new_reservation});
					}

				},
				// Only allow user turn on required authorization
				// When he actually make search time call
				_togglePaymentRequired(){
					let vue_state = window.vue_state;
					let {new_reservation} = vue_state;
					let {available_time} = new_reservation;
					if(!(available_time.length > 0)){
						window.alert('Please pick up time first');
						return;
					}
					// Ok toggle it
					let {payment_required: curr} = new_reservation;
					new_reservation.payment_required = !curr;
				},

        _getReservationRowClass(reservation){
          let {staff_read_state, is_edited_by_customer, status} = reservation;

          let className = 'active';

          // Can edit on row
          let canEditOnRow = this._isAllowedToEditOnRow(status);

          // Only update class for 'allowed to edit' reservation
          if(canEditOnRow) {
            // Update className in different case
            // ClassName as override
            // Bcs we only use background-color
            if(!staff_read_state) {
              className = '';
            }

            if(is_edited_by_customer) {
              className = 'hightlight';
            }
          }

          // Disable click on reservation
          // Just by update class style as pointer-events -> none
          if(!canEditOnRow){
            className = `${className} disabled text-muted`;
          }

          return className;
        },

        /**
         * This function used for serveral different check on status of reservation
         * To allow edit or not, edit on status is DIFFERENT from edit on ROW
         * @param status
         * @returns {boolean}
         * @private
         */
        _statusAsFinishedCase(status){
          // Can simple write it as status < 0
          // But check on single one still better
          // let statusAsFinishedCase = status == RESERVATION_NO_SHOW || status == RESERVATION_USER_CANCELLED || status == RESERVATION_STAFF_CANCELLED
          //   || status == RESERVATION_ARRIVED;
          let statusAsFinishedCase = status == RESERVATION_NO_SHOW || status == RESERVATION_USER_CANCELLED || status == RESERVATION_STAFF_CANCELLED;
          return statusAsFinishedCase;
        },

        _statusAsAmendmented(status){
          let amendmented = status == RESERVATION_AMENDMENTED;
          return amendmented;
        },

        _statusAsPaymentNotCompleted(status){
          let paymentNotCompleted = status == RESERVATION_REQUIRED_PAYMENT;
          return paymentNotCompleted;
        },

        _statusAsSuccessBooking(status){
          return status >= RESERVATION_RESERVED;
        },
        /**
         When should disable change on status
             1. When reservation not complete payment
             Any change on this reservation issss dangerous, make it from uncomplete > free complete
             Dont have to pay anything
             2. When status pump into "No Show", "User Cancelled", "Staff Cancelled" dont allow any change
         * @param status
         * @returns {boolean}
         * @private
         */
        _isAllowedToEdit(status){
          let amendmented = this._statusAsAmendmented();
          let paymentNotCompleted = this._statusAsPaymentNotCompleted(status);
          let statusAsFinishedCase = this._statusAsFinishedCase(status);
          let disabled = amendmented || paymentNotCompleted || statusAsFinishedCase;
          return !disabled;
        },

        _isAllowedToEditOnRow(status){
          // In case of payment not complete
          // Still let him edit on this payment
          let canEditOnRow = this._isAllowedToEdit(status) || this._statusAsPaymentNotCompleted(status);
          return canEditOnRow;
        },

        _isAllowSendReminderSMS(status){
          let successBooking = status >= RESERVATION_RESERVED;
          let statusAsFinshedCase = this._statusAsFinishedCase(status);
          let statusAsArrived = status == RESERVATION_ARRIVED;
          let allowed = successBooking && !(statusAsFinshedCase || statusAsArrived);
          return allowed;
        },

        _shouldShowDetailDialog(status){
          // In case of payment not complete
          // Still let him edit on this payment
          let shouldShow = this._isAllowedToEdit(status) || this._statusAsPaymentNotCompleted(status);
          return shouldShow;
        },

        _sendReminderSMS(reservation){
          let {confirm_id, outlet_id} = reservation;
          store.dispatch({
            type: SEND_SMS_REMINDER_ON_RESERVATION,
            confirm_id,
            outlet_id,
          })
        },

				_goToPrintPage(){
					//console.log(self.url());
					var hashids = new Hashids();
					let {reservations, outlet_id} = this;
					// let reservation_ids = reservations.map(r => r.id).join(',');
					let reservation_ids = reservations.map(r => r.id);
					let hash_ids_str    = hashids.encode(reservation_ids);
					let query_params    = `print?outlet_id=${outlet_id}&reservation_ids=${hash_ids_str}`;
					let redirect_url    = self.url(query_params);
					// Open new tab for print page
					window.open(redirect_url);
				},

				// Add logic with min max of outlet available time
				_updateNewReservationDate(date_str){
					let {new_reservation: currNewReservation, outlet} = this;
					let earlyToday = moment().set({hours: 0, minutes: 0, seconds: 0});
					let pickDate = moment(date_str, 'YYYY-MM-DD');
					let maxDate = earlyToday.clone().add(+outlet.max_days_in_advance, 'days');

					let inRange = pickDate.isSameOrAfter(earlyToday, 'day') && pickDate.isSameOrBefore(maxDate, 'day');

          if(inRange){
            let showing_date_str = moment(date_str, 'YYYY-MM-DD').format('DD/MM/YYYY');
						this.new_reservation = Object.assign(currNewReservation, {date_str, showing_date_str});
					}else{
						window.alert(`Please pick date in available range. Max days in advance: ${outlet.max_days_in_advance}`);
					}
				},

				_alertOutOfRange(){
					let {new_reservation: {adult_pax, children_pax}, outlet} = this;
					let paxSize = (+adult_pax) + (+children_pax);
					let inRange = paxSize >= outlet.overall_min_pax && paxSize <= outlet.overall_max_pax;
					if(!inRange){
						window.alert(`Please reselect pax, total pax should between: [${outlet.overall_min_pax}, ${outlet.overall_max_pax}]`);
					}
				},

				_updateSpecialSessionDate(session_date){
					let {special_session: current_special_session} = this;
					let special_session = Object.assign(current_special_session, {session_date});
					// Update to vue_state
					// console.log(special_session);
					this.special_session = special_session;
				},

				/**
				 * @param timing_property 'first_arrival_time', or 'last_arrival_time'
         */
				_updateTimingTime(timing_property, event){
          // return;
					let {detail: {value: timing_time}} = event;
					let momentTime = moment(timing_time, 'hh:mma');
					let {special_session: current_special_session} = this;
					let special_session = Object.assign(current_special_session, {[timing_property]: momentTime});
					// Update to vue_state
					// console.log(special_session);
					this.special_session = special_session;
				},

				/**
				 * Check then create both timing map to special session
				 * As capacity 0
         */
				_createSpecialSession(){
					let isValidObj = this._checkSpecialSessionValid();
					let {isValid, msg} = isValidObj;
					if(!isValid){
						window.alert(msg);
						return;
					}

					let store = window.store;
					// Ok fine, send to server
					store.dispatch({
						type: CREATE_CLOSE_SLOT
					});

					// Clear current_special
				},

				_checkSpecialSessionValid(){
					let {special_session} = this;
					let need_check_properties = ['session_date', 'first_arrival_time', 'last_arrival_time'];
					let hasData = need_check_properties.reduce((carry, item) => (carry && special_session[item]), true);

					if(!hasData){
						return {isValid: false, msg: 'Please fill in all fields.'};
					}

					try{
						let firstTime   = special_session.first_arrival_time;
						let lastTime    = special_session.last_arrival_time;
						let isTimeRange = lastTime.isAfter(firstTime);

						let isValid = isTimeRange;

						if(isValid){
							return {isValid, msg: ''};
						}else{
							return {isValid, msg: 'Please choose last arrival time larger than first one.'}
						}

					}catch(e){
						return {isValid: false, msg: 'Something went wrong when calling moment query.'};;
					}
				},
        
        _totalPaxInReservations(reservations){
          return reservations.map(r => (Number(r.adult_pax) + Number(r.children_pax))).reduce((c, i) => c+i, 0);
        },

        _isDisableSendReminderSMS(reservation){
          let {status} = reservation;
          let allowed = this._isAllowSendReminderSMS(status);
          return !allowed;
        },

        _resendPaymentRequiredAuthorization(reservation){
          let store = window.store;
          store.dispatch({type: RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS, confirm_id: reservation.confirm_id});
        },

        _findCustomerByPhone(){
          let store = window.store;
          store.dispatch({
            type: FIND_CUSTOMER_SAME_PHONE
          });
        },

        _totalSelectPax(){
          let state = this;
          let maxPax = Number(state.outlet.overall_max_pax);
          // Select option need an array of available option, so
          // We build up max pax into an array from 0 to max pax
          let paxArray = []
          for(let i = 0; i <= maxPax; i++){
            paxArray.push(i)
          }
          return paxArray;
        },

        /**
         * Logic on status is DIFFERENT on logic on row
         * Manually check, not REUSED which cause a lot of bug
         * Go from status -300 -200 -100 50 75 100 200 300
         * To check on each different status, which one is allowed to edit
         * @param reservation
         * @private
         */
        _isAllowChangeStatus(reservation, rowStatus){
          rowStatus = +rowStatus;
          let {status} = reservation;
          // When status bump into finished case
          // Nothing to compare with rowStatus
          // Staff dont have permission to change
          let statusAsFinishedCase = this._statusAsFinishedCase(status);
          if(statusAsFinishedCase){
            return false;
          }

          // Payment required
          if(status == RESERVATION_REQUIRED_PAYMENT){
            let isArrivedRowStatus = rowStatus == RESERVATION_ARRIVED;
            let isStaffCancelled   = rowStatus == RESERVATION_STAFF_CANCELLED;
            if(isArrivedRowStatus || isStaffCancelled){
              return true;
            }

            return false;
          }

          // Amendmented
          let statusAsAmendmented = this._statusAsAmendmented(status);
          if(statusAsAmendmented){
            return false;
          }

          // Reserved, reminder sent, confirmation, arrived
          let statusAsSuccessBooking = this._statusAsSuccessBooking(status);
          if(statusAsSuccessBooking){
            let rowStatusAsFinshedCase = this._statusAsFinishedCase(rowStatus);
            let rowStatusAsSuccessBooking = this._statusAsSuccessBooking(rowStatus);
            return rowStatusAsSuccessBooking || rowStatusAsFinshedCase;
          }
        },

        _shouldDisabledEditStatus(status, rowStatus){
          let canEdit = this._isAllowChangeStatus(status, rowStatus)
          return !canEdit;
        }
			}
		});
	}

	reservationDialogContentReducer(state, action){
		switch(action.type){
			case SHOW_RESERVATION_DIALOG_CONTENT:{
				return action.reservation_dialog_content;
			}
			case HIDE_RESERVATION_DIALOG_CONTENT:{
				return state;
			}
			default:
				return state;
		}
	}

	reservationsReducer(state, action){
		switch(action.type){
			case UPDATE_SINGLE_RESERVATION: {


				return state;
			}
			case UPDATE_RESERVATIONS: {
				let vue_state = window.vue_state;
				let reservations = Object.assign({}, vue_state.reservations);

				return reservations;
			}
			default:
				return state;
		}
	}

	_findView(){
		///Only run one time
		if(this._hasFindView)
			return;
		
		this._hasFindView = true;

		this.reservation_dialog = $('#reservation-dialog');

		this.new_reservation_dialog = $('#new-reservation-dialog');
	}

	event(){
		this._findView();
		
		let self = this;
		
		document.addEventListener('switch-outlet', (e)=>{
			let outlet_id = e.detail.outlet_id;

			store.dispatch({
				type: TOAST_SHOW,
				toast: {
					title: 'Switch Outlet',
					content: 'Fetching Data'
				}
			});

			let action = {
				type: AJAX_REFETCHING_DATA,
				outlet_id
			}

			/**
			 * By pass store
			 * When handle action in this way
			 */
			self.ajax_call(action);
		});

		document.addEventListener('go-to-page', (e) => {
			let store = window.store;
			let {outlet_id} = store.getState();
			let {redirect_url: base_url} = e.detail;
			let redirect_url = `${base_url}?outlet_id=${outlet_id}`;
			window.location.href = redirect_url;
		})
	}

	view(){
		let store = window.store;
		let self  = this;

		//Debug state
		let redux_state_element = document.querySelector('#redux-state');

		store.subscribe(()=>{
			let action   = store.getLastAction();
			let state    = store.getState();
			let prestate = store.getPrestate();

			// Debug
			let is_local = state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost');
			if(redux_state_element && is_local){
				let clone_state = Object.assign({}, state);
				// Remove 'heavy keys' which build HTML > kill performance
				clone_state.reservations          = 'Please watch in state';
				clone_state.filtered_reservations = 'Please watch in state';
				// Ok, build html
				redux_state_element.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
			}

			/**
			 * Show dialog for edit reservation detail
			 */
			if(action == SHOW_RESERVATION_DIALOG_CONTENT){
				self.reservation_dialog.modal('show');
			}


			if(action == HIDE_RESERVATION_DIALOG_CONTENT){
				self.reservation_dialog.modal('hide');
			}

			/**
			 * Show toast
			 */
			if(action == TOAST_SHOW){
				let toast = state.toast;
				//update toast in vue
				Object.assign(window.vue_state, {toast});
				window.Toast.show();
			}

			if(action == REFETCHING_DATA){
				let {filter_day: day} = state;
				let {outlet_id}       = state;

				if(day == CUSTOM){
					day = state.custom_pick_day;
				}

				self.ajax_call({
					type: AJAX_FETCH_RESERVATIONS_BY_DAY,
					day,
					outlet_id,
				});
			}

			if(action == FETCH_RESERVATIONS_BY_DAY){

				let {filter_day: day} = state;
				let {outlet_id}       = state;

				// When CUSTOM, read day from what input set
				if(day == CUSTOM){
					day = state.custom_pick_day;
				}

				self.ajax_call({
					type: AJAX_FETCH_RESERVATIONS_BY_DAY,
					day,
					outlet_id,
				});
			}

			if(action == FETCH_RESERVATIONS_BY_RANGE_DAY){

				let {filter_day: day} = state;
				let {outlet_id}       = state;

				// When CUSTOM, read day from what input set
				if(day == CUSTOM){
					day = state.custom_pick_day;
				}

				self.ajax_call({
					type: AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY,
					day,
					outlet_id,
				});
			}

			if(action == FETCH_RESERVATIONS_BY_CONFIRM_ID){
				let {filter_confirm_id: confirm_id} = state;
				let {outlet_id}       = state;

				self.ajax_call({
					type: AJAX_FIND_RESERVATION,
					outlet_id,
					confirm_id
				})
			}

			if(action == OPEN_NEW_RESERVATION_DIALOG){
				self.new_reservation_dialog.modal('show');
			}

			if(action == CLOSE_NEW_RESERVATION_DIALOG){
				self.new_reservation_dialog.modal('hide');
			}

			if(action == CREATE_NEW_RESERVATION){
				let {new_reservation} = state;

				let action = Object.assign(new_reservation, {
					type: AJAX_CREATE_NEW_RESERVATION
				});

				self.ajax_call(action);
			}

      if(action == SEND_SMS_REMINDER_ON_RESERVATION){
        let {send_sms_on_reservation} = state;
        let {outlet_id, confirm_id} = send_sms_on_reservation

        let action = {
          type: AJAX_SEND_REMINDER_SMS_ON_RESERVATION,
          outlet_id,
          confirm_id,
        }

        self.ajax_call(action);
      }

			if(action == CREATE_CLOSE_SLOT){
				let {special_session} = state;
				let {first_arrival_time, last_arrival_time} = special_session;

				let {outlet_id} = state;
				// Need transform moment time to normal str
				let data = Object.assign({}, special_session, {
					outlet_id,
					first_arrival_time: first_arrival_time.format('HH:mm:ss'),
					last_arrival_time:  last_arrival_time.format('HH:mm:ss'),
				})

				let action = {
					type: AJAX_CREATE_CLOSE_SLOT,
					outlet_id,
					special_session: data
				}

				self.ajax_call(action);
			}

      if(action == RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS){
        let {resend_reservation_confirm_id, outlet_id} = state;
        let action = {
          type: AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS,
          outlet_id,
          confirm_id: resend_reservation_confirm_id
        }

        self.ajax_call(action);
      }

      if(action == FIND_CUSTOMER_SAME_PHONE){
        let {new_reservation, outlet_id} = state;
        let action = {
          type: AJAX_FIND_CUSTOMER_SAME_PHONE,
          outlet_id,
          phone: new_reservation.phone,
          phone_country_code: new_reservation.phone_country_code,
        }

        self.ajax_call(action);
      }

			// if(action == SYNC_DATA){
			Object.assign(window.vue_state, store.getState());
			// }
		});
	}

	listener(){
		let store = window.store;
		let self = this;

		store.subscribe(()=>{
			let action   = store.getLastAction();
			let state    = store.getState();
			let prestate = store.getPrestate();
		});
	}

	ajax_call(action){
		let self = this;

		store.dispatch({
			type: TOAST_SHOW,
			toast: {
				title:  'Calling ajax',
				content: '...'
			}
		});

    // Dispatch calling ajax
    let {type} = action;
    store.dispatch({type: CALLING_AJAX, is_calling_ajax: type});


		let state = store.getState();

		switch(action.type){
			case AJAX_UPDATE_RESERVATIONS: {
				let url       = self.url('');
				let outlet_id = state.outlet_id;
				let data      = Object.assign({}, action, {outlet_id});
				
				$.jsonAjax({url, data});
				break;
			}
			case AJAX_REFETCHING_DATA: {
				let url         = self.url('');
				let data        = Object.assign({}, action);
				
				$.jsonAjax({url, data});
				break;
			}
			case AJAX_FETCH_RESERVATIONS_BY_DAY:{
				let url         = self.url('');
				let data        = Object.assign({}, action);

				$.jsonAjax({url, data});
				break;
			}
			case AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY:{
				let url         = self.url('');
				let data        = Object.assign({}, action);

				$.jsonAjax({url, data});
				break;
			}
			case AJAX_FIND_RESERVATION:{
				let url         = self.url('');
				let data        = Object.assign({}, action);

				$.jsonAjax({url, data});
				break;
			}
			case AJAX_SEARCH_AVAILABLE_TIME:{
				let url         = self.url('');
				let data        = Object.assign({}, action);
				
				$.jsonAjax({url, data});
				break;
			}
			case AJAX_CREATE_NEW_RESERVATION: {
				let url         = self.url('');
				let data        = Object.assign({}, action);

				$.jsonAjax({url, data});
				break;
			}
      case AJAX_SEND_REMINDER_SMS_ON_RESERVATION:{
        let url         = self.url('');
        let data        = Object.assign({}, action);

        $.jsonAjax({url, data});
	      break;
      }
			case AJAX_CREATE_CLOSE_SLOT:{
				let url         = self.url('');
				let data        = Object.assign({}, action);

				$.jsonAjax({url, data});
				break;
			}
      case AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS:{
        let url         = self.url('');
        let data        = Object.assign({}, action);

        $.jsonAjax({url, data});
        break;
      }
      case AJAX_FIND_CUSTOMER_SAME_PHONE:{
        let url         = self.url('');
        let data        = Object.assign({}, action);

        $.jsonAjax({url, data});
        break;
      }
			default:
				console.log('client side. ajax call not recognize the current acttion', action);
				break;
		}

		// console.log('????')
	}

	ajax_call_success(res){

		switch(res.statusMsg){
			case AJAX_SUCCESS: {
				let toast = {
					title:'Update success',
					content: '＼＿ヘ(ᐖ◞)､ '
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});
				
				store.dispatch({
					type: SYNC_DATA,
					data: res.data
				});

				break;
			}
			case AJAX_REFETCHING_DATA_SUCCESS:{
				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Fetch data from outlet',
						content: 'Received'
					}
				});

				store.dispatch({
					type: SYNC_DATA,
					data: res.data
				});

				break;
			}
			case AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS: {
				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Fetch data from outlet',
						content: 'Received'
					}
				});

				store.dispatch({
					type: SYNC_DATA,
					data: res.data
				});
				
				break;
			}
			case AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY_SUCCESS: {
				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Fetch data from outlet',
						content: 'Received'
					}
				});

				store.dispatch({
					type: SYNC_DATA,
					data: res.data
				});

				break;
			}
			case AJAX_FIND_RESERVATION_SUCCESS: {
				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Fetch data from outlet',
						content: 'Received'
					}
				});

				store.dispatch({
					type: SYNC_DATA,
					data: res.data
				});

				break;
			}
			case AJAX_VALIDATE_FAIL: {
				let toast = {
					title: 'Validate Fail',
					content: JSON.stringify(res.data)
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});

				break;
			}
			case AJAX_UNKNOWN_CASE: {
				let toast = {
					title:'Unknown case',
					content: 'xxx'
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});

				break;
			}
			case AJAX_AVAILABLE_TIME_FOUND: {
				// Update available time
				let {available_time} = res.data;
				store.dispatch({type: UPDATE_AVAILABLE_TIME, available_time});

				// Just for better experience
				// But it couple data
        // let {new_reservation: {date_str}} = store.getState();
				// let available_times_on_date = available_time[date_str];

				//if(available_times_on_date && available_times_on_date.length > 0){
					// Update info for this new_reservation
					let {payment_authorization}    = res.data;
					let {deposit, deposit: payment_amount, paypal_currency: payment_currency} = payment_authorization;
					// Apply deposit case on admin check for credit card authorization
					let payment_required = deposit != null;
					let new_reservation  = {payment_amount, payment_currency, payment_required};
					store.dispatch({type: UPDATE_NEW_RESERVATION, new_reservation});
				//}

				break;
			}
			case AJAX_RESERVATION_SUCCESS_CREATE: {
				let {reservation} = res.data;

				console.log(reservation);

				store.dispatch({type: CLOSE_NEW_RESERVATION_DIALOG});

				// let toast = {
				// 	title: 'New Reservation',
				// 	content: 'Created successfully'
				// };
				//
				// store.dispatch({type: TOAST_SHOW, toast});

				store.dispatch({type: REFETCHING_DATA});

				break;
			}
      case AJAX_SEND_REMINDER_SMS_ON_RESERVATION_SUCCESS:{
        let toast = {
          title: 'Send reminder',
          content: 'Success'
        };
        store.dispatch({
          type: TOAST_SHOW,
          toast,
        })

        store.dispatch({type: REFETCHING_DATA})
        break;
      }
			case AJAX_CREATE_CLOSE_SLOT_SUCCESS: {
				// let {data} = res;
				// console.log(data);
				store.dispatch({
					type: HIDE_CLOSE_SLOT_EMPTY_SPECIAL_SESSIOn
				});
				break;
			}
      case AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS_SUCCESS:{
        let toast = {
          title: 'Resend payment authorization sms',
          content: 'Success'
        };
        store.dispatch({
          type: TOAST_SHOW,
          toast,
        })

        store.dispatch({type: REFETCHING_DATA})
        break;
      }
      case AJAX_FIND_CUSTOMER_SAME_PHONE_NOT_FOUND:{
        // Just no data on this guy
        // Silenly return
        console.log('No info on this guy with phone number');
        break;
      }
      case AJAX_FIND_CUSTOMER_SAME_PHONE_SUCCESS:{
        // No toast here, just get this info, implement into new_reservation
        let {reservation} = res.data;
        // console.log(reservation);
        let new_reservation_info = {
          salutation: reservation.salutation,
          first_name: reservation.first_name,
          last_name: reservation.last_name,
          email: reservation.email,
        }

        store.dispatch({
          type: UPDATE_NEW_RESERVATION,
          new_reservation: new_reservation_info,
        });

        break;
      }
			default:{
				// This default cant resolve
				// Ok toast out what happen
				window.alert(JSON.stringify(res));
				break;
			}

		}
	}

	ajax_call_error(res_literal){
		console.log(res_literal);
		// Please don't modify these code
		let res = res_literal.responseJSON;

		if(res && res.statusMsg && res.errorMsg){
			window.alert(res.errorMsg);
		}else if(res && res.statusMsg){
      window.alert(res.statusMsg);
    }else{
			window.alert(JSON.stringify(res_literal));
		}
		// When fall case happen
		// Should refetch page
		let store = window.store;
    let state = store.getState();
    let lastRefreshBcsException = state.lastRefreshBcsException;
    // Only refresh if time diff is 10s
    let now = Number(moment().format('X'));
		let shouldRefresh = (now - lastRefreshBcsException) > 10;
    if(shouldRefresh){
      window.alert('We are refetching data');
      store.dispatch({type: REFETCHING_DATA});
      store.dispatch({type: UPDATE_LAST_REFRESH_BCS_EXCEPTION, time: now})
    }
	}
	
	ajax_call_complete(res){
		let store = window.store;
		store.dispatch({type: CALLING_AJAX, is_calling_ajax: false});
	}

	hack_ajax(){
		//check if not init
		if(this._hasHackAjax)
			return;

		this._hasHackAjax = true;

		let self = this;

		let o_ajax = $.ajax;
		$.jsonAjax = function(options){
			let data = options.data;
			let data_json = JSON.stringify(data);
			//console.log(data_json);
			options = Object.assign(options, {
				method  : 'POST',
				data    : data_json,
				success : self.ajax_call_success.bind(self),
				error   : self.ajax_call_error.bind(self),
				complete: self.ajax_call_complete.bind(self),
			});

			return o_ajax(options);
		}
	}

	url(path = ''){
		let store = window.store;
		let state = store.getState();

		//noinspection JSUnresolvedVariable
		let base_url = state.base_url || '';

		if(base_url.endsWith('/')){
			base_url = base_url.substr(0, base_url.length - 1);
		}

		if(path.startsWith('/')){
			path = path.substr(1);
		}

		let url = `${base_url}/${path}`;

		if(url.endsWith('/')){
			url = url.substr(0, url.length - 1);
		}

		return url;
	}
}

let adminReservations = new AdminReservations();