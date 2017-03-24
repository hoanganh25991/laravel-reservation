const INIT_VIEW = 'INIT_VIEW';

const CHANGE_RESERVATION_DIALOG_CONTENT = 'CHANGE_RESERVATION_DIALOG_CONTENT';
const UPDATE_SINGLE_RESERVATION         = 'UPDATE_SINGLE_RESERVATION';

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


//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_SESSIONS_SUCCESS   = 'AJAX_UPDATE_SESSIONS_SUCCESS';

const AJAX_SUCCESS  = 'AJAX_SUCCESS';
const AJAX_ERROR    = 'AJAX_ERROR';



class AdminReservations {
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
				case CHANGE_RESERVATION_DIALOG_CONTENT:
				case UPDATE_SINGLE_RESERVATION:
					return Object.assign({}, state, {
						reservation_dialog_content: self.reservationDialogContentReducer(state.reservation_dialog_content, action)
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
			reservation_dialog_content: {},
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

				//debug
				// let reservation = Object.assign({}, this.reservations[1]);
				// store.dispatch({
				// 	type: CHANGE_RESERVATION_DIALOG_CONTENT,
				// 	reservation_dialog_content: reservation
				// });
				//
				// $('#reservation-dialog').modal('show');
			},
			updated(){
			},
			methods: {
				_reservationDetailDialog(e){
					console.log('see tr click');
					//console.log(e);
					try{
						let tr = this._findIElement(e);
						let reservation_index = tr.getAttribute('reservation-index');
						let reservation = Object.assign({}, this.reservations[reservation_index]);

						store.dispatch({
							type: CHANGE_RESERVATION_DIALOG_CONTENT,
							reservation_dialog_content: reservation
						});
					}catch(e){
						return;
					}
				},

				_findIElement(e){
					let tr = e.target;

					if(tr.tagName == 'TR'){
						return tr;
					}

					try{
						let tr = e.path[1];

						if(tr.tagName == 'TR'){
							return tr;
						}
					}catch(e){
						return null;
					}

					return null;
				},

				_updateReservationDialog(){
					// store.dispatch({
					// 	type: UPDATE_SINGLE_RESERVATION
					// });
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

	reservationDialogContentReducer(state, action){
		switch(action.type){
			case CHANGE_RESERVATION_DIALOG_CONTENT:{
				let r = action.reservation_dialog_content;
				/**
				 * Modify custom on datetime
				 * @type {*|moment.Moment}
				 */
				let date = moment(r.reservation_timestamp, 'Y-M-D H:m:s');
				r.date_str = date.format('YYYY-MM-DD');
				r.time_str = date.format('HH:mm');
				
				return r;
			}
			case UPDATE_SINGLE_RESERVATION: {
				let r = action.reservation_dialog_content;

				r.reservation_timestamp = `${r.date_str} ${r.time_str}`;

				let action = {
					type: UPDATE_SINGLE_RESERVATION,
					reservation: r
				};


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

		this.reservation_dialog = $('#reservation-dialog');
	}

	event(){
		this.findView();
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
			 * Show dialog for edit reservation detail
			 * @type {boolean}
			 */
			let show_reservation_dialog = action == CHANGE_RESERVATION_DIALOG_CONTENT;
			if(show_reservation_dialog){
				self.reservation_dialog.modal('show');
			}

			if(true){
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
		});
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
			case AJAX_UPDATE_SETTINGS:
			case AJAX_UPDATE_DEPOSIT: {
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

let adminReservations = new AdminReservations();