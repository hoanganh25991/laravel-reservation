'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var BookingForm = function () {
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
		this.bindView();
		this.regisEvent();

		this.bindListener();

		this.initView();
	}

	_createClass(BookingForm, [{
		key: 'buildRedux',
		value: function buildRedux() {
			//assign default state
			//may from server
			//or self build
			this.state = this.defaultState();
			var scope = this;
			var reducer = Redux.combineReducers({
				has_selected_day: scope.buildHasSelectedDayReducer(),
				available_time: scope.buildAvailableTimeReducer(),
				reservation: scope.buildReservationReducer(),
				ajax_call: scope.buildAjaxCallReducer(),
				init_view: scope.buildInitViewReducer(),
				form_step: scope.buildFormStepReducer(),
				customer: scope.buildCustomerReducer(),
				outlet: scope.buildOutletReducer(),
				dialog: scope.buildDialogReducer(),
				pax: scope.buildPaxReducer()
			});

			window.store = Redux.createStore(reducer);

			/**
    * Enhance store with prestate
    */
			var o_dispatch = store.dispatch;
			store.dispatch = function (action) {
				store.prestate = store.getState();
				o_dispatch(action);
			};

			store.getPrestate = function () {
				return store.prestate;
			};

			/**
    * Use vue to update data
    * self check too slow
    */
			window.state = store.getState();
			this.buildVue();
		}
	}, {
		key: 'buildVue',
		value: function buildVue() {
			window.vue_state = this.defaultState();
			var form_vue = new Vue({
				el: '#form-step-container',
				data: function data() {
					return window.vue_state;
				}
			});
			this.form_vue = form_vue;
		}
	}, {
		key: 'shallowEqual',
		value: function shallowEqual(objA, objB) {
			if (objA === objB) {
				return true;
			}

			var keysA = Object.keys(objA),
			    keysB = Object.keys(objB),
			    hasOwn = void 0;

			if (keysA.length !== keysB.length) {
				return false;
			}

			// Test for A's keys different from B.
			hasOwn = Object.prototype.hasOwnProperty;
			for (var i = 0; i < keysA.length; i++) {
				if (!hasOwn.call(objB, keysA[i]) || objA[keysA[i]] !== objB[keysA[i]]) {
					return false;
				}
			}
			return true;
		}
	}, {
		key: 'defaultState',
		value: function defaultState() {
			if (window.booking_form_state) return window.booking_form_state;

			var state = {
				init_view: false,
				outlet: {
					id: 1,
					name: 'HoiPOS Cafe (West)'
				},
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
					// min_exist_time: 5000 //ms
				},
				available_time: {},
				ajax_call: false,
				has_selected_day: false,
				form_step: 'form-step-1',
				customer: {
					phone_country_code: '+65'
				}
			};

			return state;
		}
	}, {
		key: 'buildCustomerReducer',
		value: function buildCustomerReducer() {
			var _state = this.state.customer;

			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'CHANGE_CUSTOMER_SANLUTATION':
						return Object.assign({}, state, {
							salutation: action.salutation
						});
					case 'CHANGE_CUSTOMER_FIRST_NAME':
						return Object.assign({}, state, {
							first_name: action.first_name
						});
					case 'CHANGE_CUSTOMER_LAST_NAME':
						return Object.assign({}, state, {
							last_name: action.last_name
						});
					case 'CHANGE_CUSTOMER_EMAIL':
						return Object.assign({}, state, {
							email: action.email
						});
					case 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE':
						return Object.assign({}, state, {
							phone_country_code: action.phone_country_code
						});
					case 'CHANGE_CUSTOMER_PHONE':
						return Object.assign({}, state, {
							phone: action.phone
						});
					case 'CHANGE_CUSTOMER_REMARKS':
						return Object.assign({}, state, {
							remarks: action.remarks
						});
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildOutletReducer',
		value: function buildOutletReducer() {
			var _state = this.state.outlet;

			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'CHANGE_OUTLET':
						return action.outlet;
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildPaxReducer',
		value: function buildPaxReducer() {
			var _state = this.state.pax;

			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'CHANGE_ADULT_PAX':
						return Object.assign({}, state, {
							adult: action.adult_pax
						});
					case 'CHANGE_CHILDREN_PAX':
						return Object.assign({}, state, {
							children: action.children_pax
						});
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildReservationReducer',
		value: function buildReservationReducer() {
			var _state = this.state.reservation;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'CHANGE_RESERVATION_DATE':
						return Object.assign({}, state, {
							date: action.date
						});
					case 'CHANGE_RESERVATION_TIME':
						return Object.assign({}, state, {
							time: action.time
						});
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildDialogReducer',
		value: function buildDialogReducer() {
			var _state = this.state.dialog;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'DIALOG_SHOW':
						return Object.assign({}, state, {
							show: action.show
						});
					case 'DIALOG_SHOWN':
						return Object.assign({}, state, {
							shown: true,
							show: false
						});
					case 'DIALOG_HAS_DATA':
						state.stop.has_data = action.dialog_has_data;
						return JSON.parse(JSON.stringify(state));
					case 'DIALOG_EXCEED_MIN_EXIST_TIME':
						state.stop.exceed_min_exist_time = action.exceed_min_exist_time;
						return JSON.parse(JSON.stringify(state));
					case 'DIALOG_HIDDEN':
						state.shown = false;
						state.stop.exceed_min_exist_time = false;
						return JSON.parse(JSON.stringify(state));
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildAvailableTimeReducer',
		value: function buildAvailableTimeReducer() {
			var _state = this.state.available_time;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'CHANGE_AVAILABLE_TIME':
						if (Array.isArray(action.available_time)) {
							action.available_time = {};
						}
						// return Object.assign({}, state, action.available_time);
						return action.available_time;
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildInitViewReducer',
		value: function buildInitViewReducer() {
			var _state = this.state.init_view;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'INIT_VIEW':
						return true;
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildAjaxCallReducer',
		value: function buildAjaxCallReducer() {
			var _state = this.state.ajax_call;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'AJAX_CALL':
						return action.ajax_call;
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildHasSelectedDayReducer',
		value: function buildHasSelectedDayReducer() {
			var _state = this.state.has_selected_day;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'HAS_SELECTED_DAY':
						return true;
					default:
						return state;
				}
			};
		}
	}, {
		key: 'buildFormStepReducer',
		value: function buildFormStepReducer() {
			var _state = this.state.form_step;
			return function () {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : _state;
				var action = arguments[1];

				switch (action.type) {
					case 'CHANGE_FORM_STEP':
						return action.form_step;
					default:
						return state;
				}
			};
		}
	}, {
		key: 'bindListener',
		value: function bindListener() {
			var _this = this;

			var store = window.store;
			var scope = this;
			// let prestate;
			store.subscribe(function () {
				var state = store.getState();
				var prestate = store.getPrestate();

				if (prestate) {
					/**
      * Update available time
      * When user change his condition
      *
      * #require has_selected_day
      */
					if (prestate.has_selected_day && (prestate.pax.adult != state.pax.adult || prestate.pax.children != state.pax.children || prestate.outlet.id != state.outlet.id)) {
						// prestate = state;
						scope.ajaxCall();
					}

					if (prestate.has_selected_day == false && state.has_selected_day == true) {
						// prestate = state;
						scope.ajaxCall();
					}

					if (prestate.reservation.date != state.reservation.date) {
						// prestate = state;
						scope.ajaxCall();
					}
				}

				if (state.dialog.shown == true && state.dialog.stop.has_data == true && state.dialog.stop.exceed_min_exist_time == true) {
					// prestate = state;
					_this.ajax_dialog.modal('hide');
				}

				//update prestate
				// prestate = state;

				// if(state.ajax_call == true){
				// 	store.dispatch({type:'DIALOG_SHOW', show: true})
				//
				// 	scope.ajaxCall();
				// 	store.dispatch({type: 'AJAX_CALL', ajax_call: false});
				//
				// 	let timeout = setTimeout(function(){
				// 		store.dispatch({type: 'DIALOG_EXCEED_MIN_EXIST_TIME', exceed_min_exist_time: true});
				// 		clearTimeout(timeout);
				// 	}, state.dialog.min_exist_time);
				// }
			});
		}
	}, {
		key: 'bindView',
		value: function bindView() {
			var _this2 = this;

			this.findView();
			var store = window.store;
			// this.form_vue.$data = store.getState();
			// let form_vue = this.form_vue;
			/**
    * Debug state
    * @type {Element}
    */
			// let pre = document.querySelector('#expand');
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
				var prestate = store.getPrestate();
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

				/**
     * Date change
     * @type {boolean}
     */
				var date_change = prestate.reservation.date != state.reservation.date;
				if (date_change) {
					_this2.label.innerText = state.reservation.date.format('MMM D Y');
					_this2.inpute_date.value = state.reservation.date.format('Y-MM-DD');

					//Reservation summary step 2 header
					_this2.date_summary_step_2_span.innerText = state.reservation.date.format('MMM D Y');
				}

				/**
     * Time change
     */
				var time_change = prestate.reservation.time == state.reservation.time;
				if (time_change) {}

				/**
     * Outlet change
     * @type {boolean}
     */
				var outlet_change = prestate.outlet.name != state.outlet.name;
				if (outlet_change) {
					_this2.input_outlet.value = state.outlet.name;
					_this2.reservation_title.innerText = state.outlet.name;

					//Reservation summary step 2 header
				}

				/**
     * Available time change
     * @type {boolean}
     */
				var available_time_change = prestate.available_time != state.available_time;
				if (available_time_change) {
					_this2.updateSelectView(state.available_time);
					_this2.updateCalendarView(state.available_time);
				}

				/**
     * Dialog show
     */
				var dialog_show = state.dialog.show == true;
				if (dialog_show) {
					_this2.ajax_dialog.modal('show');
					//store.dispatch({type: 'DIALOG_HIDE'});
				}

				/**
     * Form step change
     */
				var form_step_change = prestate.form_step != state.form_step || state.form_step == 'form-step-1';
				if (form_step_change) {
					_this2.pointToFormStep();
				}

				/**
     * Pax change
     */
				_this2.pax_size_summary_step_2_span.innerText = state.pax.adult + state.pax.children + 'people';
				_this2.outlet_name_summary_step_2_span.innerText = state.outlet.name;
				_this2.time_summary_step_2_span.innerText = state.reservation.time;
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
		key: 'findView',
		value: function findView() {
			var calendarDiv = $('#calendar-box');

			this.calendar = calendarDiv.Calendar();
			this.day_tds = calendarDiv.find('td.day');
			this.label = document.querySelector('#reservation_date');
			this.select = document.querySelector('select[name="reservation_time"]');
			this.form = document.querySelector('#booking-form');

			this.adult_pax_select = document.querySelector('select[name="adult_pax"]');
			this.children_pax_select = document.querySelector('select[name="children_pax"]');

			this.ajax_dialog = $('#ajax-dialog');

			this.outlet_select = document.querySelector('select[name="outlet_id"]');
			this.inpute_date = document.querySelector('input[name="reservation_date"]');
			this.input_outlet = document.querySelector('input[name="outlet_name"]');

			this.time_select = document.querySelector('select[name="reservation_time"]');

			this.reservation_title = document.querySelector('#reservation_title');

			/**
    * Swap view
    */
			// this.btnNext  = document.querySelector('#btn_next');
			// this.queryView = document.querySelector('#query-time');
			// this.fullfillView = document.querySelector('#fullfill-info');

			this.form_step_container = document.querySelector('#form-step-container');
			this.btn_form_nexts = document.querySelectorAll('button.btn-form-next');

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
    * Reservation form step 2 header summary
    */

			this.outlet_name_summary_step_2_span = document.querySelector('#form-step-2 span[name="outlet_name"]');
			this.pax_size_summary_step_2_span = document.querySelector('#form-step-2 span[name="pax_size"]');
			this.time_summary_step_2_span = document.querySelector('#form-step-2 span[name="time"]');
			this.date_summary_step_2_span = document.querySelector('#form-step-2 span[name="date"]');
		}
	}, {
		key: 'updateSelectView',
		value: function updateSelectView(available_time) {
			var selected_day_str = moment().format('Y-MM-DD');

			var available_time_on_selected_day = available_time[selected_day_str];
			if (typeof available_time_on_selected_day == 'undefined') {
				console.info('No available time on select day');
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

			var selectDiv = this.select;
			// if(selectDiv.available_time){
			// 	if(selectDiv.available_time == available_time)
			// 		return;
			// }
			// selectDiv.available_time = available_time;
			//reset selectDiv options
			selectDiv.innerHTML = '';
			available_time_on_selected_day.forEach(function (time) {
				//console.log(time);
				var optionDiv = document.createElement('option');

				optionDiv.setAttribute('value', time.time);
				//noinspection JSUnresolvedVariable
				optionDiv.innerText = time.session_name + ' ' + time.time;

				selectDiv.appendChild(optionDiv);
			});
		}
	}, {
		key: 'updateCalendarView',
		value: function updateCalendarView(available_time) {
			if (Object.keys(available_time).length == 0) return;

			var calendar = this.calendar;
			this._addCalendarHelper(calendar);

			// if(calendar.available_time){
			// 	calendar.available_time == available_time;
			// 	return;
			// }
			//
			// calendar.available_time = available_time;

			var available_days = Object.keys(available_time);
			this.day_tds.each(function () {
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
		key: 'regisEvent',
		value: function regisEvent() {
			var store = window.store;
			var scope = this;

			var outlet_select = this.outlet_select;
			outlet_select.addEventListener('change', function () {
				var selectedOption = outlet_select.selectedOptions[0];

				store.dispatch({
					type: 'CHANGE_OUTLET',
					outlet: {
						id: selectedOption.value,
						name: selectedOption.innerText
					}
				});
			});

			var adult_pax_select = this.adult_pax_select;
			adult_pax_select.addEventListener('change', function () {
				var selectedOption = adult_pax_select.selectedOptions[0];

				store.dispatch({
					type: 'CHANGE_ADULT_PAX',
					adult_pax: selectedOption.value
				});
			});

			var children_pax_select = this.children_pax_select;
			children_pax_select.addEventListener('change', function () {
				var selectedOption = children_pax_select.selectedOptions[0];

				store.dispatch({
					type: 'CHANGE_CHILDREN_PAX',
					children_pax: selectedOption.value
				});
			});

			document.addEventListener('user-select-day', function (e) {
				var date = moment(e.detail.day, 'Y-M-D');

				store.dispatch({
					type: 'CHANGE_RESERVATION_DATE',
					date: date
				});

				store.dispatch({
					type: 'HAS_SELECTED_DAY'
				});
			});

			var time_select = this.time_select;
			time_select.addEventListener('change', function () {
				console.log('time change');
				var selectedOption = time_select.selectedOptions[0];

				var action = {
					type: 'CHANGE_RESERVATION_TIME',
					time: selectedOption.value
				};

				store.dispatch(action);
			});

			var ajax_dialog = this.ajax_dialog;
			ajax_dialog.on('hidden.bs.modal', function () {
				// store.dispatch({type: 'DIALOG_HIDE'});
				console.log('dialog hidden');
				//can dispatch something here
				//but it NOT DIALOG_HIDE
				//bcs right after state change, should dispatch hide
				//any other come later may re run on this function
				store.dispatch({ type: 'DIALOG_HIDDEN' });
			});

			ajax_dialog.on('shown.bs.modal', function () {
				// store.dispatch({type: 'DIALOG_HIDE'});
				console.log('dialog shown');
				//can dispatch something here
				//but it NOT DIALOG_HIDE
				//bcs right after state change, should dispatch hide
				//any other come later may re run on this function
				store.dispatch({ type: 'DIALOG_SHOWN' });
			});

			// let btnNext = this.btnNext;
			// btnNext.addEventListener('click', function(){
			// 	scope.gotoFullfillView();
			// });

			// let form = this.form;
			// form.addEventListener('submit', (e)=>{
			// 	console.log('submit');
			// 	e.preventDefault();
			// });

			var btn_form_nexts = this.btn_form_nexts;
			btn_form_nexts.forEach(function (btn) {
				btn.addEventListener('click', function () {
					var destination = btn.getAttribute('destination');
					store.dispatch({ type: 'CHANGE_FORM_STEP', form_step: destination });
				});
			});
			/**
    * Handle customer change info
    */
			this.customer_salutation_select.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var salutation = this.selectedOptions[0].value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_SALUTATION', salutation: salutation });
			});

			this.customer_firt_name_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var first_name = this.value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_FIRST_NAME', first_name: first_name });
			});

			this.customer_last_name_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var last_name = this.value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_LAST_NAME', last_name: last_name });
			});

			this.customer_email_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var email = this.value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_EMAIL', email: email });
			});

			this.customer_phone_country_code_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var phone_country_code = this.value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE', phone_country_code: phone_country_code });
			});

			this.customer_phone_input.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var phone = this.value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_PHONE', phone: phone });
			});

			this.customer_remarks_textarea.addEventListener('change', function () {
				//binding in this way to get out this as email input
				var remarks = this.value;
				store.dispatch({ type: 'CHANGE_CUSTOMER_REMARKS', remarks: remarks });
			});
		}
	}, {
		key: 'ajaxCall',
		value: function ajaxCall() {
			// let form = this.form;
			// let data =
			// 	$(form)
			// 		.serializeArray()
			// 		.reduce((carry, item) =>{
			// 			carry[item.name] = item.value;
			// 			return carry;
			// 		}, {});
			var store = window.store;
			var state = store.getState();

			var data = {
				outlet_id: state.outlet.id,
				// outlet_name: state.outlet.name,
				adult_pax: state.pax.adult,
				children_pax: state.pax.children
			};

			store.dispatch({
				type: 'DIALOG_SHOW',
				show: true
			});

			var timeout = setTimeout(function () {
				store.dispatch({ type: 'DIALOG_EXCEED_MIN_EXIST_TIME', exceed_min_exist_time: true });
				clearTimeout(timeout);
			}, state.dialog.min_exist_time);

			$.ajax({
				url: '',
				method: 'POST',
				data: data,
				success: function success(res) {
					console.log(res);

					store.dispatch({
						type: 'CHANGE_AVAILABLE_TIME',
						available_time: res
					});
				},
				complete: function complete() {
					store.dispatch({
						type: 'DIALOG_HAS_DATA',
						dialog_has_data: true
					});

					store.dispatch({
						type: 'AJAX_CALL',
						ajax_call: false
					});
				},
				error: function error(res) {
					console.log(res);
				}
			});
		}

		// gotoFullfillView(){
		// 	let a = this.queryView;
		// 	let b = this.fullfillView;
		//
		//
		// 	a.style.transform = 'scale(0,0)';
		// 	b.style.transform = 'scale(1,1)';
		// }

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
	}]);

	return BookingForm;
}();

var bookingForm = new BookingForm();