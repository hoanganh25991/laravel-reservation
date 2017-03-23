const INIT_VIEW = 'INIT_VIEW';

const CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';

const ADD_WEEKLY_SESSION     = 'ADD_WEEKLY_SESSION';
const CHANGE_WEEKLY_SESSIONS = 'CHANGE_WEEKLY_SESSIONS';
const SYNC_WEEKLY_SESSIONS   = 'SYNC_WEEKLY_SESSIONS';
const DELETE_TIMING          = 'DELETE_TIMING';
const DELETE_SESSION         = 'DELETE_SESSION';

const TOAST_SHOW = 'TOAST_SHOW';

// AJAX ACTION
const AJAX_ADD_WEEKLY_SESSIONS    = 'AJAX_ADD_WEEKLY_SESSIONS';
const AJAX_UPDATE_WEEKLY_SESSIONS = 'AJAX_UPDATE_WEEKLY_SESSIONS';
const AJAX_DELETE_WEEKLY_SESSIONS = 'AJAX_DELETE_WEEKLY_SESSIONS';

//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS = 'AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS';
const AJAX_UPDATE_WEEKLY_SESSIONS_ERROR   = 'AJAX_UPDATE_WEEKLY_SESSIONS_ERROR';

class AdminSettings {
	/**
	 * @namespace Redux
	 * @namespace moment
	 * @namespace $
	 */
	constructor(){
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

		// this.hack_ajax();

		let a = document.querySelector('#xxx');

		a.addEventListener('click', function(e){
			if(store.getState().admin_step != '#weekly_sessions'){
				e.preventDefault();

				a.dispatchEvent(new CustomEvent('xxx'));
			}
		});

		a.addEventListener('xxx', function(){
			store.dispatch({
				type: CHANGE_ADMIN_STEP,
				step: '#weekly_sessions'
			});

			a.click();
		});
	}

	buildRedux(){
		let self = this;
		let default_state = this.defaultState();
		let rootReducer = function(state = default_state, action){
			switch(action.type){
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
				case SYNC_WEEKLY_SESSIONS :
					return Object.assign({}, state, {
						weekly_sessions: self.weeklySessionsReducer(state.weekly_sessions, action)
					});
				case DELETE_TIMING: {
					return Object.assign({}, state, {
						deleted_timings: self.deleteTimingReducer(state.deleted_timings, action)
					});
				}
				case DELETE_SESSION: {
					return Object.assign({}, state, {
						deleted_sessions: self.deleteSessionReducer(state.deleted_sessions, action)
					});
				}
				case TOAST_SHOW:{
					return Object.assign({}, state, {
						toast: self.toastReducer(state.toast, action)
					});
				}
				default:
					return state;
			}
		}

		window.store = Redux.createStore(rootReducer);
		/**
		 * Helper function
		 */
		let o_dispatch = store.dispatch;
		store.dispatch = function(action){
			console.info(action.type);
			store.prestate = store.getState();
			store.last_action = action.type;
			o_dispatch(action);
		}

		store.getPrestate = function(){
			return store.prestate;
		}

		store.getLastAction = function(){
			return store.last_action;
		}
	}

	defaultState(){
		let default_state  = window.state || {};
		let frontend_state = {
			init_view : false,
			admin_step: 'weekly_sessions',
			// admin_step: 'weekly_sessions_view',
			deleted_sessions: [],
			deleted_timings: [],
		};

		return Object.assign(frontend_state, default_state);
	}

	buildVue(){
		let state = this.getVueState();
		let self  = this;
		this.vue = new Vue({
			el: '#app',
			data: state,
			mounted(){
				document.dispatchEvent(new CustomEvent('vue-mounted'));
				self.event();
				self.listener();
			},
			methods: {
				_addTimingToSession(e){
					console.log('see add timing');

					let btn           = e.target;
					let session_index = btn.getAttribute('session-index');
					let session       = this.weekly_sessions[session_index];

					session.timings.push(self._dumpTiming());
				},

				_deleteTiming(e){
					// console.log(e.target);
					console.log('see delete timing');
					try{
						let i = this._findIElement(e);
						let session_index = i.getAttribute('session-index');
						let timing_index  = i.getAttribute('timing-index');
						let session = this.weekly_sessions[session_index];

						let timing = session.timings[timing_index];
						session.timings.splice(timing_index, 1);

						store.dispatch({
							type: DELETE_TIMING,
							timing
						});
					}catch(e){
						return;
					}
				},

				_deleteSession(e){
					// console.log(e.target);
					console.log('see delete session');
					try{
						let i = this._findIElement(e);
						let session_index = i.getAttribute('session-index');

						let session = this.weekly_sessions[session_index];
						this.weekly_sessions.splice(session_index, 1);

						store.dispatch({
							type: DELETE_SESSION,
							session
						});
					}catch(e){
						return;
					}
				},

				_findIElement(e){
					let i = e.target;

					if(i.tagName == 'I'){
						return i;
					}

					if(i.tagName == 'BUTTON'){
						let real_i = i.querySelector('i');
						return real_i;
					}

					return null;
				}
			}

		});
	}

