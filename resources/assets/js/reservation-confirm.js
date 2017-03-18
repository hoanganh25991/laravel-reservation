/**
 * @namespace moment
 */
class ReservationConfirm{
	constructor(){
		this.buildVue();
		this.event();
	}

	buildVue(){
		let ajax_dialog = $('#ajax-dialog');
		ajax_dialog.modal('show');

		let state = window.state;
		state.reservation.date = moment(state.reservation.date, 'Y-M-D H:m:s');
		this.vue = new Vue({
			el: '#app',
			data: state,
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
