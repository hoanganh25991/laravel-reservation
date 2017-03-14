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
				has_selected_day: false
			};

			return state;
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
					case 'DIALOG_HAS_DATA':
						state.stop.has_data = action.dialog_has_data;
						return JSON.parse(JSON.stringify(state));
					case 'DIALOG_EXCEED_MIN_EXIST_TIME':
						state.stop.exceed_min_exist_time = action.exceed_min_exist_time;
						return JSON.parse(JSON.stringify(state));
					case 'DIALOG_HIDE':
						state.show = false;
						state.stop.has_data = false;
						return JSON.parse(JSON.stringify(state));
					case 'DIALOG_HIDDEN':
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

				if (state.dialog.show == true && state.dialog.stop.has_data == true && state.dialog.stop.exceed_min_exist_time == true) {
					// prestate = state;
					store.dispatch({ type: 'DIALOG_HIDE' });
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
				var prestate = store.getPrestate();
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));
				/**
     * Update input outlet name
     */

				if (prestate.outlet.name != state.outlet.name) {
					_this2.input_outlet.value = state.outlet.name;
				}

				if (prestate.reservation.date != state.reservation.date) {
					_this2.label.innerText = state.reservation.date.format('MMM D Y');
					_this2.inpute_date.value = state.reservation.date.format('Y-MM-DD');
				}

				if (prestate.outlet.name != state.outlet.name) {
					_this2.reservation_title.innerText = state.outlet.name;
				}

				// if(state.ajax_call == true)
				// 	this.updateSelectView(state.available_time);
				if (prestate.available_time != state.available_time) {
					_this2.updateSelectView(state.available_time);
					_this2.updateCalendarView(state.available_time);
				}

				if (state.dialog.show == true) {
					_this2.ajax_dialog.modal('show');
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
		}
	}, {
		key: 'ajaxCall',
		value: function ajaxCall() {
			var form = this.form;
			var data = $(form).serializeArray().reduce(function (carry, item) {
				carry[item.name] = item.value;
				return carry;
			}, {});

			var store = window.store;
			var state = store.getState();
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
	}]);

	return BookingForm;
}();

var bookingForm = new BookingForm();