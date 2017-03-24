const INIT_VIEW = 'INIT_VIEW';

const CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';

const ADD_WEEKLY_SESSION     = 'ADD_WEEKLY_SESSION';
const ADD_SPECIAL_SESSION    = 'ADD_SPECIAL_SESSION';
const UPDATE_WEEKLY_SESSIONS = 'UPDATE_WEEKLY_SESSIONS';
const SYNC_DATA              = 'SYNC_DATA';
const DELETE_TIMING          = 'DELETE_TIMING';
const DELETE_SESSION         = 'DELETE_SESSION';
const DELETE_SPECIAL_SESSION = 'DELETE_SPECIAL_SESSION';
const UPDATE_SPECIAL_SESSIONS = 'UPDATE_SPECIAL_SESSIONS';
const SAVE_EDIT_IN_VUE_TO_STORE = 'SAVE_EDIT_IN_VUE_TO_STORE';
const UPDATE_BUFFER          = 'UPDATE_BUFFER';
const UPDATE_NOTIFICATION    = 'UPDATE_NOTIFICATION';
const UPDATE_SETTINGS        = 'UPDATE_SETTINGS';

// const SYNC_DATA = 'SYNC_DATA';

const TOAST_SHOW = 'TOAST_SHOW';



// AJAX ACTION
const AJAX_ADD_WEEKLY_SESSIONS    = 'AJAX_ADD_WEEKLY_SESSIONS';
const AJAX_UPDATE_WEEKLY_SESSIONS = 'AJAX_UPDATE_WEEKLY_SESSIONS';
const AJAX_DELETE_WEEKLY_SESSIONS = 'AJAX_DELETE_WEEKLY_SESSIONS';
const AJAX_UPDATE_SESSIONS        = 'AJAX_UPDATE_SESSIONS';
const AJAX_UPDATE_BUFFER          = 'AJAX_UPDATE_BUFFER';
const AJAX_UPDATE_NOTIFICATION    = 'AJAX_UPDATE_NOTIFICATION';
const AJAX_UPDATE_SETTINGS        = 'AJAX_UPDATE_SETTINGS';

//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_SESSIONS_SUCCESS   = 'AJAX_UPDATE_SESSIONS_SUCCESS';

