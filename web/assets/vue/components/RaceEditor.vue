<template>
    <div>
        <button v-on:click="openNewRacePopin()">Créer une épreuve</button>
        <ul class="race--list">
            <li v-for="(race, index) in races">
                <header>
                    <h3>{{ race.name }}</h3>
                    <nav>
                        <button v-on:click="openNewTrackPopin(race)">Ajouter un parcours</button>
                        <button class="btn btn--danger" v-on:click="displayRemoveRacePopin(race)"><i class="fa fa-trash"></i></button>
                    </nav>
                </header>
                <ul class="race--tracks">
                    <li class="race--track" v-for="(raceTrack, idx) in race.raceTracks">
                        <header>
                            <h4>{{ htmlDecode(raceTrack.name) }}</h4>
                            <nav class="race--track--toolbar">
                                <button class="btn" v-on:click="openNewCheckpointPopin(raceTrack)">Ajouter un checkpoint</button>
                                <div class="race--track--checkpoint--order">
                                    <button class="btn" v-on:click="moveTrackUp(idx, races[index].raceTracks, races[index])"><i class="fa fa-caret-up"></i></button>
                                    <button class="btn" v-on:click="moveTrackDown(idx, races[index].raceTracks, races[index])"><i class="fa fa-caret-down"></i></button>
                                </div>
                                <button class="btn btn--danger" v-on:click="removeRaceTrack(index, races[index], races[index].raceTracks[idx])"><i class="fa fa-trash"></i></button>
                            </nav>
                        </header>
                        <ul class="race--track--checkpoints">
                            <li class="race--track--checkpoint" v-for="(checkpoint, checkpointIdx) in raceTrack.checkpoints">
                                <h5>{{ htmlDecode(checkpoint.poi.name) }}</h5>
                                <div class="race--track--checkpoint--tools">
                                    <div class="race--track--checkpoint--order">
                                        <button class="btn"><i class="fa fa-caret-up" v-on:click="moveCheckpointUp(checkpointIdx, races[index].raceTracks[idx].checkpoints,races[index], races[index].raceTracks[idx])"></i></button>
                                        <button class="btn"><i class="fa fa-caret-down" v-on:click="moveCheckpointDown(checkpointIdx, races[index].raceTracks[idx].checkpoints,races[index], races[index].raceTracks[idx])"></i></button>
                                    </div>
                                    <button class="btn btn--danger" v-on:click="removeRaceCheckpoint(index, races[index], idx, races[index].raceTracks[idx], raceTrack.checkpoints[checkpointIdx])"><i class="fa fa-trash"></i></button>
                                </div>
                            </li>
                            <li class="race--track--checkpoint" v-if="raceTrack === currentRaceTrack">
                                <form v-on:submit="addCheckpoint($event, index, idx)">
                                    <select v-model="newCheckpoint.poi" required>
                                        <option v-for="poi in checkpointsMap" :value="poi">{{ htmlDecode(poi.name) }}</option>
                                    </select>
                                    <button type="submit" class="btn">Ajouter un checkpoint</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <li class="race--track" v-if="race === currentRace">
                        <form v-on:submit="addTrack($event, index)">
                            <select v-model="newRaceTrack.track" required>
                                <option v-for="track in tracksMap" :value="track">{{ htmlDecode(track.name) }}</option>
                            </select>
                            <button type="submit" class="btn">Ajouter un parcours</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="popin-wrapper">
            <!-- ADD RACE -->
            <div id="add-race" aria-hidden="true" class="modal modal--danger" data-micromodal-close>
                <!-- [2] -->
                <div tabindex="-1" data-micromodal-close>
                    <!-- [3] -->
                    <div role="dialog" aria-modal="true" aria-labelledby="add-race-title" >
                        <div class="modal-container">
                            <header class="modal--header modal--header--danger">
                                <h2 id="add-race-title">
                                    Ajouter une épreuve
                                </h2>
                                <!-- [4] -->
                                <button aria-label="Fermer la fenêtre" data-micromodal-close class="btn--danger"><i data-micromodal-close class="fas fa-times"></i></button>
                            </header>
                            <div id="add-race-content" class="modal--content">
                                <form v-on:submit="addRace($event)">
                                    <div id="addRace">
                                        <div class="form--item">
                                            <label for="addRace_name" class="required">
                                                Nom de l'épreuve <span class="input--required">*</span>
                                            </label>
                                            <input class="form--input-text" type="text" id="addRace_name"
                                                   required="required" maxlength="100" v-model="newRace.name">
                                        </div>
                                        <div class="actions modal--actions">
                                            <button type="submit" id="addRace_submit" class="btn">Ajouter un parcours</button>
                                            <button type="button" class="btn btn--danger" data-micromodal-close>Annuler</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REMOVE RACE -->
            <div id="remove-race" aria-hidden="true" class="modal modal--danger" data-micromodal-close>
                <!-- [2] -->
                <div tabindex="-1" data-micromodal-close>
                    <!-- [3] -->
                    <div role="dialog" aria-modal="true" aria-labelledby="add-race-title" >
                        <div class="modal-container">
                            <header class="modal--header modal--header--danger">
                                <h2 id="remove-race-title">
                                    Ajouter une épreuve
                                </h2>
                                <!-- [4] -->
                                <button aria-label="Fermer la fenêtre" data-micromodal-close class="btn--danger"><i data-micromodal-close class="fas fa-times"></i></button>
                            </header>
                            <div id="delete-track-content" class="modal--content">
                                <p>Êtes-vous certains de vouloir supprimer cette épreuve ?</p>
                                <p class="text--important">Cette action est irréversible.</p>

                                <p>Pour supprimer l'épreuve, veuillez entrer son nom "<span class="text--medium" id="span--track-name" v-if="toRemoveRace != null">{{ toRemoveRace.name }}</span>" dans le champ ci-dessous : </p>

                                <input class="form--input-text" type="text" id="track-name-delete" v-model="toRemoveRaceCheck" required="required" maxlength="100">

                                <div class="actions">
                                    <button type="submit" class="btn btn--danger" id="btn--delete-race" disabled v-if="toRemoveRace != null && toRemoveRace.name !== toRemoveRaceCheck">Supprimer</button>
                                    <button type="button" v-on:click="removeRace()" class="btn btn--danger" v-if="toRemoveRace != null && toRemoveRace.name === toRemoveRaceCheck">Supprimer</button>
                                    <button data-micromodal-close class="btn btn--cancel">Annuler</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "RaceEditor",
        data() {
            return {
                races: [],
                newRace: new Race(),
                newRaceTrack: new RaceTrack(),
                newCheckpoint: new Checkpoint(),
                currentRace: null,
                currentRaceTrack: null,
                tracksMap: [],
                checkpointsMap: [],
                toRemoveRaceCheck:"",
                toRemoveRace:null,
                toRemoveRaceIndex: null
            }
        },
        created () {
            this.fetchData();
        },
        mounted () {
            console.log("initform");
            initForm();
        },
        methods : {
            fetchData () {

                let keepThis = this;

                this.races = [];

                const req = new XMLHttpRequest();
                req.onerror = function () {

                };
                req.onload = function () {
                    if (this.status === 200) {

                        let races = JSON.parse(this.responseText);

                        for(let race of races){
                            let r = new Race();
                            r.fromObj(race);
                            keepThis.races.push(r);
                            console.log(r);
                        }
                    }
                };
                req.open('GET', '/race/raid/'+raidID+'/race', true);
                req.send(null);
            },
            toJson () {
                let json = JSON.stringify(this.races);
                return json;
            },
            moveTrackUp (idx, tracks, race) {

                let keepThis = this;

                if(idx >= 1){

                    let raceId = race.id;

                    let track = tracks[idx];
                    let previousTrack = tracks[idx-1];

                    track.order = track.order-1;
                    previousTrack.order = previousTrack.order+1;

                    const req = new XMLHttpRequest();
                    req.onerror = function () {

                    };
                    req.onload = function () {
                        keepThis.move(tracks, idx, idx-1);
                    };

                    req.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+track.id, true);
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(track.toJSON());


                    /*MOVE OLD*/
                    const oldReq = new XMLHttpRequest();
                    oldReq.onerror = function () {

                    };
                    oldReq.onload = function () {

                    };

                    oldReq.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+previousTrack.id, true);
                    oldReq.setRequestHeader('Content-Type', 'application/json');
                    oldReq.send(previousTrack.toJSON());
                }
            },
            moveTrackDown (idx, tracks, race) {

                let keepThis = this;

                if(idx < tracks.length){

                    let raceId = race.id;

                    let track = tracks[idx];
                    let nextTrack = tracks[idx+1];

                    track.order = track.order+1;
                    nextTrack.order = nextTrack.order-1;

                    const req = new XMLHttpRequest();
                    req.onerror = function () {

                    };
                    req.onload = function () {
                        keepThis.move(tracks, idx, idx+1);
                    };

                    req.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+track.id, true);
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(track.toJSON());


                    /*MOVE OLD*/
                    const oldReq = new XMLHttpRequest();
                    oldReq.onerror = function () {

                    };
                    oldReq.onload = function () {

                    };

                    oldReq.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+nextTrack.id, true);
                    oldReq.setRequestHeader('Content-Type', 'application/json');
                    oldReq.send(nextTrack.toJSON());

                }
            },
            moveCheckpointUp (idx, checkpoints, race, track) {

                let keepThis = this;

                if(idx >= 1){

                    let raceId = race.id;

                    let checkpoint = checkpoints[idx];
                    let previousCheckpoint = checkpoints[idx-1];

                    checkpoint.order = checkpoint.order-1;
                    previousCheckpoint.order = previousCheckpoint.order+1;

                    const req = new XMLHttpRequest();
                    req.onerror = function () {

                    };
                    req.onload = function () {
                        keepThis.move(checkpoints, idx, idx-1);
                    };

                    req.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+track+'/raceCheckpoint/'+checkpoint.id, true);
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(checkpoint.toJSON());


                    /*MOVE OLD*/
                    const oldReq = new XMLHttpRequest();
                    oldReq.onerror = function () {

                    };
                    oldReq.onload = function () {

                    };

                    oldReq.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+track.id+'/raceCheckpoint/'+previousCheckpoint.id, true);
                    oldReq.setRequestHeader('Content-Type', 'application/json');
                    oldReq.send(previousCheckpoint.toJSON());
                }
            },
            moveCheckpointDown (idx, checkpoints, race, track) {

                let keepThis = this;

                if(idx < checkpoints.length){

                    let raceId = race.id;

                    let checkpoint = checkpoints[idx];
                    let previousCheckpoint = checkpoints[idx+1];

                    checkpoint.order = checkpoint.order+1;
                    previousCheckpoint.order = previousCheckpoint.order-1;

                    const req = new XMLHttpRequest();
                    req.onerror = function () {

                    };
                    req.onload = function () {
                        keepThis.move(checkpoints, idx, idx+1);
                    };

                    req.open('PATCH','/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+track.id+'/raceCheckpoint/'+checkpoint.id, true);
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(checkpoint.toJSON());


                    /*MOVE OLD*/
                    const oldReq = new XMLHttpRequest();
                    oldReq.onerror = function () {

                    };
                    oldReq.onload = function () {

                    };

                    oldReq.open('PATCH', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+track+'/raceCheckpoint/'+previousCheckpoint.id, true);
                    oldReq.setRequestHeader('Content-Type', 'application/json');
                    oldReq.send(previousCheckpoint.toJSON());
                }
            },
            displayRemoveRacePopin(race){
                this.toRemoveRace = race;
                this.toRemoveRaceCheck = '';
                MicroModal.show("remove-race");
            },
            removeRace() {

                let idx = this.races.indexOf(this.toRemoveRace);

                if(this.toRemoveRace.name === this.toRemoveRaceCheck){
                    let keepThis = this;
                    let race = this.toRemoveRace;

                    const req = new XMLHttpRequest();
                    req.onerror = function () {

                    };
                    req.onload = function () {
                        if (this.status === 200) {
                            keepThis.races.splice(idx,1);
                            MicroModal.close("remove-race");

                            iziToast.success({
                                message: 'L\'épreuve a bien été supprimée',
                                position: 'bottomRight',
                            });
                        }
                    };
                    req.open('DELETE', '/race/raid/'+raidID+'/race/'+race.id, true);
                    req.setRequestHeader('Content-Type', 'application/json');
                    req.send(null);
                }
            },
            removeRaceTrack (raceOrder, race, raceTrack) {
                let keepThis = this;

                const req = new XMLHttpRequest();
                req.onerror = function () {

                };
                req.onload = function () {
                    if (this.status === 200) {
                        console.log("rm");
                        keepThis.races[raceOrder].raceTracks.splice(raceTrack.order,1);
                    }
                };
                req.open('DELETE', '/race/raid/'+raidID+'/race/'+race.id+'/racetrack/'+raceTrack.id, true);
                req.setRequestHeader('Content-Type', 'application/json');
                req.send(null);

                iziToast.success({
                    message: 'Le parcours a bien été supprimé',
                    position: 'bottomRight',
                });
            },
            removeRaceCheckpoint (raceIdx, race, raceTrackIdx, raceTrack, raceCheckpoint) {
                let keepThis = this;

                const req = new XMLHttpRequest();
                req.onerror = function () {

                };
                req.onload = function () {
                    if (this.status === 200) {
                        console.log("rm");
                        keepThis.races[raceIdx].raceTracks[raceTrackIdx].checkpoints.splice(raceCheckpoint.order,1);
                    }
                };
                req.open('DELETE', '/race/raid/'+raidID+'/race/'+race.id+'/racetrack/'+raceTrack.id+'/racecheckpoint/'+raceCheckpoint.id, true);
                req.setRequestHeader('Content-Type', 'application/json');
                req.send(null);

                iziToast.success({
                    message: 'Le checkpoint a bien été supprimé',
                    position: 'bottomRight',
                });
            },
            move (array, oldIndex, newIndex) {
                if (newIndex >= array.length || newIndex < 0) {
                    return array;
                }
                array.splice(newIndex, 0, array.splice(oldIndex, 1)[0]);
                return array;
            },
            openNewRacePopin() {
                this.newRace = new Race();
                MicroModal.show("add-race");
            },
            openNewTrackPopin(race) {
                this.currentRace = race;
                this.currentRaceTrack = null;
                this.tracksMap = Array.from(mapManager.tracksMap.values());
            },
            openNewCheckpointPopin(track) {
                this.currentRaceTrack = track;
                this.currentRace = null;

                let cp = [];
                for (let poi of Array.from(mapManager.poiMap.values())){
                    if(poi.isCheckpoint){
                        cp.push(poi);
                    }
                }

                this.checkpointsMap = cp;
            },
            addRace(e){
                e.preventDefault();

                let keepThis = this;

                this.newRace.raid = raidID;
                MicroModal.close("add-race");

                const req = new XMLHttpRequest();
                req.onerror = function () {

                };
                req.onload = function () {
                    if (this.status === 200) {

                        let r = new Race();
                        r.fromObj(JSON.parse(this.responseText));
                        keepThis.races.push(r);
                    }
                };
                req.open('PUT', '/race/raid/'+raidID+'/race', true);
                req.setRequestHeader('Content-Type', 'application/json');
                req.send(this.newRace.toJSON());

                iziToast.success({
                    message: 'L\'épreuve a bien été créée.',
                    position: 'bottomRight',
                });
            },
            addTrack(e,raceIdx){
                e.preventDefault();

                let keepThis = this;

                this.newRaceTrack.order = this.currentRace.raceTracks.length;

                let raceId = keepThis.races[raceIdx].id;

                const req = new XMLHttpRequest();
                req.onerror = function () {

                };
                req.onload = function () {
                    if (this.status === 200) {

                        let rt = new RaceTrack();
                        rt.fromObj(JSON.parse(this.responseText));
                        keepThis.races[raceIdx].raceTracks.push(rt);

                        keepThis.newRaceTrack = new RaceTrack();
                        keepThis.currentRace = null;
                    }
                };
                req.open('PUT', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack', true);
                req.setRequestHeader('Content-Type', 'application/json');
                req.send(this.newRaceTrack.toJSON());

                iziToast.success({
                    message: 'Le parcours a bien été ajouté à l\'épreuve.',
                    position: 'bottomRight',
                });
            },
            addCheckpoint(e, raceIdx, trackIdx){
                e.preventDefault();

                let keepThis = this;

                this.newCheckpoint.order = this.currentRaceTrack.checkpoints.length;

                let raceId = keepThis.races[raceIdx].id;
                let raceTrackId = keepThis.races[raceIdx].raceTracks[trackIdx].id;

                const req = new XMLHttpRequest();
                req.onerror = function () {

                };
                req.onload = function () {
                    if (this.status === 200) {

                        let rc = new Checkpoint();
                        rc.fromObj(JSON.parse(this.responseText));
                        keepThis.races[raceIdx].raceTracks[trackIdx].checkpoints.push(rc);

                        keepThis.newCheckpoint = new Checkpoint();
                        keepThis.currentRaceTrack = null;
                    }
                };
                req.open('PUT', '/race/raid/'+raidID+'/race/'+raceId+'/racetrack/'+raceTrackId+'/raceCheckpoint', true);
                req.setRequestHeader('Content-Type', 'application/json');
                req.send(this.newCheckpoint.toJSON());

                iziToast.success({
                    message: 'Le checkpoint a bien été ajouté à l\'épreuve.',
                    position: 'bottomRight',
                });
            },
            htmlDecode(str){
                return htmlentities.decode(str);
            },
            log(str){
                console.log(str);
                return(str);
            }
        }
    }
</script>
