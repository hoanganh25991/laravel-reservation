'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';
var CHANGE_FORM_STEP = 'CHANGE_FORM_STEP';

var CHANGE_CUSTOMER_PHONE_COUNTRY_CODE = 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE';
var CHANGE_OUTLET = 'CHANGE_OUTLET';
var CHANGE_ADULT_PAX = 'CHANGE_ADULT_PAX';
var CHANGE_CHILDREN_PAX = 'CHANGE_CHILDREN_PAX';
var HAS_SELECTED_DAY = 'HAS_SELECTED_DAY';
var CHANGE_RESERVATION_DATE = 'CHANGE_RESERVATION_DATE';
var CHANGE_RESERVATION_TIME = 'CHANGE_RESERVATION_TIME';
var CHANGE_RESERVATION_CONFIRM_ID = 'CHANGE_RESERVATION_CONFIRM_ID';
var CHANGE_AVAILABLE_TIME = 'CHANGE_AVAILABLE_TIME';

var PAX_OVER = 'PAX_OVER';
var AJAX_CALL = 'AJAX_CALL';
var DIALOG_SHOW_HIDE = 'DIALOG_SHOW_HIDE';
var DIALOG_HAS_DATA = 'DIALOG_HAS_DATA';
var DIALOG_HIDDEN = 'DIALOG_HIDDEN';
var DIALOG_EXCEED_MIN_EXIST_TIME = 'DIALOG_EXCEED_MIN_EXIST_TIME';

var CHANGE_CUSTOMER_SALUTATION = 'CHANGE_CUSTOMER_SALUTATION';
var CHANGE_CUSTOMER_FIRST_NAME = 'CHANGE_CUSTOMER_FIRST_NAME';
var CHANGE_CUSTOMER_LAST_NAME = 'CHANGE_CUSTOMER_LAST_NAME';
var CHANGE_CUSTOMER_EMAIL = 'CHANGE_CUSTOMER_EMAIL';
var CHANGE_CUSTOMER_PHONE = 'CHANGE_CUSTOMER_PHONE';
var CHANGE_CUSTOMER_REMARKS = 'CHANGE_CUSTOMER_REMARKS';

var SYNC_RESERVATION = 'SYNC_RESERVATION';

var AJAX_SEARCH_AVAILABLE_TIME = 'AJAX_SEARCH_AVAILABLE_TIME';
var AJAX_SUBMIT_BOOKING = 'AJAX_SUBMIT_BOOKING';

var AJAX_AVAILABLE_TIME_FOUND = 'AJAX_AVAILABLE_TIME_FOUND';
var AJAX_RESERVATION_VALIDATE_FAIL = 'AJAX_RESERVATION_VALIDATE_FAIL';
var AJAX_RESERVATION_NO_LONGER_AVAILABLE = 'AJAX_RESERVATION_NO_LONGER_AVAILABLE';
var AJAX_RESERVATION_REQUIRED_DEPOSIT = 'AJAX_RESERVATION_REQUIRED_DEPOSIT';
var AJAX_RESERVATION_SUCCESS_CREATE = 'AJAX_RESERVATION_SUCCESS_CREATE';
var AJAX_BOOKING_CONDITION_VALIDATE_FAIL = 'AJAX_BOOKING_CONDITION_VALIDATE_FAIL';
//const CHANGE_RESERVATION_DEPOSIT = 'CHANGE_RESERVATION_DEPOSIT';
//const AJAX_PAYMENT_REQUEST = 'AJAX_PAYMENT_REQUEST';

//const AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
var AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
var AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';

// const AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';

