const INIT_VIEW			= 'INIT_VIEW';
const CHANGE_FORM_STEP	= 'CHANGE_FORM_STEP';

const CHANGE_CUSTOMER_PHONE_COUNTRY_CODE = 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE';
const CHANGE_SELECTED_OUTLET_ID					= 'CHANGE_SELECTED_OUTLET_ID';
const CHANGE_ADULT_PAX				= 'CHANGE_ADULT_PAX';
const CHANGE_CHILDREN_PAX			= 'CHANGE_CHILDREN_PAX';
const HAS_SELECTED_DAY	            = 'HAS_SELECTED_DAY';
const CHANGE_RESERVATION            = 'CHANGE_RESERVATION';
const CHANGE_RESERVATION_DATE		= 'CHANGE_RESERVATION_DATE';
const CHANGE_RESERVATION_TIME		= 'CHANGE_RESERVATION_TIME';
const CHANGE_RESERVATION_CONFIRM_ID = 'CHANGE_RESERVATION_CONFIRM_ID';
const CHANGE_AVAILABLE_TIME	        = 'CHANGE_AVAILABLE_TIME';
const SELECT_PAX                    = 'SELECT_PAX';

const PAX_OVER 			= 'PAX_OVER';
const AJAX_CALL			= 'AJAX_CALL';
const DIALOG_SHOW       = 'DIALOG_SHOW';
const DIALOG_HAS_DATA	= 'DIALOG_HAS_DATA';
const DIALOG_HIDDEN		= 'DIALOG_HIDDEN';
const DIALOG_EXCEED_MIN_EXIST_TIME = 'DIALOG_EXCEED_MIN_EXIST_TIME';

const CHANGE_CUSTOMER_SALUTATION  = 'CHANGE_CUSTOMER_SALUTATION';
const CHANGE_CUSTOMER_FIRST_NAME  = 'CHANGE_CUSTOMER_FIRST_NAME';
const CHANGE_CUSTOMER_LAST_NAME	  = 'CHANGE_CUSTOMER_LAST_NAME';
const CHANGE_CUSTOMER_EMAIL		  = 'CHANGE_CUSTOMER_EMAIL';
const CHANGE_CUSTOMER_PHONE 	  = 'CHANGE_CUSTOMER_PHONE';
const CHANGE_CUSTOMER_REMARKS	  = 'CHANGE_CUSTOMER_REMARKS';

const SYNC_RESERVATION = 'SYNC_RESERVATION';

const AJAX_SEARCH_AVAILABLE_TIME  = 'AJAX_SEARCH_AVAILABLE_TIME';
const AJAX_SUBMIT_BOOKING         = 'AJAX_SUBMIT_BOOKING';

const AJAX_AVAILABLE_TIME_FOUND = 'AJAX_AVAILABLE_TIME_FOUND';
const AJAX_RESERVATION_VALIDATE_FAIL = 'AJAX_RESERVATION_VALIDATE_FAIL';
const AJAX_RESERVATION_NO_LONGER_AVAILABLE = 'AJAX_RESERVATION_NO_LONGER_AVAILABLE';
const AJAX_RESERVATION_REQUIRED_DEPOSIT = 'AJAX_RESERVATION_REQUIRED_DEPOSIT';
const AJAX_RESERVATION_SUCCESS_CREATE = 'AJAX_RESERVATION_SUCCESS_CREATE';
const AJAX_BOOKING_CONDITION_VALIDATE_FAIL = 'AJAX_BOOKING_CONDITION_VALIDATE_FAIL';
//const CHANGE_RESERVATION_DEPOSIT = 'CHANGE_RESERVATION_DEPOSIT';
//const AJAX_PAYMENT_REQUEST = 'AJAX_PAYMENT_REQUEST';

//const AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
//const AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
//const AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';

