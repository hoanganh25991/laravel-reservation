<!-- Load the required components. -->
<script src="https://js.braintreegateway.com/web/3.11.0/js/client.min.js"></script>
<!-- Load the PayPal component. -->
<script src="https://js.braintreegateway.com/web/3.11.0/js/paypal.min.js"></script>
<!-- Paypal with custom dropin -->
<div id="dropin-container"></div>
<button class="btn btn-info" id="paypal-submit-button">Process to Paypal</button>
{{--<button id="submit-button">Purchase</button>--}}
<script src="https://js.braintreegateway.com/web/dropin/1.0.0-beta.6/js/dropin.min.js"></script>
<script src="{{ url_mix('js/paypal-authorize.js') }}"></script>