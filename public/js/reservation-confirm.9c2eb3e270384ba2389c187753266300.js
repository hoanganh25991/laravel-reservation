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
			//Show funny dialog
			var ajax_dialog = $('#ajax-dialog');
			ajax_dialog.modal('show');
			console.log(window.state);
			//Get state from server
			var server_state = window.state || {};

			var vue_state = Object.assign({}, server_state);

			vue_state.reservation.date = moment(state.reservation.date, 'Y-M-D H:m:s');

			this.vue = new Vue({
				el: '#app',
				data: vue_state,
				created: function created() {
					console.log('vue created');
				},
				mounted: function mounted() {
					console.log('vue mounted');
					setTimeout(function () {
						ajax_dialog.modal('hide');
					}, 690);
				}
			});
		}
	}, {
		key: 'findView',
		value: function findView() {
			if (typeof this._hasFindView == 'undefined') {
				this._hasFindView = true;
			} else {
				return;
			}

			this.ajax_dialog = $('#ajax-dialog');
		}
	}, {
		key: 'event',
		value: function event() {
			this.findView();
			var vue = this.vue;
			// let self = this;
		}
	}]);

	return ReservationConfirm;
}();

var reservationConfirm = new ReservationConfirm();