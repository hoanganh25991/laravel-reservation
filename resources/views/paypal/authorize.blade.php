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
<!-- Load the PayPal component. -->
<script src="https://js.braintreegateway.com/web/3.11.0/js/paypal.min.js"></script>
<!-- Paypal with custom dropin -->
{{--<div id="dropin-container"></div>--}}
{{--<button id="submit-button">Purchase</button>--}}
{{--<script src="https://js.braintreegateway.com/web/dropin/1.0.0-beta.6/js/dropin.min.js"></script>--}}
<script src="{{ url_mix('js/paypal-authorize.js') }}"></script>