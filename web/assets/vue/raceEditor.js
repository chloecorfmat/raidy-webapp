import Vue from 'vue';
import RaceEditor from './components/RaceEditor';


/**
 * Create a fresh Vue Application instance
 */
window.addEventListener('load', function() {
    new Vue({
        el: '#race',
        components: { RaceEditor },
    });
});
