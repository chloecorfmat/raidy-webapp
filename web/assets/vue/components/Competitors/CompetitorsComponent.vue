<template>
    <div class="content--two-third">
        <h1>Classement</h1>
        <div class="filters">
            <div>
                <label for="competitor">Rechercher</label>
                <input type="text" id="competitor" v-model="search_competitor">
            </div>

            <div>
                <label for="race">Courses</label>
                <select id="race" v-model="race_selected">
                    <option value="all">Toutes les épreuves</option>
                    <option v-for="(race, index) in this.races_object" :key=index :value=race.id>{{ race.name }}</option>
                </select>

            </div>
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
                'race_selected': 'all',
                'competitors_list': {},
                'competitors_object': {},
                'races_object': {},
            }
        },
        props: ['competitors', 'races', 'baseurl'],
        components: {
            CompetitorsList
        },
        methods: {
            filter () {
                var c = JSON.parse(JSON.stringify(this.competitors_object));

                console.log(this.search_competitor);
                if (this.search_competitor !== 'all') {
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
