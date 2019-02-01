<template>
    <div v-show="this.tweets_object != []" class="content--third">
        <h1>En direct !</h1>
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
        props: ['raidid'],
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
                axios.get('/api/public/raid/' + this.raidid + '/tweets')
                    .then(response => {
                        // JSON responses are automatically parsed.
                        this.tweets_object = response.data;
                    })
                    .catch(e => {
                        console.log(e);
                    });
            }
        }
    }
</script>