// const AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';
const SYNC_VUE_STATE = 'SYNC_VUE_STATE';
const UPDATE_CALENDAR_VIEW = 'UPDATE_CALENDAR_VIEW';
const NO_DATE_PICKED = 'NO_DATE_PICKED';
const PAYPAL_BUTTON_SHOW = 'PAYPAL_BUTTON_SHOW';
const PAYPAL_BUTTON_HIDE = 'PAYPAL_BUTTON_HIDE';
const CHANGE_PAX = 'CHANGE_PAX';

class BookingForm {
	/** @namespace selected_outlet.paypal_currency */
	/** @namespace res.statusMsg */
	/** @namespace res.responseJSON */
	/** @namespace action.adult_pax */
	/** @namespace action.children_pax */
	/** @namespace action.dialog_has_data */
	/** @namespace action.exceed_min_exist_time */

	/** @namespace window.booking_form_state */
	/** @namespace $ */
	/** @namespace moment */
	/** @namespace Vue */
	/** @namespace selected_outlet.overall_min_pax */
	/** @namespace selected_outlet.overall_max_pax */
	/** @namespace selected_outlet.max_days_in_advance */

	constructor(){
		this.buildRedux();

		this.buildVue();

		// init view
		//this.initView();
	}

	buildRedux(){
		let default_state = this.defaultState();
		let self = this;
		let rootReducer = function(state = default_state, action){
			switch(action.type){
				case INIT_VIEW:
					return Object.assign({}, state, {init_view: true});
				case CHANGE_FORM_STEP:
					return Object.assign({}, state, {form_step: action.form_step});
				case HAS_SELECTED_DAY:
					return Object.assign({}, state, {has_selected_day: true});
				case CHANGE_RESERVATION_DATE:{
					let reservation = Object.assign({}, state.reservation, {date: action.date});

					return Object.assign({}, state, {reservation});
				}
				case SYNC_RESERVATION:{
					let reservation = Object.assign({}, action.reservation);
					return Object.assign({}, state, {reservation});
				}
				case AJAX_CALL:
					return Object.assign({}, state, {
						ajax_call: self.ajaxCallReducer(state.ajax_call, action)
					});
				case DIALOG_SHOW: {
					return Object.assign({}, state, {dialog: true});
				}
				case DIALOG_HAS_DATA:{
					return Object.assign({}, state, {dialog: false});
				}
				case CHANGE_AVAILABLE_TIME: {
					return Object.assign({}, state, {available_time: action.available_time});
				}
				case SYNC_VUE_STATE:{
					return Object.assign({}, state, action.vue_state);
				}
				case PAYPAL_BUTTON_HIDE:{
					return Object.assign({}, state, {paypal_button: false});
				}
				case PAYPAL_BUTTON_SHOW:{
					return Object.assign({}, state, {paypal_button: true});
				}
				default:
					return state;
			}
		}

		// window.store = Redux.createStore(reducer);
		window.store = Redux.createStore(rootReducer);

		/**
		 * Enhance store with prestate
		 */
		let o_dispatch = store.dispatch;
		store.dispatch = function(action){
			console.info(action.type);
			store.prestate = store.getState();
			store.last_action = action.type;
			o_dispatch(action);
		}

		store.getPrestate = function(){
			return store.prestate;
		}

		store.getLastAction = function(){
			return store.last_action;
		}
	}

	defaultState(){
		let server_state = window.state || {};

		let frontend_state = {
			init_view: false,
			base_url: '',
			selected_outlet: {},
			selected_outlet_id: null,
			outlets: [],
			reservation: {
				outlet_id: null,
				adult_pax: 0,
				children_pax: 0,
				reservation_timestamp: 'be computed base on date & time',
				agree_term_condition: null,
				salutation: 'Mr.',
				first_name: '',
				last_name : '',
				email: '',
				phone_country_code: '+65',
				phone: '',
				customer_remarks: ''
			},
			dialog: {},
			available_time: {},
			has_selected_day: false,
			form_step: 'form-step-1',
			form_step_1_keys: [
				'outlet_id',
				'adult_pax',
				'children_pax',
				'agree_term_condition',
				'date',
				'time',
			],
			form_step_2_keys: [
				'salutation',
				'first_name',
				'last_name',
				'email',
				'phone_country_code',
				'phone',
			],
			paypal_button: false,
		};;

		let state = Object.assign(frontend_state, server_state);

		// For dev mode, quick insert default value
		if(state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')){
			let reservation = Object.assign(state.reservation, {
				salutation: 'Mr.',
				first_name: 'Anh',
				last_name : 'Le Hoang',
				email: 'lehoanganh25991@gmail.com',
				phone_country_code: '+84',
				phone: '903865657',
				customer_remarks: 'hello world'
			})

			state = Object.assign(state, {reservation});
		}

		return state;
	}

