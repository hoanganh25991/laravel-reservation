const INIT_VIEW = 'INIT_VIEW';

const CHANGE_RESERVATION_DIALOG_CONTENT = 'CHANGE_RESERVATION_DIALOG_CONTENT';
const UPDATE_SINGLE_RESERVATIONS         = 'UPDATE_SINGLE_RESERVATIONS';
const UPDATE_RESERVATIONS = 'UPDATE_RESERVATIONS';

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
// const SYNC_DATA = 'SYNC_DATA';

const TOAST_SHOW = 'TOAST_SHOW';

const REFETCHING_DATA_SUCCESS     = 'REFETCHING_DATA_SUCCESS';

// AJAX ACTION
const AJAX_UPDATE_RESERVATIONS    = 'AJAX_UPDATE_RESERVATIONS';

const AJAX_UPDATE_WEEKLY_SESSIONS = 'AJAX_UPDATE_WEEKLY_SESSIONS';
const AJAX_DELETE_WEEKLY_SESSIONS = 'AJAX_DELETE_WEEKLY_SESSIONS';
const AJAX_UPDATE_SESSIONS        = 'AJAX_UPDATE_SESSIONS';
const AJAX_UPDATE_BUFFER          = 'AJAX_UPDATE_BUFFER';
const AJAX_UPDATE_NOTIFICATION    = 'AJAX_UPDATE_NOTIFICATION';
const AJAX_UPDATE_SETTINGS        = 'AJAX_UPDATE_SETTINGS';
const AJAX_UPDATE_DEPOSIT         = 'AJAX_UPDATE_DEPOSIT';
const AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';
const AJAX_REFETCHING_DATA        = 'AJAX_REFETCHING_DATA';

//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_SESSIONS_SUCCESS   = 'AJAX_UPDATE_SESSIONS_SUCCESS';
const AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

const AJAX_SUCCESS  = 'AJAX_SUCCESS';
const AJAX_ERROR    = 'AJAX_ERROR';
const AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';
const AJAX_REFETCHING_DATA_SUCCESS  = 'AJAX_REFETCHING_DATA_SUCCESS';

/**
 * Payment status
 */
