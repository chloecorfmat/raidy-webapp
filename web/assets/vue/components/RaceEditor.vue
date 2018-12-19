<template>
    <div>
        <button v-on:click="add()">ADD</button>
        <ul>
            <li v-for="(race, index) in races">{{ race }}<button v-on:click="moveUp(index)">UP</button><button v-on:click="moveDown(index)">DOWN</button><button v-on:click="remove(index)">RM</button></li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "RaceEditor",
        data() {
            return {
                races: []
            }
        },
        created () {
            this.fetchData();
        },
        methods : {
            fetchData () {
                /* @TODO : FETCH RACES FROM SERVER*/
                this.races = ["aze","rty", "uio"];
            },
            toJson () {
                let json = JSON.stringify(this.races);
                return json;
            },
            add () {
              this.races.push(this.makeid(3));
              console.log(this.toJson());
            },
            moveUp (r) {
                this.move(this.races, r, r-1);
                console.log(this.toJson());
            },
            moveDown (r) {
                this.move(this.races, r, r+1);
                console.log(this.toJson());
            },
            remove (r) {
              this.races.splice(r,1);
              console.log(this.toJson());
            },
            move (array, oldIndex, newIndex) {
                if (newIndex >= array.length || newIndex < 0) {
                    return array;
                }
                array.splice(newIndex, 0, array.splice(oldIndex, 1)[0]);
                return array;
            },
            makeid(size) {
                var text = "";
                var possible = "abcdefghijklmnopqrstuvwxyz";

                for (var i = 0; i < size; i++)
                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                return text;
            }
        }
    }
</script>