	buildVueState(){
		// Vue own state to manage child view
		let vue_state = {
			// Store which selected outlet pick
			selected_outlet: {},
			selected_outlet_id: null,
			outlets: [],
			// Store reservation data
			reservation: {
				date: null,
				time: null,
				agree_term_condition: null,
				//Only show paypal authorization button, when customer accept term&condition
				agree_payment_term_condition: null,
			},
			// Handle time select box
			available_time: {},
			available_time_on_reservation_date: [],
			// Please don't change this
			// undefined here is the default value
			// to map with no time to pick
			no_answer_time: undefined,
			// Handle dynamic select pax
			adult_pax_select: {
				start: -1,
				end: 20,
			},
			children_pax_select: {
				start: -1,
				end: 20
			},
			change_pax: {
				current_label: null,
				times: 0
			},
			form_step_1_keys: [],
			form_step_2_keys: [],
			// Vue need dialog info, to manage show/hide on summary step
			dialog: null,
			// Vue need keys, to manage show|hide select time box
			// Or show msg, which better than select time box with N/A
			has_selected_day: null,
		};

		return vue_state;
	}

	buildVue(){
		window.vue_state = this.buildVueState();

		let self = this;

		this.vue = new Vue({
			el: '#form-step-container',
			data: window.vue_state,
			beforeCreated(){},
			created(){},
			beforeMount(){},
			mounted(){
				self.event();
				self.view();
				self.listener();

				let store = window.store;
				store.dispatch({type: INIT_VIEW});
			},
			beforeUpdate(){
				let store = window.store;
				let state = store.getState();

				// Ok  sync vue with redux-state
				store.dispatch({
					type: SYNC_VUE_STATE,
					vue_state: window.vue_state
				});

				// SYNC VUE STATE is clumsy event
				// Can't decide what has changed inside vue
				// Vue self check which improtant changed
				// Affect on UI
				// Notify it out for global handle
				try{
					let change_pax = state.change_pax.times < this.change_pax.times;
					if(change_pax){
						store.dispatch({type: CHANGE_PAX});
					}
				}catch(e){
					console.log('Cant resolve change_pax');
				}
			},
			updated(){},
			watch: {
				outlets(outlets){
					let first_outlet        = outlets[0] || {};
					this.selected_outlet_id = first_outlet.id;
				},
				selected_outlet_id(selected_outlet_id){
					// Update reservation
					let new_reservation = Object.assign({}, this.reservation, {outlet_id: selected_outlet_id});
					this.reservation    = new_reservation;
					// Update seleceted outlet base on
					let selected_outlets = this.outlets.filter(outlet => outlet.id == selected_outlet_id);
					this.selected_outlet = selected_outlets[0] || {};
				},
				available_time(available_time){
					// Build back available_time_on_reservation_date
					let reservation_date = this.reservation.date;
					let date_time_str    = reservation_date ? reservation_date.format('YYYY-MM-DD') : NO_DATE_PICKED;
					// Get out for specific day or default 'N/A'
					this.available_time_on_reservation_date = available_time[date_time_str] || [];
				},
				available_time_on_reservation_date(val){
					// When see this one change
					// In some way ask for update calendar view
					if(!this.reservation.time){
						let first_time = val[0] || {};
						let new_reservation = Object.assign({}, this.reservation, {time: first_time.time});

						this.reservation = new_reservation;
					}

					// User has pick one
					// BUTT this not in the new available time array
					// So.., repick the first one as default
					let find_in = val.filter(time_obj => time_obj.time == this.reservation.time);
					let is_in   = find_in.length > 0 ;
					if(this.reservation.time && !is_in){
						let first_time = val[0] || {};
						let new_reservation = Object.assign({}, this.reservation, {time: first_time.time});

						this.reservation = new_reservation;
					}
				},
				reservation(reservation){
					//console.log('See reservation update');

					if(!reservation.date && reservation.reservation_timestamp){
						//console.log('Update reservation moment date obj');
						let date = moment(reservation.reservation_timestamp, 'YYYY-MM-DD HH:mm:ss');

						Object.assign(reservation, {date});
					}
				}
			},
			methods: {
				// We check these keys on reservation
				// If it empty, not allow move next
				_checkEmpty(keys, except_keys = []){
					let reservation = this.reservation;

					let empty_keys =
						keys.filter(key => {
							if(except_keys.indexOf(key) != -1){
								return false;
							}

							let value = reservation[key];

							let isNumber = !isNaN(parseFloat(value)) && isFinite(value);

							if(isNumber){
								return false;
							}

							// If no data > empty key
							// Like undefined, '', null
							return !value;
						});

					return empty_keys.length > 0;
				},

				not_allowed_move_to_form_step_2(){
					let has_empty_keys = this._checkEmpty(this.form_step_1_keys);

					let reservation     = this.reservation;
					let selected_outlet = this.selected_outlet;
					// Get out total_pax to CROSS CHECK
					// Dynamic pax select in worst case not work well
					let total_pax = reservation.adult_pax + reservation.children_pax;
					let out_range = (total_pax < selected_outlet.overall_min_pax)
						|| (total_pax > selected_outlet.overall_max_pax);

					return has_empty_keys || out_range;
				},

				not_allowed_move_to_form_step_3(){
					let has_empty_keys =  this._checkEmpty(this.form_step_2_keys, ['remarks']);

					return has_empty_keys;
				},

				_changePax(which_pax){
					let times = this.change_pax.times;
					times++;
					this.change_pax = {
						current_label: which_pax,
						times
					};
				},

				_updatePaxSelectBox(which_pax){
					/**
					 * What trigger this function re-run
					 * As dependency of watcher
					 * Like: 'Watch these properties, if it change, call me'
					 */
					let selected_outlet     = this.selected_outlet;
					let reservation         = this.reservation;
					let adult_pax_select    = this.adult_pax_select;
					let children_pax_select = this.children_pax_select;
					let change_pax          = this.change_pax;

					// Determine which pax to base on
					// User change pax_x >>> base on pax_x
					let other_pax   = which_pax == 'adult_pax' ? 'children_pax' : 'adult_pax';
					let base_on_pax = change_pax.current_label ? change_pax.current_label : other_pax;
					let need_updated_pax_select = this[`${which_pax}_select`];
					// I'm the BASE
					// NO NEED TO UPDATE ME
					if(which_pax == base_on_pax){
						// Doesn't need to update me
						// Has run already
						let start = need_updated_pax_select.start;
						let end   = need_updated_pax_select.end;

						return (end - start);
					}
					// Minus for '1' to allow equal to minimum
					// Self loop of template, start at 'start'
					// (1,10) > 1,3,4,5,6,7,8,9,10
					// Instead of 0,1,2,3,4...
					let start = selected_outlet.overall_min_pax - reservation[base_on_pax] - 1;
					let end   = selected_outlet.overall_max_pax - reservation[base_on_pax];
					// When user first time pick up, allow him choose any thing he want
					// There are two select box, once for adult, once for children
					// Only remove check when count times >= 3
					if(!change_pax.current_label){
						start = -1;
						end   = this.selected_outlet.overall_max_pax;
					}
					// Limit start at 0, select for positive number.......
					start = start < -1 ? -1 : start;
					// Update pax_select back to vue_state
					let new_pax_select = {start, end};
					let should_update  = !self._shallowEqualObj(need_updated_pax_select, new_pax_select);
					if(should_update){
						this[`${which_pax}_select`] = new_pax_select;
					}
					// Handle case self pick for customer
					// When there pax size out of selectable range
					let pax_value = reservation[which_pax];
					let out_range = pax_value < (start + 1) || pax_value > end;
					let new_reservation = reservation;
					if(out_range){
						window.alert(`There is a minimum pax of ${this.selected_outlet.overall_min_pax} for reservation at this outlet`);
						let diff = (pax_value - start) + (pax_value - end);

						if(diff < 0){
							// Close to start
							pax_value = (start + 1);
						}else{
							// Close to end
							pax_value = end;
						}

						// Update new_reservation
						new_reservation = Object.assign({}, reservation, {[which_pax]: pax_value});
					}
					// Update vue_state
					this.reservation = new_reservation;

					// If can't resolve the range
					if(isNaN(end) || isNaN(start)){
						return 20;
					}

					return (end - start);
				},

				_submitBooking(){
					// Should not self decide
					// Decision must make be global
					// Who know exactly what is going on
					// Quick app, write in this way
					/** @warn   Loose from parent >>> lead to don't know where trigger
					 *          Parent manage whole things directly >>> easy to track what going on app
					 */
					self.ajaxCall({type: AJAX_SUBMIT_BOOKING});
				},

				_computeReservationTimestamp(date, time){
					// When date or time not specify, can't go ahead
					if(!date || !time)
						return;

					let moment_time = moment(time, 'HH:mm');
					let moment_date = date; //date already parsed

					if(!moment_date.isValid() || !moment_date.isValid()){
						console.warn('Why date, time specify but invalid when parsed???');
						return;
					}

					// date, time fine as moment obj
					let time_hour  = moment_time.hour();
					let time_minute= moment_time.minute();
					// Ok create a full date, time obj
					let date_time  = moment_date.clone().hour(time_hour).minute(time_minute);

					return date_time.format('YYYY-MM-DD HH:mm:ss');
				},

				_togglePaypalButton(){
					//console.log(this.reservation.agree_payment_term_condition);
					let paypal_button = this.reservation.agree_payment_term_condition;

					// Ok, sync state first
					// beforeUpdate is a good hook
					// But it not enough for complex case
					// Explicit tell redux what is going on
					store.dispatch({
						type: SYNC_VUE_STATE,
						vue_state: window.vue_state
					});

					// Hide or show paypal button
					// Base on 'agree_payment_term_condition'
					let show_hide_paypal_button = paypal_button ? PAYPAL_BUTTON_SHOW : PAYPAL_BUTTON_HIDE;
					store.dispatch({type: show_hide_paypal_button});
				},
			}
		});
	}

