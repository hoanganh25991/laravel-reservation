const INIT_VIEW			= 'INIT_VIEW';
const CHANGE_FORM_STEP	= 'CHANGE_FORM_STEP';

const CHANGE_CUSTOMER_PHONE_COUNTRY_CODE = 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE';
const CHANGE_OUTLET					= 'CHANGE_OUTLET';
const CHANGE_ADULT_PAX				= 'CHANGE_ADULT_PAX';
const CHANGE_CHILDREN_PAX			= 'CHANGE_CHILDREN_PAX';
const HAS_SELECTED_DAY	            = 'HAS_SELECTED_DAY';
const CHANGE_RESERVATION_DATE		= 'CHANGE_RESERVATION_DATE';
const CHANGE_RESERVATION_TIME		= 'CHANGE_RESERVATION_TIME';
const CHANGE_RESERVATION_CONFIRM_ID = 'CHANGE_RESERVATION_CONFIRM_ID';
const CHANGE_AVAILABLE_TIME	        = 'CHANGE_AVAILABLE_TIME';

const PAX_OVER 			= 'PAX_OVER';
const AJAX_CALL			= 'AJAX_CALL';
const DIALOG_SHOW_HIDE  = 'DIALOG_SHOW_HIDE';
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

const AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
const AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
const AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';

// const AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';

class BookingForm {
	/** @namespace res.statusMsg */
	/** @namespace action.adult_pax */
	/** @namespace action.children_pax */
	/** @namespace action.dialog_has_data */
	/** @namespace action.exceed_min_exist_time */

	/** @namespace window.booking_form_state */
	/** @namespace $ */
	/** @namespace moment */
	/** @namespace Vue */

	constructor(){
		this.buildRedux();

		this.buildVue();

		this.event();

		this.listener();

		this.view();

		this.initView();


		BookingForm.logObjectAssignPerformance();
	}

	static logObjectAssignPerformance(){
		let o_assign = Object.assign;

		Object.assign = function(...args){
			if(Object.keys(args[0]).length > 0){
				console.time('obj assign');
				o_assign.apply(Object, args);
				console.timeEnd('obj assign');
			}

			return o_assign.apply(Object, args);

		}
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
				case CHANGE_OUTLET:
					return Object.assign({}, state, {
						outlet: self.outletReducer(state.outlet, action)
					});
				case CHANGE_ADULT_PAX:
				case CHANGE_CHILDREN_PAX:
					return Object.assign({}, state, {
						pax: self.paxReducer(state.pax, action)
					});
				case PAX_OVER:
					return Object.assign({}, state, {
						pax_over: self.paxOverReducer(state.pax_over, action)
					});
				case HAS_SELECTED_DAY:
					return Object.assign({}, state, {
						has_selected_day: self.hasSelectedDayReducer(state.has_selected_day, action)
					});
				case CHANGE_RESERVATION_DATE:
				case CHANGE_RESERVATION_TIME:
				case CHANGE_RESERVATION_CONFIRM_ID:
				case SYNC_RESERVATION:
					return Object.assign({}, state, {
						reservation: self.reservationReducer(state.reservation, action)
					});
				case AJAX_CALL:
					return Object.assign({}, state, {
						ajax_call: self.ajaxCallReducer(state.ajax_call, action)
					});
				case DIALOG_SHOW_HIDE:
				case DIALOG_HAS_DATA:
				case DIALOG_EXCEED_MIN_EXIST_TIME:
				case DIALOG_HIDDEN:
					return Object.assign({}, state, {
						dialog: self.dialogReducer(state.dialog, action)
					});
				case CHANGE_AVAILABLE_TIME:
					return Object.assign({}, state, {
						available_time: self.availableTimeReducer(state.available_time, action)
					});
				case CHANGE_CUSTOMER_SALUTATION:
				case CHANGE_CUSTOMER_FIRST_NAME:
				case CHANGE_CUSTOMER_LAST_NAME:
				case CHANGE_CUSTOMER_EMAIL:
				case CHANGE_CUSTOMER_PHONE_COUNTRY_CODE:
				case CHANGE_CUSTOMER_PHONE:
				case CHANGE_CUSTOMER_REMARKS:
					return Object.assign({}, state, {
						customer: self.customerReducer(state.customer, action)
					});
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
		let server_state = {};
		// if(window.booking_form_state){
		// 	server_state = window.booking_form_state;
		// }
		if(window.state){
			server_state = window.state;
		}


		let frontend_state = {
			init_view: false,
			outlet: {},
			overall_min_pax: 2,
			overall_max_pax: 20,
			pax: {
				adult: 1,
				children: 0
			},
			reservation: {
				date: moment(),
				time: ''
			},
			dialog: {
				show: false,
				stop: {
					has_data: false,
					exceed_min_exist_time: false
				},
				min_exist_time: 690 //ms
				//min_exist_time: 5000 //ms
			},
			available_time: {},
			ajax_call: 0,
			has_selected_day: false,
			form_step: 'form-step-1',
			// customer: {
			// 	salutation: 'Mr.',
			// 	first_name: 'Anh',
			// 	last_name : 'Le Hoang',
			// 	email: 'lehoanganh25991@gmail.com',
			// 	phone_country_code: '+84',
			// 	phone: '903865657',
			// 	remarks: 'hello world'
			// },
			customer: {
				salutation: 'Mr.',
			},
			pax_over: "block"
		};
		
		let state = Object.assign(frontend_state, server_state);

		this.state = state;

		return this.state;
	}

