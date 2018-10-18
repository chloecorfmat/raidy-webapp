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
    this.map = L.map('map').setView([48.742917, -3.459180], 15);
    this.tracksMap = new Map();
    this.poiMap = new Map();

    this.waypoints = [];
    this.distance = 0;
    this.mode = EditorMode.READING;
    this.currentEditID = 0;

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);
    var keepThis = this;

    /* this.line.addEventListener("click", function(e){
         e.originalEvent.stopPropagation();
         var i = 0;
         console.log(e);
         newPoint = L.point(e.latlng.lat, e.latlng.lng);
         nearestPoint = this.closestLayerPoint(newPoint);
         var points = this.waypoints;
         keepThis.addWaypoint(e);
         for (var point in points) {
             if(point == nearestPoint){
                 waypoints.insert(i, item)
             }
             i++;
         }
         console.log("line: "+keepThis.line);
         keepThis.reDrawLine();
     });*/
};
MapManager.prototype.initialize = function () {

    var keepThis = this;
    mapManager.map.addEventListener('click', function (e) {
        console.log("Mode : "+EditorMode.properties[keepThis.mode].name);
        switch (keepThis.mode) {
            case EditorMode.READING :
                break;
            case EditorMode.ADD_POI:
                mapManager.addPoiFromClick(e);
                if(editor.activeTab = "pois-pan") this.mode = EditorMode.POI_EDIT;
                else this.mode = EditorMode.READING;
                document.getElementById("addPoiButton").classList.remove("add--poi");

                break;
            case EditorMode.TRACK_EDIT :
                track = mapManager.tracksMap.get(keepThis.currentEditID);
                track.addPoint(e.latlng.lat, e.latlng.lng);
                break;
            case EditorMode.POI_EDIT :
                break;
            default :
                console.log("Something goes wrong with the map editor mode. " + this.mode);
        }

    });
    this.loadTracks();


}

MapManager.prototype.addTrack = function (track) {
    newTrack = new Track(this.map);
    newTrack.fromObj(track);
    this.tracksMap.set(track.id, newTrack);

    var li = document.createElement('li');
    li.classList.add("checkbox-item");
    li.innerHTML = `
       <label class="checkbox-item--label">
           <input id = "`+newTrack.id+`" type="checkbox" checked="checked">
           <span style ="background-color : `+newTrack.color+`; border-color :`+newTrack.color+`" class="checkmark">
                <i class="fas fa-check"></i>
            </span>
            <span>`+newTrack.name+`</span>
        </label>
        <button id = "`+newTrack.id+`" class="btn--track--edit">
            <i class="fas fa-pen"></i>
        </button>`;
    document.getElementById('editor--list').appendChild(li);
    console.log(li);
    // TRACK SELECTION LISTENER
    li.querySelectorAll('input').forEach(function(input){
        input.addEventListener('change', function () {
            if(input.checked){
                mapManager.showTrack(parseInt(input.id));
                li.querySelector('.btn--track--edit').style.display = "inline-block";
            }else{
                if(mapManager.currentEditID == input.id){
                    document.querySelectorAll('.track--edit').forEach(function (el) {
                        el.classList.remove('track--edit')
                    })
                    mapManager.switchMode(EditorMode.READING);
                }
                mapManager.hideTrack(parseInt(input.id))
                li.querySelector('.btn--track--edit').style.display = "none";
            }
        });
    });
    //TRACK EDIT PENCIL
    li.querySelectorAll('.btn--track--edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!this.parentElement.classList.contains('track--edit')) {
                document.querySelectorAll('.track--edit').forEach(function (el) {
                    el.classList.remove('track--edit')
                })
            }
            mapManager.currentEditID = parseInt(btn.id) ;
            mapManager.switchMode(EditorMode.TRACK_EDIT);
            this.parentElement.classList.toggle('track--edit');
        })
    });
}

MapManager.prototype.showTrack = function(id){
    this.tracksMap.get(id).show();
}
MapManager.prototype.hideTrack = function(id){
    this.tracksMap.get(id).hide();
}

MapManager.prototype.addPoiFromClick = function (e) {
    var id = Math.floor(Math.random()*100);
    this.addPoi(id, "Nouveau POI", [e.latlng.lat, e.latlng.lng], "#333333")
    this.mode = EditorMode.READING;
}
MapManager.prototype.addPoi = function (id, name, loc, color) {

  var poi = new Poi(id, name, loc, color, mapManager.map);
  this.poiMap.set(id, poi);

}






MapManager.prototype.setPoiEditable = function(b){
    this.poiMap.forEach(function (value, key, map) {
        value.setEditable(b);
    })
}

MapManager.prototype.setTracksEditable = function(b){
    this.tracksMap.forEach(function (value, key, map) {
        value.setEditable(b);
    })
}

MapManager.prototype.formatDistance = function (distance) {
    return Math.round(10 * distance / 1000) / 10 + " Km";
}

MapManager.prototype.clearAll = function () {
    var markers = this.waypoints;
    for (marker in markers) {
        this.map.removeLayer(markers[marker]);
    }

    this.waypoints = [];
    this.distance = 0;
    this.map.removeLayer(this.line);
    this.line = L.polyline([]).addTo(this.map);

    this.updateData();
}


MapManager.prototype.switchMode = function (mode) {
    this.mode = mode;
    console.log("Mode : "+EditorMode.properties[mode].name);
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
            this.tracksMap.get(this.currentEditID).setEditable(true);
            this.setPoiEditable(false);
            document.getElementById('map').style.cursor = "crosshair";
            break;
        case  EditorMode.READING :
            this.setPoiEditable(false);
            this.setTracksEditable(false);
            document.getElementById('map').style.cursor = "auto";

            break;
    }
}
MapManager.prototype.requestNewTrack = function(){
    var track = new Track();
    track.toJSON();
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("PUT", "/organizer/raid/"+raidID+"/track", true);
    xhr_object.setRequestHeader("Content-Type","application/json"),
    xhr_object.send(track.toJSON());
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
                  //  console.log(track);
                }
            } else {
                console.log("Status de la réponse: %d (%s)", xhr_object.status, xhr_object.statusText);
            }
        }
        mapManager.switchMode(EditorMode.READING);
    };

}
