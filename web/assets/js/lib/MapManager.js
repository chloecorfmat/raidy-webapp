/*
* Map editor mode :
* 0 = reading
* 1 = add poi
* 2 = track edition
*/

var EditorMode = Object.freeze({"READING":0, "ADD_POI":1, "TRACK_EDIT":2,"POI_EDIT":3,
    properties: {
        0: {name: "READING", value: 0},
        1: {name: "ADD_POI", value: 1},
        2: {name: "TRACK_EDIT", value: 2},
        3: {name: "POI_EDIT", value: 3}

    } });

var MapManager = function () {
    this.map = L.map('map', {editable: true}).setView([48.742917, -3.459180], 15);

    this.tracksMap = new Map();
    this.poiMap = new Map();

    this.waypoints = [];

    this.distance = 0;
    this.currentEditID = 0;

    this.mode = EditorMode.READING;

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    var keepThis = this;

};
MapManager.prototype.initialize = function () {

    /* MAP LISTENERS */
    var keepThis = this;

    this.map.addEventListener('click', function (e) {
        console.log("Mode : "+EditorMode.properties[keepThis.mode].name);
        switch (keepThis.mode) {
            case EditorMode.READING :
                break;
            case EditorMode.ADD_POI:
                MicroModal.show('add-poi-popin');

                //mapManager.addPoiFromClick(e);
                if(editor.activeTab = "pois-pan") keepThis.switchMode(EditorMode.POI_EDIT);
                else keepThis.switchMode(EditorMode.READING);
                document.getElementById("addPoiButton").classList.remove("add--poi");
                break;
            case EditorMode.TRACK_EDIT :
                break;
            case EditorMode.POI_EDIT :
                break;
            default :
                console.log("Something goes wrong with the map editor mode. " + this.mode);
        }
    });

    this.map.on('editable:middlemarker:mousedown', function (e) {
        console.log("Handled : editable:shape:new");
        console.log(e);
        track = keepThis.tracksMap.get(keepThis.currentEditID);
        track.push();
    });
    this.map.on('editable:drawing:click', function (e) {
        console.log("Handled : editable:shape:new");
        console.log(e);
        track = keepThis.tracksMap.get(keepThis.currentEditID);
        track.push();
    });

    this.map.on('editable:vertex:dragend', function (e) {
        console.log("Handled : editable:dragend");
        console.log(e);
        track = keepThis.tracksMap.get(keepThis.currentEditID);
        track.push();
    });

    this.loadTracks(); //Load tracks

}

MapManager.prototype.switchMode = function (mode) {
    this.mode = mode;
    console.log("Switch mode to : "+EditorMode.properties[mode].name);
    switch (mode) {
        case  EditorMode.ADD_POI :
            this.setPoiEditable(false);
            document.getElementById('map').style.cursor = "crosshair";
            break;
        case  EditorMode.POI_EDIT :
            this.setPoiEditable(true);
            break;
        case  EditorMode.TRACK_EDIT :
            this.setTracksEditable(false);
            var res = this.tracksMap.get(this.currentEditID);
            currentTrack = this.tracksMap.get(this.currentEditID);
            currentTrack.setEditable(true);
            currentTrack.line.editor.continueForward();
            this.setPoiEditable(false);
            document.getElementById('map').style.cursor = "crosshair";
            break;
        case  EditorMode.READING :
            this.setPoiEditable(false);
            this.setTracksEditable(false);
            document.getElementById('map').style.cursor = "grab";
            break;
    }
}

MapManager.prototype.addTrack = function (track) {
    newTrack = new Track(this.map);
    newTrack.fromObj(track);
    this.tracksMap.set(track.id, newTrack);

    var li = document.createElement('li');
    li = newTrack.buildUI(li);

    console.log(li);
    document.getElementById('editor--list').appendChild(li);


}
MapManager.prototype.showTrack = function(id){
    this.tracksMap.get(id).show();
}

MapManager.prototype.hideTrack = function(id){
    this.tracksMap.get(id).hide();
}
MapManager.prototype.setTracksEditable = function(b){
    this.tracksMap.forEach(function (value, key, map) {
        value.setEditable(b);
    })
}

MapManager.prototype.requestNewTrack = function(name, color){
    var track = new Track();
    track.name = name;
    track.color = color;
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("PUT", "/organizer/raid/"+raidID+"/track", true);
    xhr_object.setRequestHeader("Content-Type","application/json");
    xhr_object.send(track.toJSON());
    //  console.log(track.toJSON());

    xhr_object.onreadystatechange = function(event) {
        // XMLHttpRequest.DONE === 4
        if (this.readyState === XMLHttpRequest.DONE) {
            if (xhr_object.status === 200) {
                track = JSON.parse(xhr_object.responseText);
                mapManager.addTrack(track);
            } else {
                console.log("Status de la réponse: %d (%s)", xhr_object.status, xhr_object.statusText);
            }
        }
    }
}

MapManager.prototype.loadTracks =  function(){

    var xhr_object = new XMLHttpRequest();
    xhr_object.open("GET", "/organizer/raid/"+raidID+"/track", true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function(event) {
        // XMLHttpRequest.DONE === 4
        if (this.readyState === XMLHttpRequest.DONE) {
            if (xhr_object.status === 200) {
                // console.log("Réponse reçue: %s", xhr_object.responseText);
                var tracks = JSON.parse(xhr_object.responseText);
                for(track of tracks){
                    mapManager.addTrack(track);
                }
            } else {
                console.log("Status de la réponse: %d (%s)", xhr_object.status, xhr_object.statusText);
            }
        }
        mapManager.switchMode(EditorMode.READING);
    };

}
MapManager.prototype.loadPois =  function(){

    var xhr_object = new XMLHttpRequest();
    xhr_object.open("GET", "/organizer/raid/"+raidID+"/poi", true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function(event) {
        // XMLHttpRequest.DONE === 4
        if (this.readyState === XMLHttpRequest.DONE) {
            if (xhr_object.status === 200) {
                // console.log("Réponse reçue: %s", xhr_object.responseText);
                var pois = JSON.parse(xhr_object.responseText);
                for(poi of pois){
                    mapManager.addPoi(poi);
                }
            } else {
                console.log("Status de la réponse: %d (%s)", xhr_object.status, xhr_object.statusText);
            }
        }
        mapManager.switchMode(EditorMode.READING);
    };

}

MapManager.prototype.addPoiFromClick = function (e) {
    var id = Math.floor(Math.random()*100);
    this.addPoi(id, "Nouveau POI", [e.latlng.lat, e.latlng.lng], "#333333")
    this.mode = EditorMode.READING;
}
MapManager.prototype.addPoi = function (id, name, loc, color) {
  var poi = new Poi(id, name, loc, color, mapManager.map);
  this.poiMap.set(id, poi);

    newPoi = new Poi(this.map);
    newPoi.fromObj(track);
    this.tracksMap.set(poi.id, newPoi);

   /* var li = document.createElement('li');
    li = newTrack.buildUI(li);

    console.log(li);
    document.getElementById('editor--list').appendChild(li);*/



}

MapManager.prototype.setPoiEditable = function(b){
    this.poiMap.forEach(function (value, key, map) {
        value.setEditable(b);
    })
}

