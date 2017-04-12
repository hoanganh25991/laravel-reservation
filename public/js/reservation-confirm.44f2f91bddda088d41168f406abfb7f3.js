'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * @namespace moment
 */
var ReservationConfirm = function () {
	function ReservationConfirm() {
		_classCallCheck(this, ReservationConfirm);

		this.buildVue();
		this.event();
	}

	_createClass(ReservationConfirm, [{
		key: 'buildVue',
		value: function buildVue() {
			var self = this;
			//Show funny dialog
			var ajax_dialog = $('#ajax-dialog');
			ajax_dialog.modal('show');
			console.log(window.state);
			//Get state from server
			var server_state = window.state || {};
			//locall vue state
			window.vue_state = Object.assign({}, server_state);
			//
			window.vue_state.reservation.date = moment(server_state.reservation.reservation_timestamp, 'Y-M-D H:m:s');

			this.vue = new Vue({
				el: '#app',
				data: vue_state,
				created: function created() {},
				mounted: function mounted() {
					//console.log('vue mounted');
					//setup auto hide funny dialog
					setTimeout(function () {
						ajax_dialog.modal('hide');
					}, 690);
					//bind view
					self.event();
					self.listener();
					self.view();

					this._initPaypal();
				},

				methods: {
					_initPaypal: function _initPaypal() {
						var reservation = this.reservation;
						//init paypal
						var amount = reservation.deposit;
						var confirm_id = reservation.confirm_id;
						var outlet_id = reservation.outlet_id;
						var token = this.paypal_token;
						var paypal_url = this.paypal_url;

						window.paypal_authorize = new PayPalAuthorize(token, { amount: amount, confirm_id: confirm_id, outlet_id: outlet_id }, paypal_url);
					}
				}
			});
		}
	}, {
		key: '_findView',
		value: function _findView() {
			if (this._hasFindView) return;

			this._hasFindView = true;

			this.ajax_dialog = $('#ajax-dialog');
		}
	}, {
		key: 'event',
		value: function event() {
			this._findView();

			document.addEventListener('PAYPAL_PAYMENT_SUCCESS', function (e) {
				console.log(e);
				var res = e.detail;

				/**
     * in this case, res.data should contain reservation
     */
				var reservation = res.data.reservation;
				Object.assign(window.vue_state, { reservation: reservation });
			});
		}
	}, {
		key: 'view',
		value: function view() {}
	}, {
		key: 'listener',
		value: function listener() {}
	}]);

	return ReservationConfirm;
}();

var reservationConfirm = new ReservationConfirm();