var BookingForm = function () {
	/** @namespace res.statusMsg */
	/** @namespace action.adult_pax */
	/** @namespace action.children_pax */
	/** @namespace action.dialog_has_data */
	/** @namespace action.exceed_min_exist_time */

	/** @namespace window.booking_form_state */
	/** @namespace $ */
	/** @namespace moment */
	/** @namespace Vue */

	function BookingForm() {
		_classCallCheck(this, BookingForm);

		this.buildRedux();

		this.buildVue();

		this.event();

		this.listener();

		this.view();

		this.initView();

		BookingForm.logObjectAssignPerformance();
	}

	_createClass(BookingForm, [{
		key: 'buildRedux',
		value: function buildRedux() {
			var default_state = this.defaultState();
			var self = this;
			var rootReducer = function rootReducer() {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : default_state;
				var action = arguments[1];

				switch (action.type) {
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
			};

			// window.store = Redux.createStore(reducer);
			window.store = Redux.createStore(rootReducer);

			/**
    * Enhance store with prestate
    */
			var o_dispatch = store.dispatch;
			store.dispatch = function (action) {
				console.info(action.type);
				store.prestate = store.getState();
				store.last_action = action.type;
				o_dispatch(action);
			};

			store.getPrestate = function () {
				return store.prestate;
			};

			store.getLastAction = function () {
				return store.last_action;
			};
		}
	}, {
		key: 'getFrontendState',
		value: function getFrontendState() {
			var state = {
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
				customer: {
					salutation: 'Mr.'
				},
				pax_over: "block"
			};

			return state;
		}
	}, {
		key: 'defaultState',
		value: function defaultState() {
			var server_state = window.state || {};

			var frontend_state = this.getFrontendState();

			var state = Object.assign(frontend_state, server_state);

			if (state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')) {
				state = Object.assign(state, {
					customer: {
						salutation: 'Mr.',
						first_name: 'Anh',
						last_name: 'Le Hoang',
						email: 'lehoanganh25991@gmail.com',
						phone_country_code: '+84',
						phone: '903865657',
						remarks: 'hello world'
					}
				});
			}

			return state;
		}
	}, {
		key: 'buildVueState',
		value: function buildVueState() {
			var vue_state = Object.assign({}, store.getState());

			return vue_state;
		}
	}, {
		key: 'buildVue',
		value: function buildVue() {
			window.vue_state = this.buildVueState();

			var sekf = this;

			this.vue = new Vue({
				el: '#form-step-container',
				data: window.vue_state
			});
		}
	}, {
		key: 'paxOverReducer',
		value: function paxOverReducer(state, action) {
			// console.log(action);
			switch (action.type) {
				case 'PAX_OVER':
					return 'none';
				default:
					return state;
			}
		}
	}, {
		key: 'customerReducer',
		value: function customerReducer(state, action) {
			switch (action.type) {
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
	}, {
		key: 'outletReducer',
		value: function outletReducer(state, action) {
			switch (action.type) {
				case CHANGE_OUTLET:
					return action.outlet;
				default:
					return state;
			}
		}
	}, {
		key: 'paxReducer',
		value: function paxReducer(state, action) {
			switch (action.type) {
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
	}, {
		key: 'reservationReducer',
		value: function reservationReducer(state, action) {
			switch (action.type) {
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
					var new_state = Object.assign(state, action.reservation);
					return new_state;
				default:
					return state;
			}
		}
	}, {
		key: 'dialogReducer',
		value: function dialogReducer(state, action) {
			switch (action.type) {
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
							exceed_min_exist_time: false
						}
					});
				default:
					return state;
			}
		}
	}, {
		key: 'availableTimeReducer',
		value: function availableTimeReducer(state, action) {
			switch (action.type) {
				case CHANGE_AVAILABLE_TIME:
					if (Array.isArray(action.available_time)) {
						action.available_time = {};
					}
					// return Object.assign({}, state, action.available_time);
					return action.available_time;
				default:
					return state;
			}
		}
	}, {
		key: 'initViewReducer',
		value: function initViewReducer(state, action) {
			switch (action.type) {
				case INIT_VIEW:
					return true;
				default:
					return state;
			}
		}
	}, {
		key: 'ajaxCallReducer',
		value: function ajaxCallReducer(state, action) {
			switch (action.type) {
				case AJAX_CALL:
					return Number(state) + 1;
				default:
					return state;
			}
		}
	}, {
		key: 'hasSelectedDayReducer',
		value: function hasSelectedDayReducer(state, action) {
			switch (action.type) {
				case HAS_SELECTED_DAY:
					return true;
				default:
					return state;
			}
		}
	}, {
		key: 'formStepReducer',
		value: function formStepReducer(state, action) {
			switch (action.type) {
				case CHANGE_FORM_STEP:
					return action.form_step;
				default:
					return state;
			}
		}
	}, {
		key: 'listener',
		value: function listener() {
			var store = window.store;
			var self = this;
			store.subscribe(function () {
				// if(store.SELF_DISPATCH_FLAG == true){
				// 	store.SELF_DISPATCH_FLAG = false;
				// 	return;
				// }

				var state = store.getState();
				var prestate = store.getPrestate();
				var last_action = store.getLastAction();

				if (prestate.ajax_call < state.ajax_call) {
					self.ajaxCall();
				}

				if (prestate.dialog.show == false && state.dialog.show == true) {
					self.ajax_dialog.modal('show');
				}

				if (prestate.dialog.show == true && state.dialog.show == false) {
					self.ajax_dialog.modal('hide');
				}

				var is_dialog_hide_self_loop = last_action == DIALOG_SHOW_HIDE && state.dialog.show == false;
				var dialog_has_data_reach_exist_time = state.dialog.stop.has_data == true && state.dialog.stop.exceed_min_exist_time == true;
				var should_hide_dialog = !is_dialog_hide_self_loop && dialog_has_data_reach_exist_time;
				if (should_hide_dialog) {
					store.dispatch({
						type: DIALOG_SHOW_HIDE,
						show: false
					});
				}

				var has_pax_over_dependency = last_action == CHANGE_ADULT_PAX || last_action == CHANGE_CHILDREN_PAX;

				var pax_over_below = state.pax.adult + state.pax.children < state.overall_min_pax;
				var pax_over_over = state.pax.adult + state.pax.children > state.overall_max_pax;

				var is_pax_over = has_pax_over_dependency && (pax_over_below || pax_over_over);

				if (is_pax_over) {
					// store.dispatch({type: PAX_OVER});
					window.alert('Total number of people should be between ' + state.overall_min_pax + ' - ' + state.overall_max_pax + ' ');
				}

				if (prestate.has_selected_day == false && state.has_selected_day == true) {
					store.dispatch({ type: AJAX_CALL, ajax_call: 1 });
				}

				var has_ajax_dependency = last_action == CHANGE_ADULT_PAX || last_action == CHANGE_CHILDREN_PAX || last_action == CHANGE_OUTLET || last_action == CHANGE_RESERVATION_DATE;

				var has_query_condition_change = state.has_selected_day && (prestate.pax.adult != state.pax.adult || prestate.pax.children != state.pax.children || prestate.outlet.id != state.outlet.id || prestate.reservation.date != state.reservation.date);

				var should_call_ajax = has_ajax_dependency && has_query_condition_change;
				if (should_call_ajax) {
					store.dispatch({ type: AJAX_CALL, ajax_call: 1 });
				}
			});
		}
	}, {
		key: 'view',
		value: function view() {
			var _this = this;

			this.findView();
			var store = window.store;
			var self = this;
			/**
    * Debug state
    */
			var pre = document.querySelector('#redux-state');
			if (!pre) {
				var body = document.querySelector('body');
				pre = document.createElement('pre');
				body.appendChild(pre);
			}

			store.subscribe(function () {
				var state = store.getState();
				//update this way for vue see it
				Object.assign(window.vue_state, state);

				//debug
				var prestate = store.getPrestate();
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

				/**
     * Available time change
     * @type {boolean}
     */
				var available_time_change = prestate.available_time != state.available_time;
				if (available_time_change) {
					_this.updateSelectView(state.available_time);
					_this.updateCalendarView(state.available_time);
				}
				/**
     * Form step change
     */
				var form_step_change = prestate.form_step != state.form_step || prestate.init_view == false && state.form_step == 'form-step-1';
				if (form_step_change) {
					console.info('pointToFormStep');
					_this.pointToFormStep();
				}
			});
		}
	}, {
		key: 'initView',
		value: function initView() {
			var action = {
				type: 'INIT_VIEW'
			};

			store.dispatch(action);
		}
	}, {
		key: '_findView',
		value: function findView() {
			if (typeof this._hasRunFindView == 'undefined') {
				this._hasRunFindView = true;
			} else {
				console.info('_findView has run');
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
			this.customer_remarks_textarea = document.querySelector('textarea[name="remarks"]');
			this.customer_firt_name_input = document.querySelector('input[name="first_name"]');
			this.customer_last_name_input = document.querySelector('input[name="last_name"]');
			this.customer_email_input = document.querySelector('input[name="email"]');
			this.customer_phone_input = document.querySelector('input[name="phone"]');

			/**
    * Swap view
    */
			this.form_step_container = document.querySelector('#form-step-container');
			this.btn_form_nexts = document.querySelectorAll('button.btn-form-next');
		}
	}, {
		key: 'updateSelectView',
		value: function updateSelectView(available_time) {
			var state = store.getState();
			var reservation_date = state.reservation.date;
			var selected_day_str = reservation_date.format('YYYY-MM-DD');

			var available_time_on_selected_day = available_time[selected_day_str];
			if (typeof available_time_on_selected_day == 'undefined') {
				// console.info('No available time on select day');
				// return;
				available_time_on_selected_day = [];
			}

			if (available_time_on_selected_day.length == 0) {
				var default_time = {
					time: 'N/A',
					session_name: ''
				};

				available_time_on_selected_day.push(default_time);
			}

			var time_select = this.time_select;

			var newInnerHtml = available_time_on_selected_day.reduce(function (carry, time) {
				var option = '<option value="' + time.time + '">' + time.session_name + ' ' + time.time + '</option>';
				carry += option;

				return carry;
			}, '');

			// requestAnimationFrame(()=>{time_select.innerHTML = newInnerHtml;});
			time_select.innerHTML = newInnerHtml;
			store.dispatch({ type: CHANGE_RESERVATION_TIME, time: time_select.selectedOptions[0].value });
		}
	}, {
		key: 'updateCalendarView',
		value: function updateCalendarView(available_time) {
			var calendar = this.calendar;

			if (Object.keys(available_time).length == 0) return;

			this._addCalendarHelper(calendar);
			//Get out all available day
			var available_days = Object.keys(available_time);

			calendar.day_tds.each(function () {
				var td = $(this);
				var td_day_str = td.attr('year') + '-' + calendar._prefix2Dec(td.attr('month')) + '-' + calendar._prefix2Dec(td.attr('day'));

				if (available_days.includes(td_day_str)) {
					calendar._pickable(td);
				} else {
					calendar._unpickable(td);
				}
			});
		}
	}, {
		key: '_addCalendarHelper',
		value: function _addCalendarHelper(calendar) {
			calendar.day_tds = $('#calendar-box').find('td');

			if (!calendar._prefix2Dec || !calendar._pickable || calendar._unpickable) {
				calendar._prefix2Dec = function (val) {
					if (val < 10) return '0' + val;

					return val;
				};

				calendar._pickable = function (td) {
					td.removeClass('past');
					td.addClass('day');
				};

				calendar._unpickable = function (td) {
					td.removeClass('day');
					td.addClass('past');
				};
			}
		}
	}, {
		key: 'event',
		value: function event() {
			var _this2 = this;

			this.findView();
			var store = window.store;

			var outlet_select = this.outlet_select;
			outlet_select.addEventListener('change', function () {
				var selectedOption = outlet_select.selectedOptions[0];

				store.dispatch({
					type: CHANGE_OUTLET,
					outlet: {
						id: selectedOption.value,
						name: selectedOption.innerText
					}
				});

				// self.computeAjaxCall();
			});

			var adult_pax_select = this.adult_pax_select;
			adult_pax_select.addEventListener('change', function () {
				var selectedOption = adult_pax_select.selectedOptions[0];

				store.dispatch({
					type: CHANGE_ADULT_PAX,
					adult_pax: selectedOption.value
				});

				// self.computePaxOver();

				// self.computeAjaxCall();
			});

			var children_pax_select = this.children_pax_select;
			children_pax_select.addEventListener('change', function () {
				var selectedOption = children_pax_select.selectedOptions[0];

				store.dispatch({
					type: CHANGE_CHILDREN_PAX,
					children_pax: selectedOption.value
				});

				// self.computePaxOver();

				// self.computeAjaxCall();
			});

			document.addEventListener('user-select-day', function (e) {
				var date = moment(e.detail.day, 'YYYY-MM-DD');

				store.dispatch({
					type: CHANGE_RESERVATION_DATE,
					date: date
				});

				var state = store.getState();
				if (state.has_selected_day == false) {
					store.dispatch({ type: HAS_SELECTED_DAY });
				}

				// self.computeAjaxCall();
			});

			var time_select = this.time_select;
			// time_select.addEventListener('DOMSubtreeModified', function(){
			// 	console.log('time_select modified');
			// 	store.dispatch({type: CHANGE_RESERVATION_TIME, time: time_select.options[0].value});
			// });
			time_select.addEventListener('change', function () {
				console.log('time change');
				var selectedOption = time_select.selectedOptions[0];

				var action = {
					type: CHANGE_RESERVATION_TIME,
					time: selectedOption.value
				};

				store.dispatch(action);
			});

			var btn_form_nexts = this.btn_form_nexts;
			btn_form_nexts.forEach(function (btn) {

				btn.addEventListener('click', function () {
					var destination = btn.getAttribute('destination');
					store.dispatch({ type: CHANGE_FORM_STEP, form_step: destination });

					if (destination == 'form-step-3') {
						store.dispatch({ type: AJAX_CALL, ajax_call: 1 });
					}
				});
			});
			/**
    * Handle customer change info
    */
			this.customer_salutation_select.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var salutation = this.selectedOptions[0].value;
				store.dispatch({ type: CHANGE_CUSTOMER_SALUTATION, salutation: salutation });
			});

			this.customer_firt_name_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var first_name = this.value;
				store.dispatch({ type: CHANGE_CUSTOMER_FIRST_NAME, first_name: first_name });
			});

			this.customer_last_name_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var last_name = this.value;
				store.dispatch({ type: CHANGE_CUSTOMER_LAST_NAME, last_name: last_name });
			});

			this.customer_email_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var email = this.value;
				store.dispatch({ type: CHANGE_CUSTOMER_EMAIL, email: email });
			});

			this.customer_phone_country_code_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var phone_country_code = this.value;
				store.dispatch({ type: CHANGE_CUSTOMER_PHONE_COUNTRY_CODE, phone_country_code: phone_country_code });
			});

			this.customer_phone_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var phone = this.value;
				store.dispatch({ type: CHANGE_CUSTOMER_PHONE, phone: phone });
			});

			this.customer_remarks_textarea.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var remarks = this.value;
				store.dispatch({ type: CHANGE_CUSTOMER_REMARKS, remarks: remarks });
			});

			this.ajax_dialog.on('hidden.bs.modal', function () {
				store.dispatch({ type: DIALOG_HIDDEN });
			});

			this.ajax_dialog.on('shown.bs.modal', function () {
				var state = store.getState();
				var timeId = setTimeout(function () {
					var state = store.getState();
					if (state.dialog.show == true) {
						store.dispatch({ type: DIALOG_EXCEED_MIN_EXIST_TIME, exceed_min_exist_time: true });
					}
					clearTimeout(timeId);
				}, state.dialog.min_exist_time);
			});

			/**
    * Handle payment success
    */
			document.addEventListener('PAYPAL_PAYMENT_SUCCESS', function (e) {
				console.log(e);
				var res = e.detail;

				/**
     * in this case, res.data should contain reservation
     */
				var reservation = res.data.reservation;
				Object.assign(vue_state, { reservation: reservation });

				store.dispatch({
					type: SYNC_RESERVATION,
					reservation: reservation
				});
			});

			document.addEventListener('calendar-change-month', function (e) {
				var state = store.getState();
				_this2.updateCalendarView(state.available_time);
			});
		}
	}, {
		key: 'ajaxCall',
		value: function ajaxCall() {
			// console.info('ajax call');
			var store = window.store;
			var state = store.getState();
			var self = this;

			store.dispatch({ type: DIALOG_SHOW_HIDE, show: true });

			var data = {
				outlet_id: state.outlet.id,
				adult_pax: state.pax.adult,
				children_pax: state.pax.children,
				type: AJAX_SEARCH_AVAILABLE_TIME
			};

			if (state.form_step == 'form-step-3') {
				var _state$reservation = state.reservation,
				    date = _state$reservation.date,
				    time = _state$reservation.time;

				var reservation_timestamp = date.format('YYYY-MM-DD') + ' ' + time + ':00';
				var _state$customer = state.customer,
				    salutation = _state$customer.salutation,
				    first_name = _state$customer.first_name,
				    last_name = _state$customer.last_name,
				    email = _state$customer.email,
				    phone_country_code = _state$customer.phone_country_code,
				    phone = _state$customer.phone,
				    remarks = _state$customer.remarks;

				data = Object.assign(data, {
					// reservation_date: date.format('Y-M-D'),
					// reservation_time: time,
					reservation_timestamp: reservation_timestamp,
					salutation: salutation,
					first_name: first_name,
					last_name: last_name,
					email: email,
					phone_country_code: phone_country_code,
					phone: phone,
					customer_remarks: remarks,
					type: AJAX_SUBMIT_BOOKING
				});
			}

			console.log(data);

			$.ajax({
				url: '',
				method: 'POST',
				data: data,
				success: function success(res) {
					console.log(res);
					//noinspection JSValidateTypes
					if (res.statusMsg == AJAX_RESERVATION_SUCCESS_CREATE) {
						// let data = res.data;
						// let {confirm_id} = data;
						var reservation = res.data.reservation;
						// let {confirm_id} = reservation;
						// store.dispatch({
						// 	type: CHANGE_RESERVATION_CONFIRM_ID,
						// 	confirm_id,
						// });
						//update reservation
						Object.assign(vue_state, { reservation: reservation });

						store.dispatch({
							type: SYNC_RESERVATION,
							reservation: reservation
						});
						return;
					}

					/**
      * Default case, search for avaialble time
      * When call ajax
      */
					//noinspection JSValidateTypes
					if (res.statusMsg == AJAX_AVAILABLE_TIME_FOUND) {
						var _data = res.data;

						store.dispatch({
							type: CHANGE_AVAILABLE_TIME,
							available_time: _data
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
				complete: function complete(res) {
					console.log(res);

					store.dispatch({
						type: DIALOG_HAS_DATA,
						dialog_has_data: true
					});
				},
				error: function error(res) {
					//console.log(res);
					res = res.responseJSON;
					if (res.statusMsg == AJAX_BOOKING_CONDITION_VALIDATE_FAIL) {
						var msg = 'Booking condition validate fail';

						window.alert(msg);
						return;
					}
					/**
      * Need update confirm_id
      * Only not for searching available_time
      */
					//noinspection JSValidateTypes
					if (res.statusMsg == AJAX_RESERVATION_NO_LONGER_AVAILABLE) {
						var _data2 = res.data;
						var _msg = 'SORRY, Someone has book before you. Rerservation no longer available';

						console.log(_msg, res.data);
						window.alert(_msg);
						return;
					}

					//noinspection JSValidateTypes
					if (res.statusMsg == AJAX_RESERVATION_REQUIRED_DEPOSIT) {
						var reservation = res.data.reservation;
						// let {confirm_id} = reservation;
						// store.dispatch({
						// 	type: CHANGE_RESERVATION_CONFIRM_ID,
						// 	confirm_id,
						// });
						//update reservation
						Object.assign(vue_state, { reservation: reservation });

						store.dispatch({
							type: SYNC_RESERVATION,
							reservation: reservation
						});

						var _data3 = res.data;
						var _msg2 = 'REQUIRED DEPOSIT, payment amount: ';

						// store.dispatch({
						// 	type: CHANGE_RESERVATION_DEPOSIT,
						// 	deposit: data.deposit
						// });
						var amount = reservation.deposit;
						var confirm_id = reservation.confirm_id;
						var outlet_id = reservation.outlet_id;
						var token = _data3.paypal_token;

						//noinspection ES6ModulesDependencies
						var base_url = self.url('paypal');
						var paypal_authorize = new PayPalAuthorize(token, { amount: amount, confirm_id: confirm_id, outlet_id: outlet_id }, base_url);

						//self.vue.reservation.deposit = amount;

						//$('#paypal-dialog').modal('show');

						console.log(_msg2, res.data);
						//window.alert(msg);
						//store.dispatch({type: PAX_OVER});
						return;
					}

					if (res.statusMsg == AJAX_RESERVATION_VALIDATE_FAIL) {
						var info = JSON.stringify(res.data);
						var _msg3 = 'VALIDATE FAIL: ' + info;

						console.log(_msg3, res.data);
						window.alert(_msg3);

						var form_step = 'form-step-1';

						try {
							var first_key = Object.keys(res.data)[0];

							var _store = window.store;
							var _state = _store.getState();

							var customer_info_keys = Object.keys(_state.customer);

							if (customer_info_keys.indexOf(first_key) != -1) {
								form_step = 'form-step-2';
							}
						} catch (e) {}

						store.dispatch({
							type: CHANGE_FORM_STEP,
							form_step: form_step
						});
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
	}, {
		key: 'url',
		value: function url() {
			var path = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

			var store = window.store;
			var state = store.getState();

			//noinspection JSUnresolvedVariable
			var base_url = state.base_url || '';

			if (base_url.endsWith('/')) {
				base_url = path.substr(1);
			}

			if (path.startsWith('/')) {
				path = path.substr(1);
			}

			var url = base_url + '/' + path;

			if (url.endsWith('/')) {
				url = path.substr(1);
			}

			return url;
		}
	}, {
		key: 'pointToFormStep',
		value: function pointToFormStep() {
			var state = store.getState();

			var form_step_container = this.form_step_container;
			form_step_container.querySelectorAll('.form-step').forEach(function (step) {
				var form_step = step.getAttribute('id');
				var transform = 'scale(0,0)';
				if (form_step == state.form_step) {
					transform = 'scale(1,1)';
				}

				step.style.transform = transform;
			});
		}
	}], [{
		key: 'logObjectAssignPerformance',
		value: function logObjectAssignPerformance() {
			var o_assign = Object.assign;

			Object.assign = function () {
				for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
					args[_key] = arguments[_key];
				}

				if (Object.keys(args[0]).length > 0) {
					console.time('obj assign');
					o_assign.apply(Object, args);
					console.timeEnd('obj assign');
				}

				return o_assign.apply(Object, args);
			};
		}
	}]);

	return BookingForm;
}();

var bookingForm = new BookingForm();