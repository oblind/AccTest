const { mix } = require('laravel-mix');

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
//import path from 'path'
//import PrerenderSpaPlugin from 'prerender-spa-plugin'

//new PrerenderSpaPlugin(path.join(__dirname, 'public'), ['/about'])

mix
.js('resources/assets/js/main.js', 'public/js')
.js('resources/assets/js/test.js', 'public/js')
.extract(['axios', 'js-cookie', 'vue', 'vuex', 'vue-router', 'chart.js', 'chartjs-plugin-zoom'])
.sourceMaps()
.combine(['resources/assets/css/app.css', 'resources/assets/css/login.css', 'resources/assets/css/table.css'], 'public/css/app.css')
