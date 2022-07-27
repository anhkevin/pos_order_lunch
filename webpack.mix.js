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
   .combine('resources/assets/css/*', 'public/css/app.css')
   .copy('resources/assets/icons', 'public/icons')
   .copy('resources/assets/vendor', 'public/vendor')
   .copy('resources/assets/images', 'public/images')
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
