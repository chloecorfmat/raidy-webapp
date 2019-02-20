<template>
    <div v-show="this.tweets_object != []" class="content--third">
        <h2>En direct !</h2>
        <ul class="tweets-list">
            <Tweet v-for="(tweet, index) in this.tweets_object" :key=index :tweet=tweet>
            </Tweet>
        </ul>
    </div>
</template>

<script>
    import axios from 'axios';
    import Tweet from './Tweet';

    export default {
        name: "TweetsList",
        props: ['raidid', 'baseurl'],
        data() {
            return {'tweets_object': [],};
        },
        components: {
          Tweet
        },
        created () {
            this.tweets = this.getTweetsObject();
            setInterval(this.getTweetsObject, 60*1000);
        },
        methods: {
            getTweetsObject () {
                axios.get(this.baseurl + 'api/public/raid/' + this.raidid + '/tweets')
                    .then(response => {
                        // JSON responses are automatically parsed.
                        this.tweets_object = response.data;
                    })
                    .catch(e => {
                        
                    });
            }
        }
    }
</script>