	_shallowEqualObj(objA, objB){
		if (Object.is(objA, objB)) {
			return true;
		}

		if (typeof objA !== 'object' || objA === null || typeof objB !== 'object' || objB === null) {
			return false;
		}

		var keysA = Object.keys(objA);
		var keysB = Object.keys(objB);

		if (keysA.length !== keysB.length) {
			return false;
		}

		// Test for A's keys different from B.
		for (var i = 0; i < keysA.length; i++) {
			if (!Object.prototype.hasOwnProperty.call(objB, keysA[i]) || !Object.is(objA[keysA[i]], objB[keysA[i]])) {
				return false;
			}
		}

		return true;
	}


	ajaxCallReducer(state, action){
		switch(action.type){
			case AJAX_CALL:
				return (Number(state) + 1);
			default:
				return state;
		}
	}

	event(){
		this._findView();
		let store = window.store;

		document.addEventListener('user-select-day', function(e){
			let date = moment(e.detail.day, 'YYYY-MM-DD');
			// Tell state, which date customer change
			store.dispatch({type: CHANGE_RESERVATION_DATE, date});

			// Oh, customer just pick a day
			// Dispatch to state
			let state              = store.getState();
			let still_not_pick_day = state.has_selected_day == false;
			if(still_not_pick_day)
				store.dispatch({type: HAS_SELECTED_DAY});
		});

		this.btn_form_nexts
		    .forEach((btn)=>{
			    btn.addEventListener('click', ()=>{
				    let destination = btn.getAttribute('destination');
				    store.dispatch({type: CHANGE_FORM_STEP, form_step: destination});
			    });
		    });

		/**
		 * Handle payment success
		 */
		document.addEventListener('PAYPAL_PAYMENT_SUCCESS', (e)=>{
			console.log(e);
			let res = e.detail;

			/**
			 * in this case, res.data should contain reservation
			 */
			let reservation = res.data.reservation;
			Object.assign(vue_state, {reservation});

			store.dispatch({
				type: SYNC_RESERVATION,
				reservation,
			});
		});

		document.addEventListener('calendar-change-month', (e) => {
			this.updateCalendarView();
		});
	}

