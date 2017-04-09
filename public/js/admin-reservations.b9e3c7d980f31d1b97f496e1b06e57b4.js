'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

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
var PAYMENT_REFUNDED = 25;
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
		//Hack into these core concept, to get log
		this.hack_store();
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
							console.log('still not handle SYNC DATA case');
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
				reservation_dialog_content: {},
				toast: {
					title: 'Title',
					content: 'Content'
				}
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
			window.vue_state = this.buildVueState();

			var self = this;

			this.vue = new Vue({
				el: '#app',
				data: window.vue_state,
				mounted: function mounted() {
					document.dispatchEvent(new CustomEvent('vue-mounted'));
					self.event();
					self.view();
					self.listener();
				},

				methods: {
					_reservationDetailDialog: function _reservationDetailDialog(e) {
						try {
							var tr = this._findTrElement(e);
							var reservation_index = tr.getAttribute('reservation-index');
							var picked_reservation = this.reservations[reservation_index];
							//Update reservations staff_read
							picked_reservation.staff_read_state = true;
							//Clone it into reservation dialog content
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
					_findTrElement: function _findTrElement(e) {
						var tr = e.target;

						var path = [tr].concat(e.path);

						var i = 0;
						while (i < path.length) {
							var _tr = path[i];

							/**
        * Click on input / select to edit info
        */
							var is_click_on_edit_form = _tr.tagName == 'INPUT' || _tr.tagName == 'TEXTAREA' || _tr.tagName == 'SELECT' || _tr.tagName == 'BUTTON';

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
								e.stopPropagation();

								this._updateReservations();
							} catch (e) {}
						}
					}
				}
			});
		}
	}, {
		key: 'buildVueState',
		value: function buildVueState() {
			return Object.assign({}, store.getState());
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
					type: AJAX_UPDATE_SCOPE_OUTLET_ID,
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
			var pre = document.querySelector('#redux-state');
			if (!pre) {
				var body = document.querySelector('body');
				pre = document.createElement('pre');
				//body.appendChild(pre);
			}

			store.subscribe(function () {
				var action = store.getLastAction();
				var state = store.getState();
				var prestate = store.getPrestate();

				//Debug
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

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
						var url = self.url('reservations');
						var data = action;
						data.outlet_id = state.outlet_id;
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
				case AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS:
					{
						var action = {
							type: AJAX_REFETCHING_DATA
						};

						self.ajax_call(action);
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
			//console.log(res);
		}
	}]);

	return AdminReservations;
}();

var adminReservations = new AdminReservations();