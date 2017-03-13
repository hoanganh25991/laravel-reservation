const {mix} = require('laravel-mix');

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

// mix.js('resources/assets/js/app.js', 'public/js')
//    .sass('resources/assets/sass/app.scss', 'public/css');
// mix.js('resources/assets/js/bootstrap.calendar.js', 'public/js')
mix.scripts('resources/assets/js/calendar.js', 'public/js/calendar.js')
   .version();

mix.scripts('resources/assets/js/booking-form.js', 'public/js/booking-form.js')
   .version();

mix.scripts('resources/assets/js/syntax-highlight.js', 'public/js/syntax-highlight.js')
   .version();

mix.styles('resources/assets/css/admin.css', 'public/css/admin.css')
   .version();

mix.styles('resources/assets/css/reservation.css', 'public/css/reservation.css')
   .version();