	_changeBookingCondition(previous_reservation, reservation){
		let store = window.store;
		let last_action = store.getLastAction();

		// Code in this way issss clumsy to a function
		if(last_action == SYNC_RESERVATION){
			return false;
		}

		return previous_reservation.outlet_id != reservation.outlet_id
			|| previous_reservation.adult_pax != reservation.adult_pax
			|| previous_reservation.children_pax != reservation.children_pax
			|| previous_reservation.date != reservation.date;
	}

	view(){
		this._findView();
		let store = window.store;
		let self = this;

		//Debug state by redux_debug_html
		let redex_debug_element = document.querySelector('#redux-state');

		store.subscribe(()=>{
			let state    = store.getState();
			let prestate = store.getPrestate();
			let last_action = store.getLastAction();

			// Only run debug when needed & in local
			let on_local = state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost');
			if(redex_debug_element && on_local){
				let clone_state = Object.assign({}, state);
				// In case available_time so large
				if(clone_state.available_time){
					let keys = Object.keys(clone_state.available_time);
					if(keys.length > 14){
						delete clone_state.available_time;
						console.warn('available_time is large, debug build HTML will slow app, removed it');
					}
				}

				redex_debug_element.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
			}

			// Form step change
			let change_step    = prestate.form_step != state.form_step;
			// First time run, self point to the first one
			let run_first_step = prestate.init_view == false;
			if(change_step || run_first_step){
				self.pointToFormStep();
			}

			// Handle dialog
			if(last_action == DIALOG_SHOW){
				this.ajax_dialog.modal('show');
			}

			// Show dialog
			if(last_action == DIALOG_HAS_DATA){
				self.ajax_dialog.modal('hide');
			}

			// Update calendar view
			let first_time     = prestate.init_view == false;
			let outlet_changed = prestate.selected_outlet_id != state.selected_outlet_id;
			if(first_time || outlet_changed){
				self.updateCalendarView();
			}

			// Call ajax to search available time
			// Why still need this?
			// Event after sync, but deep keys just a reference
			// Can't see condition changed in state vs prestate
			// Explicit tell host changed pax
			let change_pax        = last_action == CHANGE_PAX;
			let changed_condition = self._changeBookingCondition(prestate.reservation, state.reservation) || change_pax;
			let just_select_day   = prestate.has_selected_day == false && state.has_selected_day == true;
			// Ok should call ajax for searching out available time
			if(state.has_selected_day && changed_condition || just_select_day){
				self.ajaxCall({type: AJAX_SEARCH_AVAILABLE_TIME});
			}

			// Ok update calendar view dynamic base on available_time
			if(last_action == CHANGE_AVAILABLE_TIME){
				self.updateCalendarView();
			}

			// Show paypal button
			if(last_action == PAYPAL_BUTTON_SHOW){
				self.paypal_button.style.transform = 'scale(1,1)';
			}

			// Hide paypal button
			if(last_action == PAYPAL_BUTTON_HIDE){
				self.paypal_button.style.transform = 'scale(0,1)';
			}

			// Redux state may just get sync from Vue
			// Then it updated, it talk back to Vue
			// After 2 times of SYNC
			// They are now in the same state
			Object.assign(window.vue_state, store.getState());
		});


	}

