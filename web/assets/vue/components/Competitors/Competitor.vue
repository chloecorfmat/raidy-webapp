<template>
    <tr v-bind:class="{ isfraud: competitor.fraud }">
        <td v-if="competitor.classment !== 0">{{ competitor.classment}}</td>
        <td v-else>-</td>
        <td>{{ competitor.timing }}</td>
        <td>{{ competitor.firstname}} {{ competitor.lastname}}</td>
        <td>{{ competitor.numbersign}}</td>
        <td>{{ competitor.category }}</td>
        <td>{{ competitor.race_name }}</td>
    </tr>
</template>

<script>
    export default {
        name: "Competitor",
        props: ['competitor'],
    }
</script>
