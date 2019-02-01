<template>
    <div class="content--third">
        <h1>En direct !</h1>
        <ul class="tweets-list">
            <Tweet v-for="(tweet, index) in this.tweets_object" :key=index :tweet=tweet>
            </Tweet>
        </ul>
    </div>
</template>

<script>
    import Tweet from './Tweet'

    export default {
        name: "TweetsList",
        props: ['tweets'],
        components: {
          Tweet
        },
        computed: {
            tweets_object: function () {
                return JSON.parse(this.tweets)
            }
        },
    }
</script>
