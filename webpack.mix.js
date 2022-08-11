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

mix.webpackConfig({ stats: { children: true, }, });
mix.js('resources/assets/js/app.js', 'public/js').vue()
   .styles([
      'resources/assets/icons/simple-line-icons/css/simple-line-icons.css',
      'resources/assets/icons/font-awesome-old/css/font-awesome.min.css',
      'resources/assets/icons/material-design-iconic-font/css/materialdesignicons.min.css', 
      'resources/assets/icons/themify-icons/css/themify-icons.css',
      'resources/assets/icons/line-awesome/css/line-awesome.min.css',
      'resources/assets/icons/avasta/css/style.css',
      'resources/assets/icons/flaticon/flaticon.css',
      'resources/assets/vendor/animate/animate.min.css',
      'resources/assets/vendor/aos/css/aos.min.css',
      'resources/assets/vendor/perfect-scrollbar/css/perfect-scrollbar.css',
      'resources/assets/vendor/metismenu/css/metisMenu.min.css',
      'resources/assets/css/style.css'
   ], 'public/css/app.css')
   .copy('resources/assets/icons', 'public/icons')
   .copy('resources/assets/vendor', 'public/vendor')
   .copy('resources/assets/images', 'public/images')
   .copy('resources/assets/images/icon.png', 'public/icon.png')
   .copy('resources/assets/images/favicon.ico', 'public/favicon.ico')
   .copy('resources/assets/pwa/manifest.json', 'public/manifest.json')
   .copy('resources/assets/pwa/sw.js', 'public/sw.js')
   .scripts([
      'resources/assets/vendor/global/global.min.js',
      'resources/assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
      'resources/assets/vendor/chart.js/Chart.bundle.min.js', 
      'resources/assets/vendor/owl-carousel/owl.carousel.js',
      'resources/assets/vendor/peity/jquery.peity.min.js',
      'resources/assets/vendor/apexchart/apexchart.js',
      'resources/assets/script/dashboard/dashboard-1.js',
      'resources/assets/script/custom.js',
      'resources/assets/script/deznav-init.js'
   ], 'public/js/vendor.js')
   .version();
