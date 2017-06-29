'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var INIT_VIEW = 'INIT_VIEW';
var DIALOG_SHOW = 'DIALOG_SHOW';
var DIALOG_HAS_DATA = 'DIALOG_HAS_DATA';
var SYNC_RESERVATION = 'SYNC_RESERVATION';
var SYNC_VUE_STATE = 'SYNC_VUE_STATE';

var AJAX_CONFIRM_RESERVATION = 'AJAX_CONFIRM_RESERVATION';
var AJAX_CONFIRM_RESERVATION_SUCCESS = 'AJAX_CONFIRM_RESERVATION_SUCCESS';
var AJAX_RESERVATION_STILL_NOT_RESERVED = 'AJAX_RESERVATION_STILL_NOT_RESERVED';

var ReservationConfirm = function () {

	/**
  * @namespace moment
  * @namespace  vue.thank_you_url
  */

	function ReservationConfirm() {
		_classCallCheck(this, ReservationConfirm);

		this.buildRedux();
		this.buildVue();
		//this.event();
	}

	_createClass(ReservationConfirm, [{
		key: 'buildRedux',
		value: function buildRedux() {
			var frontend_state = { init_view: false, dialog: null };
			var server_state = window.state || {};
			var default_state = Object.assign(frontend_state, server_state);
			// xxx
			var self = this;

			var rootReducer = function rootReducer() {
				var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : default_state;
				var action = arguments[1];

				switch (action.type) {
					case INIT_VIEW:
						{
							var fuck = Object.assign({}, state, { init_view: true });
							return fuck;
						}
					case DIALOG_SHOW:
						{
							return Object.assign({}, state, { dialog: true });
						}
					case DIALOG_HAS_DATA:
						{
							return Object.assign({}, state, { dialog: false });
						}
					case SYNC_RESERVATION:
						{
							var reservation = action.reservation;

							return Object.assign({}, state, { reservation: reservation });
						}
					case SYNC_VUE_STATE:
						{
							return Object.assign({}, state, action.vue_state);
						}
					default:
						return state;
				}
			};

			// window.store = Redux.createStore(reducer);
			window.store = Redux.createStore(rootReducer);

			/**
    * Enhance store with prestate
    */
			var o_dispatch = store.dispatch;
			store.dispatch = function (action) {
				console.info(action.type);
				store.prestate = store.getState();
				store.last_action = action.type;
				o_dispatch(action);
			};

			store.getPrestate = function () {
				return store.prestate;
			};

			store.getLastAction = function () {
				return store.last_action;
			};
		}
	}, {
		key: 'initView',
		value: function initView() {
			var store = window.store;
			store.dispatch({ type: INIT_VIEW });
		}
	}, {
		key: 'buildVue',
		value: function buildVue() {
			var store = window.store;
			//Show funny dialog
			store.dispatch({ type: DIALOG_SHOW });

			var self = this;
			// Vue state at the begining
			// Each keys in this initial state
			// Is what WATCHED by vue
			var vue_state = {
				base_url: '',
				selected_outlet: {},
				reservation: {},
				paypal_token: null,
				thank_you_url: ''
			};
			// Store as global reference
			window.vue_state = vue_state;

			this.vue = new Vue({
				el: '#app',
				data: vue_state,
				beforeCreate: function beforeCreate() {},
				created: function created() {},
				beforeUpdate: function beforeUpdate() {
					// Sync vue with redux-state
					// I love this one
					// Should sync in EVERY STEP
					store.dispatch({
						type: SYNC_VUE_STATE,
						vue_state: window.vue_state
					});
				},
				mounted: function mounted() {
					// Auto hide funny dialog
					setTimeout(function () {
						store.dispatch({ type: DIALOG_HAS_DATA });
					}, 690);
					//bind view
					self.event();
					self.listener();
					self.view();
					// Here we go
					self.initView();
				},
				updated: function updated() {},

				watch: {
					// When reservation change|init at first time
					// Base on his own 'reservation_timestamp' > build on date moment obj
					reservation: function reservation(_reservation) {
						var date_not_init = !_reservation.date;
						var has_timestamp_data = _reservation.reservation_timestamp;
						// Decide should run
						var should_run = date_not_init && has_timestamp_data;
						if (!should_run) return;

						var date_time = moment(_reservation.reservation_timestamp, 'YYYY-MM-DD HH:mm:ss');
						// Ok, everything is fine
						if (date_time.isValid()) {
							var new_reservation = Object.assign({}, _reservation, { date: date_time });

							this.reservation = new_reservation;
						}
					},

					// Ok if has paypal_token
					// Init one braintree to conduct payment
					paypal_token: function paypal_token() {
						// Ok we has paypal_token
						// So init the paypal method
						var reservation = this.reservation;
						// Get out info
						var amount = reservation.deposit;
						var confirm_id = reservation.confirm_id;
						var outlet_id = reservation.outlet_id;
						var paypal_token = this.paypal_token;
						var base_url = this.base_url;

						var paypal_options = {
							amount: amount,
							confirm_id: confirm_id,
							outlet_id: outlet_id
						};

						var paypal_authorize = new PayPalAuthorize(paypal_token, paypal_options, base_url);
					}
				},
				methods: {
					_confirmReservation: function _confirmReservation() {
						var vue = this;
						var data = { type: AJAX_CONFIRM_RESERVATION };

						store.dispatch({ type: DIALOG_SHOW });

						// Do a post request
						// Handle response
						$.ajax({
							url: vue.base_url,
							method: 'POST',
							data: data,
							success: function success(res) {
								console.log(res);
								switch (res.statusMsg) {
									case AJAX_CONFIRM_RESERVATION_SUCCESS:
										{
											var reservation = res.data.reservation;


											store.dispatch({
												type: SYNC_RESERVATION,
												reservation: reservation
											});

											// Ok, move to thank you page
											window.location.href = vue.thank_you_url;

											break;
										}
									default:
										{
											console.warn('Unknown case', res);
											break;
										}
								}
							},
							error: function error(res_literal) {
								//console.log(res);
								//noinspection JSUnresolvedVariable
								console.log(res_literal.responseJSON);
								// It quite weird that in browser window
								// Response as status code != 200
								// res obj now wrap by MANY MANY INFO
								// Please dont change this
								var res = res_literal.responseJSON;
								switch (res.statusMsg) {
									case AJAX_RESERVATION_STILL_NOT_RESERVED:
										{
											var reservation = res.data.reservation;


											store.dispatch({
												type: SYNC_RESERVATION,
												reservation: reservation
											});

											var msg = 'Please complete your payment first. Thank you.';
											window.alert(msg);
											break;
										}
									default:
										{
											console.warn('Unknown case', res);
											break;
										}
								}
							},
							complete: function complete() {
								store.dispatch({ type: DIALOG_HAS_DATA });
							}
						});
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
				var res = e.detail;
				// res.data should contain reservation
				var reservation = res.data.reservation;

				store.dispatch({
					type: SYNC_RESERVATION,
					reservation: reservation
				});
			});
		}
	}, {
		key: 'view',
		value: function view() {
			var _this = this;

			var store = window.store;
			var redex_debug_element = document.querySelector('#redux-state');

			store.subscribe(function () {
				var store = window.store;
				var state = store.getState();
				var prestate = store.getPrestate();
				var last_action = store.getLastAction();
				var self = _this;
				// Only run debug when needed & in local
				var on_local = state.base_url && state.base_url.includes('reservation.dev') || state.base_url.includes('localhost');
				if (redex_debug_element && on_local) {
					var clone_state = Object.assign({}, state);
					// In case available_time so large
					if (clone_state.available_time) {
						var keys = Object.keys(clone_state.available_time);
						if (keys.length > 14) {
							delete clone_state.available_time;
							console.warn('available_time is large, debug build HTML will slow app, removed it');
						}
					}

					redex_debug_element.innerHTML = syntaxHighlight(JSON.stringify(clone_state, null, 4));
				}

				if (last_action == DIALOG_SHOW) {
					self.ajax_dialog.modal('show');
				}

				if (last_action == DIALOG_HAS_DATA) {
					self.ajax_dialog.modal('hide');
				}

				// Sync state to vue
				//console.log('SYNC_STAT_2_VUE');
				Object.assign(window.vue_state, state);
			});
		}
	}, {
		key: 'listener',
		value: function listener() {}
	}]);

	return ReservationConfirm;
}();

var reservationConfirm = new ReservationConfirm();