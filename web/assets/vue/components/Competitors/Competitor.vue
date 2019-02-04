<template>
    <tr>
        <td>{{ competitor.classment }}</td>
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
        props: ['competitor']
    }
</script>
