const INIT_VIEW = 'INIT_VIEW';

const CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';

const ADD_WEEKLY_SESSION     = 'ADD_WEEKLY_SESSION';
const CHANGE_WEEKLY_SESSIONS = 'CHANGE_WEEKLY_SESSIONS';

class AdminSettings {
	/**
	 * @namespace Redux
	 * @namespace moment
	 */
	constructor(){
		this.buildRedux();

		this.buildVue();

		/**
		 * Unsafe to bind event when vue not sure init
		 * Bind inside vue-mounted
		 */
		//this.event();

		this.view();

		this.initView();

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
					return Object.assign({}, state, {
						weekly_sessions: self.weeklySessionsReducer(state.weekly_sessions, action)
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
			admin_step: '#weekly_sessions',
			// admin_step: '#weekly_sessions_view',
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
					console.log('see delete timing');

					let i           = e.target;
					if(i.tagName != 'I'){
						console.log('Only handle when click on <i>');
						return;
					}
					let session_index = i.getAttribute('session-index');
					let timing_index  = i.getAttribute('timing-index');
					let session       = this.weekly_sessions[session_index];

					session.timings.splice(timing_index, 1);
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
			let weekly_sessions_change = (action == CHANGE_WEEKLY_SESSIONS);

			let should_compute_weekly_view_for_vue = first_view || weekly_sessions_change;
			if(should_compute_weekly_view_for_vue){
				let weekly_view = self.computeWeeklyView();
				Object.assign(vue_state, {weekly_view});
			}
		});
	}

	pointToAdminStep(){
		let state = store.getState();

		this.admin_step
			.forEach((step)=>{
				let admin_step = step.getAttribute('id');
				let transform = 'scale(0,0)';
				if('#' + admin_step == state.admin_step){
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
}

let adminSettings = new AdminSettings();