	getVueState(){
		if(typeof window.vue_state == 'undefined'){
			window.vue_state = Object.assign({}, store.getState());
		}

		return window.vue_state;
	}

	buildVue(){
		let vue_state = this.getVueState();

		// let form_vue = new Vue({
		this.vue = new Vue({
			el: '#form-step-container',
			data: vue_state
		});
		// this.form_vue = form_vue;
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
			case CHANGE_OUTLET:
				return action.outlet;
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
			case SYNC_RESERVATION:
				let new_state = Object.assign(state, action.reservation);
				return new_state;
			default:
				return state;
		}
	}

	dialogReducer(state, action){
		switch(action.type){
			case DIALOG_SHOW_HIDE:
				return Object.assign({}, state, {
					show: action.show
				});
			case DIALOG_HAS_DATA:
				state.stop.has_data = action.dialog_has_data;
				return JSON.parse(JSON.stringify(state));
			case DIALOG_EXCEED_MIN_EXIST_TIME:
				state.stop.exceed_min_exist_time = action.exceed_min_exist_time;
				return JSON.parse(JSON.stringify(state));
			case DIALOG_HIDDEN:
				return Object.assign({}, state, {
					show: false,
					stop: {
						has_data: false,
						exceed_min_exist_time: false,
					}
				});
			default:
				return state;
		}
	}

	availableTimeReducer(state, action){
		switch(action.type){
			case CHANGE_AVAILABLE_TIME:
				if(Array.isArray(action.available_time)){
					action.available_time = {};
				}
				// return Object.assign({}, state, action.available_time);
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

			if(prestate.dialog.show == false && state.dialog.show == true){
				self.ajax_dialog.modal('show');
			}

			if(prestate.dialog.show == true && state.dialog.show == false){
				self.ajax_dialog.modal('hide');
			}

			let is_dialog_hide_self_loop = last_action == DIALOG_SHOW_HIDE && state.dialog.show == false;
			let dialog_has_data_reach_exist_time = state.dialog.stop.has_data == true && state.dialog.stop.exceed_min_exist_time == true;
			let should_hide_dialog  = !is_dialog_hide_self_loop && dialog_has_data_reach_exist_time;
			if(should_hide_dialog){
				store.dispatch({
					type: DIALOG_SHOW_HIDE,
					show: false
				});
			}

			let has_pax_over_dependency =
				(last_action == CHANGE_ADULT_PAX
				|| last_action == CHANGE_CHILDREN_PAX);

			let pax_over_below =(state.pax.adult + state.pax.children) < state.overall_min_pax;
			let pax_over_over =(state.pax.adult + state.pax.children)  > state.overall_max_pax;
			
			let is_pax_over = has_pax_over_dependency && (pax_over_below || pax_over_over);
			
			if(is_pax_over){
				// store.dispatch({type: PAX_OVER});
				window.alert(`Over all pax should between ${state.overall_min_pax} - ${state.overall_max_pax} `);
			}

			if(prestate.has_selected_day == false && state.has_selected_day == true){
				store.dispatch({type: AJAX_CALL, ajax_call: 1});
			}

			let has_ajax_dependency =
				last_action == CHANGE_ADULT_PAX
				|| last_action == CHANGE_CHILDREN_PAX
				|| last_action == CHANGE_OUTLET
				|| last_action == CHANGE_RESERVATION_DATE;

			let has_query_condition_change =
				state.has_selected_day
				&& (prestate.pax.adult != state.pax.adult
				||prestate.pax.children != state.pax.children
				||prestate.outlet.id != state.outlet.id
				||prestate.reservation.date != state.reservation.date);

			let should_call_ajax = has_ajax_dependency && has_query_condition_change;
			if(should_call_ajax){
				store.dispatch({type: AJAX_CALL, ajax_call: 1});
			}


		});
	}

	view(){
		this.findView();
		let store = window.store;
		let self = this;
		/**
		 * Debug state
		 */
		let pre = document.querySelector('#redux-state');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			body.appendChild(pre);
		}

		store.subscribe(()=>{
			let state    = store.getState();
			//update this way for vue see it
			let vue_state = self.getVueState();
			Object.assign(vue_state, state);

			//debug
			let prestate = store.getPrestate();
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

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
		});


	}

	initView(){
		let action = {
			type: 'INIT_VIEW'
		}

		store.dispatch(action);
	}

	findView(){
		if(typeof this._hasRunFindView == 'undefined'){
			this._hasRunFindView = true;
		}else{
			console.info('findView has run');
			return;
		}


		this.calendar = $('#calendar-box').Calendar();

		this.adult_pax_select = document.querySelector('select[name="adult_pax"]');
		this.children_pax_select = document.querySelector('select[name="children_pax"]');

		this.ajax_dialog = $('#ajax-dialog');

		this.outlet_select = document.querySelector('select[name="outlet_id"]');

		this.time_select = document.querySelector('select[name="reservation_time"]');

		/**
		 * Customer info
		 */
		this.customer_phone_country_code_input = document.querySelector('input[name="phone_country_code"]');
		this.customer_salutation_select = document.querySelector('select[name="salutation"]');
		this.customer_remarks_textarea  = document.querySelector('textarea[name="remarks"]');
		this.customer_firt_name_input   = document.querySelector('input[name="first_name"]');
		this.customer_last_name_input   = document.querySelector('input[name="last_name"]');
		this.customer_email_input       = document.querySelector('input[name="email"]');
		this.customer_phone_input       = document.querySelector('input[name="phone"]');

		/**
		 * Swap view
		 */
		this.form_step_container = document.querySelector('#form-step-container');
		this.btn_form_nexts      = document.querySelectorAll('button.btn-form-next');
	}

	updateSelectView(available_time) {
		let state = store.getState();
		let reservation_date = state.reservation.date;
	    let selected_day_str = reservation_date.format('YYYY-MM-DD');

	    let available_time_on_selected_day = available_time[selected_day_str];
	    if (typeof available_time_on_selected_day == 'undefined'){
			// console.info('No available time on select day');
		    // return;
		    available_time_on_selected_day = [];
	    }

		if (available_time_on_selected_day.length == 0) {
	        let default_time = {
	            time: 'N/A',
	            session_name: ''
	        };

	        available_time_on_selected_day.push(default_time);
	    }

		let time_select = this.time_select;

		let newInnerHtml = available_time_on_selected_day.reduce((carry, time) => {
			let option = `<option value="${time.time}">${time.session_name} ${time.time}</option>`;
			carry += option;

			return carry;
		}, '');

		// requestAnimationFrame(()=>{time_select.innerHTML = newInnerHtml;});
		time_select.innerHTML = newInnerHtml;
		store.dispatch({type: CHANGE_RESERVATION_TIME, time: time_select.selectedOptions[0].value});
	}

	updateCalendarView(available_time) {
		let calendar = this.calendar;
		if(Object.keys(available_time).length == 0)
			return

		
		this._addCalendarHelper(calendar);

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
		if(typeof calendar.day_tds == 'undefined'){
			calendar.day_tds = $('#calendar-box').find('td');
		}
		
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

	event(){
		this.findView();
		let store = window.store;

		let outlet_select = this.outlet_select;
		outlet_select.addEventListener('change', function(){
			let selectedOption = outlet_select.selectedOptions[0];

			store.dispatch({
				type: CHANGE_OUTLET,
				outlet: {
					id: selectedOption.value,
					name: selectedOption.innerText
				}
			});

			// self.computeAjaxCall();
		});

		let adult_pax_select = this.adult_pax_select;
		adult_pax_select.addEventListener('change', function(){
			let selectedOption = adult_pax_select.selectedOptions[0];

			store.dispatch({
				type: CHANGE_ADULT_PAX,
				adult_pax: selectedOption.value
			});

			// self.computePaxOver();

			// self.computeAjaxCall();
		});

		let children_pax_select = this.children_pax_select;
		children_pax_select.addEventListener('change', function(){
			let selectedOption = children_pax_select.selectedOptions[0];

			store.dispatch({
				type: CHANGE_CHILDREN_PAX,
				children_pax: selectedOption.value
			});

			// self.computePaxOver();

			// self.computeAjaxCall();
		});

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

		let time_select = this.time_select;
		// time_select.addEventListener('DOMSubtreeModified', function(){
		// 	console.log('time_select modified');
		// 	store.dispatch({type: CHANGE_RESERVATION_TIME, time: time_select.options[0].value});
		// });
		time_select.addEventListener('change', function(){
			console.log('time change');
			let selectedOption = time_select.selectedOptions[0];

			let action = {
				type: CHANGE_RESERVATION_TIME,
				time: selectedOption.value
			};

			store.dispatch(action);
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
		 * Handle customer change info
		 */
		this.customer_salutation_select
			.addEventListener('change', function(){
				//binding in this way to get out this as email input
				let salutation = this.selectedOptions[0].value;
				store.dispatch({type: CHANGE_CUSTOMER_SALUTATION, salutation});
			});

		this.customer_firt_name_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let first_name = this.value;
			    store.dispatch({type: CHANGE_CUSTOMER_FIRST_NAME, first_name});
		    });

		this.customer_last_name_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let last_name = this.value;
			    store.dispatch({type: CHANGE_CUSTOMER_LAST_NAME, last_name});
		    });

		this.customer_email_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let email = this.value;
			    store.dispatch({type: CHANGE_CUSTOMER_EMAIL, email});
		    });

		this.customer_phone_country_code_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let phone_country_code = this.value;
			    store.dispatch({type: CHANGE_CUSTOMER_PHONE_COUNTRY_CODE, phone_country_code});
		    });

		this.customer_phone_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let phone = this.value;
			    store.dispatch({type: CHANGE_CUSTOMER_PHONE, phone});
		    });

		this.customer_remarks_textarea
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let remarks = this.value;
			    store.dispatch({type: CHANGE_CUSTOMER_REMARKS, remarks});
		    });

		this.ajax_dialog
			.on('hidden.bs.modal', function(){
				store.dispatch({type: DIALOG_HIDDEN});
			});

		this.ajax_dialog
			.on('shown.bs.modal', function(){
				let state = store.getState();
				let timeId = setTimeout(function(){
					let state = store.getState();
					if(state.dialog.show == true){
						store.dispatch({type: DIALOG_EXCEED_MIN_EXIST_TIME, exceed_min_exist_time: true});
					}
					clearTimeout(timeId);
				}, state.dialog.min_exist_time);
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
	}

	ajaxCall(){
		// console.info('ajax call');
		let store = window.store;
		let state = store.getState();
		let self  = this;

		store.dispatch({type: DIALOG_SHOW_HIDE, show: true});

		
		let data = {
			outlet_id: state.outlet.id,
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
					// let data = res.data;
					// let {confirm_id} = data;
					let reservation = res.data.reservation;
					// let {confirm_id} = reservation;
					// store.dispatch({
					// 	type: CHANGE_RESERVATION_CONFIRM_ID,
					// 	confirm_id,
					// });
					//update reservation
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

				// if(res.statusMsg == AJAX_PAYMENT_REQUEST_SUCCESS){
				// 	$('#paypal-dialog').modal('hide');
				// 	console.log(res);
				// 	console.log('success payment');
				// 	return;
				// }
			},
			complete(res){
				console.log(res);

				store.dispatch( {
					type: DIALOG_HAS_DATA,
					dialog_has_data: true
				});
			},
			error(res){
				//console.log(res);
				res = res.responseJSON;
				if(res.statusMsg == AJAX_BOOKING_CONDITION_VALIDATE_FAIL){
					let msg = 'Booking condition validate fail';

					window.alert(msg);
					return;
				}
				/**
				 * Need update confirm_id
				 * Only not for searching available_time
				 */
				//noinspection JSValidateTypes
				if(res.statusMsg == AJAX_RESERVATION_NO_LONGER_AVAILABLE){
					let data = res.data;
					let msg  = 'SORRY, Someone has book before you. Rerservation no longer available';

					console.log(msg, res.data);
					window.alert(msg);
					return;
				}

				//noinspection JSValidateTypes
				if(res.statusMsg == AJAX_RESERVATION_REQUIRED_DEPOSIT){
					let reservation = res.data.reservation;
					// let {confirm_id} = reservation;
					// store.dispatch({
					// 	type: CHANGE_RESERVATION_CONFIRM_ID,
					// 	confirm_id,
					// });
					//update reservation
					Object.assign(vue_state, {reservation});
					
					store.dispatch({
						type: SYNC_RESERVATION,
						reservation,
					});

					let data = res.data;
					let msg = 'REQUIRED DEPOSIT, payment amount: ';
					
					// store.dispatch({
					// 	type: CHANGE_RESERVATION_DEPOSIT,
					// 	deposit: data.deposit
					// });
					let amount     = reservation.deposit
					let confirm_id = reservation.confirm_id;
					let token      = data.paypal_token;

					//noinspection ES6ModulesDependencies
					let base_url = self.url('paypal');
					let paypal_authorize = new PayPalAuthorize(token, {amount, confirm_id}, base_url);
					
					//self.vue.reservation.deposit = amount;

					//$('#paypal-dialog').modal('show');
					
					console.log(msg, res.data);
					//window.alert(msg);
					//store.dispatch({type: PAX_OVER});
					return;
				}

				if(res.statusMsg == AJAX_RESERVATION_VALIDATE_FAIL){
					let msg = 'VALIDATE FAIL: ';

					console.log(msg, res.data);
					window.alert(msg);
					return;
				}

				// if(res.statusMsg == AJAX_PAYMENT_REQUEST_VALIDATE_FAIL
				// || res.statusMsg == AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL
				// || res.statusMsg == AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL){
				// 	let msg = 'PAYPAL FAIL: see log';
				//
				// 	console.log(msg, res.data);
				// 	window.alert(msg);
				// 	return;
				// }
			}
		});
	}

	url(path){
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

		return `${base_url}/${path}`;
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

