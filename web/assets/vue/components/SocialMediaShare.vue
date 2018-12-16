<template>
    <div class="social-media-share">
        <ul>
            <li>
                <a v-bind:href="loadFbUrl" class="share-facebook" target="_blank">
                    <i class="fab fa-facebook-square fa-2x"></i>
                </a>
            </li>
            <li>
                <a v-bind:href="loadTwitterUrl" class="share-twitter" target="_blank">
                    <i class="fab fa-twitter-square fa-2x"></i>
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "SocialMediaShare",
        props: ['url'],
        computed: {
            loadFbUrl: function () {
                return "https://www.facebook.com/sharer/sharer.php?u=" + this.url;
            },
            loadTwitterUrl: function() {
                let twitter = "https://twitter.com/intent/tweet";
                twitter += '?text=Je suis le raid';
                twitter += '&url=' + this.url;
                twitter += '&via=Raidy';
                return twitter;
            }
        }
    }
</script>