const AJAX_SUCCESS  = 'AJAX_SUCCESS';
const AJAX_ERROR    = 'AJAX_ERROR';



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
				case SAVE_EDIT_IN_VUE_TO_STORE: {
					let branch = action.branch;
					let modified = {};
					let value    = self.vue[branch];
					modified[branch] = value;

					return Object.assign({}, state, modified);
				}
				case UPDATE_WEEKLY_SESSIONS: {
					return Object.assign({}, state, {
						weekly_sessions  : self.vue.weekly_sessions,
						deleted_sessions : self.vue.deleted_sessions,
						deleted_timings  : self.vue.deleted_timings
					});
				}
				case UPDATE_SPECIAL_SESSIONS: {
					return Object.assign({}, state, {
						special_sessions : self.vue.special_sessions,
						deleted_sessions : self.vue.deleted_sessions,
						deleted_timings  : self.vue.deleted_timings
					});
				}
				case UPDATE_BUFFER:
					return Object.assign({}, state, {
						buffer: self.bufferReducer(state.buffer, action)
					});
				case SYNC_DATA : {
					let data = action.data;
					return Object.assign({}, state, data);
				}
				case UPDATE_NOTIFICATION:
					return Object.assign({}, state, {
						notification: self.notificationReducer(state.notification, action)
					});
				case UPDATE_SETTINGS:
					return Object.assign({}, state, {
						settings: self.settingsReducer(state.settings, action)
					});
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
				_addWeeklySession(){
					let new_session = self._dumpWeeklySession();
					let current = this.weekly_sessions;
					this.weekly_sessions = [
						...current,
						new_session
					];
				},
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

						this.deleted_timings.push(timing);
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

						this.deleted_sessions.push(session);
					}catch(e){
						return;
					}
				},
				_addSpecialSession(){
					let new_special_session = self._dumpSpecialSession();
					let current = this.special_sessions;
					this.special_sessions = [
						...current,
						new_special_session
					];
				},

				_addTimingToSpecialSession(e){
					console.log('see add timing');

					let btn           = e.target;
					let session_index = btn.getAttribute('session-index');
					let session       = this.special_sessions[session_index];

					session.timings.push(self._dumpTiming());
				},

				_deleteSpecialSession(e){
					// console.log(e.target);
					console.log('see delete session');
					try{
						let i = this._findIElement(e);
						let session_index = i.getAttribute('session-index');

						let session = this.special_sessions[session_index];
						this.special_sessions.splice(session_index, 1);

						this.deleted_sessions.push(session);
					}catch(e){
						return;
					}
				},

				_deleteTimingInSpecialSession(e){
					// console.log(e.target);
					console.log('see delete timing');
					try{
						let i = this._findIElement(e);
						let session_index = i.getAttribute('session-index');
						let timing_index  = i.getAttribute('timing-index');
						let session = this.special_sessions[session_index];

						let timing = session.timings[timing_index];
						session = session.timings.splice(timing_index, 1);

						this.deleted_timings.push(timing);
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
				},

				_updateWeeklySessions(){
					store.dispatch({
						type: UPDATE_WEEKLY_SESSIONS
					});
				},

				_updateSpecialSession(){
					store.dispatch({
						type: UPDATE_SPECIAL_SESSIONS
					});
				},

				_updateBuffer(){
					store.dispatch({
						type: UPDATE_BUFFER
					});
				},

				_updateNotification(){
					store.dispatch({
						type: UPDATE_NOTIFICATION
					});
				},

				_updateSettings(){
					store.dispatch({
						type: UPDATE_SETTINGS
					});
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
			case SYNC_DATA: {
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

	specialSessionsReducer(state, action){
		switch(action.type){
			case ADD_SPECIAL_SESSION: {
				let new_special_session = this._dumpSpecialSession();
				return [
					...state,
					new_special_session
				];
			}
			case DELETE_SPECIAL_SESSION: {
				return [
					...state,
					action.session
				];
			}
			default:
				return state;
		}
	}

	_dumpSpecialSession(){
		let today = moment();
		let date_str = today.format('YYYY-MM-DD');
		let dump_special_session = {
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
			"timings": [
				this._dumpTiming()
			]
		};

		return dump_special_session;
	}


	bufferReducer(state, action){
		let self = this;
		switch(action.type){
			case UPDATE_BUFFER: {
				return self.vue.buffer
			}
			default:
				return state;
		}
	}

	notificationReducer(state, action){
		let self = this;
		switch(action.type){
			case UPDATE_NOTIFICATION: {
				//noinspection JSUnresolvedVariable
				return self.vue.notification;
			}
			default:
				return state;
		}
	}

	settingsReducer(state, action){
		let self = this;
		switch(action.type){
			case UPDATE_SETTINGS: {
				//noinspection JSUnresolvedVariable
				return self.vue.settings;
			}
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

		this.add_special_session_btn = document.querySelector('#add_special_session_btn');

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
			let action = store.getLastAction();
			let state = store.getState();
			let prestate = store.getPrestate();

			/**
			 * Debug
			 */
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));




			/**
			 * Change admin step
			 * @type {boolean}
			 */
			let first_view  = prestate.init_view == false && state.init_view == true;
			let change_step = prestate.admin_step != state.admin_step;

			let run_admin_step =
				   first_view
				|| change_step;

			if(run_admin_step){
				self.pointToAdminStep();
			}

			/**
			 * Self build weekly_view from weekly_sessions
			 */
			// let sync_data = (action == SYNC_DATA);
			let sync_on_weekly = prestate.weekly_sessions != state.weekly_sessions;

			let should_compute_weekly_view_for_vue =
				   first_view
				// || sync_data;
				|| sync_on_weekly;

			if(should_compute_weekly_view_for_vue){
				let weekly_view = self.computeWeeklyView();
				Object.assign(vue_state, {weekly_view});
			}

			/**
			 * Show toast
			 * @type {boolean}
			 */
			let show_toast = prestate.toast != state.toast;

			if(show_toast){
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
			let change_admin_step  = prestate.admin_step != state.admin_step;
			let jump_out_edit_mode =
				change_admin_step
				&& (
						prestate.admin_step == 'weekly_sessions'
					|| prestate.admin_step == 'special_sessions'
					|| prestate.admin_step == 'buffer'
					|| prestate.admin_step == 'notification'
					|| prestate.admin_step == 'settings'
				);


			if(jump_out_edit_mode){
				let branch = prestate.admin_step;
				store.dispatch({
					type: SAVE_EDIT_IN_VUE_TO_STORE,
					branch: branch
				});
			}

			/**
			 * Update state for vue
			 * @type {boolean}
			 */
			let is_reuse_vue_state =
				action == UPDATE_WEEKLY_SESSIONS
				|| action == ADD_WEEKLY_SESSION
				|| action == ADD_SPECIAL_SESSION
				|| action == DELETE_SESSION
				|| action == DELETE_SPECIAL_SESSION
				|| action == DELETE_TIMING;

			if(!is_reuse_vue_state){
				let vue_state = self.getVueState();
				Object.assign(vue_state, state);
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

			if(action == UPDATE_WEEKLY_SESSIONS){
				let action = {
					type             : AJAX_UPDATE_SESSIONS,
					sessions         : state.weekly_sessions,
					deleted_sessions : state.deleted_sessions,
					deleted_timings  : state.deleted_timings
				};

				self.ajax_call(action);
			}

			if(action == UPDATE_SPECIAL_SESSIONS){
				let action = {
					type             : AJAX_UPDATE_SESSIONS,
					sessions         : state.special_sessions,
					deleted_sessions : state.deleted_sessions,
					deleted_timings  : state.deleted_timings
				};

				self.ajax_call(action);
			}

			if(action == UPDATE_BUFFER){
				let action = {
					type : AJAX_UPDATE_BUFFER,
					buffer : state.buffer
				}

				self.ajax_call(action);
			}

			if(action == UPDATE_NOTIFICATION){
				let action = {
					type : AJAX_UPDATE_NOTIFICATION,
					notification : state.notification
				}

				self.ajax_call(action);
			}
			
			if(action == UPDATE_SETTINGS){
				let action = {
					type : AJAX_UPDATE_SETTINGS,
					settings : state.settings
				}

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

		let weekly_view_in_order = Object.assign({
			'Monday'   : null,
			'Tuesday'  : null,
			'Wednesday': null,
			'Thursday' : null,
			'Friday'   : null,
			'Saturday' : null,
			'Sunday'   : null,
		}, weekly_view);

		// return weekly_view;
		return weekly_view_in_order;
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
			case AJAX_UPDATE_SESSIONS: {
				let url  = self.url('sessions');
				// let data = JSON.stringify(action);
				let data = action;
				$.ajax({url, data});
				break;
			}
			case AJAX_UPDATE_BUFFER: 
			case AJAX_UPDATE_NOTIFICATION: 
			case AJAX_UPDATE_SETTINGS: {
				let url = self.url('outlet-reservation-settings');
				let data = action;
				$.ajax({url, data});
				break;
		}
			default:
				console.log('client side. ajax call not recognize the current acttion', action);
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
			case AJAX_SUCCESS: {
				let toast = {
					title:'Update success',
					content: '＼＿ヘ(ᐖ◞)､ '
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});
				
				store.dispatch({
					type: SYNC_DATA,
					data: res.data
				});

				break;
			}
			case AJAX_ERROR: {
				let toast = {
					title:'Update fail',
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
		let toast = {
			title:'Server error',
			content: '(⊙.☉)7'
		};

		store.dispatch({
			type: TOAST_SHOW,
			toast
		});
	}
	
	ajax_call_complete(){
		
	}
}

let adminSettings = new AdminSettings();