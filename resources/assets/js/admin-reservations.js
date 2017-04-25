const INIT_VIEW = 'INIT_VIEW';

const SHOW_RESERVATION_DIALOG_CONTENT = 'SHOW_RESERVATION_DIALOG_CONTENT';
const HIDE_RESERVATION_DIALOG_CONTENT = 'HIDE_RESERVATION_DIALOG_CONTENT';
const UPDATE_SINGLE_RESERVATION         = 'UPDATE_SINGLE_RESERVATION';
const UPDATE_RESERVATIONS = 'UPDATE_RESERVATIONS';

const SYNC_DATA              = 'SYNC_DATA';
const REFETCHING_DATA        = 'REFETCHING_DATA';
// const SYNC_DATA = 'SYNC_DATA';

const TOAST_SHOW = 'TOAST_SHOW';

const REFETCHING_DATA_SUCCESS     = 'REFETCHING_DATA_SUCCESS';

// AJAX ACTION
const AJAX_UPDATE_RESERVATIONS    = 'AJAX_UPDATE_RESERVATIONS';

const AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';
const AJAX_REFETCHING_DATA        = 'AJAX_REFETCHING_DATA';

//AJAX MSG
const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
const AJAX_UPDATE_SESSIONS_SUCCESS   = 'AJAX_UPDATE_SESSIONS_SUCCESS';
const AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

const AJAX_SUCCESS  = 'AJAX_SUCCESS';
const AJAX_ERROR    = 'AJAX_ERROR';
const AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';
const AJAX_REFETCHING_DATA_SUCCESS = 'AJAX_REFETCHING_DATA_SUCCESS';

//Paypal
const PAYMENT_REFUNDED = 50;
const PAYMENT_PAID     = 100;
const PAYMENT_CHARGED  = 200;

const TODAY        = 'TODAY';
const TOMORROW     = 'TOMORROW';
const NEXT_3_DAYS  = 'NEXT_3_DAYS';
const NEXT_7_DAYS  = 'NEXT_7_DAYS';
const NEXT_30_DAYS = 'NEXT_30_DAYS';
const CUSTOM       = 'CUSTOM';
const CLEAR        = 'CLEAR';

const MODE_EXACTLY = 'MODE_EXACTLY';
const MODE_FROM = 'MODE_FROM';


