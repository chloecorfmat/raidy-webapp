import Vue from 'vue';
import SocialMediaShare from './components/SocialMediaShare';
import TweetsList from './components/Tweets/TweetsList';
import CompetitorsComponent from './components/Competitors/CompetitorsComponent';

/**
 * Create a fresh Vue Application instance
 */
window.addEventListener('load', function() {
    new Vue({
        el: '#app',
        components: {
            SocialMediaShare,
            TweetsList,
            CompetitorsComponent
        }
    });
});
