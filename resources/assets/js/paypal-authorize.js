const AJAX_SEND_CLIENT_TOKEN = 'AJAX_SEND_CLIENT_TOKEN';

class PayPalAuthorize {

	construct(token, paypal_instance_options, base_url){
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
						$.ajax({
							url: `${self.base_url}/pay_pal}`,
							method: 'POST',
							data: {
								tokenizationPayload: JSON.stringify(tokenizationPayload),
								type: AJAX_SEND_CLIENT_TOKEN
							},
							success(res){
								console.log(res);
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