class AdminReservations {
	/**
	 * @namespace Redux
	 * @namespace moment
	 * @namespace $
	 */
	constructor(){
		this.buildRedux();
		this.buildVue();
		//Hack into these core concept, to get log
		this.hack_store();
		this.hack_ajax();

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
				case SHOW_RESERVATION_DIALOG_CONTENT:
				case HIDE_RESERVATION_DIALOG_CONTENT:
					return Object.assign({}, state, {
						reservation_dialog_content: self.reservationDialogContentReducer(state.reservation_dialog_content, action)
					});
				case TOAST_SHOW:
					return Object.assign({}, state, {
						toast: action.toast
					});
				case SYNC_DATA:{
					return Object.assign(state, action.data);
				}
				default:
					return state;
			}
		}

		window.store = Redux.createStore(rootReducer);
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

	defaultState(){
		let default_state  = window.state || {};

		//let frontend_state = this.getFrontEndState();
		let frontend_state = {
			reservation_dialog_content: {},
			toast: {
				title: 'Title',
					content: 'Content'
			},
			reservations: [],
			// Manage filterd on reservations
			filtered_reservations: [],
			// Decide show|hide
			filtered: false,
			next_3_days: null,
			next_7_days: null,
			next_30_days: null,
			filter_date_picker: null,
			custom_pick_day: null,
		};

		
		return Object.assign(frontend_state, default_state);
	}

	buildVue(){
		window.vue_state = this.buildVueState();

		let self  = this;

		this.vue  = new Vue({
			/** @namespace moment */
			el: '#app',
			data: window.vue_state,
			mounted(){
				document.dispatchEvent(new CustomEvent('vue-mounted'));
				self.event();
				self.view();
				self.listener();
			},
			methods: {
				_reservationDetailDialog(e){
					try{
						let tr = this._findTrElement(e);
						this._remarksAsStaffRead(tr);
						//Clone it into reservation dialog content
						let reservation_index  = tr.getAttribute('reservation-index');
						let picked_reservation = this.reservations[reservation_index];
						let dialog_reservation = Object.assign({}, picked_reservation);
						//Diloag need data for other stuff
						//Self update for itself
						let date = moment(dialog_reservation.reservation_timestamp, 'Y-M-D H:m:s');
						dialog_reservation.date_str = date.format('YYYY-MM-DD');
						dialog_reservation.time_str = date.format('HH:mm');

						//Update these info into vue
						Object.assign(window.vue_state, {reservation_dialog_content: dialog_reservation});

						store.dispatch({
							type: SHOW_RESERVATION_DIALOG_CONTENT,
							reservation_dialog_content: dialog_reservation
						});
					}catch(e){}
				},

				_remarksAsStaffRead(tr){
					try{
						let reservation_index  = tr.getAttribute('reservation-index');
						let picked_reservation = this.reservations[reservation_index];
						//Update reservations staff_read
						picked_reservation.staff_read_state = true;
					}catch(e){}
				},

				_findTrElement(e){
					let tr = e.target;

					let path = [tr].concat(e.path);

					let i = 0;
					let found_tr = null;
					let is_click_on_edit_form = false;

					while(i < path.length && !found_tr){
						let tr = path[i];

						/**
						 * Click on input / select to edit info
						 */
						if(!is_click_on_edit_form){
							//try does it click on edit form
							is_click_on_edit_form =
								tr.tagName == 'INPUT'
								|| tr.tagName == 'TEXTAREA'
								|| tr.tagName == 'SELECT'
								|| tr.tagName == 'BUTTON';
						}

						if(tr.tagName == 'TR'){
							found_tr = tr;
						}

						i++;
					}

					if(found_tr){
						//click on edit form, consider as already read it
						//has take action
						if(is_click_on_edit_form){
							this._remarksAsStaffRead(found_tr);
							return null;
						}
					}

					return found_tr;
				},

				_updateSingleReservation(){
					let reservation_dialog_content = this.reservation_dialog_content;
					//Recalculate reservation timestamp from select data
					reservation_dialog_content.reservation_timestamp = `${reservation_dialog_content.date_str} ${reservation_dialog_content.time_str}:00`;

					let reservations = this.reservations;

					/**
					 * Find which reservation need update info
					 * Base on reservation dialog content
					 */
					let i = 0, found = false;
					while(i < reservations.length && !found){
						if(reservations[i].id == reservation_dialog_content.id){
							found = true;
						}

						i++;
					}

					/**
					 * Get him out
					 */
					let need_update_reservation = reservations[i-1];

					/**
					 * Only assign on reservation key
					 * Not all what come from reservation_dialog_content
					 */
					Object
						.keys(need_update_reservation)
						.forEach(key => {
							need_update_reservation[key] = reservation_dialog_content[key];
						});

					store.dispatch({
						type: HIDE_RESERVATION_DIALOG_CONTENT
					});

					this._updateReservations();
				},

				_updateReservations(){
					let reservations = this.reservations;
					let action = {
						type: AJAX_UPDATE_RESERVATIONS,
						reservations
					}

					self.ajax_call(action);
				},

				_updateReservationPayment(e){
					console.log(e);
					let vue = this;
					let button = e.target;
					if(button.tagName == 'BUTTON'){
						try{
							let action = button.getAttribute('action');
							let reservation_index  = button.getAttribute('reservation-index');
							let picked_reservation = vue.reservations[reservation_index];
							
							let payment_status;

							switch(action){
								default:
									//payment_status = PAYMENT_PAID;
									break;
								case 'refund':
									payment_status = PAYMENT_REFUNDED;
									break;
								case 'charge':
									payment_status = PAYMENT_CHARGED;
									break;
							}

							if(payment_status){
								picked_reservation.payment_status = payment_status;
							}

							//Stop bubble event
							//e.stopPropagation();
							//let it touch to tr to resolve as read

							this._updateReservations();
						}
						catch(e){}
					}
				},

				_toggleFilter(){
					let current_state = this.filtered;
					// Ok, toggle it
					current_state     = !current_state;
					// Update it
					this.filtered     = current_state;
				},

				/**
				 * Fitler base on a date
				 * @param date
				 * @param mode
				 *      two mode supported: 'exactly', 'from'
				 * @private
				 */
				_fitlerReservationByDay(date, mode = MODE_EXACTLY){
					let reservations = this.reservations;
					// Assign reservations with moment date obj
					let reservations_with_date = reservations.map(reservation => {
						if(!reservation.date){
							let timestamp    = reservation.reservation_timestamp;
							reservation.date = moment(timestamp, 'YYYY-MM-DD HH:mm:ss');
						}

						return reservation;
					});

					// Update back resersvations
					this.reservations = reservations_with_date;

					let dateQueryFunction = '';
					switch(mode){
						case MODE_EXACTLY:{
							dateQueryFunction = function(reservation){
								return reservation.date.isSame(date, 'day');
							};
							break;
						}
						case MODE_FROM:{
							dateQueryFunction = function(reservation){
								return reservation.date.isSameOrAfter(date);
							};
							break;
						}
						default: {
							throw 'No mode is specified';
						}

					}

					let filtered_reservations = reservations_with_date.filter(dateQueryFunction);

					// Update filtered reservations;
					this.filtered_reservations = filtered_reservations;
				},

				_filter(which_case){
					//console.log(which_case);
					switch(which_case){
						case TODAY:{
							console.log('see click today');
							let today = moment({hour: 0, minute: 0, seconds: 0});
							let mode  = MODE_EXACTLY;

							this._fitlerReservationByDay(today, mode);
							break;
						}
						case TOMORROW:{
							console.log('see click tomorrow');
							let tomorrow = moment({hour: 0, minute: 0, seconds: 0}).add(1, 'days');
							let mode     = MODE_EXACTLY;

							this._fitlerReservationByDay(tomorrow, mode);
							break;
						}
						case NEXT_3_DAYS:{
							// When call next_3_days, means next 3 days from current search
							// if no current search stored > default is today
							if(!this.next_3_days){
								this.next_3_days = moment({hour: 0, minute: 0, seconds: 0});
							}

							let current     = this.next_3_days;

							let next_3_days = current.clone().add(3, 'days');
							// Update search step ifself
							this.next_3_days= next_3_days;

							// Call filter base on this step
							let mode = MODE_FROM;
							this._fitlerReservationByDay(next_3_days, mode);

							break;
						}
						case NEXT_7_DAYS:{
							// When call next_30_days, means next 7 days from current search
							// if no current search stored > default is today
							if(!this.next_30_days){
								this.next_30_days = moment({hour: 0, minute: 0, seconds: 0});
							}

							let current     = this.next_30_days;

							let next_7_days = current.clone().add(7, 'days');
							// Update search step ifself
							this.next_30_days= next_7_days;

							// Call filter base on this step
							let mode = MODE_FROM;
							this._fitlerReservationByDay(next_7_days, mode);

							break;
						}
						case NEXT_30_DAYS:{
							// When call next_30_days, means next 30 days from current search
							// if no current search stored > default is today
							if(!this.next_30_days){
								this.next_30_days = moment({hour: 0, minute: 0, seconds: 0});
							}

							let current     = this.next_30_days;

							let next_30_days = current.clone().add(30, 'days');
							// Update search step ifself
							this.next_30_days= next_30_days;

							// Call filter base on this step
							let mode = MODE_FROM;
							this._fitlerReservationByDay(next_30_days, mode);

							break;
						}
						case CUSTOM:{
							let date_str = this.custom_pick_day;
							console.log(date_str);
							// Luckily, format of date is YYYY-MM-DD
							// Can't change this default
							// Ok, cross platform, parse it
							let date    = moment(date_str, 'YYYY-MM-DD');
							//console.log(date);
							let mode    = MODE_EXACTLY;
							this._fitlerReservationByDay(date, mode);

							break;
						}
						case CLEAR:{break;}
					}
				},

				_clearSearch(){
					/**
					 * Return current query to first state
					 */
					this.next_3_days     = null;
					this.next_7_days     = null;
					this.next_30_days    = null;
					this.custom_pick_day = null;

					this.filter_date_picker = null;

					this.filtered = false;
				},

			}
		});
	}

	buildVueState(){
		return Object.assign({}, store.getState());
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
			case SHOW_RESERVATION_DIALOG_CONTENT:{
				return action.reservation_dialog_content;
			}
			case HIDE_RESERVATION_DIALOG_CONTENT:{
				return state;
			}
			default:
				return state;
		}
	}

	reservationsReducer(state, action){
		switch(action.type){
			case UPDATE_SINGLE_RESERVATION: {


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

	_findView(){
		///Only run one time
		if(this._hasFindView)
			return;
		
		this._hasFindView = true;

		this.reservation_dialog = $('#reservation-dialog');
	}

	event(){
		this._findView();
		
		let self = this;
		
		document.addEventListener('switch-outlet', (e)=>{
			let outlet_id = e.detail.outlet_id;

			store.dispatch({
				type: TOAST_SHOW,
				toast: {
					title: 'Switch Outlet',
					content: 'Fetching Data'
				}
			});

			let action = {
				type: AJAX_REFETCHING_DATA,
				outlet_id
			}

			/**
			 * By pass store
			 * When handle action in this way
			 */
			self.ajax_call(action);
		});
	}

	view(){
		let store = window.store;
		let self  = this;

		//Debug state
		let pre = document.querySelector('#redux-state');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			//body.appendChild(pre);
		}

		store.subscribe(()=>{
			let action   = store.getLastAction();
			let state    = store.getState();
			let prestate = store.getPrestate();

			// Debug
			if(state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost')){
				pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));
			}

			/**
			 * Show dialog for edit reservation detail
			 */
			if(action == SHOW_RESERVATION_DIALOG_CONTENT){
				self.reservation_dialog.modal('show');
			}


			if(action == HIDE_RESERVATION_DIALOG_CONTENT){
				self.reservation_dialog.modal('hide');
			}

			/**
			 * Show toast
			 */
			if(action == TOAST_SHOW){
				let toast = state.toast;
				//update toast in vue
				Object.assign(window.vue_state, {toast});
				window.Toast.show();
			}

			if(action == SYNC_DATA){
				Object.assign(window.vue_state, store.getState());
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
		});
	}

	ajax_call(action){
		let self = this;

		store.dispatch({
			type: TOAST_SHOW,
			toast: {
				title:  'Calling ajax',
				content: '...'
			}
		});


		let state = store.getState();

		switch(action.type){
			case AJAX_UPDATE_RESERVATIONS: {
				let url       = self.url('');
				let outlet_id = state.outlet_id;
				let data      = Object.assign({}, action, {outlet_id});
				
				$.ajax({url, data});
				break;
			}
			case AJAX_REFETCHING_DATA: {
				let url         = self.url('');
				let data        = Object.assign({}, action);
				
				$.ajax({url, data});
				break;
			}
			default:
				console.log('client side. ajax call not recognize the current acttion', action);
				break;
		}

		// console.log('????')
	}

	ajax_call_success(res){
		let self = this;
		
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
			case AJAX_REFETCHING_DATA_SUCCESS:{
				store.dispatch({
					type: TOAST_SHOW,
					toast: {
						title: 'Switch Outlet',
						content: 'Fetched Data'
					}
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
			case AJAX_UNKNOWN_CASE: {
				let toast = {
					title:'Unknown case',
					content: 'xxx'
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
		//console.log(res);
	}

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

let adminReservations = new AdminReservations();