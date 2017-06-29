'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var AJAX_PAYMENT_REQUEST = 'AJAX_PAYMENT_REQUEST';
var AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';
var AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
var AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
var AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';

var PayPalAuthorize = function () {
	function PayPalAuthorize(paypal_token, paypal_options, base_url) {
		_classCallCheck(this, PayPalAuthorize);

		var dependencies = ['Current endpoint support ajax call: ' + AJAX_PAYMENT_REQUEST, 'Ajax dialog for 2 action: ' + DIALOG_SHOW + ', ' + DIALOG_HAS_DATA];
		console.warn('Dependencies', dependencies);

		this.paypal_options = Object.assign({
			flow: 'checkout', // Required
			amount: 10.00, // Required
			currency: 'SGD', // Required
			locale: 'en_US',

			enableShippingAddress: false
		}, paypal_options);

		this.token = paypal_token;
		this.base_url = base_url;

		this.initPaypal();
	}

	_createClass(PayPalAuthorize, [{
		key: 'initPaypal',
		value: function initPaypal() {
			var self = this;
			// Fetch the button you are using to initiate the PayPal flow
			var paypalButton = document.getElementById('paypal-button');
			// Create a Client component
			//noinspection JSUnresolvedVariable
			braintree.client.create({
				authorization: self.token
			}, function (clientErr, clientInstance) {
				// Create PayPal component
				//noinspection JSUnresolvedVariable
				braintree.paypal.create({
					client: clientInstance
				}, function (err, paypalInstance) {
					if (err) {
						throw err;
					}
					console.log('paypalInstance', paypalInstance);
					paypalButton.addEventListener('click', function () {
						// Tokenize here!
						paypalInstance.tokenize(self.paypal_options, function (err, tokenizationPayload) {
							// Tokenization complete
							// Send tokenizationPayload.nonce to server
							if (err) throw err;

							self.handlePayload(tokenizationPayload);
						});
					});
				});
			});
		}
	}, {
		key: 'handlePayload',
		value: function handlePayload(tokenizationPayload) {
			var self = this;
			// Send payload.nonce to your server
			console.log('tokenizationPayload', tokenizationPayload);

			var data = Object.assign({
				tokenizationPayload: JSON.stringify(tokenizationPayload),
				type: AJAX_PAYMENT_REQUEST
			}, self.paypal_options);

			// Ask for ajax_dialog
			var store = window.store;

			store.dispatch({ type: DIALOG_SHOW });

			$.ajax({
				url: self.base_url,
				method: 'POST',
				data: data,
				success: function success(res) {
					console.log(res);
					switch (res.statusMsg) {
						case AJAX_PAYMENT_REQUEST_SUCCESS:
							{
								console.log('%c Success paypal payment', 'background:#FDD835');
								// Dispatch at document
								document.dispatchEvent(new CustomEvent('PAYPAL_PAYMENT_SUCCESS', { detail: res }));
								break;
							}
						default:
							{
								console.warn('Unknown case of res.statusMsg');
								break;
							}
					}
				},
				error: function error(res_literal) {
					console.log(res_literal);
					//noinspection JSUnresolvedVariable
					console.log(res_literal.responseJSON);
					// It quite weird that in browser window
					// Response as status code != 200
					// res obj now wrap by MANY MANY INFO
					// Please dont change this
					var res = res_literal.responseJSON;
					try {
						// Do normal things with res as in success case
						switch (res.statusMsg) {
							case AJAX_PAYMENT_REQUEST_VALIDATE_FAIL:
							case AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL:
							case AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL:
								{
									var info = JSON.stringify(res);
									var msg = 'Paypal payment fail: ' + info;
									window.alert(msg);
									break;
								}
							default:
								{
									window.alert(res.errorMsg);
									break;
								}
						}
					} catch (e) {
						window.alert(JSON.stringify(res_literal));
					}
				},
				complete: function complete(res) {
					//console.log('response from tokenizationPayload.php', res);
					store.dispatch({ type: DIALOG_HAS_DATA });
					document.dispatchEvent(new CustomEvent('PAYPAL_PAYMENT_FAIL', { detail: res }));
				}
			});
		}
	}]);

	return PayPalAuthorize;
}();