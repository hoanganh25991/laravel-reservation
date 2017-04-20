const INIT_VIEW   = 'INIT_VIEW';
const DIALOG_SHOW = 'DIALOG_SHOW';
const DIALOG_HAS_DATA = 'DIALOG_SHOW';
const SYNC_RESERVATION = 'SYNC_RESERVATION';
const SYNC_VUE_STATE = 'SYNC_VUE_STATE';
/**
 * @namespace moment
 */
class ReservationConfirm{
	constructor(){
		this.buildRedux();
		this.buildVue();
		//this.event();
	}

	buildRedux(){
		let frontend_state= {init_view: false, dialog: null};
		let server_state  = window.state || {};
		let default_state = Object.assign(frontend_state, server_state);
		// xxx
		let self = this;

		let rootReducer = function(state = default_state, action){
			switch(action.type){
				case INIT_VIEW:{
					let fuck = Object.assign({}, state, {init_view: true});
					return fuck;
				}
				case DIALOG_SHOW: {
					return Object.assign({}, state, {dialog: true});
				}
				case DIALOG_HAS_DATA:{
					return Object.assign({}, state, {dialog: false});
				}
				case SYNC_RESERVATION: {
					let reservation = action.reservation;

					return Object.assign({}, state, {reservation});
				}
				case SYNC_VUE_STATE:{
					return Object.assign({}, state, action.vue_state);
				}
				default:
					return state;
			}
		}

		// window.store = Redux.createStore(reducer);
		window.store = Redux.createStore(rootReducer);

		/**
		 * Enhance store with prestate
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

	initView(){
		let store = window.store;
		store.dispatch({type: INIT_VIEW});
	}

	buildVue(){
		let self = this;
		let store= window.store;
		//Show funny dialog
		let ajax_dialog = $('#ajax-dialog');
		ajax_dialog.modal('show');
		//console.log(window.state);
		//Get state from server
		let vue_state = {
			base_url: '',
			selected_outlet: {},
			reservation: {},
			paypal_token: null,
		};
		//locall vue state
		window.vue_state = vue_state;

		this.vue = new Vue({
			el: '#app',
			data: vue_state,
			created(){},
			beforeUpdate(){
				store.dispatch({
					type: SYNC_VUE_STATE,
					vue_state: window.vue_state
				});
			},
			mounted(){
				//console.log('vue mounted');
				//setup auto hide funny dialog
				setTimeout(function(){
					ajax_dialog.modal('hide');
				}, 690);
				//bind view
				self.event();
				self.listener();
				self.view();
				// Here we go
				self.initView();
				//this._initPaypal();
			},
			updated(){},
			watch: {
				reservation(reservation){
					let date_not_init      = !reservation.date;
					let has_timestamp_data = reservation.reservation_timestamp;
					// Decide should run
					let should_run = date_not_init && has_timestamp_data;
					if(!should_run)
						return;

					let date_time = moment(reservation.reservation_timestamp, 'YYYY-MM-DD HH:mm:ss');
					// Ok, everything is fine
					if(date_time.isValid()){
						let new_reservation = Object.assign({}, reservation, {date: date_time});

						this.reservation = new_reservation;
					}
				},
				paypal_token(){
					// Ok we has paypal_token
					// So init the paypal method
					let reservation = this.reservation;
					// Get out info
					let amount      = reservation.deposit;
					let confirm_id  = reservation.confirm_id;
					let outlet_id   = reservation.outlet_id;
					let paypal_token= this.paypal_token;
					let base_url    = this.base_url;

					let paypal_options = {
						amount,
						confirm_id,
						outlet_id
					};

					let paypal_authorize = new PayPalAuthorize(paypal_token, paypal_options, base_url);
				}
			},
			methods:{}
		});
	}

	_findView(){
		if(this._hasFindView)
			return;

		this._hasFindView = true;

		this.ajax_dialog  = $('#ajax-dialog');
	}

	event(){
		this._findView();

		document.addEventListener('PAYPAL_PAYMENT_SUCCESS', (e)=>{
			let res = e.detail;
			// res.data should contain reservation
			let {reservation} = res.data;
			store.dispatch({
				type: SYNC_RESERVATION,
				reservation
			});
		});
	}

	view(){
		let store = window.store;
		let redex_debug_element = document.querySelector('#redux-state');

		store.subscribe(()=>{
			let store       = window.store;
			let state       = store.getState();
			let prestate    = store.getPrestate();
			let last_action = store.getLastAction();
			let self = this;
			// Only run debug when needed & in local
			let on_local = state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost');
			if(redex_debug_element && on_local){
				let clone_state = Object.assign({}, state);
				// In case available_time so large
				if(clone_state.available_time){
					let keys = Object.keys(clone_state.available_time);
					if(keys.length > 14){
						delete clone_state.available_time;
						console.warn('available_time is large, debug build HTML will slow app, removed it');
					}
				}

				redex_debug_element.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
			}

			if(last_action == DIALOG_SHOW){
				self.ajax_dialog.modal('show');
			}

			if(last_action == DIALOG_HAS_DATA){
				self.ajax_dialog.modal('hide');
			}

			// Sync state to vue
			//console.log('SYNC_STAT_2_VUE');
			Object.assign(window.vue_state, state);
		});
	}

	listener(){}

}

let reservationConfirm = new ReservationConfirm();
