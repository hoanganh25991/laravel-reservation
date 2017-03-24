'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';

var CHANGE_RESERVATION_DIALOG_CONTENT = 'CHANGE_RESERVATION_DIALOG_CONTENT';

var ADD_WEEKLY_SESSION = 'ADD_WEEKLY_SESSION';
var ADD_SPECIAL_SESSION = 'ADD_SPECIAL_SESSION';
var UPDATE_WEEKLY_SESSIONS = 'UPDATE_WEEKLY_SESSIONS';
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

// const SYNC_DATA = 'SYNC_DATA';

var TOAST_SHOW = 'TOAST_SHOW';

// AJAX ACTION
var AJAX_ADD_WEEKLY_SESSIONS = 'AJAX_ADD_WEEKLY_SESSIONS';
var AJAX_UPDATE_WEEKLY_SESSIONS = 'AJAX_UPDATE_WEEKLY_SESSIONS';
var AJAX_DELETE_WEEKLY_SESSIONS = 'AJAX_DELETE_WEEKLY_SESSIONS';
var AJAX_UPDATE_SESSIONS = 'AJAX_UPDATE_SESSIONS';
var AJAX_UPDATE_BUFFER = 'AJAX_UPDATE_BUFFER';
var AJAX_UPDATE_NOTIFICATION = 'AJAX_UPDATE_NOTIFICATION';
var AJAX_UPDATE_SETTINGS = 'AJAX_UPDATE_SETTINGS';
var AJAX_UPDATE_DEPOSIT = 'AJAX_UPDATE_DEPOSIT';

//AJAX MSG
var AJAX_UNKNOWN_CASE = 'AJAX_UNKNOWN_CASE';
var AJAX_UPDATE_SESSIONS_SUCCESS = 'AJAX_UPDATE_SESSIONS_SUCCESS';

var AJAX_SUCCESS = 'AJAX_SUCCESS';
var AJAX_ERROR = 'AJAX_ERROR';

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

		this.view();

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
					default:
						return state;
				}
			};

			window.store = Redux.createStore(rootReducer);
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
			var frontend_state = {
				init_view: false,
				reservation_dialog_content: {}
			};

			return Object.assign(frontend_state, default_state);
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
					self.listener();
				},
				updated: function updated() {},

				methods: {
					_reservationDetailDialog: function _reservationDetailDialog(e) {
						console.log('see tr click');
						//console.log(e);
						try {
							var tr = this._findIElement(e);
							var reservation_index = tr.getAttribute('reservation-index');
							var reservation = Object.assign({}, this.reservations[reservation_index]);

							store.dispatch({
								type: CHANGE_RESERVATION_DIALOG_CONTENT,
								reservation_dialog_content: reservation
							});
						} catch (e) {
							return;
						}
					},
					_findIElement: function _findIElement(e) {
						var tr = e.target;

						if (tr.tagName == 'TR') {
							return tr;
						}

						try {
							var _tr = e.path[1];

							if (_tr.tagName == 'TR') {
								return _tr;
							}
						} catch (e) {
							return null;
						}

						return null;
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
						return action.reservation_dialog_content;
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
		}
	}, {
		key: 'event',
		value: function event() {
			this.findView();
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

				if (action == UPDATE_WEEKLY_SESSIONS) {
					var _action = {
						type: AJAX_UPDATE_SESSIONS,
						sessions: state.weekly_sessions,
						deleted_sessions: state.deleted_sessions,
						deleted_timings: state.deleted_timings
					};

					self.ajax_call(_action);
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
				case AJAX_UPDATE_SESSIONS:
					{
						var url = self.url('sessions');
						// let data = JSON.stringify(action);
						var data = action;
						$.ajax({ url: url, data: data });
						break;
					}
				case AJAX_UPDATE_BUFFER:
				case AJAX_UPDATE_NOTIFICATION:
				case AJAX_UPDATE_SETTINGS:
				case AJAX_UPDATE_DEPOSIT:
					{
						var _url = self.url('outlet-reservation-settings');
						var _data = action;
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
			console.log(res);
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
				case AJAX_ERROR:
					{
						var _toast = {
							title: 'Update fail',
							content: res.data.substr(0, 50)
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: _toast
						});
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
		value: function ajax_call_complete() {}
	}]);

	return AdminReservations;
}();

var adminReservations = new AdminReservations();