	getVueState(){
		if(typeof window.vue_state != 'undefined'){
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

	initViewReducer(state, action){
		switch(action.type){
			case INIT_VIEW:{
				return true;
			}
			default:
				return state;
		}
	}

	initView(){
		store.dispatch({type: INIT_VIEW});
	}

	adminStepReducer(state, action){
		switch(action.type){
			case CHANGE_ADMIN_STEP:
				return action.step;
			default:
				return state;
		}
	}

	weeklySessionsReducer(state, action){
		switch(action.type){
			case ADD_WEEKLY_SESSION: {
				let new_session = this._dumpWeeklySession();
				let weekly_sessions = [
					...state,
					new_session
				];

				return weekly_sessions;
			}
			case CHANGE_WEEKLY_SESSIONS: {
				/**
				 * Vue as watch div manager
				 * Store what he see as new data for weekly_sessions
				 */
				let weekly_sessions = this.vue.weekly_sessions.map(session => session);

				return weekly_sessions;
			}
			case SYNC_WEEKLY_SESSIONS: {
				return action.weekly_sessions;
			}
			default:
				return state;
		}
	}

	/**
	 * Make sure random id as tring
	 */
	_randomId(){
		return Math.random().toString(36).slice(-4);
	}

	_dumpWeeklySession(){
		let dump_session = {
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
			"timings": [
				this._dumpTiming()
			]
		};

		return dump_session;
	}

	_dumpTiming(){
		let dump_timing = {
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

	deleteTimingReducer(state, action){
		switch(action.type){
			case DELETE_TIMING:
				let deleted_timings = [
					...state,
					action.timing
				];
				return deleted_timings;
			default:
				return state;
		}
	}

	deleteSessionReducer(state, action){
		switch(action.type){
			case DELETE_SESSION:
				let deleted_sessions = [
					...state,
					action.session
				];
				return deleted_sessions;
			default:
				return state;
		}
	}

	toastReducer(state, action){
		switch(action.type){
			case TOAST_SHOW:
				return action.toast;
			default:
				return state;
		}
	}

	findView(){
		/**
		 * Only run one time
		 */
		if(this._hasFindView){
			return;
		}
		this._hasFindView = true;

		this.admin_step_go = document.querySelectorAll('.go');
		this.admin_step    = document.querySelectorAll('#admin-step-container .admin-step');

		this.add_session_btn  = document.querySelector('#add_session_btn');
		this.save_session_btn = document.querySelector('#save_session_btn');
	}

	event(){
		this.findView();

		this.admin_step_go
			.forEach((el)=>{
				el.addEventListener('click', ()=>{
					let destination = el.getAttribute('destination');
					store.dispatch({type: CHANGE_ADMIN_STEP, step: destination});
				});
			});

		this.add_session_btn
			.addEventListener('click', function(){
				store.dispatch({
					type: ADD_WEEKLY_SESSION
				});
			});

		this.save_session_btn
			.addEventListener('click', function(){
				store.dispatch({
					type: CHANGE_WEEKLY_SESSIONS
				});

				store.dispatch({
					type: CHANGE_ADMIN_STEP,
					step: 'weekly_sessions_view'
				});
			});
	}

	view(){
		let store = window.store;
		let self  = this;

		/**
		 * Debug state
		 */
		let pre = document.querySelector('#redux-state');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			body.appendChild(pre);
		}

		store.subscribe(()=>{
			let action   = store.getLastAction();
			let state    = store.getState();
			let prestate = store.getPrestate();

			/**
			 * Update state for vue
			 * @type {boolean}
			 */
			let is_reuse_vue_state = (action == CHANGE_WEEKLY_SESSIONS);
			if(!is_reuse_vue_state){
				let vue_state = self.getVueState();
				Object.assign(vue_state, state);
			}
			//debug
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

			let first_view  = prestate.init_view == false && state.init_view == true;
			let change_step = prestate.admin_step != state.admin_step;

			let run_admin_step = first_view || change_step;
			if(run_admin_step){
				self.pointToAdminStep();
			}

			/**
			 * Self build weekly_view from weekly_sessions
			 */
			let weekly_sessions_sync = (action == SYNC_WEEKLY_SESSIONS);

			let should_compute_weekly_view_for_vue = first_view || weekly_sessions_sync;
			if(should_compute_weekly_view_for_vue){
				let weekly_view = self.computeWeeklyView();
				Object.assign(vue_state, {weekly_view});
			}

			let show_toast = prestate.toast != state.toast;
			if(show_toast){
				window.Toast.show();
			}
		});
	}

	listener(){
		let store = window.store;
		let self = this;

		store.subscribe(()=>{
			let action   = store.getLastAction();
			let state    = store.getState();
			let prestate = store.getPrestate();

			let is_change_weekly_sessions = (action == CHANGE_WEEKLY_SESSIONS);
			if(is_change_weekly_sessions){
				let action = {
					type: AJAX_UPDATE_WEEKLY_SESSIONS,
					weekly_sessions: state.weekly_sessions,
					deleted_sessions: state.deleted_sessions
				};

				self.ajax_call(action);
			}
		});
	}

	pointToAdminStep(){
		let state = store.getState();

		this.admin_step
			.forEach((step)=>{
				let admin_step = step.getAttribute('id');
				let transform = 'scale(0,0)';
				if(admin_step == state.admin_step){
					transform = 'scale(1,1)';
				}
				step.style.transform = transform;
			});
	}

	computeWeeklyView(){
		let store = window.store;
		let state = store.getState();

		let weekly_sessions = state.weekly_sessions;

		let today  = moment();
		let monday = today.clone().startOf('isoWeek');
		let sunday = today.clone().endOf('isoWeek');

		let weekly_sessions_with_date = [];

		weekly_sessions
			.forEach(session => {
				let current = monday.clone();

				while(current.isBefore(sunday)){
					let day_of_week = current.format('dddd').toLocaleLowerCase();
					let session_day         = `on_${day_of_week}s`;

					if(session[session_day] == 1){
						//clone current session
						//which reuse for many day
						let s  = Object.assign({}, session);

						//assign moment date for session
						s.date = current.clone();
						weekly_sessions_with_date.push(s);
					}

					current.add(1, 'days');
				}
			});


		let weekly_view =
			weekly_sessions_with_date.reduce((carry, session)=>{
				let group_name = session.date.format('dddd');

				if(typeof carry[group_name] == 'undefined'){
					carry[group_name] = [];
				}

				carry[group_name].push(session);

				return carry;
			}, {});

		return weekly_view;
	}

	ajax_call(action){
		if(typeof action.type != 'undefined'){console.log('ajax call', action.type);}
		let self = this;

		store.dispatch({
			type: TOAST_SHOW,
			toast: {
				title:  'Calling ajax',
				content: '...'
			}
		});

		this.hack_ajax();

		switch(action.type){
			case AJAX_UPDATE_WEEKLY_SESSIONS:
				let url  = self.url('sessions');
				// let data = JSON.stringify(action);
				let data = action;
				$.ajax({url, data});
				break;
			default:
				console.log('ajax call not recognize the current acttion', action);
				break;
		}

		// console.log('????')
	}

	hack_ajax(){
		//check if not init
		if(typeof this._has_hack_ajax != 'undefined'){
			return;
		}
		this._has_hack_ajax = true;

		let self = this;

		let o_ajax = $.ajax;
		$.ajax = function(options){
			let data = options.data;
			let data_json = JSON.stringify(data);
			//console.log(data_json);
			options = Object.assign(options, {
				method  : 'POST',
				data    : data_json,
				success : self.ajax_call_success,
				error   : self.ajax_call_error,
				compelte: self.ajax_call_complete
			});


			return o_ajax(options);
		}
	}
	
	url(path){
		let store = window.store;
		let state = store.getState();
		
		//noinspection JSUnresolvedVariable
		let base_url = state.base_url || '';
		
		if(base_url.endsWith('/')){
			base_url = path.substr(1);
		}
		
		if(path.startsWith('/')){
			path = path.substr(1);
		}
		
		return `${base_url}/${path}`;
	}

	ajax_call_success(res){
		console.log(res);
		switch(res.statusMsg){
			case AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS: {
				let toast = {
					title:'Update weekly sessions',
					content: 'Synching success'
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});

				store.dispatch({
					type: SYNC_WEEKLY_SESSIONS,
					weekly_sessions: res.data
				});

				break;
			}
			case AJAX_UPDATE_WEEKLY_SESSIONS_ERROR: {
				let toast = {
					title:'Update weekly sessions fail',
					content: res.data.substr(0, 50)
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});
			}
			default:
				break;

		}
	}

	ajax_call_error(res){
		console.log(res);
	}
	
	ajax_call_complete(){
		
	}
}

let adminSettings = new AdminSettings();