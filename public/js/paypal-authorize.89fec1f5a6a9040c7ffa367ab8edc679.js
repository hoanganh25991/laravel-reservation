'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var AJAX_SEND_CLIENT_TOKEN = 'AJAX_SEND_CLIENT_TOKEN';

var PayPalAuthorize = function () {
	function PayPalAuthorize() {
		_classCallCheck(this, PayPalAuthorize);
	}

	_createClass(PayPalAuthorize, [{
		key: 'construct',
		value: function construct(token, paypal_instance_options, base_url) {
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
	}, {
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
							$.ajax({
								url: self.base_url + '/pay_pal}',
								method: 'POST',
								data: {
									tokenizationPayload: JSON.stringify(tokenizationPayload),
									type: AJAX_SEND_CLIENT_TOKEN
								},
								success: function success(res) {
									console.log(res);
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