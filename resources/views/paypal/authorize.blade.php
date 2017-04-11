<span class="text-danger">Click PayPal button below to make a deposit.</span>
<script src="https://www.paypalobjects.com/api/button.js?"
        data-merchant="braintree"
        data-id="paypal-button"
        data-button="checkout"
        data-color="gold"
        data-size="medium"
        data-shape="pill"
        data-button_type="submit"
        data-button_disabled="false"
></script>
<!-- Load the required components. -->
<script src="https://js.braintreegateway.com/web/3.11.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.11.0/js/paypal.min.js"></script>
<script src="{{ url_mix('js/paypal-authorize.js') }}"></script>