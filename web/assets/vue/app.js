import Vue from 'vue';

import Example from './components/Example'

/**
 * Create a fresh Vue Application instance
 */
window.addEventListener('load', function() {
    new Vue({
        el: '#app',
        components: {Example}
    });
});
