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
//mix.styles(['resources/assets/css/admin.css'], css_output_dir + '/admin.css');
mix.styles(['resources/assets/css/reservation.css'], css_output_dir + '/reservation.css');
/**
 * Js code handle reservation booking page
 */
mix.babel(['resources/assets/js/calendar.js'], js_output_dir + '/calendar.js');
mix.babel(['resources/assets/js/booking-form.js'], js_output_dir + '/booking-form.js');
mix.babel(['resources/assets/js/syntax-highlight.js'], js_output_dir + '/syntax-highlight.js');

/**
 * Js code for reservation>confirm page
 */
mix.babel(['resources/assets/js/reservation-confirm.js'], js_output_dir + '/reservation-confirm.js');
/**
 * Js code for admin>settings page
 */
mix.babel(['resources/assets/js/admin-settings.js'], js_output_dir + '/admin-settings.js');
/**
 * Js code for admin>reservations page
 */
mix.babel(['resources/assets/js/admin-reservations.js'], js_output_dir + '/admin-reservations.js');



/**
 * Doesn't need to check
 * Sourcemap self check in code
 */
mix.version();
// mix.sourceMaps();