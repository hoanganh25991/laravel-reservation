'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

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
var SHOW_USER_DIALOG = 'SHOW_USER_DIALOG';
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

var HIDE_USER_DIALOG = 'HIDE_USER_DIALOG';

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
					case TOAST_SHOW:
						return Object.assign({}, state, {
							toast: self.toastReducer(state.toast, action)
						});
					case SYNC_DATA:
						{
							return Object.assign(state, action.data);
						}
					case HIDE_USER_DIALOG:
					case SHOW_USER_DIALOG:
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
		key: 'getFrontendState',
		value: function getFrontendState() {
			return {
				init_view: false,
				admin_step: 'weekly_sessions_view',
				user_dialog_content: {
					outlet_ids: []
				},
				deleted_sessions: [],
				deleted_timings: [],
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

			var frontend_state = this.getFrontendState();

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
				updated: function updated() {},

				methods: {
					_askRecomputeWeeklyView: function _askRecomputeWeeklyView() {},
					_addWeeklySession: function _addWeeklySession() {
						var new_session = self._dumpWeeklySession();
						this.weekly_sessions.push(new_session);
						this._askRecomputeWeeklyView();
					},
					_addTimingToSession: function _addTimingToSession(e) {
						console.log('see add timing');

						var btn = e.target;
						var session_index = btn.getAttribute('session-index');
						var session = this.weekly_sessions[session_index];
						//should destruct array
						session.timings.push(self._dumpTiming());
					},
					_deleteSession: function _deleteSession(e) {
						// console.log(e.target);
						console.log('see delete session');
						try {
							var i = this._findIElement(e);
							var session_index = i.getAttribute('session-index');
							var session = this.weekly_sessions[session_index];
							this.weekly_sessions.splice(session_index, 1);
							//should destruct array
							this.deleted_sessions.push(session);
						} catch (e) {
							return;
						}
					},
					_deleteTiming: function _deleteTiming(e) {
						// console.log(e.target);
						console.log('see delete timing');
						try {
							var i = this._findIElement(e);
							var session_index = i.getAttribute('session-index');
							var timing_index = i.getAttribute('timing-index');
							var session = this.weekly_sessions[session_index];
							var timing = session.timings[timing_index];
							session.timings.splice(timing_index, 1);
							//should destruct array
							this.deleted_timings.push(timing);
						} catch (e) {
							return;
						}
					},
					_addSpecialSession: function _addSpecialSession() {
						var new_special_session = self._dumpSpecialSession();
						this.special_sessions.push(new_special_session);
						this._askRecomputeWeeklyView();
					},
					_addTimingToSpecialSession: function _addTimingToSpecialSession(e) {
						console.log('see add timing');

						var btn = e.target;
						var session_index = btn.getAttribute('session-index');
						var session = this.special_sessions[session_index];
						//should destruct array
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
							//should destruct array
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
							//should destruct array
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
						var vue = this;

						//noinspection JSUnresolvedVariable
						this._resolveTimingArrivalTime(vue.weekly_sessions);

						//noinspection JSUnresolvedVariable
						var action = {
							type: AJAX_UPDATE_SESSIONS,
							sessions: vue.weekly_sessions,
							deleted_sessions: vue.deleted_sessions,
							deleted_timings: vue.deleted_timings
						};

						self.ajax_call(action);
					},
					_updateSpecialSession: function _updateSpecialSession() {
						var vue = this;

						//noinspection JSUnresolvedVariable
						this._resolveTimingArrivalTime(vue.special_sessions);

						//noinspection JSUnresolvedVariable
						var action = {
							type: AJAX_UPDATE_SESSIONS,
							sessions: vue.special_sessions,
							deleted_sessions: vue.deleted_sessions,
							deleted_timings: vue.deleted_timings
						};

						self.ajax_call(action);
					},
					_updateBuffer: function _updateBuffer() {
						var vue = this;

						var action = {
							type: AJAX_UPDATE_BUFFER,
							buffer: vue.buffer
						};

						self.ajax_call(action);
					},
					_updateNotification: function _updateNotification() {
						var vue = this;

						var action = {
							type: AJAX_UPDATE_NOTIFICATION,
							notification: vue.notification
						};

						self.ajax_call(action);
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

							return;
						}

						var action = {
							type: AJAX_UPDATE_SETTINGS,
							settings: this.settings
						};

						self.ajax_call(action);
					},
					_updateDeposit: function _updateDeposit() {
						var action = {
							type: AJAX_UPDATE_DEPOSIT,
							deposit: this.deposit
						};

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

						store.dispatch({
							type: HIDE_USER_DIALOG
						});

						var user_dialog_content = this.user_dialog_content;

						var users = this.settings.users;

						var i = 0,
						    found = false;
						while (i < users.length && !found) {
							if (users[i].id == user_dialog_content.id) {
								found = true;
							}

							i++;
						}

						/**
       * Get him out
       */
						var need_update_user = users[i - 1];

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
							Object.assign(window.vue_state, { user_dialog_content: user_dialog_content });

							/**
        * @warn Should call store for update value
        */
							store.dispatch({
								type: SHOW_USER_DIALOG
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
					_updateTimingDisabled: function _updateTimingDisabled(e) {
						console.log(e);
						var input = e.target;
						if (input.tagName == 'INPUT') {
							try {
								var session_id = input.getAttribute('session-id');
								var timing_index = input.getAttribute('timing-index');

								var sessions = this.weekly_sessions.filter(function (session) {
									return session.id == session_id;
								});

								//try to find him in special
								/** @warn data should be nomarlize, in this way, hard to keep track event when has session-id */
								if (sessions.length == 0) {
									sessions = this.special_sessions.filter(function (session) {
										return session.id == session_id;
									});
								}

								if (sessions.length == 0) {
									return;
								}

								var timing = sessions[0].timings[timing_index];

								timing.disabled = !input.checked;
							} catch (e) {
								return;
							}
						}
					},
					_resolveTimingArrivalTime: function _resolveTimingArrivalTime(sessions) {
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
				}

			});
		}
	}, {
		key: 'buildVueState',
		value: function buildVueState() {
			var vue_state = Object.assign({}, store.getState());

			var vue_need = {
				weekly_view: {}
			};

			Object.assign(vue_state, vue_need);

			return vue_state;
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
		key: 'userDialogContentReducer',
		value: function userDialogContentReducer(state, action) {
			switch (action.type) {
				case SHOW_USER_DIALOG:
				case HIDE_USER_DIALOG:
					{
						return state;
					}
				default:
					return state;
			}
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
			var store = window.store;
			var state = store.getState();

			var outlet_id = state.outlet_id;

			var dump_session = {
				"id": this._randomId(),
				"outlet_id": outlet_id,
				"session_name": "Lunch time",
				"on_mondays": 1,
				"on_tuesdays": 1,
				"on_wednesdays": 1,
				"on_thursdays": 1,
				"on_fridays": 1,
				"on_saturdays": 1,
				"on_sundays": 1,
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
				"is_outdoor": null
			};

			return dump_timing;
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
		key: '_dumpSpecialSession',
		value: function _dumpSpecialSession() {
			var store = window.store;
			var state = store.getState();

			var outlet_id = state.outlet_id;
			var today = moment();
			var date_str = today.format('YYYY-MM-DD');

			var dump_special_session = {
				//"id": this._randomId(),
				"outlet_id": outlet_id,
				"session_name": "Special session",
				"on_mondays": null,
				"on_tuesdays": null,
				"on_wednesdays": null,
				"on_thursdays": null,
				"on_fridays": null,
				"on_saturdays": null,
				"on_sundays": null,
				"one_off": 1,
				"one_off_date": date_str,
				"timings": [this._dumpTiming()]
			};

			return dump_special_session;
		}
	}, {
		key: '_findView',
		value: function _findView() {
			/**
    * Only run one time
    */
			if (this._hasFindView) return;

			this._hasFindView = true;

			this.admin_step_container = document.querySelector('#admin-step-container');

			this.admin_step_go = document.querySelectorAll('.go');
			this.user_dialog = $('#user-dialog');
		}
	}, {
		key: 'event',
		value: function event() {
			this._findView();

			var self = this;

			this.admin_step_go.forEach(function (el) {
				el.addEventListener('click', function () {
					var destination = el.getAttribute('destination');
					store.dispatch({ type: CHANGE_ADMIN_STEP, step: destination });
				});
			});

			document.addEventListener('switch-outlet', function (e) {
				// console.log(e);
				var outlet_id = e.detail.outlet_id;

				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Switch Outlet',
						content: 'Fecthing data'
					}
				});

				var action = {
					type: AJAX_REFETCHING_DATA,
					outlet_id: outlet_id
				};

				/**
     * Handle action in this way
     * Means bypass store & state
     * Not respect app-state
     */
				self.ajax_call(action);
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
				//body.appendChild(pre);
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
     * Show toast
     * @type {boolean}
     */
				if (action == TOAST_SHOW) {
					var toast = state.toast;
					Object.assign(window.vue_state, { toast: toast });
					window.Toast.show();
				}

				if (action == SHOW_USER_DIALOG) {
					self.user_dialog.modal('show');
				}

				if (action == HIDE_USER_DIALOG) {
					self.user_dialog.modal('hide');
				}

				if (action == SYNC_DATA) {
					Object.assign(window.vue_state, store.getState());
				}

				/**
     * Self build weekly_view from weekly_sessions
     */
				var should_compute_weekly_view = first_view || action == SYNC_DATA;

				if (should_compute_weekly_view) {
					var weekly_view = self.computeWeeklyView();
					Object.assign(vue_state, { weekly_view: weekly_view });
				}

				if (action == SYNC_DATA) {
					/**
      * Guest next admin step
      */
					var next_admin_step = state.admin_step + '_view';
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
			var store = window.store;
			var state = store.getState();
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
						var url = self.url('');
						var outlet_id = state.outlet_id;
						var data = Object.assign({}, action, { outlet_id: outlet_id });

						$.ajax({ url: url, data: data });
						break;
					}
				case AJAX_UPDATE_BUFFER:
				case AJAX_UPDATE_NOTIFICATION:
				case AJAX_UPDATE_SETTINGS:
				case AJAX_UPDATE_DEPOSIT:
					{
						var _url = self.url('');
						var _outlet_id = state.outlet_id;
						var _data = Object.assign({}, action, { outlet_id: _outlet_id });

						$.ajax({ url: _url, data: _data });
						break;
					}
				case AJAX_REFETCHING_DATA:
					{
						var _url2 = self.url('');
						var _data2 = action;

						$.ajax({ url: _url2, data: _data2 });
						break;
					}
				default:
					console.log('client side. ajax call not recognize the current acttion', action);
					break;
			}
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
				case AJAX_REFETCHING_DATA_SUCCESS:
					{
						var _toast2 = {
							title: 'Switch Outlet',
							content: 'Fetched Data'
						};

						store.dispatch({
							type: TOAST_SHOW,
							toast: _toast2
						});

						store.dispatch({
							type: SYNC_DATA,
							data: res.data
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

	return AdminSettings;
}();

var adminSettings = new AdminSettings();