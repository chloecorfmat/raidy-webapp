<template>
    <li class="tweet">
        <header>
            <a :href=this.tweet_user_url target="_blank" class="tweet-user-link">
                <img :src=this.tweet.user.profile_image_url_https class="tweet-user-profile"/>
                <p class="tweet-user-name">@{{ this.tweet.user.screen_name }}</p>
            </a>
            <p class="tweet-date">{{ this.tweet_date_formatted }}</p>
        </header>
        <main>
            <p>{{ this.tweet.text }}</p>
        </main>
        <footer>
            <span v-if=this.tweet.retweeted_status><i class="fas fa-retweet fa-lg tweet-is-retweet"></i></span>
            <a :href=this.tweet_url target="_blank">
                <i class="fab fa-twitter fa-lg"></i>
            </a>
        </footer>
    </li>
</template>

<script>
    export default {
        name: "Tweet",
        props: ['tweet'],
        computed: {
            tweet_url: function() {
                if (this.tweet.retweeted_status) {
                    return 'https://twitter.com/' + this.tweet.retweeted_status.user.screen_name + '/status/' + this.tweet.retweeted_status.id_str;
                } else {
                    return  'https://twitter.com/' + this.tweet.user.screen_name + '/status/' + this.tweet.id_str;
                }
            },
            tweet_date_formatted: function () {
                let date = new Date(this.tweet.created_at);
                return date.toLocaleString();
            },
            tweet_user_url: function() {
                return 'https://twitter.com/' + this.tweet.user.screen_name;
            }
        },
    }
</script>