	listener(){}

	_findView(){
		if(this._hasRunFindView){
			return;
		}
		// Run only one time
		this._hasRunFindView = true;

		// For update calendar
		this.calendar = $('#calendar-box').Calendar();

		// Ajax dialog
		this.ajax_dialog = $('#ajax-dialog');

		// Change form step
		this.form_step_container = document.querySelector('#form-step-container');
		this.btn_form_nexts      = document.querySelectorAll('button.btn-form-next');
		this.paypal_button       = document.querySelector('#paypal-container');
	}

	updateCalendarView() {
		// Self get data from redux-state
		let state = store.getState();
		let available_time      = state.available_time;
		let selected_outlet     = state.selected_outlet;
		let max_days_in_advance = selected_outlet.max_days_in_advance;

		// Ok now check which day should disabled
		let calendar = this.calendar;

		// Build date_range base on max_days_in_advance
		// Start from today
		let today = moment();
		let date_range = [];
		let i = 0;
		while(i < max_days_in_advance){
			let current = today.clone().add(i, 'days');
			date_range.push(current);

			i++;
		}

		// Available days as arr of str
		let available_days = date_range.map(date => date.format('YYYY-MM-DD'));
		// Bind some helper function, only init one time
		this._addCalendarHelper(calendar);
		//Get out all available day
		calendar.day_tds.each(function() {
			let td    = $(this);
			// Read year, month, day stored in this td
			let year  = td.attr('year');
			let month = calendar._prefix2Dec(td.attr('month'));
			let day   = calendar._prefix2Dec(td.attr('day'));
			// Rebuild whole string
			let td_day_str = `${year}-${month}-${day}`;
			// Check if day is available
			// Style it
			let in_date_range = available_days.includes(td_day_str);
			let times_on_date = available_time[td_day_str];
			let has_time      = times_on_date ? times_on_date.length > 0 : false;
			let no_available_time_data = Object.keys(available_time).length == 0;

			if (in_date_range && (has_time || no_available_time_data)) {
				calendar._pickable(td);
			} else {
				calendar._unpickable(td);
			}
		});

	}

