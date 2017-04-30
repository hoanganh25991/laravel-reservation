'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';

var SHOW_RESERVATION_DIALOG_CONTENT = 'SHOW_RESERVATION_DIALOG_CONTENT';
var HIDE_RESERVATION_DIALOG_CONTENT = 'HIDE_RESERVATION_DIALOG_CONTENT';
var UPDATE_SINGLE_RESERVATION = 'UPDATE_SINGLE_RESERVATION';
var UPDATE_RESERVATIONS = 'UPDATE_RESERVATIONS';

var SYNC_DATA = 'SYNC_DATA';
var REFETCHING_DATA = 'REFETCHING_DATA';
// const SYNC_DATA = 'SYNC_DATA';

var TOAST_SHOW = 'TOAST_SHOW';

var REFETCHING_DATA_SUCCESS = 'REFETCHING_DATA_SUCCESS';

// AJAX ACTION
var AJAX_UPDATE_RESERVATIONS = 'AJAX_UPDATE_RESERVATIONS';

var AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';
var AJAX_REFETCHING_DATA = 'AJAX_REFETCHING_DATA';

//AJAX MSG
var AJAX_UNKNOWN_CASE = 'AJAX_UNKNOWN_CASE';
var AJAX_UPDATE_SESSIONS_SUCCESS = 'AJAX_UPDATE_SESSIONS_SUCCESS';
var AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

var AJAX_SUCCESS = 'AJAX_SUCCESS';
var AJAX_ERROR = 'AJAX_ERROR';
var AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';
var AJAX_REFETCHING_DATA_SUCCESS = 'AJAX_REFETCHING_DATA_SUCCESS';

//Paypal
var PAYMENT_REFUNDED = 50;
var PAYMENT_PAID = 100;
var PAYMENT_CHARGED = 200;

var TODAY = 'TODAY';
var TOMORROW = 'TOMORROW';
var NEXT_3_DAYS = 'NEXT_3_DAYS';
var NEXT_7_DAYS = 'NEXT_7_DAYS';
var NEXT_30_DAYS = 'NEXT_30_DAYS';
var CUSTOM = 'CUSTOM';
var CLEAR = 'CLEAR';

var MODE_EXACTLY = 'MODE_EXACTLY';
var MODE_BETWEEN = 'MODE_BETWEEN';
var SYNC_VUE_STATE = 'SYNC_VUE_STATE';

var RESERVATION_NO_SHOW = -300;
var RESERVATION_STAFF_CANCELLED = -200;
var RESERVATION_USER_CANCELLED = -100;
var RESERVATION_DEPOSIT = 50;
var RESERVATION_RESERVED = 100;
var RESERVATION_REMINDER_SENT = 200;
var RESERVATION_CONFIRMATION = 300;
var RESERVATION_ARRIVED = 400;

var FILTER_TYPE_DAY = 'FILTER_TYPE_DAY';
var FILTER_TYPE_STATUS = 'FILTER_TYPE_STATUS';
var FILTER_TYPE_CONFIRM_ID = 'FILTER_TYPE_CONFIRM_ID';

