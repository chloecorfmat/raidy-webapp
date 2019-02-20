<template>
    <div class="content--two-third">
        <h2>Classement</h2>
        <div class="filters">
            <div class="filter">
                <label for="competitor" class="sr-only">Rechercher</label>
                <i class="fas fa-search"></i>
                <input class="search" type="text" id="competitor" v-model="search_competitor" placeholder="Rechercher">
            </div>

            <div class="filter">
                <label for="race" class="sr-only">Épreuve</label>
                <select class="race" id="race" v-model="race_selected">
                    <option value="all">Toutes les épreuves</option>
                    <option v-for="(race, index) in this.races_object" :key=index :value=race.id>{{ race.name }}</option>
                </select>

            </div>
        </div>

        <competitors-list :competitors=competitors_list></competitors-list>
    </div>
</template>

<script>
    import axios from 'axios';
    import CompetitorsList from './CompetitorsList'

    export default {
        name: "CompetitorsComponent",
        data() {
            return {
                'search_competitor': '',
                'race_selected': 'all',
                'competitors_list': {},
                'competitors_object': {},
                'races_object': {},
            }
        },
        props: ['raidid', 'competitors', 'races', 'baseurl'],
        components: {
            CompetitorsList
        },
        created () {
            this.tweets = this.getCompetitorsObject();
            setInterval(this.getCompetitorsObject, 60*1000);
        },
        methods: {
            filter () {
                var c = JSON.parse(JSON.stringify(this.competitors_object));

                if (this.search_competitor !== '') {
                    for (var o in c) {
                        if (c.hasOwnProperty(o)) {
                            if (
                                (c[o].firstname.toLowerCase().indexOf(this.search_competitor.toLowerCase()) === -1) &&
                                (c[o].lastname.toLowerCase().indexOf(this.search_competitor.toLowerCase()) === -1) &&
                                (c[o].numbersign.toLowerCase().indexOf(this.search_competitor.toLowerCase()) === -1)
                            ) {
                                delete c[o];
                            }
                        }
                    }
                }

                if (this.race_selected !== 'all') {
                    for (var o in c) {
                        if (c.hasOwnProperty(o)) {
                            if (
                                (c[o].race_id !== this.race_selected)
                            ) {
                                delete c[o];
                            }
                        }
                    }
                }

                this.competitors_list = Object.assign({}, c);
            },
            getCompetitorsObject () {
                axios.get(this.baseurl + 'api/public/raid/' + this.raidid + '/competitors')
                    .then(response => {
                        // JSON responses are automatically parsed.
                        this.competitors_object = response.data;
                        this.filter();
                    })
                    .catch(e => {

                    });
            }
        },
        watch: {
            search_competitor: function (newValue) {
                this.search_competitor = newValue;
                this.filter();
            },
            race_selected: function (newValue) {
                this.race_selected = newValue;
                this.filter();
            },
        },
        mounted() {
            this.races_object = JSON.parse(this.races);
            this.competitors_object = JSON.parse(this.competitors);
            this.competitors_list = this.competitors_object;
        }
    }
</script>
