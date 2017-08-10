/** @namespace mix.config.inProduction */
/** @namespace Mix.File */
const {mix} = require('laravel-mix');
// let path = require('path');


/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
let js_output_dir = 'public/js/';
let css_output_dir = 'public/css';
/**
 * Css code for whole app
 */
mix.styles(['resources/assets/css/reservation.css'], css_output_dir + '/reservation.css');

/**
 * Css code for admin page
 */
mix.styles(['resources/assets/css/admin.css'], css_output_dir + '/admin.css');

/**
 * Css code for admin reservation page
 */
mix.styles(['resources/assets/css/admin-reservation.css'], css_output_dir + '/admin-reservation.css');

/**
 * Css code handle animation
 */
mix.styles(['resources/assets/css/animate.css'], css_output_dir + '/animate.css');

/**
 * Css code handle animation
 */
mix.styles(['resources/assets/css/flex.css'], css_output_dir + '/flex.css');

/**
 * Js code handle reservation booking page
 */
mix.babel(['resources/assets/js/calendar.js'], js_output_dir + '/calendar.js');
mix.babel(['resources/assets/js/booking-form.js'], js_output_dir + '/booking-form.js');
mix.babel(['resources/assets/js/syntax-highlight.js'], js_output_dir + '/syntax-highlight.js');

 /**
 * Js code for reservation > confirm page
 */
mix.babel(['resources/assets/js/reservation-confirm.js'], js_output_dir + '/reservation-confirm.js');

/**
 * Js code for admin > settings page
 */
mix.babel(['resources/assets/js/admin-settings.js'], js_output_dir + '/admin-settings.js');

/**
 * Js code for admin > reservations page
 */
mix.babel(['resources/assets/js/admin-reservations.js'], js_output_dir + '/admin-reservations.js');

/**
 * Js code for paypal-authorize page
 */
mix.babel(['resources/assets/js/paypal-authorize.js'], js_output_dir + '/paypal-authorize.js');

/**
 * Js code for admin > navigator page
 */
mix.babel(['resources/assets/js/admin-navigator.js'], js_output_dir + '/admin-navigator.js');

/**
 * Js code for admin > index page
 */
mix.babel(['resources/assets/js/admin-index.js'], js_output_dir + '/admin-index.js');


/**
 * Doesn't need to check
 * Sourcemap self check in code
 */
mix.version();