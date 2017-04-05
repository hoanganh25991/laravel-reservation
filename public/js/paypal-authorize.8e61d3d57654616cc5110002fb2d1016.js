'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var AJAX_PAYMENT_REQUEST = 'AJAX_PAYMENT_REQUEST';
var AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';
var AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
var AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
var AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';

var PayPalAuthorize = function () {
	function PayPalAuthorize(token, paypal_instance_options, base_url) {
		_classCallCheck(this, PayPalAuthorize);

		this.paypal_instance_options = Object.assign({
			flow: 'checkout', // Required
			amount: 10.00, // Required
			currency: 'USD', // Required
			locale: 'en_US',
			enableShippingAddress: true,
			shippingAddressEditable: false,
			shippingAddressOverride: {
				recipientName: 'Scruff McGruff',
				line1: '1234 Main St.',
				line2: 'Unit 1',
				city: 'Chicago',
				countryCode: 'US',
				postalCode: '60652',
				state: 'IL',
				phone: '123.456.7890'
			}
		}, paypal_instance_options);

		this.token = token;

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
				//		authorization: 'TOKEN'
				authorization: self.token
			}, function (clientErr, clientInstance) {
				// Create PayPal component
				//noinspection JSUnresolvedVariable
				braintree.paypal.create({
					client: clientInstance
				}, function (err, paypalInstance) {
					if (err) {
						console.log(err);
						throw err;
					}
					console.log('paypalInstance', paypalInstance);
					paypalButton.addEventListener('click', function () {
						// Tokenize here!
						paypalInstance.tokenize(self.paypal_instance_options, function (err, tokenizationPayload) {
							// Tokenization complete
							// Send tokenizationPayload.nonce to server
							if (err) {
								console.log(err);
								throw err;
							}
							console.log('tokenizationPayload', tokenizationPayload);

							var data = Object.assign({
								tokenizationPayload: JSON.stringify(tokenizationPayload),
								type: AJAX_PAYMENT_REQUEST
							}, self.paypal_instance_options);

							$.ajax({
								url: self.base_url,
								method: 'POST',
								data: data,
								success: function success(res) {
									console.log(res);
									if (res.statusMsg == AJAX_PAYMENT_REQUEST_SUCCESS) {
										$('#paypal-dialog').modal('hide');
										console.log(res);
										console.log('success payment');
										return;
									}
								},
								error: function error(res) {
									if (res.statusMsg == AJAX_PAYMENT_REQUEST_VALIDATE_FAIL || res.statusMsg == AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL || res.statusMsg == AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL) {
										var msg = 'PAYPAL FAIL: see log';

										console.log(msg, res.data);
										window.alert(msg);
										return;
									}
								},
								complete: function complete(res) {
									console.log('response from tokenizationPayload.php', res);
								}
							});
						});
					});
				});
			});
		}
	}]);

	return PayPalAuthorize;
}();