'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';
var CHANGE_FORM_STEP = 'CHANGE_FORM_STEP';

var CHANGE_CUSTOMER_PHONE_COUNTRY_CODE = 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE';
var CHANGE_SELECTED_OUTLET_ID = 'CHANGE_SELECTED_OUTLET_ID';
var CHANGE_ADULT_PAX = 'CHANGE_ADULT_PAX';
var CHANGE_CHILDREN_PAX = 'CHANGE_CHILDREN_PAX';
var HAS_SELECTED_DAY = 'HAS_SELECTED_DAY';
var CHANGE_RESERVATION = 'CHANGE_RESERVATION';
var CHANGE_RESERVATION_DATE = 'CHANGE_RESERVATION_DATE';
var CHANGE_RESERVATION_TIME = 'CHANGE_RESERVATION_TIME';
var CHANGE_RESERVATION_CONFIRM_ID = 'CHANGE_RESERVATION_CONFIRM_ID';
var CHANGE_AVAILABLE_TIME = 'CHANGE_AVAILABLE_TIME';
var SELECT_PAX = 'SELECT_PAX';

var PAX_OVER = 'PAX_OVER';
var AJAX_CALL = 'AJAX_CALL';
var DIALOG_SHOW = 'DIALOG_SHOW';
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
//const AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
//const AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';

// const AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';
var SYNC_VUE_STATE = 'SYNC_VUE_STATE';
var UPDATE_CALENDAR_VIEW = 'UPDATE_CALENDAR_VIEW';
var NO_DATE_PICKED = 'NO_DATE_PICKED';
var PAYPAL_BUTTON_SHOW = 'PAYPAL_BUTTON_SHOW';
var PAYPAL_BUTTON_HIDE = 'PAYPAL_BUTTON_HIDE';
var CHANGE_PAX = 'CHANGE_PAX';

