'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';

var CHANGE_RESERVATION_DIALOG_CONTENT = 'CHANGE_RESERVATION_DIALOG_CONTENT';
var UPDATE_SINGLE_RESERVATIONS = 'UPDATE_SINGLE_RESERVATIONS';
var UPDATE_RESERVATIONS = 'UPDATE_RESERVATIONS';

var SYNC_DATA = 'SYNC_DATA';
var DELETE_TIMING = 'DELETE_TIMING';
var DELETE_SESSION = 'DELETE_SESSION';
var DELETE_SPECIAL_SESSION = 'DELETE_SPECIAL_SESSION';
var UPDATE_SPECIAL_SESSIONS = 'UPDATE_SPECIAL_SESSIONS';
var SAVE_EDIT_IN_VUE_TO_STORE = 'SAVE_EDIT_IN_VUE_TO_STORE';
var UPDATE_BUFFER = 'UPDATE_BUFFER';
var UPDATE_NOTIFICATION = 'UPDATE_NOTIFICATION';
var UPDATE_SETTINGS = 'UPDATE_SETTINGS';
var UPDATE_DEPOSIT = 'UPDATE_DEPOSIT';
var REFETCHING_DATA = 'REFETCHING_DATA';
// const SYNC_DATA = 'SYNC_DATA';

var TOAST_SHOW = 'TOAST_SHOW';

var REFETCHING_DATA_SUCCESS = 'REFETCHING_DATA_SUCCESS';

// AJAX ACTION
var AJAX_UPDATE_RESERVATIONS = 'AJAX_UPDATE_RESERVATIONS';

var AJAX_UPDATE_WEEKLY_SESSIONS = 'AJAX_UPDATE_WEEKLY_SESSIONS';
var AJAX_DELETE_WEEKLY_SESSIONS = 'AJAX_DELETE_WEEKLY_SESSIONS';
var AJAX_UPDATE_SESSIONS = 'AJAX_UPDATE_SESSIONS';
var AJAX_UPDATE_BUFFER = 'AJAX_UPDATE_BUFFER';
var AJAX_UPDATE_NOTIFICATION = 'AJAX_UPDATE_NOTIFICATION';
var AJAX_UPDATE_SETTINGS = 'AJAX_UPDATE_SETTINGS';
var AJAX_UPDATE_DEPOSIT = 'AJAX_UPDATE_DEPOSIT';
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

/**
 * Payment status
 */
