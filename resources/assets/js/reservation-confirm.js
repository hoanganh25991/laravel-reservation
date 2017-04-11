/**
 * @namespace moment
 */
class ReservationConfirm{
	constructor(){
		this.buildVue();
		this.event();
	}

	buildVue(){
		let self = this;
		//Show funny dialog
		let ajax_dialog = $('#ajax-dialog');
		ajax_dialog.modal('show');
		console.log(window.state);
		//Get state from server
		let server_state = window.state || {};
		//locall vue state
		window.vue_state = Object.assign({}, server_state);
		//
		window.vue_state.reservation.date = moment(server_state.reservation.date, 'Y-M-D H:m:s');

		this.vue = new Vue({
			el: '#app',
			data: vue_state,
			created(){},
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

				this._initPaypal();
			},
			methods:{
				_initPaypal(){
					let reservation= this.reservation;
					//init paypal
					let amount     = reservation.deposit;
					let confirm_id = reservation.confirm_id;
					let outlet_id  = reservation.outlet_id;
					let token      = this.paypal_token;
					let paypal_url = this.paypal_url;

					window.paypal_authorize = new PayPalAuthorize(token, {amount, confirm_id, outlet_id}, paypal_url);
				}
			}
		});
	}

	_findView(){
		if(this._hasFindView)
			return;

		this._hasFindView = true;

		this.ajax_dialog = $('#ajax-dialog');
	}

	event(){
		this._findView();

		document.addEventListener('PAYPAL_PAYMENT_SUCCESS', (e)=>{
			console.log(e);
			let res = e.detail;

			/**
			 * in this case, res.data should contain reservation
			 */
			let reservation = res.data.reservation;
			Object.assign(window.vue_state, {reservation});
		});
	}

	view(){}

	listener(){}

}

let reservationConfirm = new ReservationConfirm();