var BookingForm = function () {
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

	function BookingForm() {
		_classCallCheck(this, BookingForm);

		this.buildRedux();

		this.buildVue();

		// init view
		//this.initView();
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
						return Object.assign({}, state, { init_view: true });
					case CHANGE_FORM_STEP:
						return Object.assign({}, state, { form_step: action.form_step });
					case HAS_SELECTED_DAY:
						return Object.assign({}, state, { has_selected_day: true });
					case CHANGE_RESERVATION_DATE:
						{
							var reservation = Object.assign({}, state.reservation, { date: action.date });

							return Object.assign({}, state, { reservation: reservation });
						}
					case SYNC_RESERVATION:
						{
							var _reservation = Object.assign({}, action.reservation);
							return Object.assign({}, state, { reservation: _reservation });
						}
					case AJAX_CALL:
						return Object.assign({}, state, {
							ajax_call: self.ajaxCallReducer(state.ajax_call, action)
						});
					case DIALOG_SHOW:
						{
							return Object.assign({}, state, { dialog: true });
						}
					case DIALOG_HAS_DATA:
						{
							return Object.assign({}, state, { dialog: false });
						}
					case CHANGE_AVAILABLE_TIME:
						{
							return Object.assign({}, state, { available_time: action.available_time });
						}
					case SYNC_VUE_STATE:
						{
							return Object.assign({}, state, action.vue_state);
						}
					case PAYPAL_BUTTON_HIDE:
						{
							return Object.assign({}, state, { paypal_button: false });
						}
					case PAYPAL_BUTTON_SHOW:
						{
							return Object.assign({}, state, { paypal_button: true });
						}
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
		key: 'defaultState',
		value: function defaultState() {
			var server_state = window.state || {};

			var frontend_state = {
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
					last_name: '',
					email: '',
					phone_country_code: '+65',
					phone: '',
					customer_remarks: ''
				},
				dialog: {},
				available_time: {},
				has_selected_day: false,
				form_step: 'form-step-1',
				form_step_1_keys: ['outlet_id', 'adult_pax', 'children_pax', 'agree_term_condition', 'date', 'time'],
				form_step_2_keys: ['salutation', 'first_name', 'last_name', 'email', 'phone_country_code', 'phone'],
				paypal_button: false
			};;

			var state = Object.assign(frontend_state, server_state);

			// For dev mode, quick insert default value
			if (state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')) {
				var reservation = Object.assign(state.reservation, {
					salutation: 'Mr.',
					first_name: 'Anh',
					last_name: 'Le Hoang',
					email: 'lehoanganh25991@gmail.com',
					phone_country_code: '+84',
					phone: '903865657',
					customer_remarks: 'hello world'
				});

				state = Object.assign(state, { reservation: reservation });
			}

			return state;
		}
	}, {
		key: 'buildVueState',
		value: function buildVueState() {
			// Vue own state to manage child view
			var vue_state = {
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
					agree_payment_term_condition: null
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
					end: 20
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
				has_selected_day: null
			};

			return vue_state;
		}
	}, {
		key: 'buildVue',
		value: function buildVue() {
			window.vue_state = this.buildVueState();

			var self = this;

			this.vue = new Vue({
				el: '#form-step-container',
				data: window.vue_state,
				beforeCreated: function beforeCreated() {},
				created: function created() {},
				beforeMount: function beforeMount() {},
				mounted: function mounted() {
					self.event();
					self.view();
					self.listener();

					var store = window.store;
					store.dispatch({ type: INIT_VIEW });
				},
				beforeUpdate: function beforeUpdate() {
					var store = window.store;
					var state = store.getState();

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
					try {
						var change_pax = state.change_pax.times < this.change_pax.times;
						if (change_pax) {
							store.dispatch({ type: CHANGE_PAX });
						}
					} catch (e) {
						console.log('Cant resolve change_pax');
					}
				},
				updated: function updated() {},

				watch: {
					outlets: function outlets(_outlets) {
						var first_outlet = _outlets[0] || {};
						this.selected_outlet_id = first_outlet.id;
					},
					selected_outlet_id: function selected_outlet_id(_selected_outlet_id) {
						// Update reservation
						var new_reservation = Object.assign({}, this.reservation, { outlet_id: _selected_outlet_id });
						this.reservation = new_reservation;
						// Update seleceted outlet base on
						var selected_outlets = this.outlets.filter(function (outlet) {
							return outlet.id == _selected_outlet_id;
						});
						this.selected_outlet = selected_outlets[0] || {};
					},
					available_time: function available_time(_available_time) {
						// Build back available_time_on_reservation_date
						var reservation_date = this.reservation.date;
						var date_time_str = reservation_date ? reservation_date.format('YYYY-MM-DD') : NO_DATE_PICKED;
						// Get out for specific day or default 'N/A'
						this.available_time_on_reservation_date = _available_time[date_time_str] || [];
					},
					available_time_on_reservation_date: function available_time_on_reservation_date(val) {
						var _this = this;

						// When see this one change
						// In some way ask for update calendar view
						if (!this.reservation.time) {
							var first_time = val[0] || {};
							var new_reservation = Object.assign({}, this.reservation, { time: first_time.time });

							this.reservation = new_reservation;
						}

						// User has pick one
						// BUTT this not in the new available time array
						// So.., repick the first one as default
						var find_in = val.filter(function (time_obj) {
							return time_obj.time == _this.reservation.time;
						});
						var is_in = find_in.length > 0;
						if (this.reservation.time && !is_in) {
							var _first_time = val[0] || {};
							var _new_reservation = Object.assign({}, this.reservation, { time: _first_time.time });

							this.reservation = _new_reservation;
						}
					},
					reservation: function reservation(_reservation2) {
						//console.log('See reservation update');

						if (!_reservation2.date && _reservation2.reservation_timestamp) {
							//console.log('Update reservation moment date obj');
							var date = moment(_reservation2.reservation_timestamp, 'YYYY-MM-DD HH:mm:ss');

							Object.assign(_reservation2, { date: date });
						}
					}
				},
				methods: {
					// We check these keys on reservation
					// If it empty, not allow move next
					_checkEmpty: function _checkEmpty(keys) {
						var except_keys = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

						var reservation = this.reservation;

						var empty_keys = keys.filter(function (key) {
							if (except_keys.indexOf(key) != -1) {
								return false;
							}

							var value = reservation[key];

							var isNumber = !isNaN(parseFloat(value)) && isFinite(value);

							if (isNumber) {
								return false;
							}

							// If no data > empty key
							// Like undefined, '', null
							return !value;
						});

						return empty_keys.length > 0;
					},
					not_allowed_move_to_form_step_2: function not_allowed_move_to_form_step_2() {
						var has_empty_keys = this._checkEmpty(this.form_step_1_keys);

						var reservation = this.reservation;
						var selected_outlet = this.selected_outlet;
						// Get out total_pax to CROSS CHECK
						// Dynamic pax select in worst case not work well
						var total_pax = reservation.adult_pax + reservation.children_pax;
						var out_range = total_pax < selected_outlet.overall_min_pax || total_pax > selected_outlet.overall_max_pax;

						return has_empty_keys || out_range;
					},
					not_allowed_move_to_form_step_3: function not_allowed_move_to_form_step_3() {
						var has_empty_keys = this._checkEmpty(this.form_step_2_keys, ['remarks']);

						return has_empty_keys;
					},
					_changePax: function _changePax(which_pax) {
						var times = this.change_pax.times;
						times++;
						this.change_pax = {
							current_label: which_pax,
							times: times
						};
					},
					_updatePaxSelectBox: function _updatePaxSelectBox(which_pax) {
						/**
       * What trigger this function re-run
       * As dependency of watcher
       * Like: 'Watch these properties, if it change, call me'
       */
						var selected_outlet = this.selected_outlet;
						var reservation = this.reservation;
						var adult_pax_select = this.adult_pax_select;
						var children_pax_select = this.children_pax_select;
						var change_pax = this.change_pax;

						// Determine which pax to base on
						// User change pax_x >>> base on pax_x
						var other_pax = which_pax == 'adult_pax' ? 'children_pax' : 'adult_pax';
						var base_on_pax = change_pax.current_label ? change_pax.current_label : other_pax;
						var need_updated_pax_select = this[which_pax + '_select'];
						// I'm the BASE
						// NO NEED TO UPDATE ME
						if (which_pax == base_on_pax) {
							// Doesn't need to update me
							// Has run already
							var _start = need_updated_pax_select.start;
							var _end = need_updated_pax_select.end;

							return _end - _start;
						}
						// Minus for '1' to allow equal to minimum
						// Self loop of template, start at 'start'
						// (1,10) > 1,3,4,5,6,7,8,9,10
						// Instead of 0,1,2,3,4...
						var start = selected_outlet.overall_min_pax - reservation[base_on_pax] - 1;
						var end = selected_outlet.overall_max_pax - reservation[base_on_pax];
						// When user first time pick up, allow him choose any thing he want
						// There are two select box, once for adult, once for children
						// Only remove check when count times >= 3
						if (!change_pax.current_label) {
							start = -1;
							end = this.selected_outlet.overall_max_pax;
						}
						// Limit start at 0, select for positive number.......
						start = start < -1 ? -1 : start;
						// Update pax_select back to vue_state
						var new_pax_select = { start: start, end: end };
						var should_update = !self._shallowEqualObj(need_updated_pax_select, new_pax_select);
						if (should_update) {
							this[which_pax + '_select'] = new_pax_select;
						}
						// Handle case self pick for customer
						// When there pax size out of selectable range
						var pax_value = reservation[which_pax];
						var out_range = pax_value < start + 1 || pax_value > end;
						var new_reservation = reservation;
						if (out_range) {
							window.alert('There is a minimum pax of ' + this.selected_outlet.overall_min_pax + ' for reservation at this outlet');
							var diff = pax_value - start + (pax_value - end);

							if (diff < 0) {
								// Close to start
								pax_value = start + 1;
							} else {
								// Close to end
								pax_value = end;
							}

							// Update new_reservation
							new_reservation = Object.assign({}, reservation, _defineProperty({}, which_pax, pax_value));
						}
						// Update vue_state
						this.reservation = new_reservation;

						// If can't resolve the range
						if (isNaN(end) || isNaN(start)) {
							return 20;
						}

						return end - start;
					},
					_submitBooking: function _submitBooking() {
						// Should not self decide
						// Decision must make be global
						// Who know exactly what is going on
						// Quick app, write in this way
						/** @warn   Loose from parent >>> lead to don't know where trigger
       *          Parent manage whole things directly >>> easy to track what going on app
       */
						self.ajaxCall({ type: AJAX_SUBMIT_BOOKING });
					},
					_computeReservationTimestamp: function _computeReservationTimestamp(date, time) {
						// When date or time not specify, can't go ahead
						if (!date || !time) return;

						var moment_time = moment(time, 'HH:mm');
						var moment_date = date; //date already parsed

						if (!moment_date.isValid() || !moment_date.isValid()) {
							console.warn('Why date, time specify but invalid when parsed???');
							return;
						}

						// date, time fine as moment obj
						var time_hour = moment_time.hour();
						var time_minute = moment_time.minute();
						// Ok create a full date, time obj
						var date_time = moment_date.clone().hour(time_hour).minute(time_minute);

						return date_time.format('YYYY-MM-DD HH:mm:ss');
					},
					_togglePaypalButton: function _togglePaypalButton() {
						//console.log(this.reservation.agree_payment_term_condition);
						var paypal_button = this.reservation.agree_payment_term_condition;

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
						var show_hide_paypal_button = paypal_button ? PAYPAL_BUTTON_SHOW : PAYPAL_BUTTON_HIDE;
						store.dispatch({ type: show_hide_paypal_button });
					}
				}
			});
		}
	}, {
		key: '_shallowEqualObj',
		value: function _shallowEqualObj(objA, objB) {
			if (Object.is(objA, objB)) {
				return true;
			}

			if ((typeof objA === 'undefined' ? 'undefined' : _typeof(objA)) !== 'object' || objA === null || (typeof objB === 'undefined' ? 'undefined' : _typeof(objB)) !== 'object' || objB === null) {
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
		key: 'event',
		value: function event() {
			var _this2 = this;

			this._findView();
			var store = window.store;

			document.addEventListener('user-select-day', function (e) {
				var date = moment(e.detail.day, 'YYYY-MM-DD');
				// Tell state, which date customer change
				store.dispatch({ type: CHANGE_RESERVATION_DATE, date: date });

				// Oh, customer just pick a day
				// Dispatch to state
				var state = store.getState();
				var still_not_pick_day = state.has_selected_day == false;
				if (still_not_pick_day) store.dispatch({ type: HAS_SELECTED_DAY });
			});

			this.btn_form_nexts.forEach(function (btn) {
				btn.addEventListener('click', function () {
					var destination = btn.getAttribute('destination');
					store.dispatch({ type: CHANGE_FORM_STEP, form_step: destination });
				});
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
				_this2.updateCalendarView();
			});
		}
	}, {
		key: '_changeBookingCondition',
		value: function _changeBookingCondition(previous_reservation, reservation) {
			var store = window.store;
			var last_action = store.getLastAction();

			// Code in this way issss clumsy to a function
			if (last_action == SYNC_RESERVATION) {
				return false;
			}

			return previous_reservation.outlet_id != reservation.outlet_id || previous_reservation.adult_pax != reservation.adult_pax || previous_reservation.children_pax != reservation.children_pax || previous_reservation.date != reservation.date;
		}
	}, {
		key: 'view',
		value: function view() {
			var _this3 = this;

			this._findView();
			var store = window.store;
			var self = this;

			//Debug state by redux_debug_html
			var redex_debug_element = document.querySelector('#redux-state');

			store.subscribe(function () {
				var state = store.getState();
				var prestate = store.getPrestate();
				var last_action = store.getLastAction();

				// Only run debug when needed & in local
				var on_local = state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost');
				if (redex_debug_element && on_local) {
					var clone_state = Object.assign({}, state);
					// In case available_time so large
					if (clone_state.available_time) {
						var keys = Object.keys(clone_state.available_time);
						if (keys.length > 14) {
							delete clone_state.available_time;
							console.warn('available_time is large, debug build HTML will slow app, removed it');
						}
					}

					redex_debug_element.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
				}

				// Form step change
				var change_step = prestate.form_step != state.form_step;
				// First time run, self point to the first one
				var run_first_step = prestate.init_view == false;
				if (change_step || run_first_step) {
					self.pointToFormStep();
				}

				// Handle dialog
				if (last_action == DIALOG_SHOW) {
					_this3.ajax_dialog.modal('show');
				}

				// Show dialog
				if (last_action == DIALOG_HAS_DATA) {
					self.ajax_dialog.modal('hide');
				}

				// Update calendar view
				var first_time = prestate.init_view == false;
				var outlet_changed = prestate.selected_outlet_id != state.selected_outlet_id;
				if (first_time || outlet_changed) {
					self.updateCalendarView();
				}

				// Call ajax to search available time
				// Why still need this?
				// Event after sync, but deep keys just a reference
				// Can't see condition changed in state vs prestate
				// Explicit tell host changed pax
				var change_pax = last_action == CHANGE_PAX;
				var changed_condition = self._changeBookingCondition(prestate.reservation, state.reservation) || change_pax;
				var just_select_day = prestate.has_selected_day == false && state.has_selected_day == true;
				// Ok should call ajax for searching out available time
				if (state.has_selected_day && changed_condition || just_select_day) {
					self.ajaxCall({ type: AJAX_SEARCH_AVAILABLE_TIME });
				}

				// Ok update calendar view dynamic base on available_time
				if (last_action == CHANGE_AVAILABLE_TIME) {
					self.updateCalendarView();
				}

				// Show paypal button
				if (last_action == PAYPAL_BUTTON_SHOW) {
					self.paypal_button.style.transform = 'scale(1,1)';
				}

				// Hide paypal button
				if (last_action == PAYPAL_BUTTON_HIDE) {
					self.paypal_button.style.transform = 'scale(0,1)';
				}

				// Redux state may just get sync from Vue
				// Then it updated, it talk back to Vue
				// After 2 times of SYNC
				// They are now in the same state
				Object.assign(window.vue_state, store.getState());
			});
		}
	}, {
		key: 'listener',
		value: function listener() {}
	}, {
		key: '_findView',
		value: function _findView() {
			if (this._hasRunFindView) {
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
			this.btn_form_nexts = document.querySelectorAll('button.btn-form-next');
			this.paypal_button = document.querySelector('#paypal-container');
		}
	}, {
		key: 'updateCalendarView',
		value: function updateCalendarView() {
			// Self get data from redux-state
			var state = store.getState();
			var available_time = state.available_time;
			var selected_outlet = state.selected_outlet;
			var max_days_in_advance = selected_outlet.max_days_in_advance;

			// Ok now check which day should disabled
			var calendar = this.calendar;

			// Build date_range base on max_days_in_advance
			// Start from today
			var today = moment();
			var date_range = [];
			var i = 0;
			while (i < max_days_in_advance) {
				var current = today.clone().add(i, 'days');
				date_range.push(current);

				i++;
			}

			// Available days as arr of str
			var available_days = date_range.map(function (date) {
				return date.format('YYYY-MM-DD');
			});
			// Bind some helper function, only init one time
			this._addCalendarHelper(calendar);
			//Get out all available day
			calendar.day_tds.each(function () {
				var td = $(this);
				// Read year, month, day stored in this td
				var year = td.attr('year');
				var month = calendar._prefix2Dec(td.attr('month'));
				var day = calendar._prefix2Dec(td.attr('day'));
				// Rebuild whole string
				var td_day_str = year + '-' + month + '-' + day;
				// Check if day is available
				// Style it
				var in_date_range = available_days.includes(td_day_str);
				var times_on_date = available_time[td_day_str];
				var has_time = times_on_date ? times_on_date.length > 0 : false;
				var no_available_time_data = Object.keys(available_time).length == 0;

				if (in_date_range && (has_time || no_available_time_data)) {
					calendar._pickable(td);
				} else {
					calendar._unpickable(td);
				}
			});
		}
	}, {
		key: '_addCalendarHelper',
		value: function _addCalendarHelper(calendar) {
			// IMPORTANT, each time calendar change month
			// It rebuild calendar's day_tds
			// So, don't store this reference
			// re-search out which one
			calendar.day_tds = $('#calendar-box').find('td');

			// Bind some helper function into calendar
			if (!calendar._prefix2Dec || !calendar._pickable || !calendar._unpickable) {
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
		key: 'ajaxCall',
		value: function ajaxCall(action) {
			var store = window.store;
			var state = store.getState();
			var self = this;
			// Ask to show dialog
			store.dispatch({ type: DIALOG_SHOW });
			console.log('%c ajaxCall: ' + action.type, 'background:#FDD835');

			var data = {};

			switch (action.type) {
				case AJAX_SEARCH_AVAILABLE_TIME:
					{
						var _state$reservation = state.reservation,
						    outlet_id = _state$reservation.outlet_id,
						    adult_pax = _state$reservation.adult_pax,
						    children_pax = _state$reservation.children_pax;

						Object.assign(data, { outlet_id: outlet_id, adult_pax: adult_pax, children_pax: children_pax }, { type: action.type });
						break;
					}
				case AJAX_SUBMIT_BOOKING:
					{
						Object.assign(data, state.reservation, { type: action.type });
						// Compute timestamp
						var vue = self.vue;
						var _state$reservation2 = state.reservation,
						    date = _state$reservation2.date,
						    time = _state$reservation2.time;

						var timestamp = vue._computeReservationTimestamp(date, time);
						//console.log(timestamp);
						// Add timestamp, requirement for submit booking
						data.reservation_timestamp = timestamp;
						// BCS of limit of AJAX from jQuery
						// We have to manually do this
						// Remove moment obj inside data
						delete data.date;
						break;
					}
				default:
					{
						break;
					}
			}

			console.log('ajaxCall: data', data);

			$.ajax({
				url: '',
				method: 'POST',
				data: data,
				success: function success(res) {
					console.log(res);
					switch (res.statusMsg) {
						case AJAX_AVAILABLE_TIME_FOUND:
							{
								var available_time = res.data.available_time;
								// Oh Yeah, we get available_time

								store.dispatch({
									type: CHANGE_AVAILABLE_TIME,
									available_time: available_time
								});
								break;
							}
						case AJAX_RESERVATION_SUCCESS_CREATE:
							{
								var reservation = res.data.reservation;
								// Ok, sync what from server

								store.dispatch({
									type: SYNC_RESERVATION,
									reservation: reservation
								});
								break;
							}
						case AJAX_RESERVATION_REQUIRED_DEPOSIT:
							{
								var _reservation3 = res.data.reservation;


								store.dispatch({
									type: SYNC_RESERVATION,
									reservation: _reservation3
								});

								/**
         * Init paypal
         */
								var amount = _reservation3.deposit;
								// currency accepted by this outlet
								var currency = _reservation3.paypal_currency;
								var confirm_id = _reservation3.confirm_id;
								var _outlet_id = _reservation3.outlet_id;
								var paypal_token = res.data.paypal_token;

								//noinspection ES6ModulesDependencies

								var base_url = self.url('paypal');
								// Create state data for paypal
								var paypal_options = {
									amount: amount,
									currency: currency,
									outlet_id: _outlet_id,
									confirm_id: confirm_id
								};
								var paypal_authorize = new PayPalAuthorize(paypal_token, paypal_options, base_url);

								break;
							}
						default:
							{
								console.warn('Unknown case of res.statusMsg');
								break;
							}
					}
				},
				error: function error(res_literal) {
					console.log(res_literal);
					//noinspection JSUnresolvedVariable
					//console.log(res_literal.responseJSON);
					// It quite weird that in browser window
					// Response as status code != 200
					// res obj now wrap by MANY MANY INFO
					// Please dont change this
					var res = res_literal.responseJSON;
					// Do normal things with res as in success case
					try {
						switch (res.statusMsg) {
							case AJAX_BOOKING_CONDITION_VALIDATE_FAIL:
								{
									var info = JSON.stringify(res.data);
									window.alert('Booking condition validate fail: ' + info);
									break;
								}
							case AJAX_RESERVATION_NO_LONGER_AVAILABLE:
								{
									window.alert('SORRY, Someone has book before you. Rerservation no longer available');
									break;
								}
							case AJAX_RESERVATION_VALIDATE_FAIL:
								{
									var _info = JSON.stringify(res.data);
									window.alert('Validate fail: ' + _info);

									var form_step = 'form-step-1';

									// Try to move user to where he got mistake
									// When fullfill form
									try {
										var first_key = Object.keys(res.data)[0];

										// Simple list out all keys in form-step-2
										var form_step_2_keys = ['first_name', 'last_name', 'email', 'phone_country_code', 'phone'];

										// Move him to step 2 if validate fail key in
										if (form_step_2_keys.indexOf(first_key) != -1) form_step = 'form-step-2';
									} catch (e) {}

									// Here we go ᕕ( ᐛ )ᕗ
									store.dispatch({
										type: CHANGE_FORM_STEP,
										form_step: form_step
									});

									break;
								}
							default:
								{
									break;
								}
						}
					} catch (e) {
						//console.warn('Unknown case of res or has error in code', e);
						window.alert(JSON.stringify(res_literal));
					}
				},
				complete: function complete() {
					store.dispatch({ type: DIALOG_HAS_DATA });
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
	}]);

	return BookingForm;
}();

var bookingForm = new BookingForm();