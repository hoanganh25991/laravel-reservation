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
const UPDATE_DEPOSIT         = 'UPDATE_DEPOSIT';
const REFETCHING_DATA        = 'REFETCHING_DATA';
const REFETCHING_DATA_SUCCESS= 'REFETCHING_DATA_SUCCESS';
const SWITCH_OUTLET          = 'SWITCH_OUTLET';
const AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';
const SHOW_USER_DIALOG = 'SHOW_USER_DIALOG';
const UPDATE_SINGLE_USER     = 'UPDATE_SINGLE_USER';
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
const AJAX_UPDATE_DEPOSIT         = 'AJAX_UPDATE_DEPOSIT';
const AJAX_REFETCHING_DATA        = 'AJAX_REFETCHING_DATA';


//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_SESSIONS_SUCCESS   = 'AJAX_UPDATE_SESSIONS_SUCCESS';
const AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';

const AJAX_SUCCESS  = 'AJAX_SUCCESS';
const AJAX_ERROR    = 'AJAX_ERROR';
const AJAX_REFETCHING_DATA_SUCCESS = 'AJAX_REFETCHING_DATA_SUCCESS';
const AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

const HIDE_USER_DIALOG = 'HIDE_USER_DIALOG';
const SYNC_VUE_STATE   = 'SYNC_VUE_STATE';

const DONT_HAVE_PERMISSION = 'DONT_HAVE_PERMISSION';


