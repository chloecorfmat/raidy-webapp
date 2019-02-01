<template>
    <tr>
        <td>{{ competitor.firstname}} {{ competitor.lastname}}</td>
        <td>{{ competitor.numbersign}}</td>
    </tr>
</template>

<script>
    export default {
        name: "Competitor",
        props: ['competitor']
    }
</script>
