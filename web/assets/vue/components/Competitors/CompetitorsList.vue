<template>
    <div>
        <div class="table--container">
            <table class="table">
                <caption class="sr-only">Liste des participants</caption>
                <thead class="table--headers">
                <tr>
                    <th scope="col">Classement</th>
                    <th scope="col">Temps</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Dossard</th>
                    <th scope="col">Catégorie</th>
                    <th scope="col">Course</th>
                </tr>
                </thead>
                <competitor v-for="(competitor, index) in this.competitors_object" :key=index :competitor=competitor>
                </competitor>
            </table>
        </div>
    </div>
</template>

<script>
    import Competitor from './Competitor'

    export default {
        name: "CompetitorsList",
        props: ['competitors'],
        components: {
            Competitor
        },
        computed: {
            competitors_object: function() {
                return Object.assign({}, this.competitors);
            }
        }
    }
</script>