class AdminSettings {
	/** @namespace user.permission_level */
	/** @namespace window.outlets */
	/** @namespace vue.settings.users */
	/**
	 * @namespace Redux
	 * @namespace moment
	 * @namespace $
	 */
	constructor(){
		this.buildRedux();
		this.buildVue();
		this.initView();
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
				case TOAST_SHOW:
					return Object.assign({}, state, {
						toast: self.toastReducer(state.toast, action)
					});
				case SYNC_DATA : {
					return Object.assign({}, state, action.data);
				}
				case HIDE_USER_DIALOG:
				case SHOW_USER_DIALOG: {
					return Object.assign({}, state, {
						user_dialog_content: self.userDialogContentReducer(state.user_dialog_content, action)
					});
				}
				case SYNC_VUE_STATE:{
					return Object.assign({}, state, action.vue_state);
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

	getFrontendState(){
		return {
			init_view: false,
			base_url: null,
			outlet_id: null,
			outlets: [],
			weekly_sessions: [],
			special_sessions: [],
			buffer: {},
			notification: {},
			settings: {},
			deposit: {},
			user: {},
			admin_step: 'weekly_sessions_view',
			user_dialog_content: {
				outlet_ids: []
			},
			deleted_sessions: [],
			deleted_timings: [],
			toast: {
				title: 'Title',
				content: 'Content'
			},
			weekly_view: {},
		};
	}

	defaultState(){
		let default_state  = window.state || {};

		let frontend_state = this.getFrontendState();

		return Object.assign(frontend_state, default_state);
	}

	buildVue(){
		window.vue_state = this.buildVueState();

		let self  = this;

		this.vue  = new Vue({
			el: '#app',
			data: window.vue_state,
			mounted(){
				document.dispatchEvent(new CustomEvent('vue-mounted'));
				self.event();
				self.view();
				self.listener();
			},
			beforeUpdate(){
				let store = window.store;
				let preState = store.getState();

				// Sync vue_state with its parent redux-state
				// Always respect state
				store.dispatch({
					type: SYNC_VUE_STATE,
					vue_state: window.vue_state
				});

				/** Bad code */
				if(preState.user_dialog_content != window.vue_state.user_dialog_content){
					/**
					 * @warn Should call store for update value
					 */
					store.dispatch({
						type: SHOW_USER_DIALOG
					});
				}
			},
			updated(){
				/**
				 * @warn bad code here
				 * should have better way to detect when DOM mounted
				 */
				console.time('$ bind time-picker');
				$('.jonthornton-time').timepicker({
					//selectOnBlur: true,
					step: 30,
					disableTextInput: true
				})
				.on('change', function(){
					let $i = $(this);
					let i  = $i[0];
					let value = $i.val();

					i.dispatchEvent(new CustomEvent('$change', {detail: {value}}));
				});
				console.timeEnd('$ bind time-picker');
			},
			watch: {
				outlet_id(outlet_id){
					let data = {outlet_id};
					document.dispatchEvent(new CustomEvent('outlet_id', {detail: data}));
				},
				weekly_sessions(weekly_sessions){
					let weekly_view = self.computeWeeklyView(weekly_sessions);
					// Update weekly view
					this.weekly_view= weekly_view;
				}
			},
			methods: {
				_addWeeklySession(){
					let new_session = self._dumpWeeklySession();
					this.weekly_sessions.push(new_session);
				},
				_addTimingToSession(e){
					console.log('see add timing');

					let btn           = e.target;
					let session_index = btn.getAttribute('session-index');
					let session       = this.weekly_sessions[session_index];
					//should destruct array
					session.timings.push(self._dumpTiming());
				},

				_deleteSession(e){
					// console.log(e.target);
					console.log('see delete session');
					try{
						let i = this._findIElement(e);
						let session_index = i.getAttribute('session-index');
						let session = this.weekly_sessions[session_index];
						this.weekly_sessions.splice(session_index, 1);
						//should destruct array
						this.deleted_sessions.push(session);
					}catch(e){
						return;
					}
				},

				_updateSingleTimingArrival(timing, timing_property, event){
					//It quite weird that, vue can't see update value from
					//another time picker set value for input
					//console.log('see timing updated', timing, timing_property, event);
					let data = event.detail;
					//@see timing_edit_mode.blade.php, jquery at script show
					// which data handled file
					timing[timing_property] = data.value;
				},

				_timingsMounted(){
					console.log('see timings mounted');
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
						//should destruct array
						this.deleted_timings.push(timing);
					}catch(e){
						return;
					}
				},

				_addSpecialSession(){
					let new_special_session = self._dumpSpecialSession();
					this.special_sessions.push(new_special_session);
				},

				_addTimingToSpecialSession(e){
					console.log('see add timing');

					let btn           = e.target;
					let session_index = btn.getAttribute('session-index');
					let session       = this.special_sessions[session_index];
					//should destruct array
					session.timings.push(self._dumpTiming());
				},

				_deleteSpecialSession(e){
					// console.log(e.target);
					console.log('see delete session');
					try{
						let i = this._findTrElement(e);
						let session_index = i.getAttribute('session-index');

						let session = this.special_sessions[session_index];
						this.special_sessions.splice(session_index, 1);
						//should destruct array
						this.deleted_sessions.push(session);
					}catch(e){
						return;
					}
				},

				_deleteTimingInSpecialSession(e){
					// console.log(e.target);
					console.log('see delete timing');
					try{
						let i = this._findTrElement(e);
						let session_index = i.getAttribute('session-index');
						let timing_index  = i.getAttribute('timing-index');
						let session = this.special_sessions[session_index];

						let timing = session.timings[timing_index];
						session = session.timings.splice(timing_index, 1);
						//should destruct array
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
					let vue = this;

					//noinspection JSUnresolvedVariable
					this._resolveTimingArrivalTime(vue.weekly_sessions);

					//noinspection JSUnresolvedVariable
					let action = {
						type             : AJAX_UPDATE_SESSIONS,
						sessions         : vue.weekly_sessions,
						deleted_sessions : vue.deleted_sessions,
						deleted_timings  : vue.deleted_timings
					};

					self.ajax_call(action);
				},

				_updateSpecialSession(){
					let vue = this;

					//noinspection JSUnresolvedVariable
					this._resolveTimingArrivalTime(vue.special_sessions);

					//noinspection JSUnresolvedVariable
					let action = {
						type             : AJAX_UPDATE_SESSIONS,
						sessions         : vue.special_sessions,
						deleted_sessions : vue.deleted_sessions,
						deleted_timings  : vue.deleted_timings
					};

					self.ajax_call(action);
				},

				_updateBuffer(){
					let vue = this;

					let action = {
						type : AJAX_UPDATE_BUFFER,
						buffer : vue.buffer
					}

					self.ajax_call(action);
				},

				_updateNotification(){
					let vue = this;

					let action = {
						type : AJAX_UPDATE_NOTIFICATION,
						notification : vue.notification
					}

					self.ajax_call(action);
				},

				_updateSettings(){
					/**
					 * Check user list has at least 1 Administrator
					 */
					let users = this.settings.users;
					let administrator = users.filter(user => user.permission_level == 10);
					if(administrator.length == 0){
						let toast = {
							title: 'Settings > Users',
							content: 'Need at least one Administrator',
							type: 'danger'
						}

						store.dispatch({
							type: TOAST_SHOW,
							toast
						});

						return;
					}

					let action = {
						type : AJAX_UPDATE_SETTINGS,
						settings : this.settings
					}

					self.ajax_call(action);
				},

				_updateDeposit(){
					let action = {
						type : AJAX_UPDATE_DEPOSIT,
						deposit : this.deposit
					}

					self.ajax_call(action);
				},

				_updateSingleUser(){
					console.log('see you click');
					let u = this.user_dialog_content;
					/**
					 * Before call update
					 * Check if validate password
					 */
					if(u.reset_password){
						if(!u.password || u.password.length < 6){
							u.password_error = true;
							return;
						}else{
							u.password_error = false;
						}

						if(u.password != u.confirm_password){
							u.password_mismatch = true;
							return;
						}else{
							u.password_mismatch = false;
						}
					}

					store.dispatch({
						type: HIDE_USER_DIALOG
					});

					let user_dialog_content = this.user_dialog_content;

					let users = this.settings.users;

					let i = 0, found = false;
					while(i < users.length && !found){
						if(users[i].id == user_dialog_content.id){
							found = true;
						}

						i++;
					}

					/**
					 * Get him out
					 */
					let need_update_user = users[i-1];

					/**
					 * Only assign on reservation key
					 * Not all what come from reservation_dialog_content
					 */
					Object
						.keys(need_update_user)
						.forEach(key => {
							need_update_user[key] = user_dialog_content[key];
						});

					/**
					 * Allow reset password
					 */
					let {reset_password, password} = user_dialog_content;

					Object.assign(need_update_user, {reset_password, password});

					this._updateSettings();
				},

				_wantToChangePassword(){
					console.log('see as for reset password');
					this.user_dialog_content.reset_password = true;
					this.user_dialog_content.password          = '';
					this.user_dialog_content.confirm_password  = '';
				},

				_updateUserDialog(e){
					console.log('see tr click');
					try{
						let tr = this._findTrElement(e);

						let user_index = tr.getAttribute('user-index');
						let selected_user       = this.settings.users[user_index];

						let user_dialog_content = Object.assign({}, selected_user);
						/**
						 * Init fake password
						 * If don't want to change
						 * @type {boolean}
						 */
						user_dialog_content.reset_password    = false;
						user_dialog_content.password          = 'xxxxxx';
						user_dialog_content.confirm_password  = 'xxxxxx';
						user_dialog_content.password_mismatch = false;
						user_dialog_content.password_error    = false;
						/**
						 * Set up user dialog content data
						 */
						// this.user_dialog_content = user_dialog_content
						Object.assign(window.vue_state, {user_dialog_content});


					}catch(e){}
				},

				_findTrElement(e){
					let tr = e.target;

					let path = [tr].concat(e.path);

					let i = 0;
					while(i < path.length){
						let tr = path[i];

						/**
						 * Click on input / select to edit info
						 */
						let is_click_on_edit_form =
							tr.tagName == 'INPUT'
							|| tr.tagName == 'TEXTAREA'
							|| tr.tagName == 'SELECT';

						if(is_click_on_edit_form){
							return null;
						}

						if(tr.tagName == 'TR'){
							return tr;
						}

						i++;
					}

					return null;
				},

				_updateTimingDisabled(e){
					console.log(e);
					let input = e.target;
					if(input.tagName == 'INPUT'){
						try{
							let session_id = input.getAttribute('session-id');
							let timing_index = input.getAttribute('timing-index');

							let sessions = this.weekly_sessions.filter(session => session.id == session_id);

							//try to find him in special
							/** @warn data should be nomarlize, in this way, hard to keep track event when has session-id */
							if(sessions.length == 0){
								sessions = this.special_sessions.filter(session => session.id == session_id);
							}

							if(sessions.length == 0){
								return;
							}

							let timing = sessions[0].timings[timing_index];

							timing.disabled = !input.checked;
						}catch(e){
							return;
						}

					}
				},

				_resolveTimingArrivalTime(sessions){
					return;
					sessions.forEach(session => {
						session
							.timings
							.forEach(timing => {
								if(timing.first_arrival_time.split(':').length == 2){
									timing.first_arrival_time = timing.first_arrival_time + ':00';
								}

								if(timing.last_arrival_time.split(':').length == 2){
									timing.last_arrival_time = timing.last_arrival_time + ':00';
								}
							});
					});
				}
			}

		});


	}

	buildVueState(){
		let vue_state = this.getFrontendState();

		return vue_state;
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


	userDialogContentReducer(state, action){
		switch(action.type){
			case SHOW_USER_DIALOG:
			case HIDE_USER_DIALOG: {
				return state;
			}
			default:
				return state;
		}
	}

	adminStepReducer(state, action){
		switch(action.type){
			case CHANGE_ADMIN_STEP:
				return action.step;
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
		let store = window.store;
		let state = store.getState();

		let outlet_id = state.outlet_id;

		let dump_session = {
			"id": null,
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
			"first_arrival_time": "10:00:00",
			"last_arrival_time": "13:00:00",
			"timings": [
				this._dumpTiming()
			]
		};

		return dump_session;
	}

	_dumpTiming(){
		let dump_timing = {
			"id": null,
			"session_id": null,
			"timing_name": "timing x",
			"disabled": false,
			"first_arrival_time": "10:00:00",
			"last_arrival_time": "11:00:00",
			"interval_minutes": 30,
			"capacity_1": 1,
			"capacity_2": 1,
			"capacity_3_4": 1,
			"capacity_5_6": 1,
			"capacity_7_x": 1,
			"max_pax": 20,
			"children_allowed": true,
			"is_outdoor": null,
		};

		return dump_timing;
	}

	toastReducer(state, action){
		switch(action.type){
			case TOAST_SHOW:
				return action.toast;
			default:
				return state;
		}
	}

	_dumpSpecialSession(){
		let store = window.store;
		let state = store.getState();

		let outlet_id = state.outlet_id;
		let today     = moment();
		let date_str  = today.format('YYYY-MM-DD');

		let dump_special_session = {
			"id": null,
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
			"timings": [
				this._dumpTiming()
			]
		};

		return dump_special_session;
	}


	_findView(){
		/**
		 * Only run one time
		 */
		if(this._hasFindView)
			return;
		
		this._hasFindView = true;

		this.admin_step_container = document.querySelector('#admin-step-container');

		this.admin_step_go = document.querySelectorAll('.go');
		this.user_dialog   = $('#user-dialog');
	}

	event(){
		this._findView();

		let self = this;

		this.admin_step_go
			.forEach((el)=>{
				el.addEventListener('click', ()=>{
					let destination = el.getAttribute('destination');
					store.dispatch({type: CHANGE_ADMIN_STEP, step: destination});
				});
			});


		document.addEventListener('switch-outlet', (e) => {
			// console.log(e);
			let outlet_id = e.detail.outlet_id;

			store.dispatch({
				type: TOAST_SHOW,
				toast: {
					title: 'Switch Outlet',
					content: 'Fecthing data'
				}
			});

			let action = {
				type: AJAX_REFETCHING_DATA,
				outlet_id
			}

			/**
			 * Handle action in this way
			 * Means bypass store & state
			 * Not respect app-state
			 */
			self.ajax_call(action);
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
			//body.appendChild(pre);
		}

		store.subscribe(()=>{
			let action = store.getLastAction();
			let state = store.getState();
			let prestate = store.getPrestate();

			// Debug
			if(state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')){
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));
			}

			/**
			 * Change admin step
			 * @type {boolean}
			 */
			let first_view  = prestate.init_view == false && state.init_view == true;
			let change_step = prestate.admin_step != state.admin_step;

			let run_admin_step = first_view || change_step;

			if(run_admin_step){
				self.pointToAdminStep();
			}

			/**
			 * Show toast
			 * @type {boolean}
			 */
			if(action == TOAST_SHOW){
				window.Toast.show();
			}

			if(action == SHOW_USER_DIALOG){
				self.user_dialog.modal('show');
			}

			if(action == HIDE_USER_DIALOG){
				self.user_dialog.modal('hide');
			}


			/**
			 * Self build weekly_view from weekly_sessions
			 */

			if(action == SYNC_DATA){
				/**
				 * Guest next admin step
				 */
				let next_admin_step = state.admin_step + '_view';
				/**
				 * Check if guest is right
				 */
				let element = document.querySelector('#' + next_admin_step);
				if(element){
					store.dispatch({
						type: CHANGE_ADMIN_STEP,
						step: next_admin_step
					});
				}
			}

			// Sync data from redux state to vue
			Object.assign(window.vue_state, store.getState());
		});
	}

	listener(){
		let store = window.store;
		let self = this;

		store.subscribe(()=>{
			let action   = store.getLastAction();
			let state    = store.getState();
			let prestate = store.getPrestate();

		});
	}

	pointToAdminStep(){
		let state = store.getState();
		let prestate = store.getPrestate();

		/**
		 * Improve performance by ONLY toggle 2 step
		 */

		let pre_step     = this.admin_step_container.querySelector('#' + prestate.admin_step);
		let current_step = this.admin_step_container.querySelector('#' + state.admin_step);
		if(pre_step){
			pre_step.style.transform = 'scale(0,0)';
		}

		if(current_step){
			current_step.style.transform = 'scale(1,1)';
		}
	}

	computeWeeklyView(weekly_sessions){
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
		let store = window.store;
		let state = store.getState();
		let self  = this;


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
				let url       = self.url('');
				let outlet_id = state.outlet_id
				let data      = Object.assign({}, action, {outlet_id});

				$.ajax({url, data});
				break;
			}
			case AJAX_UPDATE_BUFFER: 
			case AJAX_UPDATE_NOTIFICATION: 
			case AJAX_UPDATE_SETTINGS:
			case AJAX_UPDATE_DEPOSIT: {
				let url       = self.url('');
				let outlet_id = state.outlet_id
				let data      = Object.assign({}, action, {outlet_id});

				$.ajax({url, data});
				break;
			}
			case AJAX_REFETCHING_DATA: {
				let url  = self.url('');
				let data = action;
				
				$.ajax({url, data});
				break;
			}
			default:
				console.log('client side. ajax call not recognize the current acttion', action);
				break;
		}
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
			case AJAX_VALIDATE_FAIL: {
				let toast = {
					title: 'Validate Fail',
					content: JSON.stringify(res.data)
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});
				
				break;
			}
			case AJAX_REFETCHING_DATA_SUCCESS: {
				let toast = {
					title:'Switch Outlet',
					content: 'Fetched Data'
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
			default:
				break;

		}
	}

	ajax_call_error(resLiteral){
		console.log(resLiteral);
		// Please don't change these code
		let res = JSON.parse(resLiteral.responseText);

		let toast = {};

		switch(res.statusMsg){
			case DONT_HAVE_PERMISSION:{
				let info = JSON.stringify(res.data);

				toast = {
					title: DONT_HAVE_PERMISSION,
					content: info
				};

				break;
			}
			default: {
				toast = {
					title:'Server error',
					content: resLiteral.responseText
				};

				break;
			}
		}

		store.dispatch({
			type: TOAST_SHOW,
			toast
		});
	}
	
	ajax_call_complete(){}



	hack_ajax(){
		//check if not init
		if(this._hasHackAjax)
			return;

		this._hasHackAjax = true;

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

	url(path = ''){
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

		let url = `${base_url}/${path}`;

		if(url.endsWith('/')){
			url = path.substr(1);
		}
		
		return url;
	}
}

let adminSettings = new AdminSettings();