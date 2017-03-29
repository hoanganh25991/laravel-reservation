'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';

var CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';

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
var REFETCHING_DATA = 'REFETCHING_DATA';
var REFETCHING_DATA_SUCCESS = 'REFETCHING_DATA_SUCCESS';
var SWITCH_OUTLET = 'SWITCH_OUTLET';
var AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';
var CHANGE_USER_DIALOG_CONTENT = 'CHANGE_USER_DIALOG_CONTENT';
var UPDATE_SINGLE_USER = 'UPDATE_SINGLE_USER';
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
var AJAX_REFETCHING_DATA = 'AJAX_REFETCHING_DATA';

//AJAX MSG
var AJAX_UNKNOWN_CASE = 'AJAX_UNKNOWN_CASE';
var AJAX_UPDATE_SESSIONS_SUCCESS = 'AJAX_UPDATE_SESSIONS_SUCCESS';
var AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';

var AJAX_SUCCESS = 'AJAX_SUCCESS';
var AJAX_ERROR = 'AJAX_ERROR';
var AJAX_REFETCHING_DATA_SUCCESS = 'AJAX_REFETCHING_DATA_SUCCESS';
var AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

var AdminSettings = function () {
	/** @namespace user.permission_level */
	/** @namespace window.outlets */
	/** @namespace vue.settings.users */
	/**
  * @namespace Redux
  * @namespace moment
  * @namespace $
  */
	function AdminSettings() {
		_classCallCheck(this, AdminSettings);

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

	_createClass(AdminSettings, [{
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
					case CHANGE_ADMIN_STEP:
						return Object.assign({}, state, {
							admin_step: self.adminStepReducer(state.admin_step, action)
						});
					case ADD_WEEKLY_SESSION:
					case DELETE_TIMING:
						return Object.assign({}, state, {
							deleted_timings: self.deleteTimingReducer(state.deleted_timings, action)
						});
					case DELETE_SESSION:
						return Object.assign({}, state, {
							deleted_sessions: self.deleteSessionReducer(state.deleted_sessions, action)
						});
					case TOAST_SHOW:
						return Object.assign({}, state, {
							toast: self.toastReducer(state.toast, action)
						});
					case ADD_SPECIAL_SESSION:
					case DELETE_SPECIAL_SESSION:
						return Object.assign({}, state, {
							special_sessions: self.specialSessionsReducer(state.special_sessions, action)
						});
					case SAVE_EDIT_IN_VUE_TO_STORE:
						{
							var branch = action.branch;
							var modified = {};
							var value = self.vue[branch];
							modified[branch] = value;

							return Object.assign({}, state, modified);
						}
					case UPDATE_WEEKLY_SESSIONS:
						{
							var weekly_sessions = [].concat(_toConsumableArray(self.vue.weekly_sessions));

							/**
        * Input time of browser only return value as "H:i"
        */
							self.resolveTimingArrivalTime(weekly_sessions);
							/**
        * @warn write weekly_sessions
        * NOT AS ASSIGN from {} is dangerous
        */
							return Object.assign({}, state, {
								weekly_sessions: weekly_sessions,
								deleted_sessions: [].concat(_toConsumableArray(self.vue.deleted_sessions)),
								deleted_timings: [].concat(_toConsumableArray(self.vue.deleted_timings))
							});
						}
					case UPDATE_SPECIAL_SESSIONS:
						{
							var special_sessions = [].concat(_toConsumableArray(self.vue.special_sessions));

							/**
        * Input time of browser only return value as "H:i"
        */
							self.resolveTimingArrivalTime(special_sessions);
							/**
        * @warn write special_sessions
        * NOT AS ASSIGN from {} is dangerous
        */
							return Object.assign({}, state, {
								special_sessions: special_sessions,
								deleted_sessions: [].concat(_toConsumableArray(self.vue.deleted_sessions)),
								deleted_timings: [].concat(_toConsumableArray(self.vue.deleted_timings))
							});
						}
					case UPDATE_BUFFER:
						return Object.assign({}, state, {
							buffer: self.bufferReducer(state.buffer, action)
						});
					case SYNC_DATA:
						{
							var data = action.data;
							data.deleted_sessions = [];
							data.deleted_timings = [];
							return Object.assign({}, state, data);
						}
					case UPDATE_NOTIFICATION:
						return Object.assign({}, state, {
							notification: self.notificationReducer(state.notification, action)
						});
					case UPDATE_SETTINGS:
					case UPDATE_SINGLE_USER:
						return Object.assign({}, state, {
							settings: self.settingsReducer(state.settings, action)
						});
					case UPDATE_DEPOSIT:
						return Object.assign({}, state, {
							deposit: self.depositReducer(state.deposit, action)
						});
						// case SWITCH_OUTLET:
						// case REFETCHING_DATA:
						return state;
					case REFETCHING_DATA_SUCCESS:
						{
							var _state = action.state;

							// let vue_state = window.vue_state;
							return _state;
						}
					case CHANGE_USER_DIALOG_CONTENT:
						{
							return Object.assign({}, state, {
								user_dialog_content: self.userDialogContentReducer(state.user_dialog_content, action)
							});
						}
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

			if (window.outlets) {
				default_state = Object.assign(default_state, {
					outlets: window.outlets
				});
			}
			var frontend_state = {
				init_view: false,
				// admin_step: 'weekly_sessions',
				admin_step: 'weekly_sessions_view',
				user_dialog_content: {
					outlet_ids: []
				},
				deleted_sessions: [],
				deleted_timings: []
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
				data: function data() {
					return state;
				},
				mounted: function mounted() {
					document.dispatchEvent(new CustomEvent('vue-mounted'));
					self.event();
					self.view();
					self.listener();
				},
				updated: function updated() {
					var store = window.store;
					var action = store.getLastAction();

					if (action == SYNC_DATA) {
						/**
       * Guest next admin step
       */
						var next_admin_step = this.admin_step + '_view';
						/**
       * Check if guest is right
       */
						var element = document.querySelector('#' + next_admin_step);
						if (element) {
							store.dispatch({
								type: CHANGE_ADMIN_STEP,
								step: next_admin_step
							});
						}
					}
				},

				methods: {
					_addWeeklySession: function _addWeeklySession() {
						var new_session = self._dumpWeeklySession();
						var current = this.weekly_sessions;
						this.weekly_sessions = [].concat(_toConsumableArray(current), [new_session]);
					},
					_addTimingToSession: function _addTimingToSession(e) {
						console.log('see add timing');

						var btn = e.target;
						var session_index = btn.getAttribute('session-index');
						var session = this.weekly_sessions[session_index];

						session.timings.push(self._dumpTiming());
					},
					_deleteTiming: function _deleteTiming(e) {
						// console.log(e.target);
						console.log('see delete timing');
						try {
							var i = this._findTrElement(e);
							var session_index = i.getAttribute('session-index');
							var timing_index = i.getAttribute('timing-index');
							var session = this.weekly_sessions[session_index];

							var timing = session.timings[timing_index];
							session.timings.splice(timing_index, 1);

							this.deleted_timings.push(timing);
						} catch (e) {
							return;
						}
					},
					_deleteSession: function _deleteSession(e) {
						// console.log(e.target);
						console.log('see delete session');
						try {
							var i = this._findTrElement(e);
							var session_index = i.getAttribute('session-index');

							var session = this.weekly_sessions[session_index];
							this.weekly_sessions.splice(session_index, 1);

							this.deleted_sessions.push(session);
						} catch (e) {
							return;
						}
					},
					_addSpecialSession: function _addSpecialSession() {
						var new_special_session = self._dumpSpecialSession();
						var current = this.special_sessions;
						this.special_sessions = [].concat(_toConsumableArray(current), [new_special_session]);
					},
					_addTimingToSpecialSession: function _addTimingToSpecialSession(e) {
						console.log('see add timing');

						var btn = e.target;
						var session_index = btn.getAttribute('session-index');
						var session = this.special_sessions[session_index];

						session.timings.push(self._dumpTiming());
					},
					_deleteSpecialSession: function _deleteSpecialSession(e) {
						// console.log(e.target);
						console.log('see delete session');
						try {
							var i = this._findTrElement(e);
							var session_index = i.getAttribute('session-index');

							var session = this.special_sessions[session_index];
							this.special_sessions.splice(session_index, 1);

							this.deleted_sessions.push(session);
						} catch (e) {
							return;
						}
					},
					_deleteTimingInSpecialSession: function _deleteTimingInSpecialSession(e) {
						// console.log(e.target);
						console.log('see delete timing');
						try {
							var i = this._findTrElement(e);
							var session_index = i.getAttribute('session-index');
							var timing_index = i.getAttribute('timing-index');
							var session = this.special_sessions[session_index];

							var timing = session.timings[timing_index];
							session = session.timings.splice(timing_index, 1);

							this.deleted_timings.push(timing);
						} catch (e) {
							return;
						}
					},
					_findIElement: function _findIElement(e) {
						var i = e.target;

						if (i.tagName == 'I') {
							return i;
						}

						if (i.tagName == 'BUTTON') {
							var real_i = i.querySelector('i');
							return real_i;
						}

						return null;
					},
					_updateWeeklySessions: function _updateWeeklySessions() {
						store.dispatch({
							type: UPDATE_WEEKLY_SESSIONS
						});
					},
					_updateSpecialSession: function _updateSpecialSession() {
						store.dispatch({
							type: UPDATE_SPECIAL_SESSIONS
						});
					},
					_updateBuffer: function _updateBuffer() {
						store.dispatch({
							type: UPDATE_BUFFER
						});
					},
					_updateNotification: function _updateNotification() {
						store.dispatch({
							type: UPDATE_NOTIFICATION
						});
					},
					_updateSettings: function _updateSettings() {
						/**
       * Check user list has at least 1 Administrator
       */
						var users = this.settings.users;
						var administrator = users.filter(function (user) {
							return user.permission_level == 10;
						});
						if (administrator.length == 0) {
							var toast = {
								title: 'Settings > Users',
								content: 'Need at least one Administrator',
								type: 'danger'
							};

							store.dispatch({
								type: TOAST_SHOW,
								toast: toast
							});

							// setTimeout(function(){
							// 	let toast = {
							// 		title: 'Setting > Users',
							// 		content: 'Please assign some one as Administrator'
							// 	};
							//
							// 	store.dispatch({
							// 		type: TOAST_SHOW,
							// 		toast
							// 	});
							// }, 2000);

							return;
						}

						store.dispatch({
							type: UPDATE_SETTINGS
						});
					},
					_updateDeposit: function _updateDeposit() {
						store.dispatch({
							type: UPDATE_DEPOSIT
						});
					},
					_switchOutlet: function _switchOutlet(data) {
						store.dispatch({
							type: TOAST_SHOW,
							toast: {
								title: 'Switch Outlet',
								content: 'Fecthing data'
							}
						});

						var action = {
							type: AJAX_UPDATE_SCOPE_OUTLET_ID,
							data: data
						};

						/**
       * Handle action in this way
       * Means bypass store & state
       * Not respect app-state
       */
						self.ajax_call(action);
					},
					_updateSingleUser: function _updateSingleUser() {
						console.log('see you click');
						var u = this.user_dialog_content;
						/**
       * Before call update
       * Check if validate password
       */
						if (u.reset_password) {
							if (!u.password || u.password.length < 6) {
								u.password_error = true;
								return;
							} else {
								u.password_error = false;
							}

							if (u.password != u.confirm_password) {
								u.password_mismatch = true;
								return;
							} else {
								u.password_mismatch = false;
							}
						}

						self.user_dialog.modal('hide');

						store.dispatch({
							type: UPDATE_SINGLE_USER,
							user_dialog_content: u
						});

						this._updateSettings();
					},
					_wantToChangePassword: function _wantToChangePassword() {
						console.log('see as for reset password');
						this.user_dialog_content.reset_password = true;
						this.user_dialog_content.password = '';
						this.user_dialog_content.confirm_password = '';
					},
					_updateUserDialog: function _updateUserDialog(e) {
						console.log('see tr click');
						try {
							var tr = this._findTrElement(e);

							var user_index = tr.getAttribute('user-index');
							var selected_user = this.settings.users[user_index];

							var user_dialog_content = Object.assign({}, selected_user);
							/**
        * Init fake password
        * If don't want to change
        * @type {boolean}
        */
							user_dialog_content.reset_password = false;
							user_dialog_content.password = 'xxxxxx';
							user_dialog_content.confirm_password = 'xxxxxx';
							user_dialog_content.password_mismatch = false;
							user_dialog_content.password_error = false;
							/**
        * Set up user dialog content data
        */
							// this.user_dialog_content = user_dialog_content

							/**
        * @warn Should call store for update value
        */
							store.dispatch({
								type: CHANGE_USER_DIALOG_CONTENT,
								user_dialog_content: user_dialog_content
							});
							self.user_dialog.modal('show');
						} catch (e) {
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
			window.vue_state.weekly_view = {};

			/**
    * Notification with toast
    */
			window.vue_state.toast = {
				title: 'Title',
				content: 'Content'
			};

			//debug
			// window.vue_state.deposit = {
			// 	type: '1'
			// }

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
		key: 'adminStepReducer',
		value: function adminStepReducer(state, action) {
			switch (action.type) {
				case CHANGE_ADMIN_STEP:
					return action.step;
				default:
					return state;
			}
		}
	}, {
		key: 'weeklySessionsReducer',
		value: function weeklySessionsReducer(state, action) {
			switch (action.type) {
				case ADD_WEEKLY_SESSION:
					{
						var new_session = this._dumpWeeklySession();
						var weekly_sessions = [].concat(_toConsumableArray(state), [new_session]);

						return weekly_sessions;
					}
				case SYNC_DATA:
					{
						return action.weekly_sessions;
					}
				default:
					return state;
			}
		}

		/**
   * Make sure random id as tring
   */

	}, {
		key: '_randomId',
		value: function _randomId() {
			return Math.random().toString(36).slice(-4);
		}
	}, {
		key: '_dumpWeeklySession',
		value: function _dumpWeeklySession() {
			var dump_session = {
				"id": this._randomId(),
				"outlet_id": 1,
				"session_name": "Lunch time",
				"on_mondays": 1,
				"on_tuesdays": 1,
				"on_wednesdays": 1,
				"on_thursdays": 1,
				"on_fridays": 1,
				"on_saturdays": 1,
				"on_sundays": 1,
				"created_timestamp": "2017-03-03 21:39:39",
				"modified_timestamp": "2017-03-06 21:39:33",
				"one_off": 0,
				"one_off_date": null,
				"first_arrival_time": "05:00:00",
				"last_arrival_time": "12:00:00",
				"timings": [this._dumpTiming()]
			};

			return dump_session;
		}
	}, {
		key: '_dumpTiming',
		value: function _dumpTiming() {
			var dump_timing = {
				"id": this._randomId(),
				"session_id": 2,
				"timing_name": "12-16",
				"disabled": true,
				"first_arrival_time": "05:00:00",
				"last_arrival_time": "08:00:00",
				"interval_minutes": 30,
				"capacity_1": 1,
				"capacity_2": 1,
				"capacity_3_4": 1,
				"capacity_5_6": 1,
				"capacity_7_x": 1,
				"max_pax": 20,
				"children_allowed": true,
				"is_outdoor": null,
				"created_timestamp": "2017-03-02 20:11:45",
				"modified_timestamp": "2017-03-02 21:51:41"
			};

			return dump_timing;
		}
	}, {
		key: 'deleteTimingReducer',
		value: function deleteTimingReducer(state, action) {
			switch (action.type) {
				case DELETE_TIMING:
					var deleted_timings = [].concat(_toConsumableArray(state), [action.timing]);
					return deleted_timings;
				default:
					return state;
			}
		}
	}, {
		key: 'deleteSessionReducer',
		value: function deleteSessionReducer(state, action) {
			switch (action.type) {
				case DELETE_SESSION:
					var deleted_sessions = [].concat(_toConsumableArray(state), [action.session]);
					return deleted_sessions;
				default:
					return state;
			}
		}
	}, {
		key: 'toastReducer',
		value: function toastReducer(state, action) {
			switch (action.type) {
				case TOAST_SHOW:
					return action.toast;
				default:
					return state;
			}
		}
	}, {
		key: 'specialSessionsReducer',
		value: function specialSessionsReducer(state, action) {
			switch (action.type) {
				case ADD_SPECIAL_SESSION:
					{
						var new_special_session = this._dumpSpecialSession();
						return [].concat(_toConsumableArray(state), [new_special_session]);
					}
				case DELETE_SPECIAL_SESSION:
					{
						return [].concat(_toConsumableArray(state), [action.session]);
					}
				default:
					return state;
			}
		}
	}, {
		key: '_dumpSpecialSession',
		value: function _dumpSpecialSession() {
			var today = moment();
			var date_str = today.format('YYYY-MM-DD');
			var dump_special_session = {
				"id": this._randomId(),
				"outlet_id": 1,
				"session_name": "Special session",
				"on_mondays": null,
				"on_tuesdays": null,
				"on_wednesdays": null,
				"on_thursdays": null,
				"on_fridays": null,
				"on_saturdays": null,
				"on_sundays": null,
				"created_timestamp": "2017-03-03 21:39:39",
				"modified_timestamp": "2017-03-06 21:39:33",
				"one_off": 1,
				"one_off_date": date_str,
				"timings": [this._dumpTiming()]
			};

			return dump_special_session;
		}
	}, {
		key: 'bufferReducer',
		value: function bufferReducer(state, action) {
			var self = this;
			switch (action.type) {
				case UPDATE_BUFFER:
					{
						return self.vue.buffer;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'notificationReducer',
		value: function notificationReducer(state, action) {
			var self = this;
			switch (action.type) {
				case UPDATE_NOTIFICATION:
					{
						return self.vue.notification;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'settingsReducer',
		value: function settingsReducer(state, action) {
			var self = this;
			switch (action.type) {
				case UPDATE_SETTINGS:
					{
						return self.vue.settings;
					}
				case UPDATE_SINGLE_USER:
					{
						var user_dialog_content = action.user_dialog_content;

						var i = 0,
						    index = 0;
						var users = state.users;
						while (i < users.length) {
							if (users[i].id == user_dialog_content.id) {
								index = i;
							}

							i++;
						}

						/**
       * Get him out
       */
						var need_update_user = users[index];

						/**
       * Only assign on reservation key
       * Not all what come from reservation_dialog_content
       */
						Object.keys(need_update_user).forEach(function (key) {
							need_update_user[key] = user_dialog_content[key];
						});

						/**
       * Allow reset password
       */
						var reset_password = user_dialog_content.reset_password,
						    password = user_dialog_content.password;


						Object.assign(need_update_user, { reset_password: reset_password, password: password });

						return state;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'depositReducer',
		value: function depositReducer(state, action) {
			var self = this;
			switch (action.type) {
				case UPDATE_DEPOSIT:
					{
						return self.vue.deposit;
					}
				default:
					return state;
			}
		}
	}, {
		key: 'userDialogContentReducer',
		value: function userDialogContentReducer(state, action) {
			switch (action.type) {
				case CHANGE_USER_DIALOG_CONTENT:
					{
						return action.user_dialog_content;
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

			this.admin_step_go = document.querySelectorAll('.go');
			this.admin_step = document.querySelectorAll('#admin-step-container .admin-step');
			this.admin_step_container = document.querySelector('#admin-step-container');
			this.user_dialog = $('#user-dialog');
		}
	}, {
		key: 'event',
		value: function event() {
			var _this = this;

			this.findView();

			var self = this;

			this.admin_step_go.forEach(function (el) {
				el.addEventListener('click', function () {
					var destination = el.getAttribute('destination');
					store.dispatch({ type: CHANGE_ADMIN_STEP, step: destination });
				});
			});

			/**
    * Move inside VUE
    */
			// this.add_session_btn
			// 	.addEventListener('click', function(){
			// 		store.dispatch({
			// 			type: ADD_WEEKLY_SESSION
			// 		});
			// 	});

			// this.save_session_btn
			// 	.addEventListener('click', function(){
			// 		store.dispatch({
			// 			type: UPDATE_WEEKLY_SESSIONS
			// 		});
			//
			// 		store.dispatch({
			// 			type: CHANGE_ADMIN_STEP,
			// 			step: 'weekly_sessions_view'
			// 		});
			// 	});

			/**
    * Move inside VUE
    */
			// this.add_special_session_btn
			// 	.addEventListener('click', function(){
			// 		store.dispatch({
			// 			type: ADD_SPECIAL_SESSION
			// 		});
			// 	});

			document.addEventListener('switch-outlet', function (e) {
				// console.log(e);
				var data = e.detail;
				// console.log(data);
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
     * Change admin step
     * @type {boolean}
     */
				var first_view = prestate.init_view == false && state.init_view == true;
				var change_step = prestate.admin_step != state.admin_step;

				var run_admin_step = first_view || change_step;

				if (run_admin_step) {
					self.pointToAdminStep();
				}

				/**
     * Self build weekly_view from weekly_sessions
     */
				// let sync_data = (action == SYNC_DATA);
				var sync_on_weekly = prestate.weekly_sessions != state.weekly_sessions;
				var refectching_data = action == REFETCHING_DATA_SUCCESS;

				var should_compute_weekly_view_for_vue = first_view
				// || sync_data;
				|| sync_on_weekly || refectching_data;

				if (should_compute_weekly_view_for_vue) {
					var weekly_view = self.computeWeeklyView();
					Object.assign(vue_state, { weekly_view: weekly_view });
				}

				/**
     * Show toast
     * @type {boolean}
     */
				var show_toast = prestate.toast != state.toast;

				if (show_toast) {
					window.Toast.show();
				}

				/**
     * Jump out of edit mode, save dynamic data in vue BACK TO store
     * When user mutate data of session}timing
     * Dispatch too much on store >>> CPU halt
     *
     * Change in vuew_instance should save to store
     * Event before use hit SAVE
     */
				var change_admin_step = prestate.admin_step != state.admin_step;
				var jump_out_edit_mode = change_admin_step && (prestate.admin_step == 'weekly_sessions' || prestate.admin_step == 'special_sessions' || prestate.admin_step == 'buffer' || prestate.admin_step == 'notification' || prestate.admin_step == 'settings');

				if (jump_out_edit_mode) {
					var branch = prestate.admin_step;
					store.dispatch({
						type: SAVE_EDIT_IN_VUE_TO_STORE,
						branch: branch
					});
				}

				/**
     *
     */
				var show_user_dialog = action == CHANGE_USER_DIALOG_CONTENT;
				if (show_user_dialog) {
					self.user_dialog.modal('show');
				}

				/**
     * Update state for vue
     * @type {boolean}
     */
				var is_reuse_vue_state = action == UPDATE_WEEKLY_SESSIONS || action == ADD_WEEKLY_SESSION || action == ADD_SPECIAL_SESSION || action == DELETE_SESSION || action == DELETE_SPECIAL_SESSION || action == DELETE_TIMING;
				// let should_sync_vue_state =
				// 	action == INIT_VIEW
				// 	|| action == REFETCHING_DATA_SUCCESS;

				if (!is_reuse_vue_state) {
					// if(should_sync_vue_state){
					var _vue_state = self.getVueState();
					Object.assign(_vue_state, state);
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

				if (action == UPDATE_SPECIAL_SESSIONS) {
					var _action2 = {
						type: AJAX_UPDATE_SESSIONS,
						sessions: state.special_sessions,
						deleted_sessions: state.deleted_sessions,
						deleted_timings: state.deleted_timings
					};

					self.ajax_call(_action2);
				}

				if (action == UPDATE_BUFFER) {
					var _action3 = {
						type: AJAX_UPDATE_BUFFER,
						buffer: state.buffer
					};

					self.ajax_call(_action3);
				}

				if (action == UPDATE_NOTIFICATION) {
					var _action4 = {
						type: AJAX_UPDATE_NOTIFICATION,
						notification: state.notification
					};

					self.ajax_call(_action4);
				}

				if (action == UPDATE_SETTINGS) {
					var _action5 = {
						type: AJAX_UPDATE_SETTINGS,
						settings: state.settings
					};

					self.ajax_call(_action5);
				}

				if (action == UPDATE_DEPOSIT) {
					var _action6 = {
						type: AJAX_UPDATE_DEPOSIT,
						deposit: state.deposit
					};

					self.ajax_call(_action6);
				}

				if (action == SWITCH_OUTLET) {
					var _action7 = {
						type: AJAX_UPDATE_SCOPE_OUTLET_ID
					};
				}

				if (action == REFETCHING_DATA) {
					var _action8 = {
						type: AJAX_REFETCHING_DATA
					};

					self.ajax_call(_action8);
				}
			});
		}
	}, {
		key: 'pointToAdminStep',
		value: function pointToAdminStep() {
			var state = store.getState();
			var prestate = store.getPrestate();

			/**
    * Improve performance by ONLY toggle 2 step
    */

			var pre_step = this.admin_step_container.querySelector('#' + prestate.admin_step);
			var current_step = this.admin_step_container.querySelector('#' + state.admin_step);
			if (pre_step) {
				pre_step.style.transform = 'scale(0,0)';
			}

			if (current_step) {
				current_step.style.transform = 'scale(1,1)';
			}
		}
	}, {
		key: 'computeWeeklyView',
		value: function computeWeeklyView() {
			var store = window.store;
			var state = store.getState();

			var weekly_sessions = state.weekly_sessions;

			var today = moment();
			var monday = today.clone().startOf('isoWeek');
			var sunday = today.clone().endOf('isoWeek');

			var weekly_sessions_with_date = [];

			weekly_sessions.forEach(function (session) {
				var current = monday.clone();

				while (current.isBefore(sunday)) {
					var day_of_week = current.format('dddd').toLocaleLowerCase();
					var session_day = 'on_' + day_of_week + 's';

					if (session[session_day] == 1) {
						//clone current session
						//which reuse for many day
						var s = Object.assign({}, session);

						//assign moment date for session
						s.date = current.clone();
						weekly_sessions_with_date.push(s);
					}

					current.add(1, 'days');
				}
			});

			var weekly_view = weekly_sessions_with_date.reduce(function (carry, session) {
				var group_name = session.date.format('dddd');

				if (typeof carry[group_name] == 'undefined') {
					carry[group_name] = [];
				}

				carry[group_name].push(session);

				return carry;
			}, {});

			var weekly_view_in_order = Object.assign({
				'Monday': null,
				'Tuesday': null,
				'Wednesday': null,
				'Thursday': null,
				'Friday': null,
				'Saturday': null,
				'Sunday': null
			}, weekly_view);

			// return weekly_view;
			return weekly_view_in_order;
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
				case AJAX_UPDATE_SCOPE_OUTLET_ID:
					{
						var _url2 = self.url('admin');
						var _data2 = action.data;
						$.ajax({ url: _url2, data: _data2 });
						break;
					}
				case AJAX_REFETCHING_DATA:
					{
						var _url3 = self.url('admin/settings');
						var _data3 = action;
						$.ajax({ url: _url3, data: _data3 });
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
							content: res.data.substr(0, 50)
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: _toast2
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
						var _toast3 = {
							title: 'Switch Outlet',
							content: 'Fetched Data'
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: _toast3
						});

						store.dispatch({
							type: REFETCHING_DATA_SUCCESS,
							state: res.data
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
		value: function ajax_call_complete() {}
	}, {
		key: 'resolveTimingArrivalTime',
		value: function resolveTimingArrivalTime(sessions) {
			sessions.forEach(function (session) {
				session.timings.forEach(function (timing) {
					if (timing.first_arrival_time.split(':').length == 2) {
						timing.first_arrival_time = timing.first_arrival_time + ':00';
					}

					if (timing.last_arrival_time.split(':').length == 2) {
						timing.last_arrival_time = timing.last_arrival_time + ':00';
					}
				});
			});
		}
	}]);

	return AdminSettings;
}();

var adminSettings = new AdminSettings();