	_addCalendarHelper(calendar){
		// IMPORTANT, each time calendar change month
		// It rebuild calendar's day_tds
		// So, don't store this reference
		// re-search out which one
		calendar.day_tds = $('#calendar-box').find('td');

		// Bind some helper function into calendar
		if(!calendar._prefix2Dec || !calendar._pickable || !calendar._unpickable){
			calendar._prefix2Dec = function(val) {
				if (val < 10)
					return `0${val}`;

				return val;
			}

			calendar._pickable = function(td){
				td.removeClass('past');
				td.addClass('day');
			}

			calendar._unpickable = function(td){
				td.removeClass('day');
				td.addClass('past');
			}
		}
	}

	ajaxCall(action){
		let store = window.store;
		let state = store.getState();
		let self  = this;
		// Ask to show dialog
		store.dispatch({type: DIALOG_SHOW});
		console.log(`%c ajaxCall: ${action.type}`, 'background:#FDD835');

		let data = {};

		switch(action.type){
			case AJAX_SEARCH_AVAILABLE_TIME:{
				let {outlet_id, adult_pax, children_pax} = state.reservation;
				Object.assign(data, {outlet_id, adult_pax, children_pax}, {type: action.type});
				break;
			}
			case AJAX_SUBMIT_BOOKING:{
				Object.assign(data, state.reservation, {type: action.type});
				// Compute timestamp
				let vue = self.vue;
				let {date, time} = state.reservation;
				let timestamp    = vue._computeReservationTimestamp(date, time);
				//console.log(timestamp);
				// Add timestamp, requirement for submit booking
				data.reservation_timestamp = timestamp;
				// BCS of limit of AJAX from jQuery
				// We have to manually do this
				// Remove moment obj inside data
				delete data.date;
				break;
			}
			default: {
				break;
			}
		}

		console.log('ajaxCall: data', data);

		$.ajax({
			url: '',
			method: 'POST',
			data,
			success(res) {
				console.log(res);
				switch(res.statusMsg){
					case AJAX_AVAILABLE_TIME_FOUND: {
						let {available_time} = res.data;
						// Oh Yeah, we get available_time
						store.dispatch({
							type: CHANGE_AVAILABLE_TIME,
							available_time
						});
						break;
					}
					case AJAX_RESERVATION_SUCCESS_CREATE: {
						let {reservation} = res.data;
						// Ok, sync what from server
						store.dispatch({
							type: SYNC_RESERVATION,
							reservation,
						});
						break;
					}
					case AJAX_RESERVATION_REQUIRED_DEPOSIT: {
						let {reservation} = res.data;

						store.dispatch({
							type: SYNC_RESERVATION,
							reservation,
						});

						/**
						 * Init paypal
						 */
						let amount        = reservation.deposit;
						// currency accepted by this outlet
						let currency      = reservation.paypal_currency;
						let confirm_id    = reservation.confirm_id;
						let outlet_id     = reservation.outlet_id;
						let {paypal_token}= res.data;

						//noinspection ES6ModulesDependencies
						let base_url = self.url('paypal');
						// Create state data for paypal
						let paypal_options = {
							amount,
							currency,
							outlet_id,
							confirm_id,
						};
						let paypal_authorize = new PayPalAuthorize(paypal_token, paypal_options, base_url);

						break;
					}
					default:{
						console.warn('Unknown case of res.statusMsg');
						break;
					}
				}
			},
			error(res_literal){
				console.log(res_literal);
				//noinspection JSUnresolvedVariable
				//console.log(res_literal.responseJSON);
				// It quite weird that in browser window
				// Response as status code != 200
				// res obj now wrap by MANY MANY INFO
				// Please dont change this
				let res = res_literal.responseJSON;
				// Do normal things with res as in success case
				try{
					switch(res.statusMsg){
						case AJAX_BOOKING_CONDITION_VALIDATE_FAIL: {
							let info = JSON.stringify(res.data);
							window.alert(`Booking condition validate fail: ${info}`);
							break;
						}
						case AJAX_RESERVATION_NO_LONGER_AVAILABLE: {
							window.alert(`SORRY, Someone has book before you. Rerservation no longer available`);
							break;
						}
						case AJAX_RESERVATION_VALIDATE_FAIL: {
							let info = JSON.stringify(res.data);
							window.alert(`Validate fail: ${info}`);

							let form_step =  'form-step-1';

							// Try to move user to where he got mistake
							// When fullfill form
							try{
								let first_key = Object.keys(res.data)[0];

								// Simple list out all keys in form-step-2
								let form_step_2_keys = [
									'first_name',
									'last_name',
									'email',
									'phone_country_code',
									'phone'
								];

								// Move him to step 2 if validate fail key in
								if(form_step_2_keys.indexOf(first_key) != -1)
									form_step = 'form-step-2';

							}catch(e){}

							// Here we go ᕕ( ᐛ )ᕗ
							store.dispatch({
								type: CHANGE_FORM_STEP,
								form_step
							});

							break;
						}
						default: {
							break;
						}
					}
				}catch(e){
					//console.warn('Unknown case of res or has error in code', e);
					window.alert(JSON.stringify(res_literal));
				}
			},
			complete(){
				store.dispatch({type: DIALOG_HAS_DATA});
			},
		});
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

	pointToFormStep(){
		let state = store.getState();

		let form_step_container = this.form_step_container;
		form_step_container
			.querySelectorAll('.form-step')
			.forEach((step)=>{
				let form_step = step.getAttribute('id');
				let transform = 'scale(0,0)';
				if(form_step == state.form_step){
					transform = 'scale(1,1)';
				}

				step.style.transform = transform;
			});
	}
}

let bookingForm = new BookingForm();
