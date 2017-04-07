const AJAX_PAYMENT_REQUEST = 'AJAX_PAYMENT_REQUEST';
const AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';
const AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
const AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
const AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';
class PayPalAuthorize {

	constructor(token, paypal_instance_options, base_url){
		this.paypal_instance_options =
			Object.assign({
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

	initPaypal(){
		let self = this;
		// Fetch the button you are using to initiate the PayPal flow
		var paypalButton = document.getElementById('paypal-button');

		// Create a Client component
		//noinspection JSUnresolvedVariable
		braintree.client.create({
			//		authorization: 'TOKEN'
			authorization: self.token
		}, function(clientErr, clientInstance) {
			// Create PayPal component
			//noinspection JSUnresolvedVariable
			braintree.paypal.create({
				client: clientInstance
			}, function(err, paypalInstance) {
				if(err){
					console.log(err);
					throw err;
				}
				console.log('paypalInstance', paypalInstance);
				paypalButton.addEventListener('click', function() {
					// Tokenize here!
					paypalInstance.tokenize(self.paypal_instance_options, function(err, tokenizationPayload) {
						// Tokenization complete
						// Send tokenizationPayload.nonce to server
						if(err){
							console.log(err);
							throw err;
						}
						console.log('tokenizationPayload', tokenizationPayload);
						
						let data = 
							Object.assign({
								tokenizationPayload: JSON.stringify(tokenizationPayload),
								type: AJAX_PAYMENT_REQUEST
							}, self.paypal_instance_options);
						
						$.ajax({
							url: self.base_url,
							method: 'POST',
							data: data,
							success(res){
								console.log(res);
								if(res.statusMsg == AJAX_PAYMENT_REQUEST_SUCCESS){
									//$('#paypal-dialog').modal('hide');
									console.log(res);
									console.log('success payment');
									document.dispatchEvent(new CustomEvent('PAYPAL_PAYMENT_SUCCESS', {detail: res}));
									return;
								}
							},
							error(res){
								if(res.statusMsg == AJAX_PAYMENT_REQUEST_VALIDATE_FAIL
									|| res.statusMsg == AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL
									|| res.statusMsg == AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL){
									let msg = 'PAYPAL FAIL: see log';

									console.log(msg, res.data);
									window.alert(msg);
									return;
								}
							},
							complete(res){
								console.log('response from tokenizationPayload.php', res);
							}
						});
					});
				});
			});
		});
	}



}