'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';

var CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';

var ADD_WEEKLY_SESSION = 'ADD_WEEKLY_SESSION';
var CHANGE_WEEKLY_SESSIONS = 'CHANGE_WEEKLY_SESSIONS';

var AdminSettings = function () {
	/**
  * @namespace Redux
  * @namespace moment
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

		this.view();

		this.initView();

		var a = document.querySelector('#xxx');

		a.addEventListener('click', function (e) {
			if (store.getState().admin_step != '#weekly_sessions') {
				e.preventDefault();

				a.dispatchEvent(new CustomEvent('xxx'));
			}
		});

		a.addEventListener('xxx', function () {
			store.dispatch({
				type: CHANGE_ADMIN_STEP,
				step: '#weekly_sessions'
			});

			a.click();
		});
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
					case CHANGE_WEEKLY_SESSIONS:
						return Object.assign({}, state, {
							weekly_sessions: self.weeklySessionsReducer(state.weekly_sessions, action)
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
				admin_step: '#weekly_sessions'
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
				},

				methods: {
					_addTimingToSession: function _addTimingToSession(e) {
						console.log('see add timing');

						var btn = e.target;
						var session_index = btn.getAttribute('session-index');
						var session = this.weekly_sessions[session_index];

						session.timings.push(self._dumpTiming());
					},
					_deleteTiming: function _deleteTiming(e) {
						console.log('see delete timing');

						var i = e.target;
						if (i.tagName != 'I') {
							console.log('Only handle when click on <i>');
							return;
						}
						var session_index = i.getAttribute('session-index');
						var timing_index = i.getAttribute('timing-index');
						var session = this.weekly_sessions[session_index];

						session.timings.splice(timing_index, 1);
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
    * Create new weekly session
    */
			window.vue_state.new_weekly_sessions = [];

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
				case CHANGE_WEEKLY_SESSIONS:
					{
						/**
       * Vue as watch div manager
       * Store what he see as new data for weekly_sessions
       */
						var _weekly_sessions = this.vue.weekly_sessions.map(function (session) {
							return session;
						});

						return _weekly_sessions;
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
				"disabled": false,
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

			this.add_session_btn = document.querySelector('#add_session_btn');
			this.save_session_btn = document.querySelector('#save_session_btn');
		}
	}, {
		key: 'event',
		value: function event() {
			this.findView();

			this.admin_step_go.forEach(function (el) {
				el.addEventListener('click', function () {
					var destination = el.getAttribute('destination');
					store.dispatch({ type: CHANGE_ADMIN_STEP, step: destination });
				});
			});

			this.add_session_btn.addEventListener('click', function () {
				store.dispatch({
					type: ADD_WEEKLY_SESSION
				});
			});

			this.save_session_btn.addEventListener('click', function () {
				store.dispatch({
					type: CHANGE_WEEKLY_SESSIONS
				});
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
     * Update state for vue
     * @type {boolean}
     */
				var is_reuse_vue_state = action == CHANGE_WEEKLY_SESSIONS;
				if (!is_reuse_vue_state) {
					var _vue_state = self.getVueState();
					Object.assign(_vue_state, state);
				}
				//debug
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

				var first_view = prestate.init_view == false && state.init_view == true;
				var change_step = prestate.admin_step != state.admin_step;

				var run_admin_step = first_view || change_step;
				if (run_admin_step) {
					self.pointToAdminStep();
				}

				/**
     * Self build weekly_view from weekly_sessions
     */
				var weekly_sessions_change = action == CHANGE_WEEKLY_SESSIONS;

				var should_compute_weekly_view_for_vue = first_view || weekly_sessions_change;
				if (should_compute_weekly_view_for_vue) {
					var weekly_view = self.computeWeeklyView();
					Object.assign(vue_state, { weekly_view: weekly_view });
				}
			});
		}
	}, {
		key: 'pointToAdminStep',
		value: function pointToAdminStep() {
			var state = store.getState();

			this.admin_step.forEach(function (step) {
				var admin_step = step.getAttribute('id');
				var transform = 'scale(0,0)';
				if ('#' + admin_step == state.admin_step) {
					transform = 'scale(1,1)';
				}
				step.style.transform = transform;
			});
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

			return weekly_view;
		}
	}]);

	return AdminSettings;
}();

var adminSettings = new AdminSettings();