var PAYMENT_UNPAID = 25;
var PAYMENT_REFUNDED = 50;
var PAYMENT_PAID = 100;
var PAYMENT_CHARGED = 200;

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
		/**
   * Unsafe to bind event when vue not sure init
   * Bind inside vue-mounted
   */
		//this.event();
		//this.listener();
		this.initView();
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
						return Object.assign({}, state, {
							init_view: self.initViewReducer(state.init_view, action)
						});
					case CHANGE_RESERVATION_DIALOG_CONTENT:
						return Object.assign({}, state, {
							reservation_dialog_content: self.reservationDialogContentReducer(state.reservation_dialog_content, action)
						});
					case UPDATE_SINGLE_RESERVATIONS:
					case UPDATE_RESERVATIONS:
						return Object.assign({}, state, {
							reservations: self.reservationsReducer(state.reservations, action)
						});
					case TOAST_SHOW:
						return Object.assign({}, state, {
							toast: action.toast
						});
					case SYNC_DATA:
						{
							console.log('still not handle SYNC DATA case');
							return state;
						}
					case REFETCHING_DATA:
						{
							return state;
						}
					case REFETCHING_DATA_SUCCESS:
						{
							var _state = action.state;
							var frontend_state = self.getFrontEndState();

							return Object.assign(_state, frontend_state);
						}
					default:
						return state;
				}
			};

			window.store = Redux.createStore(rootReducer);

			this.hack_store();
		}
	}, {
		key: 'hack_store',
		value: function hack_store() {
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
		key: 'getFrontEndState',
		value: function getFrontEndState() {
			return {
				init_view: false,
				reservation_dialog_content: {}
			};
		}
	}, {
		key: 'defaultState',
		value: function defaultState() {
			var default_state = window.state || {};
			var frontend_state = this.getFrontEndState();

			return Object.assign(default_state, frontend_state);
		}
	}, {
		key: 'buildVue',
		value: function buildVue() {
			var state = this.getVueState();
			var self = this;
			this.vue = new Vue({
				el: '#app',
				data: state,
				mounted: function mounted() {
					document.dispatchEvent(new CustomEvent('vue-mounted'));
					self.event();
					self.view();
					self.listener();
				},
				beforeUpdate: function beforeUpdate() {},
				updated: function updated() {
					// let store  = window.store;
					// let action = store.getLastAction();
					//
					// /**
					//  * Calling out dialog for reservation detail
					//  * To bundle change, wait for SAVE clicked
					//  * @type {boolean}
					//  */
					// let should_auto_update = action != CHANGE_RESERVATION_DIALOG_CONTENT;
					// if(should_auto_update){
					// 	store.dispatch({
					// 		type: UPDATE_RESERVATIONS
					// 	});
					// }
				},

				methods: {
					_reservationDetailDialog: function _reservationDetailDialog(e) {
						// console.log('see tr click');
						// console.log(e);
						try {
							var tr = this._findTrElement(e);
							var reservation_index = tr.getAttribute('reservation-index');

							/**
        * Update to mark as staff read
        * @warn modify in this way VERY DANGEROUS
        * Many thing may make a reservation maked as READ
        * Type on something,...
        * Change on something,...
        * @type {boolean}
        */
							this.reservations[reservation_index].staff_read_state = true;

							var reservation = Object.assign({}, this.reservations[reservation_index]);

							store.dispatch({
								type: CHANGE_RESERVATION_DIALOG_CONTENT,
								reservation_dialog_content: reservation
							});
						} catch (e) {
							// console.log('click on other element, which more important than tr')
							return;
						}
					},
					_findTrElement: function _findTrElement(e) {
						var tr = e.target;

						var path = [tr].concat(e.path);

						var i = 0;
						while (i < path.length) {
							var _tr = path[i];

							/**
        * Click on input / select to edit info
        */
							var is_click_on_edit_form = _tr.tagName == 'INPUT' || _tr.tagName == 'TEXTAREA' || _tr.tagName == 'SELECT';

							if (is_click_on_edit_form) {
								return null;
							}

							if (_tr.tagName == 'TR') {
								return _tr;
							}

							i++;
						}

						return null;
					},
					_updateReservationDialog: function _updateReservationDialog() {
						var reservation_dialog_content = this.reservation_dialog_content;
						store.dispatch({
							type: UPDATE_SINGLE_RESERVATIONS,
							reservation_dialog_content: reservation_dialog_content
						});
					},
					_updateReservations: function _updateReservations() {
						store.dispatch({
							type: UPDATE_RESERVATIONS
						});
					},
					_switchOutlet: function _switchOutlet(data) {
						store.dispatch({
							type: TOAST_SHOW,
							toast: {
								title: 'Switch Outlet',
								content: 'Fetching Data'
							}
						});

						var action = {
							type: AJAX_UPDATE_SCOPE_OUTLET_ID,
							data: data
						};

						/**
       * By pass store
       * When handle action in this way
       */
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

								var reservation = vue.reservations[reservation_index];

								var payment_status = void 0;
								switch (action) {
									default:
										payment_status = PAYMENT_REFUNDED;
										break;
									case 'refund':
										payment_status = PAYMENT_REFUNDED;
										break;
									case 'charge':
										payment_status = PAYMENT_CHARGED;
										break;
								}

								reservation.payment_status = payment_status;

								e.stopPropagation();

								store.dispatch({
									type: UPDATE_RESERVATIONS
								});
							} catch (e) {}
						}
					}
				}

			});
		}
	}, {
		key: 'getVueState',
		value: function getVueState() {
			if (typeof window.vue_state != 'undefined') {
				return window.vue_state;
			}

			// window.vue_state = store.getState();
			/**
    * Above assign go wrong
    * BCS vue will modifed on given state
    * Which will change state of store
    * >>> hard to understand workflow
    */
			window.vue_state = Object.assign({}, store.getState());

			/**
    * Vue handle weekly_view
    * Bring compute weekly_view to client
    */

			/**
    * Notification with toast
    */
			window.vue_state.toast = {
				title: 'Title',
				content: 'Content'
			};

			return window.vue_state;
		}
	}, {
		key: 'initViewReducer',
		value: function initViewReducer(state, action) {
			switch (action.type) {
				case INIT_VIEW:
					{
						return true;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'initView',
		value: function initView() {
			store.dispatch({ type: INIT_VIEW });
		}
	}, {
		key: 'reservationDialogContentReducer',
		value: function reservationDialogContentReducer(state, action) {
			switch (action.type) {
				case CHANGE_RESERVATION_DIALOG_CONTENT:
					{
						var r = action.reservation_dialog_content;
						/**
       * Modify custom on datetime
       * @type {*|moment.Moment}
       */
						var date = moment(r.reservation_timestamp, 'Y-M-D H:m:s');
						r.date_str = date.format('YYYY-MM-DD');
						r.time_str = date.format('HH:mm');

						return r;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'reservationsReducer',
		value: function reservationsReducer(state, action) {
			switch (action.type) {
				case UPDATE_SINGLE_RESERVATIONS:
					{
						var reservation_dialog_content = action.reservation_dialog_content;

						reservation_dialog_content.reservation_timestamp = reservation_dialog_content.date_str + ' ' + reservation_dialog_content.time_str + ':00';

						/**
       * Find which reservation need update info
       * Base on reservation dialog content
       * @type {number}
       */
						var i = 0,
						    index = 0;
						while (i < state.length) {
							if (state[i].id == reservation_dialog_content.id) {
								index = i;
							}

							i++;
						}

						/**
       * Get him out
       */
						var need_update_reservation = state[index];

						/**
       * Only assign on reservation key
       * Not all what come from reservation_dialog_content
       */
						Object.keys(need_update_reservation).forEach(function (key) {
							need_update_reservation[key] = reservation_dialog_content[key];
						});

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
		key: 'findView',
		value: function findView() {
			/**
    * Only run one time
    */
			if (this._hasFindView) {
				return;
			}
			this._hasFindView = true;

			this.reservation_dialog = $('#reservation-dialog');
		}
	}, {
		key: 'event',
		value: function event() {
			var _this = this;

			this.findView();

			var self = this;

			document.addEventListener('switch-outlet', function (e) {
				var data = e.detail;

				_this.vue._switchOutlet(data);
			});
		}
	}, {
		key: 'view',
		value: function view() {
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
				var action = store.getLastAction();
				var state = store.getState();
				var prestate = store.getPrestate();

				/**
     * Debug
     */
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

				/**
     * Show dialog for edit reservation detail
     * @type {boolean}
     */
				var show_reservation_dialog = action == CHANGE_RESERVATION_DIALOG_CONTENT;
				if (show_reservation_dialog) {
					self.reservation_dialog.modal('show');
				}

				var success_update_single_reservation = action == UPDATE_SINGLE_RESERVATIONS;
				if (success_update_single_reservation) {
					self.reservation_dialog.modal('hide');
				};
				/**
     * Show toast
     */
				if (action == TOAST_SHOW) {
					window.Toast.show();
				}

				if (true) {
					var vue_state = self.getVueState();
					Object.assign(vue_state, state);
				}
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

				var update_single_reservation = action == UPDATE_SINGLE_RESERVATIONS;
				if (update_single_reservation) {
					var _action = {
						type: AJAX_UPDATE_RESERVATIONS,
						reservations: state.reservations
					};

					self.ajax_call(_action);
				}

				var update_reservations = action == UPDATE_RESERVATIONS;
				if (update_reservations) {
					var _action2 = {
						type: AJAX_UPDATE_RESERVATIONS,
						reservations: state.reservations
					};

					self.ajax_call(_action2);
				}

				if (action == REFETCHING_DATA) {
					var _action3 = {
						type: AJAX_REFETCHING_DATA
					};

					self.ajax_call(_action3);
				}
			});
		}
	}, {
		key: 'ajax_call',
		value: function ajax_call(action) {
			if (typeof action.type != 'undefined') {
				console.log('ajax call', action.type);
			}
			var self = this;

			store.dispatch({
				type: TOAST_SHOW,
				toast: {
					title: 'Calling ajax',
					content: '...'
				}
			});

			this.hack_ajax();

			switch (action.type) {
				case AJAX_UPDATE_RESERVATIONS:
					{
						var url = self.url('reservations');
						var data = action;
						$.ajax({ url: url, data: data });
						break;
					}
				case AJAX_UPDATE_SCOPE_OUTLET_ID:
					{
						var _url = self.url('admin');
						var _data = action.data;
						$.ajax({ url: _url, data: _data });
						break;
					}
				case AJAX_REFETCHING_DATA:
					{
						var _url2 = self.url('admin/reservations');
						$.ajax({ url: _url2 });
						break;
					}
				default:
					console.log('client side. ajax call not recognize the current acttion', action);
					break;
			}

			// console.log('????')
		}
	}, {
		key: 'hack_ajax',
		value: function hack_ajax() {
			//check if not init
			if (typeof this._has_hack_ajax != 'undefined') {
				return;
			}
			this._has_hack_ajax = true;

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
		value: function url(path) {
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

			return base_url + '/' + path;
		}
	}, {
		key: 'ajax_call_success',
		value: function ajax_call_success(res) {
			// console.log(res);
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
				case AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS:
					{
						store.dispatch({
							type: REFETCHING_DATA
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
							type: REFETCHING_DATA_SUCCESS,
							state: res.data
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
				case AJAX_ERROR:
					{
						var _toast2 = {
							title: 'Update fail',
							content: JSON.stringify(res)
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
			console.log(res);
		}
	}]);

	return AdminReservations;
}();

var adminReservations = new AdminReservations();