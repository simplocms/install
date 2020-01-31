/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) { }

window.csrf_token = document.head.querySelector('meta[name="csrf-token"]').content;

if (window.csrf_token) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.csrf_token
        }
    });
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import 'babel-polyfill';

require('./vendor/Localization.js');
require('../lib/uniform/uniform.min.js');
require('../lib/jgrowl/jquery.jgrowl.min.js');
require('./vendor/element_lock.js');
require('./vendor/Request.js');
require('./vendor/$Form.js');
require('./vendor/Converter.js');
require('./vendor/Utils.js');

/**
 * Pace
 * @type {Pace}
 */
window.pace = require('../lib/pace/pace.min.js');
window.pace.start();

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');
window.axios = require('axios');
window.EventBus = new Vue();

require('sweetalert');
require('./components');
