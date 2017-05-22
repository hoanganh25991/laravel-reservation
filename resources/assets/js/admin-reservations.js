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

//Paypal
const PAYMENT_REFUNDED = 50;
const PAYMENT_PAID     = 100;
const PAYMENT_CHARGED  = 200;

const TODAY        = 'TODAY';
const TOMORROW     = 'TOMORROW';
const NEXT_3_DAYS  = 'NEXT_3_DAYS';
const NEXT_7_DAYS  = 'NEXT_7_DAYS';
const NEXT_30_DAYS = 'NEXT_30_DAYS';
const CUSTOM       = 'CUSTOM';
const CLEAR        = 'CLEAR';

const MODE_EXACTLY = 'MODE_EXACTLY';
const MODE_BETWEEN = 'MODE_BETWEEN';
const SYNC_VUE_STATE = 'SYNC_VUE_STATE';

const RESERVATION_NO_SHOW          = -300;
const RESERVATION_STAFF_CANCELLED = -200;
const RESERVATION_USER_CANCELLED      = -100;
const RESERVATION_DEPOSIT          = 50;
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
const AJAX_FETCH_RESERVATIONS_BY_DAY = 'AJAX_FETCH_RESERVATIONS_BY_DAY';
const AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS = 'AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS';

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
				case FETCH_RESERVATIONS_BY_DAY:{
					let {day: filter_day, day_str: custom_pick_day} = action;

					return Object.assign({}, state, {filter_day, custom_pick_day});
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
			// Decide show|hide
			filter_panel: true,
			// Manage filterd on reservations
			filtered_reservations: [],
			// contains all filters
			filter_options: [],
			// manage filter date picker
			filter_date_picker: null,
			// store which day pick by staff
			custom_pick_day: null,
			// support multilple status
			filter_statuses: [],
			// store which type of filter by day
			filter_day: null,
			// allow search by confirm_id
			filter_confirm_id: null,
			// store search confirm_id search state
			filter_search: null,
			// auto refresh
			auto_refresh_status: null,
			is_calling_ajax: null,
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
			deposit: null,
			time: null,
			paypal_currency: null,
			date: null,
			date_str: null,
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
				salutation: 'Mr.',
				first_name: 'Anh',
				last_name : 'Le Hoang',
				email: 'lehoanganh25991@gmail.com',
				phone_country_code: '+84',
				phone: '903865657',
				customer_remarks: 'hello world'
			});
		}

		return new_reservation;
	}

	buildVue(){
		window.vue_state = this.frontEndState();

		let self  = this;

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
					let dp = flatpickr('#flatpickr');
					dp.open();
				}
				this.is_flatpickr_mounted = is_flatpickr_mounted;
			},
			computed:{
				updateFilteredReservations() {
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
					// let reservations_with_date =
					// 	reservations.map(reservation => {
					// 		// Only run when date not assing
					// 		// Of course, don't do silly thing > infinite loop
					// 		// ᕕ( ᐛ )ᕗ
					// 		if(!reservation.date){
					// 			let timestamp = reservation.reservation_timestamp;
					// 			let date      = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
					// 			// Assign date
					// 			reservation.date = date;
					// 		}
					//
					// 		return reservation;
					// 	});
					//
					// // Ok update it to vue state explicit
					// // By calling in this style
					// // Redux state also be synced
					// this.reservations = reservations_with_date;

					// Assign date
					reservations.forEach(reservation => {
						let timestamp = reservation.reservation_timestamp;
						let date      = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
						// Assign date
						reservation.date = date;
					});
				},
				updateFilteredReservations(){
					/**
					 * List out dependecies, which trigger this function re-run
					 * Like, hey 'watch on these properties, if you change it, i recompute
					 */
					let reservations   = this.reservations;
					let filter_options = this.filter_options;
					/**
					 * Special case
					 * When filter by confirm_id exist
					 * Only run this one, no need to apply other
					 * @warn which check is best practice
					 */
					let filters_by_confirm_id = filter_options.filter(filter => filter.type == FILTER_TYPE_CONFIRM_ID);
					// If exist a filter by confirm id option
					// Only run this one
					if(filters_by_confirm_id.length > 0){
						// Get this first one
						filter_options = [filters_by_confirm_id[0]];
					}
					// Loop through each filter_options, run on current reservations
					let filtered_reservations =
						filter_options.reduce((carry, filter) => {
							// aplly current filter
							let _f_reservations = carry.filter(filter);
							// return result for next row call filter on
							return _f_reservations;
						}, reservations);

					// Update filtered reservations
					this.filtered_reservations = filtered_reservations;
				},
				outlet_id(outlet_id){
					let data = {outlet_id};
					document.dispatchEvent(new CustomEvent('outlet_id', {detail: data}));
				}
			},
			methods: {
				_reservationDetailDialog(e){
					try{
						let tr = this._findTrElement(e);
						this._remarksAsStaffRead(tr);
						//Clone it into reservation dialog content
						let reservation_id  = tr.getAttribute('reservation-id');
						let picked_reservation = this.reservations.filter(reservation => reservation.id == reservation_id)[0];
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
					try{
						let reservation_index  = tr.getAttribute('reservation-index');
						let picked_reservation = this.reservations[reservation_index];
						//Update reservations staff_read
						picked_reservation.staff_read_state = true;
					}catch(e){}
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

				_updateReservationPayment(e, which_payment){
					//console.log(e);
					let vue = this;
					let button = e.target;
					if(button.tagName == 'BUTTON'){
						try{
							//let action = button.getAttribute('action');
							let reservation_index  = button.getAttribute('reservation-index');
							let picked_reservation = vue.reservations[reservation_index];
							
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

				_autoSave(reservation = null, key = null){
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
					let new_filter_statuses = [];
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

				_addFilterByConfirmId(confirm_id){
					let filter = (reservation) => {
						return reservation.confirm_id == confirm_id;
					};

					if(confirm_id == ""){
						//filter = () => {return true};
						// I want a default filter as return true
						// But check in array of filters, if it appear
						// Only run it, not other

						// Ok, remove it
						let new_filter_options = this.filter_options.filter(filter => filter.type != FILTER_TYPE_CONFIRM_ID);
						this.filter_options    = new_filter_options;
						return;
					}

					let iFilter = this._createFilter(filter, {name: 'filter by confirm id', type: FILTER_TYPE_CONFIRM_ID, priority: 1});

					this._addNewFilter(iFilter);
				},

				_toggleFilterSearch(){
					// Toggle it
					this.filter_search = !this.filter_search;
					// If staff want to search
					// Build confirm id to search
					// If not remove filter
					let new_filter_confirm_id;
					if(this.filter_search){
						// Get confirm_id from input
						let confirm_id = this.filter_confirm_id;
						// Transform to uppercase
						let uppercase_confirm_id = confirm_id ? confirm_id.toUpperCase() : '';
						// Update vue state
						new_filter_confirm_id    = uppercase_confirm_id;
					}else{
						new_filter_confirm_id    = "";
					}

					// Update vue state
					this.filter_confirm_id = new_filter_confirm_id;

					this._addFilterByConfirmId(new_filter_confirm_id);
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
					console.log('fetch for me, please');
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
						'email',
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
				let {available_time} = res.data;
				
				store.dispatch({type: UPDATE_AVAILABLE_TIME, available_time});

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
			default:
				break;

		}
	}

	ajax_call_error(res_literal){
		console.log(res_literal);
		// Please don't modify these code
		let res = res_literal.responseJSON;

		if(res && res.statusMsg && res.errorMsg){
			window.alert(res.errorMsg);
		}else{
			window.alert(JSON.stringify(res_literal));
		}
		// When fall case happen
		// Should refetch page
		let store = window.store;
		window.alert('We are refetching data');
		store.dispatch({type: REFETCHING_DATA});
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
			// Dispatch calling ajax
			let store = window.store;
			store.dispatch({type: CALLING_AJAX, is_calling_ajax: true});

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
			base_url = path.substr(1);
		}

		if(path.startsWith('/')){
			path = path.substr(1);
		}

		let url = `${base_url}/${path}`;

		if(url.endsWith('/')){
			url = path.substr(1);
		}

		return url;
	}
}

let adminReservations = new AdminReservations();