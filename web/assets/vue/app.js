import Vue from 'vue';
import SocialMediaShare from './components/SocialMediaShare';

/**
 * Create a fresh Vue Application instance
 */
window.addEventListener('load', function() {
    new Vue({
        el: '#app',
        components: {SocialMediaShare}
    });
});
