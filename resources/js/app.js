import router from './routes.js';

require('./bootstrap');
require('chart.js');
require('es6-promise').polyfill();

const app = new Vue({
    el: '#app',
    router
});
