/**
 * @namespace moment
 */
class ReservationConfirm{
	constructor(){
		this.buildVue();
		this.event();
	}

	buildVue(){
		//Show funny dialog
		let ajax_dialog = $('#ajax-dialog');
		ajax_dialog.modal('show');
		console.log(window.state);
		//Get state from server
		let server_state = window.state || {};

		let vue_state = Object.assign({}, server_state);

		vue_state.reservation.date = moment(state.reservation.date, 'Y-M-D H:m:s');

		this.vue = new Vue({
			el: '#app',
			data: vue_state,
			created(){
				console.log('vue created');
			},
			mounted(){
				console.log('vue mounted');
				setTimeout(function(){
					ajax_dialog.modal('hide');
				}, 690);
			}
		});
	}

	findView(){
		if(typeof this._hasFindView == 'undefined'){
			this._hasFindView = true;
		}else{
			return;
		}

		this.ajax_dialog = $('#ajax-dialog');
	}

	event(){
		this.findView();
		let vue = this.vue;
		// let self = this;
	}
}

let reservationConfirm = new ReservationConfirm();