var AdminReservations = function () {
	/**
  * @namespace Redux
  * @namespace moment
  * @namespace $
  */
	function AdminReservations() {
		_classCallCheck(this, AdminReservations);

		this.buildRedux();
		this.buildVue();
		//Hack into these core concept, to get log
		//this.hack_store();
		this.hack_ajax();
	}

	_createClass(AdminReservations, [{
		key: 'buildRedux',
		value: function buildRedux() {
			var self = this;
			var default_state = this.defaultState();
			var rootReducer = function rootReducer() {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : default_state;
				var action = arguments[1];

				switch (action.type) {
					case INIT_VIEW:
						return Object.assign({}, state, { init_view: true });
					case SHOW_RESERVATION_DIALOG_CONTENT:
					case HIDE_RESERVATION_DIALOG_CONTENT:
						return Object.assign({}, state, {
							reservation_dialog_content: self.reservationDialogContentReducer(state.reservation_dialog_content, action)
						});
					case TOAST_SHOW:
						return Object.assign({}, state, {
							toast: action.toast
						});
					case SYNC_DATA:
						{
							return Object.assign({}, state, action.data);
						}
					case SYNC_VUE_STATE:
						{
							return Object.assign({}, state, action.vue_state);
							break;
						}
					default:
						return state;
				}
			};

			window.store = Redux.createStore(rootReducer);

			var store = window.store;
			/**
    * Helper function
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
			var default_state = window.state || {};

			//let frontend_state = this.getFrontEndState();
			var frontend_state = this.frontEndState();

			return Object.assign({}, frontend_state, default_state);
		}
	}, {
		key: 'frontEndState',
		value: function frontEndState() {
			return {
				init_view: false,
				base_url: null,
				outlet_id: null,
				user: {},
				reservation_dialog_content: {},
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
				filter_search: null
			};
		}
	}, {
		key: 'buildVue',
		value: function buildVue() {
			window.vue_state = this.frontEndState();

			var self = this;

			this.vue = new Vue({
				/** @namespace moment */
				el: '#app',
				data: window.vue_state,
				mounted: function mounted() {
					document.dispatchEvent(new CustomEvent('vue-mounted'));
					self.event();
					self.view();
					self.listener();
					var store = window.store;
					// Init view
					store.dispatch({ type: INIT_VIEW });
				},
				beforeUpdate: function beforeUpdate() {
					store.dispatch({
						type: SYNC_VUE_STATE,
						vue_state: window.vue_state
					});
				},

				computed: {
					updateFilteredReservations: function updateFilteredReservations() {
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
					reservations: function reservations(_reservations) {
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
						_reservations.forEach(function (reservation) {
							var timestamp = reservation.reservation_timestamp;
							var date = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
							// Assign date
							reservation.date = date;
						});
					},
					updateFilteredReservations: function updateFilteredReservations() {
						/**
       * List out dependecies, which trigger this function re-run
       * Like, hey 'watch on these properties, if you change it, i recompute
       */
						var reservations = this.reservations;
						var filter_options = this.filter_options;
						/**
       * Special case
       * When filter by confirm_id exist
       * Only run this one, no need to apply other
       * @warn which check is best practice
       */
						var filters_by_confirm_id = filter_options.filter(function (filter) {
							return filter.type == FILTER_TYPE_CONFIRM_ID;
						});
						// If exist a filter by confirm id option
						// Only run this one
						if (filters_by_confirm_id.length > 0) {
							// Get this first one
							filter_options = [filters_by_confirm_id[0]];
						}
						// Loop through each filter_options, run on current reservations
						var filtered_reservations = filter_options.reduce(function (carry, filter) {
							// aplly current filter
							var _f_reservations = carry.filter(filter);
							// return result for next row call filter on
							return _f_reservations;
						}, reservations);

						// Update filtered reservations
						this.filtered_reservations = filtered_reservations;
					},
					outlet_id: function outlet_id(_outlet_id) {
						var data = { outlet_id: _outlet_id };
						document.dispatchEvent(new CustomEvent('outlet_id', { detail: data }));
					}
				},
				methods: {
					_reservationDetailDialog: function _reservationDetailDialog(e) {
						try {
							var tr = this._findTrElement(e);
							this._remarksAsStaffRead(tr);
							//Clone it into reservation dialog content
							var reservation_id = tr.getAttribute('reservation-id');
							var picked_reservation = this.reservations.filter(function (reservation) {
								return reservation.id == reservation_id;
							})[0];
							var dialog_reservation = Object.assign({}, picked_reservation);
							//Diloag need data for other stuff
							//Self update for itself
							var date = moment(dialog_reservation.reservation_timestamp, 'Y-M-D H:m:s');
							dialog_reservation.date_str = date.format('YYYY-MM-DD');
							dialog_reservation.time_str = date.format('HH:mm');

							//Update these info into vue
							Object.assign(window.vue_state, { reservation_dialog_content: dialog_reservation });

							store.dispatch({
								type: SHOW_RESERVATION_DIALOG_CONTENT,
								reservation_dialog_content: dialog_reservation
							});
						} catch (e) {}
					},
					_remarksAsStaffRead: function _remarksAsStaffRead(tr) {
						try {
							var reservation_index = tr.getAttribute('reservation-index');
							var picked_reservation = this.reservations[reservation_index];
							//Update reservations staff_read
							picked_reservation.staff_read_state = true;
						} catch (e) {}
					},
					_findTrElement: function _findTrElement(e) {
						var tr = e.target;

						var path = [tr].concat(e.path);

						var i = 0;
						var found_tr = null;
						var is_click_on_edit_form = false;

						while (i < path.length && !found_tr) {
							var _tr = path[i];

							/**
        * Click on input / select to edit info
        */
							if (!is_click_on_edit_form) {
								//try does it click on edit form
								is_click_on_edit_form = _tr.tagName == 'INPUT' || _tr.tagName == 'TEXTAREA' || _tr.tagName == 'SELECT' || _tr.tagName == 'BUTTON';
							}

							if (_tr.tagName == 'TR') {
								found_tr = _tr;
							}

							i++;
						}

						if (found_tr) {
							//click on edit form, consider as already read it
							//has take action
							if (is_click_on_edit_form) {
								this._remarksAsStaffRead(found_tr);
								return null;
							}
						}

						return found_tr;
					},
					_updateSingleReservation: function _updateSingleReservation() {
						var reservation_dialog_content = this.reservation_dialog_content;
						//Recalculate reservation timestamp from select data
						reservation_dialog_content.reservation_timestamp = reservation_dialog_content.date_str + ' ' + reservation_dialog_content.time_str + ':00';

						var reservations = this.reservations;

						/**
       * Find which reservation need update info
       * Base on reservation dialog content
       */
						var i = 0,
						    found = false;
						while (i < reservations.length && !found) {
							if (reservations[i].id == reservation_dialog_content.id) {
								found = true;
							}

							i++;
						}

						/**
       * Get him out
       */
						var need_update_reservation = reservations[i - 1];

						/**
       * Only assign on reservation key
       * Not all what come from reservation_dialog_content
       */
						Object.keys(need_update_reservation).forEach(function (key) {
							need_update_reservation[key] = reservation_dialog_content[key];
						});

						store.dispatch({
							type: HIDE_RESERVATION_DIALOG_CONTENT
						});

						this._updateReservations();
					},
					_updateReservations: function _updateReservations() {
						var reservations = this.reservations;
						var action = {
							type: AJAX_UPDATE_RESERVATIONS,
							reservations: reservations
						};

						self.ajax_call(action);
					},
					_updateReservationPayment: function _updateReservationPayment(e) {
						console.log(e);
						var vue = this;
						var button = e.target;
						if (button.tagName == 'BUTTON') {
							try {
								var action = button.getAttribute('action');
								var reservation_index = button.getAttribute('reservation-index');
								var picked_reservation = vue.reservations[reservation_index];

								var payment_status = void 0;

								switch (action) {
									default:
										//payment_status = PAYMENT_PAID;
										break;
									case 'refund':
										payment_status = PAYMENT_REFUNDED;
										break;
									case 'charge':
										payment_status = PAYMENT_CHARGED;
										break;
								}

								if (payment_status) {
									picked_reservation.payment_status = payment_status;
								}

								//Stop bubble event
								//e.stopPropagation();
								//let it touch to tr to resolve as read

								this._updateReservations();
							} catch (e) {}
						}
					},


					/**
      * Fitler base on a date
      * @param date
      * @param mode
      *      two mode supported: 'exactly', 'from'
      * @private
      */
					_fitlerReservationByDay: function _fitlerReservationByDay(date) {
						var mode = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : MODE_EXACTLY;

						var reservations = this.reserved_reservations;
						// Assign reservations with moment date obj
						var reservations_with_date = reservations.map(function (reservation) {
							if (!reservation.date) {
								var timestamp = reservation.reservation_timestamp;
								reservation.date = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
							}

							return reservation;
						});

						// Update back resersvations
						this.reservations = reservations_with_date;

						var dateQueryFunction = '';
						switch (mode) {
							case MODE_EXACTLY:
								{
									dateQueryFunction = function dateQueryFunction(reservation) {
										return reservation.date.isSame(date, 'day');
									};
									break;
								}
							case MODE_BETWEEN:
								{
									dateQueryFunction = function dateQueryFunction(reservation) {
										return reservation.date.isBefore(date);
									};
									break;
								}
							default:
								{
									throw 'No mode is specified';
								}

						}

						var filtered_reservations = reservations_with_date.filter(dateQueryFunction);

						// Update filtered reservations;
						this.filtered_reservations = filtered_reservations;
					},
					_filter: function _filter(which_case) {
						//console.log(which_case);
						switch (which_case) {
							case TODAY:
								{
									console.log('see click today');
									// Find out which date
									var today = moment({ hour: 0, minute: 0, seconds: 0 });
									var mode = MODE_EXACTLY;

									this._fitlerReservationByDay(today, mode);
									break;
								}
							case TOMORROW:
								{
									console.log('see click tomorrow');
									// Find out which date
									var tomorrow = moment({ hour: 0, minute: 0, seconds: 0 }).add(1, 'days');
									var _mode = MODE_EXACTLY;

									this._fitlerReservationByDay(tomorrow, _mode);
									break;
								}
							case NEXT_3_DAYS:
								{
									// When call next_3_days, means next 3 days from current search
									// if no current search stored > default is today
									if (!this.next_3_days) {
										var _today = moment({ hour: 0, minute: 0, seconds: 0 });
										this.next_3_days = _today.clone().add(4, 'days');
									}

									var date = this.next_3_days;
									var _mode2 = MODE_BETWEEN;
									this._fitlerReservationByDay(date, _mode2);

									break;
								}
							case NEXT_7_DAYS:
								{
									// When call next_7_days, means next 7 days from current search
									// if no current search stored > default is today
									if (!this.next_7_days) {
										var _today2 = moment({ hour: 0, minute: 0, seconds: 0 });
										this.next_7_days = _today2.clone().add(8, 'days');
									}

									var _date = this.next_7_days;
									var _mode3 = MODE_BETWEEN;
									this._fitlerReservationByDay(_date, _mode3);

									break;
								}
							case NEXT_30_DAYS:
								{
									// When call next_7_days, means next 30 days from current search
									// if no current search stored > default is today
									if (!this.next_30_days) {
										var _today3 = moment({ hour: 0, minute: 0, seconds: 0 });
										this.next_30_days = _today3.clone().add(31, 'days');
									}

									var _date2 = this.next_30_days;
									var _mode4 = MODE_BETWEEN;
									this._fitlerReservationByDay(_date2, _mode4);

									break;
								}
							case CUSTOM:
								{
									var date_str = this.custom_pick_day;
									console.log(date_str);
									// Luckily, format of date is YYYY-MM-DD
									// Can't change this default
									// Ok, cross platform, parse it
									var _date3 = moment(date_str, 'YYYY-MM-DD');
									//console.log(date);
									var _mode5 = MODE_EXACTLY;
									this._fitlerReservationByDay(_date3, _mode5);

									break;
								}
							case CLEAR:
								{
									break;
								}
						}
					},
					_clearFilterByDay: function _clearFilterByDay() {
						// clean date picker
						this.filter_date_picker = null;
						/** @warn annoy code, should improve */
						this.custom_pick_day = null;
						// clean filter
						var new_filter_day = null;
						// Update vue state
						this.filter_day = new_filter_day;
						// Hide filter panel
						//this.filtered_reservations = [];
						this._addFilterByDay(new_filter_day);
					},
					_autoSave: function _autoSave() {
						// Get out reservations & save it
						var reservations = this.reservations;
						var action = {
							type: AJAX_UPDATE_RESERVATIONS,
							reservations: reservations
						};

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
					_createFilter: function _createFilter(filter_function, options) {
						// Check required keys of type
						var required_keys = ['type', 'priority'];
						var empty_keys = required_keys.filter(function (key) {
							return typeof options[key] == 'undefined';
						});

						if (empty_keys.length > 0) {
							throw '_createFilter, type lack of required key';
						}

						filter_function.priority = options.priority;
						filter_function.type = options.type;
						filter_function.toJSON = function () {
							return options.name;
						};

						return filter_function;
					},
					_addNewFilter: function _addNewFilter(new_filter) {
						// Push it back to filter_options
						// Filter here is same type & doesn't have higher priority
						// >>> remove it out
						var new_filter_options = this.filter_options.filter(function (filter) {
							if (filter.type == new_filter.type && filter.priority <= new_filter.priority) {
								return false;
							}

							return true;
						});

						new_filter_options.push(new_filter);

						this.filter_options = new_filter_options;
					},
					_addFilterByDay: function _addFilterByDay(which_day) {
						var start_day = moment({ hour: 0, minute: 0, seconds: 0 });
						var num_days = 0;
						switch (which_day) {
							case TODAY:
								{
									// in today, start day is start day of default
									num_days = 1;
									break;
								}
							case TOMORROW:
								{
									// as tomorrow case, start day is early of tomorrow
									// ok, at one more
									start_day = start_day.add(1, 'days');
									num_days = 1;
									break;
								}
							case NEXT_3_DAYS:
								{
									// why at 4 in 3_days case
									// bcs we want to reach up to 23:59:59
									// when filter in between as [)
									// equal at first start
									// less than at last end
									num_days = 4;
									break;
								}
							case NEXT_7_DAYS:
								{
									num_days = 8;
									break;
								}
							case NEXT_30_DAYS:
								{
									num_days = 31;
									break;
								}
							case CUSTOM:
								{
									var date_str = this.custom_pick_day;
									// Browser pick day, ONLY RETURN AS YYYY-MM-DD
									// So lucky at this point
									start_day = moment(date_str, 'YYYY-MM-DD');
									num_days = 1;
									break;
								}
							// When specify as null, means no filter apply
							case null:
								{
									break;
								}
							default:
								{
									throw '_addFilterByDay: not support case';
									break;
								}
						}

						var end_day = start_day.clone().add(num_days, 'days');

						// Filter receive a reservation
						// Base on that reservation filter out
						var filter = function filter(reservation) {
							// Get out date of reservation to compare
							var date = reservation.date;
							// wow the last parameter is [}, [], () compare on equal or not
							return date.isBetween(start_day, end_day, null, '[)');
						};

						// When no filter apply, filter function return true in all cases
						if (which_day == null) {
							filter = function filter() {
								return true;
							};
						}

						var iFilter = this._createFilter(filter, { name: 'filter reservation by day', type: FILTER_TYPE_DAY, priority: 1 });

						this._addNewFilter(iFilter);
					},


					/**
      * Support multiple status
      * @param which_status
      * @private
      */
					_addFilterByStatus: function _addFilterByStatus() {
						for (var _len = arguments.length, which_status = Array(_len), _key = 0; _key < _len; _key++) {
							which_status[_key] = arguments[_key];
						}

						// Support case when status as number
						var integer_status = which_status.map(function (status) {
							return Number(status);
						});
						var filter = function filter(reservation) {
							if (which_status.includes(reservation.status) || integer_status.includes(reservation.status)) {
								return true;
							}

							return false;
						};

						// When no status specify as empty array
						// Which means no filter to apply
						if (which_status.length == 0) {
							filter = function filter() {
								return true;
							};
						}

						var iFilter = this._createFilter(filter, { name: 'filter reservation by status', type: FILTER_TYPE_STATUS, priority: 1 });

						this._addNewFilter(iFilter);
					},
					_clearFilterByStatus: function _clearFilterByStatus() {
						var new_filter_statuses = [];
						// Update vue state
						this.filter_statuses = new_filter_statuses;
						// add to filter queue
						this._addFilterByStatus.apply(this, new_filter_statuses);
					},
					_toggleFilterStatus: function _toggleFilterStatus(status, $event) {
						//console.log(which_status, $event);
						var filter_statuses = this.filter_statuses;
						var current_state = filter_statuses.includes(status);
						// Toggle state
						current_state = !current_state;

						var new_filter_statuses = void 0;
						// true it means show push
						if (current_state) {
							new_filter_statuses = [].concat(_toConsumableArray(filter_statuses), [status]);
							// should remove
						} else {
							new_filter_statuses = filter_statuses.filter(function (_status) {
								return _status != status;
							});
						}

						// Update vue state
						this.filter_statuses = new_filter_statuses;

						//console.log(new_filter_statuses);
						// Ok, now call search
						this._addFilterByStatus.apply(this, _toConsumableArray(new_filter_statuses));
					},
					_toggleFilterByDay: function _toggleFilterByDay(which_day) {
						var current_state = this.filter_day == which_day;
						// toggle it
						current_state = !current_state;
						/**
       * This is quite ANNOY
       * But when toggle on custom day
       * Which only means that we change a pick day
       * So, still be at CUSTOM
       * @type {boolean}
       */
						// which_day == CUSTOM, means when change a pick day
						// still be at CUSTOM, rather than close it
						if (which_day == CUSTOM) {
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
						if (which_day != CUSTOM) {
							//clear custom_pick_day
							this.custom_pick_day = null;
							this.filter_date_picker = null;
						}

						var new_filter_day = void 0;
						// true it means should push
						if (current_state) {
							new_filter_day = which_day;
						} else {
							// Update current filter day
							new_filter_day = null;
						}

						// Update vue state
						this.filter_day = new_filter_day;
						// Call filter
						this._addFilterByDay(new_filter_day);
					},
					_addFilterByConfirmId: function _addFilterByConfirmId(confirm_id) {
						var filter = function filter(reservation) {
							return reservation.confirm_id == confirm_id;
						};

						if (confirm_id == "") {
							//filter = () => {return true};
							// I want a default filter as return true
							// But check in array of filters, if it appear
							// Only run it, not other

							// Ok, remove it
							var new_filter_options = this.filter_options.filter(function (filter) {
								return filter.type != FILTER_TYPE_CONFIRM_ID;
							});
							this.filter_options = new_filter_options;
							return;
						}

						var iFilter = this._createFilter(filter, { name: 'filter by confirm id', type: FILTER_TYPE_CONFIRM_ID, priority: 1 });

						this._addNewFilter(iFilter);
					},
					_toggleFilterSearch: function _toggleFilterSearch() {
						// Toggle it
						this.filter_search = !this.filter_search;
						// If staff want to search
						// Build confirm id to search
						// If not remove filter
						var new_filter_confirm_id = void 0;
						if (this.filter_search) {
							// Get confirm_id from input
							var confirm_id = this.filter_confirm_id;
							// Transform to uppercase
							var uppercase_confirm_id = confirm_id ? confirm_id.toUpperCase() : '';
							// Update vue state
							new_filter_confirm_id = uppercase_confirm_id;
						} else {
							new_filter_confirm_id = "";
						}

						// Update vue state
						this.filter_confirm_id = new_filter_confirm_id;

						this._addFilterByConfirmId(new_filter_confirm_id);
					}
				}
			});
		}
	}, {
		key: 'reservationDialogContentReducer',
		value: function reservationDialogContentReducer(state, action) {
			switch (action.type) {
				case SHOW_RESERVATION_DIALOG_CONTENT:
					{
						return action.reservation_dialog_content;
					}
				case HIDE_RESERVATION_DIALOG_CONTENT:
					{
						return state;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'reservationsReducer',
		value: function reservationsReducer(state, action) {
			switch (action.type) {
				case UPDATE_SINGLE_RESERVATION:
					{

						return state;
					}
				case UPDATE_RESERVATIONS:
					{
						var vue_state = window.vue_state;
						var reservations = Object.assign({}, vue_state.reservations);

						return reservations;
					}
				default:
					return state;
			}
		}
	}, {
		key: '_findView',
		value: function _findView() {
			///Only run one time
			if (this._hasFindView) return;

			this._hasFindView = true;

			this.reservation_dialog = $('#reservation-dialog');
		}
	}, {
		key: 'event',
		value: function event() {
			this._findView();

			var self = this;

			document.addEventListener('switch-outlet', function (e) {
				var outlet_id = e.detail.outlet_id;

				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Switch Outlet',
						content: 'Fetching Data'
					}
				});

				var action = {
					type: AJAX_REFETCHING_DATA,
					outlet_id: outlet_id
				};

				/**
     * By pass store
     * When handle action in this way
     */
				self.ajax_call(action);
			});
		}
	}, {
		key: 'view',
		value: function view() {
			var store = window.store;
			var self = this;

			//Debug state
			var redux_state_element = document.querySelector('#redux-state');

			store.subscribe(function () {
				var action = store.getLastAction();
				var state = store.getState();
				var prestate = store.getPrestate();

				// Debug
				var is_local = state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost');
				if (redux_state_element && is_local) {
					var clone_state = Object.assign({}, state);
					// Remove 'heavy keys' which build HTML > kill performance
					clone_state.reservations = 'Please watch in state';
					clone_state.filtered_reservations = 'Please watch in state';
					// Ok, build html
					redux_state_element.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
				}

				/**
     * Show dialog for edit reservation detail
     */
				if (action == SHOW_RESERVATION_DIALOG_CONTENT) {
					self.reservation_dialog.modal('show');
				}

				if (action == HIDE_RESERVATION_DIALOG_CONTENT) {
					self.reservation_dialog.modal('hide');
				}

				/**
     * Show toast
     */
				if (action == TOAST_SHOW) {
					var toast = state.toast;
					//update toast in vue
					Object.assign(window.vue_state, { toast: toast });
					window.Toast.show();
				}

				// if(action == SYNC_DATA){
				Object.assign(window.vue_state, store.getState());
				// }
			});
		}
	}, {
		key: 'listener',
		value: function listener() {
			var store = window.store;
			var self = this;

			store.subscribe(function () {
				var action = store.getLastAction();
				var state = store.getState();
				var prestate = store.getPrestate();
			});
		}
	}, {
		key: 'ajax_call',
		value: function ajax_call(action) {
			var self = this;

			store.dispatch({
				type: TOAST_SHOW,
				toast: {
					title: 'Calling ajax',
					content: '...'
				}
			});

			var state = store.getState();

			switch (action.type) {
				case AJAX_UPDATE_RESERVATIONS:
					{
						var url = self.url('');
						var outlet_id = state.outlet_id;
						var data = Object.assign({}, action, { outlet_id: outlet_id });

						$.ajax({ url: url, data: data });
						break;
					}
				case AJAX_REFETCHING_DATA:
					{
						var _url = self.url('');
						var _data = Object.assign({}, action);

						$.ajax({ url: _url, data: _data });
						break;
					}
				default:
					console.log('client side. ajax call not recognize the current acttion', action);
					break;
			}

			// console.log('????')
		}
	}, {
		key: 'ajax_call_success',
		value: function ajax_call_success(res) {
			var self = this;

			switch (res.statusMsg) {
				case AJAX_SUCCESS:
					{
						var toast = {
							title: 'Update success',
							content: '＼＿ヘ(ᐖ◞)､ '
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: toast
						});

						store.dispatch({
							type: SYNC_DATA,
							data: res.data
						});

						break;
					}
				case AJAX_REFETCHING_DATA_SUCCESS:
					{
						store.dispatch({
							type: TOAST_SHOW,
							toast: {
								title: 'Switch Outlet',
								content: 'Fetched Data'
							}
						});

						store.dispatch({
							type: SYNC_DATA,
							data: res.data
						});

						break;
					}
				case AJAX_VALIDATE_FAIL:
					{
						var _toast = {
							title: 'Validate Fail',
							content: JSON.stringify(res.data)
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: _toast
						});

						break;
					}
				case AJAX_UNKNOWN_CASE:
					{
						var _toast2 = {
							title: 'Unknown case',
							content: 'xxx'
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: _toast2
						});

						break;
					}
				default:
					break;

			}
		}
	}, {
		key: 'ajax_call_error',
		value: function ajax_call_error(res) {
			console.log(res);
			var toast = {
				title: 'Server error',
				content: '(⊙.☉)7'
			};

			store.dispatch({
				type: TOAST_SHOW,
				toast: toast
			});
		}
	}, {
		key: 'ajax_call_complete',
		value: function ajax_call_complete(res) {
			//console.log(res);
		}
	}, {
		key: 'hack_ajax',
		value: function hack_ajax() {
			//check if not init
			if (this._hasHackAjax) return;

			this._hasHackAjax = true;

			var self = this;

			var o_ajax = $.ajax;
			$.ajax = function (options) {
				var data = options.data;
				var data_json = JSON.stringify(data);
				//console.log(data_json);
				options = Object.assign(options, {
					method: 'POST',
					data: data_json,
					success: self.ajax_call_success,
					error: self.ajax_call_error,
					compelte: self.ajax_call_complete
				});

				return o_ajax(options);
			};
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
	}]);

	return AdminReservations;
}();

var adminReservations = new AdminReservations();