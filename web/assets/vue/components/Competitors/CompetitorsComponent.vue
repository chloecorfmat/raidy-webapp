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
        props: ['competitors', 'races'],
        components: {
            CompetitorsList
        },
        computed: {
        },
        watch: {
            search_competitor: function (newValue) {
                this.search_competitor = newValue;

                // Filter object (competitors) in table.
                if (this.search_competitor !== 'all') {
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
            },
            race_selected: function (newValue) {
                this.race_selected = newValue;

                console.log(this.competitors_object);

                if (this.race_selected !== 'all') {
                    var c = JSON.parse(JSON.stringify(this.competitors_object));
                    for (var o in c) {
                        if (c.hasOwnProperty(o)) {
                            if (
                                (c[o].race_id !== this.race_selected)
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
            this.races_object = JSON.parse(this.races);
            this.competitors_object = JSON.parse(this.competitors);
            this.competitors_list = this.competitors_object;
        }
    }
</script>
