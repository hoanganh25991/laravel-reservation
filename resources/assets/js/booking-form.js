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
const DIALOG_SHOW  = 'DIALOG_SHOW';
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

class BookingForm {
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

	constructor(){
		this.buildRedux();

		this.buildVue();

		// init view
		this.initView();
	}

	buildRedux(){
		let default_state = this.defaultState();
		let self = this;
		let rootReducer = function(state = default_state, action){
			switch(action.type){
				case INIT_VIEW:
					return Object.assign({}, state, {
						init_view: self.initViewReducer(state.init_view, action)
					});
				case CHANGE_FORM_STEP:
					return Object.assign({}, state, {
						form_step: self.formStepReducer(state.form_step, action)
					});
				case HAS_SELECTED_DAY:
					return Object.assign({}, state, {
						has_selected_day: self.hasSelectedDayReducer(state.has_selected_day, action)
					});
				case CHANGE_RESERVATION_DATE:
				case CHANGE_RESERVATION_TIME:
				case CHANGE_RESERVATION_CONFIRM_ID:
				case CHANGE_RESERVATION:
				case SYNC_RESERVATION:
					return Object.assign({}, state, {
						reservation: self.reservationReducer(state.reservation, action)
					});
				case AJAX_CALL:
					return Object.assign({}, state, {
						ajax_call: self.ajaxCallReducer(state.ajax_call, action)
					});
				case DIALOG_SHOW:
				case DIALOG_HAS_DATA:
					return Object.assign({}, state, {
						dialog: self.dialogReducer(state.dialog, action)
					});
				case CHANGE_AVAILABLE_TIME:
					return Object.assign({}, state, {
						available_time: self.availableTimeReducer(state.available_time, action)
					});
				case SELECT_PAX:
					return Object.assign({}, state, {
						select_pax_times: self.selectPaxTimesReducer(state.select_pax_times, action)
					});
				case SYNC_VUE_STATE:{
					return Object.assign(state, action.vue_state);
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
				date: moment(),
				time: null,
				salutation: 'Mr.',
				first_name: 'Anh',
				last_name : 'Le Hoang',
				email: 'lehoanganh25991@gmail.com',
				phone_country_code: '+84',
				phone: '903865657',
				customer_remarks: 'hello world'
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
				'date'
			],
			form_step_2_keys: [
				'salutation',
				'first_name',
				'last_name',
				'email',
			],
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

		// Self pick the first outlet as selected_outlet_id
		let first_outlet         = state.outlets[0] || {};
		state.selected_outlet_id = first_outlet.id;
		state.selected_outlet    = first_outlet;

		return state;
	}

	buildVueState(){
		let store = window.store;
		let state = store.getState();

		// Vue own state to manage child view
		let vue_state = {
			// Store which selected outlet pick
			selected_outlet: {},
			selected_outlet_id: null,
			outlets: [],
			// Store reservation data
			reservation: {},
			// Handle time select box
			available_time_on_reservation_date: [],
			// Handle dynamic select pax
			adult_pax_select: {
				start: -1,
				end: null,
			},
			children_pax_select: {
				start: -1,
				end: null
			},
			has_changed_pax: null
		};

		// Sync with parent for things changed
		//this.syncVueStateWithParent(vue_state, state);
		Object.assign(vue_state, state);
		// Compute first time for pax select box
		let selected_outlet = vue_state.selected_outlet;
		Object.assign(vue_state, {
			adult_pax_select: {
				start: -1,
				end: selected_outlet.overall_max_pax,
			},
			children_pax_select: {
				start: -1,
				end: selected_outlet.overall_max_pax,
			},
		});

		// When init, reservation date consider as today
		// Self compute it
		//this.initVueState(vue_state);
		// Sorry but i don't watch on this obj
		// At config 90 days, available time is HUGE
		// delete vue_state.available_time;

		return vue_state;
	}

	buildVue(){
		window.vue_state = this.buildVueState();

		let self = this;

		this.vue = new Vue({
			el: '#form-step-container',
			data: window.vue_state,
			created(){},
			beforeMount(){
				store.dispatch({
					type: SYNC_VUE_STATE,
					vue_state: window.vue_state
				});
			},
			mounted(){
				self.event();
				self.view();
				self.listener();
			},
			beforeUpdate(){
				store.dispatch({
					type: SYNC_VUE_STATE,
					vue_state: window.vue_state
				});
			},
			updated(){},
			watch: {
				selected_outlet_id: function(val){
					// Update reservation
					let new_reservation = Object.assign({}, this.reservation, {outlet_id: val});
					this.reservation    = new_reservation;
					// Update seleceted outlet base on
					let selected_outlets = this.outlets.filter(outlet => outlet.id == val);
					this.selected_outlet = selected_outlets[0] || {};
				}
			},
			methods: {
				_checkEmpty(obj, except_keys = []){
					let empty_keys = Object.keys(obj).filter(key => {
						if(except_keys.indexOf(key) != -1){
							return false;
						}

						let value = obj[key];

						let isNumber = !isNaN(parseFloat(value)) && isFinite(value);

						if(isNumber){
							return false;
						}

						if(key == 'time' && value == 'N/A'){
							//this is the false case
							//no data select
							return true;
						}

						return !value;
					});

					return empty_keys.length > 0;
				},

				not_allowed_move_to_form_step_2(){
					// let has_empty_keys = this._checkEmpty(this.reservation);
					//
					// let total_pax = this.pax.adult + this.pax.children;
					//
					// let out_range = (total_pax < this.overall_min_pax) || (total_pax > this.overall_max_pax);
					//
					// return has_empty_keys || out_range;
				},

				not_allowed_move_to_form_step_3(){
					// let has_empty_keys = this._checkEmpty(this.customer, ['remarks']);
					// return has_empty_keys;
				},
				
				_changePax(which_pax){
					this.has_changed_pax = which_pax;
				},

				_fun(){
					let a = this.selected_outlet_id;
					let b = this.reservation;
					let x = Math.floor(Math.random()*10);
					return x;
				},

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

					// Determine which pax to base on
					// User change pax_x >>> base on pax_x
					let other_pax   = which_pax == 'adult_pax' ? 'children_pax' : 'adult_pax';
					let base_on_pax = this.has_changed_pax ? this.has_changed_pax : other_pax;
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
					if(!this.has_changed_pax){
						start = -1;
						end   = this.selected_outlet.overall_max_pax;
					}
					// Limit start at 0, select for positive number.......
					start = start < -1 ? -1 : start;
					// Update pax_select back to vue_state
					//if(adult_pax_select.)
					//this[other_pax_select] = {start, end};
					let new_pax_select = {start, end};
					let should_update  = !this._shallowEqualObj(need_updated_pax_select, new_pax_select);
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
					//this.reservation[which_pax] = pax_value;
					this.reservation = new_reservation;
					//Object.assign(this.reservation, {[other_pax]: pax_value});
					// Update to state, we ONLY HAVE ONE STATE
					// For whole app, place can be trusted
					// let store = window.store;
					// let vue   = this;
					// store.dispatch({
					// 	type: CHANGE_RESERVATION,
					// 	reservation: vue.reservation
					// });

					// Return for template loop
					// Return range to auto build <option>
					return (end - start);
				},
			}
		});
	}

	paxOverReducer(state, action){
		// console.log(action);
		switch(action.type){
			case 'PAX_OVER':
				return 'none';
			default:
				return state;
		}
	}

	customerReducer(state, action){
		switch(action.type){
			case CHANGE_CUSTOMER_SALUTATION:
				return Object.assign({}, state, {
					salutation: action.salutation
				});
			case CHANGE_CUSTOMER_FIRST_NAME:
				return Object.assign({}, state, {
					first_name: action.first_name
				});
			case CHANGE_CUSTOMER_LAST_NAME:
				return Object.assign({}, state, {
					last_name: action.last_name
				});
			case CHANGE_CUSTOMER_EMAIL:
				return Object.assign({}, state, {
					email: action.email
				});
			case CHANGE_CUSTOMER_PHONE_COUNTRY_CODE:
				return Object.assign({}, state, {
					phone_country_code: action.phone_country_code
				});
			case CHANGE_CUSTOMER_PHONE:
				return Object.assign({}, state, {
					phone: action.phone
				});
			case CHANGE_CUSTOMER_REMARKS:
				return Object.assign({}, state, {
					remarks: action.remarks
				});
			default:
				return state;
		}
	}

	outletReducer(state, action){
		switch(action.type){
			case CHANGE_SELECTED_OUTLET_ID:
				return action.selected_outlet;
			default:
				return state;
		}
	}

	paxReducer(state, action){
		switch(action.type){
			case CHANGE_ADULT_PAX:
				return Object.assign({}, state, {
					adult: Number(action.adult_pax)
				});
			case CHANGE_CHILDREN_PAX:
				return Object.assign({}, state, {
					children: Number(action.children_pax)
				});
			default:
				return state;
		}
	}

	reservationReducer(state, action){
		switch(action.type){
			case CHANGE_RESERVATION_DATE:
				return Object.assign({}, state, {
					date: action.date
				});
			case CHANGE_RESERVATION_TIME:
				return Object.assign({}, state, {
					time: action.time
				});
			case CHANGE_RESERVATION_CONFIRM_ID:
				return Object.assign({}, state, {
					confirm_id: action.confirm_id
				});
			case CHANGE_RESERVATION: {
				return action.reservation;
			}
			case SYNC_RESERVATION:
				let new_state = Object.assign(state, action.reservation);
				return new_state;
			default:
				return state;
		}
	}

	dialogReducer(state, action){
		switch(action.type){
			case DIALOG_SHOW:
			case DIALOG_HAS_DATA:
				return state;
			default:
				return state;
		}
	}

	availableTimeReducer(state, action){
		switch(action.type){
			case CHANGE_AVAILABLE_TIME:
				return action.available_time;
			default:
				return state;
		}
	}

	initViewReducer(state, action){
		switch(action.type){
			case INIT_VIEW:
				return true;
			default:
				return state;
		}
	}

	ajaxCallReducer(state, action){
		switch(action.type){
			case AJAX_CALL:
				return (Number(state) + 1);
			default:
				return state;
		}
	}

	hasSelectedDayReducer(state, action){
		switch(action.type){
			case HAS_SELECTED_DAY:
				return true;
			default:
				return state;
		}
	}

	formStepReducer(state, action){
		switch(action.type){
			case CHANGE_FORM_STEP:
				return action.form_step;
			default:
				return state;
		}
	}

	selectPaxTimesReducer(state, action){
		switch(action.type){
			case SELECT_PAX:
				// Increase count times
				let new_state = (state + 1);
				return new_state;
			default:
				return state;
		}
	}
	event(){
		this._findView();
		let store = window.store;

		document.addEventListener('user-select-day', function(e){
			let date = moment(e.detail.day, 'YYYY-MM-DD');

			store.dispatch({
				type: CHANGE_RESERVATION_DATE,
				date
			});

			let state = store.getState();
			if(state.has_selected_day == false){
				store.dispatch({type: HAS_SELECTED_DAY});
			}

			// self.computeAjaxCall();
		});

		let btn_form_nexts = this.btn_form_nexts;
		btn_form_nexts
			.forEach((btn)=>{

				btn.addEventListener('click', ()=>{
					let destination = btn.getAttribute('destination');
					store.dispatch({type: CHANGE_FORM_STEP, form_step: destination});

					if(destination == 'form-step-3'){
						store.dispatch({type: AJAX_CALL, ajax_call: 1});
					}
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
			let state = store.getState();
			this.updateCalendarView(state.available_time);
		});
	}

	view(){
		this._findView();
		let store = window.store;
		let self = this;
		/**
		 * Debug state
		 */
		let pre = document.querySelector('#redux-state');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			//body.appendChild(pre);
		}

		store.subscribe(()=>{
			let state    = store.getState();
			let last_action = store.getLastAction();

			//self.syncVueStateWithParent(window.vue_state, state);
			//Object.assign(window.vue_state, state);

			//debug
			let prestate = store.getPrestate();

			if(state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')){
				let clone_state = Object.assign({}, state);
				// In case available_time so large
				if(clone_state.available_time){
					let keys = Object.keys(clone_state.available_time);
					if(keys.length > 14){
						delete clone_state.available_time;
						console.warn('available_time is large, debug build HTML will slow app, removed it');
					}
				}

				pre.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
			}

			/**
			 * Available time change
			 * @type {boolean}
			 */
			let available_time_change = (prestate.available_time != state.available_time);
			if(available_time_change){
				this.updateSelectView(state.available_time);
				this.updateCalendarView(state.available_time);
			}
			/**
			 * Form step change
			 */
			let form_step_change = (prestate.form_step != state.form_step)
				|| (prestate.init_view == false
				&& state.form_step == 'form-step-1');
			if(form_step_change){
				console.info('pointToFormStep');
				this.pointToFormStep();
			}

			if(last_action == DIALOG_SHOW){
				this.ajax_dialog.modal('show');
			}

			if(last_action == DIALOG_HAS_DATA){
				this.ajax_dialog.modal('hide');
			}
		});


	}

	listener(){
		let store = window.store;
		let self = this;
		store.subscribe(()=>{
			// if(store.SELF_DISPATCH_FLAG == true){
			// 	store.SELF_DISPATCH_FLAG = false;
			// 	return;
			// }

			let state       = store.getState();
			let prestate    = store.getPrestate();
			let last_action = store.getLastAction();

			if(prestate.ajax_call < state.ajax_call){
				self.ajaxCall();
			}

			// Handle ajax call
			if(prestate.has_selected_day == false && state.has_selected_day == true){
				store.dispatch({type: AJAX_CALL, ajax_call: 1});
			}


		});
	}

	initView(){
		let action = {
			type: 'INIT_VIEW'
		}

		store.dispatch(action);
	}

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
	}

	updateCalendarView(available_time) {
		let calendar = this.calendar;

		if(Object.keys(available_time).length == 0)
			return

		this._addCalendarHelper(calendar);
		//Get out all available day
		let available_days = Object.keys(available_time);

		calendar.day_tds.each(function() {
			let td = $(this);
			let td_day_str = `${td.attr('year')}-${calendar._prefix2Dec(td.attr('month'))}-${calendar._prefix2Dec(td.attr('day'))}`;

			if (available_days.includes(td_day_str)) {
				calendar._pickable(td);
			} else {
				calendar._unpickable(td);
			}
		});

	}

	_addCalendarHelper(calendar){
		calendar.day_tds = $('#calendar-box').find('td');

		if(!calendar._prefix2Dec || !calendar._pickable || calendar._unpickable){
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

	ajaxCall(){
		// console.info('ajax call');
		let store = window.store;
		let state = store.getState();
		let self  = this;

		store.dispatch({type: DIALOG_SHOW, show: true});


		let data = {
			outlet_id: state.selected_outlet.id,
			adult_pax: state.pax.adult,
			children_pax: state.pax.children,
			type: AJAX_SEARCH_AVAILABLE_TIME,
		};

		if(state.form_step == 'form-step-3'){
			let {date, time} = state.reservation;
			let reservation_timestamp = `${date.format('YYYY-MM-DD')} ${time}:00`;
			let {salutation, first_name, last_name, email, phone_country_code, phone, remarks} = state.customer;
			data = Object.assign(data, {
				// reservation_date: date.format('Y-M-D'),
				// reservation_time: time,
				reservation_timestamp,
				salutation,
				first_name,
				last_name,
				email,
				phone_country_code,
				phone,
				customer_remarks: remarks,
				type: AJAX_SUBMIT_BOOKING,
			});

		}

		console.log(data);

		$.ajax({
			url: '',
			method: 'POST',
			data,
			success(res) {
				console.log(res);
				//noinspection JSValidateTypes
				if(res.statusMsg == AJAX_RESERVATION_SUCCESS_CREATE){
					let reservation = res.data.reservation;

					Object.assign(vue_state, {reservation});

					store.dispatch({
						type: SYNC_RESERVATION,
						reservation,
					});
					return;
				}

				/**
				 * Default case, search for avaialble time
				 * When call ajax
				 */
				//noinspection JSValidateTypes
				if(res.statusMsg == AJAX_AVAILABLE_TIME_FOUND){
					let data = res.data;

					store.dispatch({
						type: CHANGE_AVAILABLE_TIME,
						available_time: data
					});

					return;
				}
			},
			complete(res){
				//console.log(res);
				store.dispatch( {
					type: DIALOG_HAS_DATA,
					dialog_has_data: true
				});
			},
			error(res){
				//noinspection JSUnresolvedVariable
				console.log(res.responseJSON);
				try{
					let data_obj = res.responseJSON;
					let statusMsg= data_obj.statusMsg;
					switch(statusMsg){
						case AJAX_BOOKING_CONDITION_VALIDATE_FAIL: {
							let info = JSON.stringify(data_obj.data);
							window.alert(`Booking condition validate fail: ${info}`);
							break;
						}

						case AJAX_RESERVATION_NO_LONGER_AVAILABLE: {
							window.alert(`SORRY, Someone has book before you. Rerservation no longer available`);
							break;
						}

						case AJAX_RESERVATION_REQUIRED_DEPOSIT: {
							let reservation = data_obj.data.reservation;

							Object.assign(vue_state, {reservation});

							store.dispatch({
								type: SYNC_RESERVATION,
								reservation,
							});

							/**
							 * Init paypal
							 */
							let amount     = reservation.deposit;
							let confirm_id = reservation.confirm_id;
							let outlet_id  = reservation.outlet_id;
							let token      = data.paypal_token;

							//noinspection ES6ModulesDependencies
							let base_url = self.url('paypal');
							let paypal_authorize = new PayPalAuthorize(token, {amount, confirm_id, outlet_id}, base_url);

							break;
						}

						case AJAX_RESERVATION_VALIDATE_FAIL: {
							let info = JSON.stringify(data_obj.data);
							window.alert(`Validate fail: ${info}`);

							let form_step =  'form-step-1';

							// Try to move user to where he got mistake
							// When fullfill form
							try{
								let first_key = Object.keys(data_obj.data)[0];

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

				}catch(e){}
			}
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
