const mix = require('laravel-mix');

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
 mix.js('resources/js/app.js', 'public/js')
   .vue()
    .sass('resources/sass/app.scss', 'public/css');

mix.styles([
    'node_modules/@fortawesome/fontawesome-free/css/all.css',
    // 'resources/coreui/css/font-awesome.min.css',
    'resources/coreui/css/style.min.css',
    'resources/coreui/vendors/@coreui/chartjs/css/coreui-chartjs.css',
    'resources/css/intranet.css',
    'node_modules/toastr/build/toastr.min.css',
], 'public/css/cepre-coreui.css')
.scripts([
    'resources/coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js',
    // 'resources/coreui/vendors/@coreui/chartjs/js/coreui-chartjs.bundle.js',
    'resources/coreui/vendors/@coreui/utils/js/coreui-utils.js',
    'resources/coreui/js/main.js'
], 'public/js/cepre-coreui.js');
//.//js(['resources/js/app.js'], 'public/js/app.js');

// css y js para WEB
mix.scripts([
    'resources/js/web.js',
], 'public/js/web/main.js')
.styles([
    'resources/css/web.css',
    'node_modules/toastr/build/toastr.min.css',
], 'public/css/web/main.css');
