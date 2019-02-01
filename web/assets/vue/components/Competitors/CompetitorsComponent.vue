<template>
    <div class="content--two-third">
        <h1>Classement</h1>
        <div class="filters">
            <label for="competitor">Rechercher</label>
            <input type="text" id="competitor" v-model="search_competitor">
        </div>

        <competitors-list :competitors=competitors_list></competitors-list>
    </div>
</template>

<script>
    import CompetitorsList from './CompetitorsList'

    export default {
        name: "CompetitorsComponent",
        data() {
            return {
                'search_competitor': '',
                'competitors_list': {},
                'competitors_object': {},
            }
        },
        props: ['competitors'],
        components: {
            CompetitorsList
        },
        computed: {
        },
        watch: {
            search_competitor: function (newValue) {
                this.search_competitor = newValue;

                // Filter object (competitors) in table.
                if (this.search_competitor !== '') {
                    var c = JSON.parse(JSON.stringify(this.competitors_object));
                    for (var o in c) {
                        if (c.hasOwnProperty(o)) {
                            if (
                                (c[o].firstname.toLowerCase().indexOf(newValue.toLowerCase()) === -1) &&
                                (c[o].lastname.toLowerCase().indexOf(newValue.toLowerCase()) === -1) &&
                                (c[o].numbersign.toLowerCase().indexOf(newValue.toLowerCase()) === -1)
                            ) {
                                delete c[o];
                            }
                        }
                    }
                    this.competitors_list = Object.assign({}, c);
                } else {
                    // If filter is empty, show all competitors.
                    this.competitors_list = this.competitors_object;
                }
            }
        },
        mounted() {
            this.competitors_object = JSON.parse(this.competitors);
            this.competitors_list = this.competitors_object;
        }
    }
</script>