const PAYMENT_UNPAID         = 25;
const PAYMENT_REFUNDED       = 50;
const PAYMENT_PAID           = 100;
const PAYMENT_CHARGED        = 200;



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
					return Object.assign({}, state, {
						reservation_dialog_content: self.reservationDialogContentReducer(state.reservation_dialog_content, action)
					});
				case UPDATE_SINGLE_RESERVATIONS:
				case UPDATE_RESERVATIONS:
					return Object.assign({}, state, {
						reservations: self.reservationsReducer(state.reservations, action)
					});
				case TOAST_SHOW:
					return Object.assign({}, state, {
						toast: action.toast
					});
				case SYNC_DATA:{
					console.log('still not handle SYNC DATA case');
					return state;
				}
				case REFETCHING_DATA: {
					return state;
				}
				case REFETCHING_DATA_SUCCESS: {
					let state = action.state;
					let frontend_state =  self.getFrontEndState();

					return Object.assign(state, frontend_state);
				}
				default:
					return state;
			}
		}

		window.store = Redux.createStore(rootReducer);

		this.hack_store();

	}

	hack_store(){
		let store = window.store;
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

	getFrontEndState(){
		return {
			init_view : false,
			reservation_dialog_content: {},
		};
	}

	defaultState(){
		let default_state  = window.state || {};
		let frontend_state =  this.getFrontEndState();

		return Object.assign(default_state, frontend_state);
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
				self.view();
				self.listener();


			},
			beforeUpdate(){


			},
			updated(){
				// let store  = window.store;
				// let action = store.getLastAction();
				//
				// /**
				//  * Calling out dialog for reservation detail
				//  * To bundle change, wait for SAVE clicked
				//  * @type {boolean}
				//  */
				// let should_auto_update = action != CHANGE_RESERVATION_DIALOG_CONTENT;
				// if(should_auto_update){
				// 	store.dispatch({
				// 		type: UPDATE_RESERVATIONS
				// 	});
				// }
			},
			methods: {
				_reservationDetailDialog(e){
					// console.log('see tr click');
					// console.log(e);
					try{
						let tr = this._findTrElement(e);
						let reservation_index = tr.getAttribute('reservation-index');

						/**
						 * Update to mark as staff read
						 * @warn modify in this way VERY DANGEROUS
						 * Many thing may make a reservation maked as READ
						 * Type on something,...
						 * Change on something,...
						 * @type {boolean}
						 */
						this.reservations[reservation_index].staff_read_state = true;

						let reservation = Object.assign({}, this.reservations[reservation_index]);

						store.dispatch({
							type: CHANGE_RESERVATION_DIALOG_CONTENT,
							reservation_dialog_content: reservation
						});
					}catch(e){
						// console.log('click on other element, which more important than tr')
						return;
					}
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

				_updateReservationDialog(){
					let reservation_dialog_content = this.reservation_dialog_content
					store.dispatch({
						type: UPDATE_SINGLE_RESERVATIONS,
						reservation_dialog_content
					});
				},

				_updateReservations(){
					store.dispatch({
						type: UPDATE_RESERVATIONS
					});
				},

				_switchOutlet(data){
					store.dispatch({
						type: TOAST_SHOW,
						toast: {
							title: 'Switch Outlet',
							content: 'Fetching Data'
						}
					});

					let action = {
						type: AJAX_UPDATE_SCOPE_OUTLET_ID,
						data
					}

					/**
					 * By pass store
					 * When handle action in this way
					 */
					self.ajax_call(action);
				},

				_updateReservationPayment(e){
					console.log(e);
					let vue = this;
					let button = e.target;
					if(button.tagName == 'BUTTON'){
						try{
							let action = button.getAttribute('action');
							let reservation_index = button.getAttribute('reservation-index');
							
							let reservation = vue.reservations[reservation_index];
							
							let payment_status;
							switch(action){
								default:
									payment_status = PAYMENT_REFUNDED;
									break;
								case 'refund':
									payment_status = PAYMENT_REFUNDED;
									break;
								case 'charge':
									payment_status = PAYMENT_CHARGED;
									break;
							}
							
							reservation.payment_status = payment_status;
							
							e.stopPropagation();

							store.dispatch({
								type: UPDATE_RESERVATIONS,
								// reservations: vue.reservations,
							});
						}
						catch(e){}
					}
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
			default:
				return state;
		}
	}

	reservationsReducer(state, action){
		switch(action.type){
			case UPDATE_SINGLE_RESERVATIONS: {
				let reservation_dialog_content = action.reservation_dialog_content;

				reservation_dialog_content.reservation_timestamp = `${reservation_dialog_content.date_str} ${reservation_dialog_content.time_str}:00`;

				/**
				 * Find which reservation need update info
				 * Base on reservation dialog content
				 * @type {number}
				 */
				let i = 0, index = 0;
				while(i < state.length){
					if(state[i].id == reservation_dialog_content.id){
						index = i;
					}

					i++;
				}

				/**
				 * Get him out
				 */
				let need_update_reservation = state[index];

				/**
				 * Only assign on reservation key
				 * Not all what come from reservation_dialog_content
				 */
				Object
					.keys(need_update_reservation)
					.forEach(key => {
						need_update_reservation[key] = reservation_dialog_content[key];
					});

				return state;
			}
			case UPDATE_RESERVATIONS: {
				let vue_state = window.vue_state;
				let reservations = Object.assign({}, vue_state.reservations);

				return reservations;
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
		
		let self = this;
		
		document.addEventListener('switch-outlet', (e)=>{
			let data = e.detail;
			
			this.vue._switchOutlet(data);
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

			let success_update_single_reservation = action == UPDATE_SINGLE_RESERVATIONS;
			if(success_update_single_reservation){
				self.reservation_dialog.modal('hide');
			};
			/**
			 * Show toast
			 */
			if(action == TOAST_SHOW){
				window.Toast.show();
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

			let update_single_reservation = action == UPDATE_SINGLE_RESERVATIONS;
			if(update_single_reservation){
				let action = {
					type: AJAX_UPDATE_RESERVATIONS,
					reservations: state.reservations
				}

				self.ajax_call(action);
			}

			let update_reservations = action == UPDATE_RESERVATIONS;
			if(update_reservations){
				let action = {
					type: AJAX_UPDATE_RESERVATIONS,
					reservations: state.reservations
				}

				self.ajax_call(action);
			}
			
			if(action == REFETCHING_DATA){
				let action = {
					type: AJAX_REFETCHING_DATA
				}

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
			case AJAX_UPDATE_RESERVATIONS: {
				let url  = self.url('reservations');
				let data = action;
				$.ajax({url, data});
				break;
			}
			case AJAX_UPDATE_SCOPE_OUTLET_ID: {
				let url = self.url('admin');
				let data = action.data;
				$.ajax({url, data});
				break;
			}
			case AJAX_REFETCHING_DATA: {
				let url = self.url('admin/reservations');
				$.ajax({url});
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
		// console.log(res);
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
			case AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS: {
				store.dispatch({
					type: REFETCHING_DATA
				});
				break;
			}
			case AJAX_REFETCHING_DATA_SUCCESS:{
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
			case AJAX_ERROR: {
				let toast = {
					title:'Update fail',
					content: JSON.stringify(res)
				}

				store.dispatch({
					type: TOAST_SHOW,
					toast
				});

				break;
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
	
	ajax_call_complete(res){
		console.log(res);
	}
}

let adminReservations = new AdminReservations();