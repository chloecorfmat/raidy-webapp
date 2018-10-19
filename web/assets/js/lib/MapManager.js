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
    this.mode = EditorMode.READING;
    this.currentEditID = 0;
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    var keepThis = this;

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
                if(editor.activeTab = "pois-pan") keepThis.switchMode(EditorMode.POI_EDIT);
                else keepThis.switchMode(EditorMode.READING);
                document.getElementById("addPoiButton").classList.remove("add--poi");

                break;
            case EditorMode.TRACK_EDIT :
               // track = mapManager.tracksMap.get(keepThis.currentEditID);
               // track.addPoint(e.latlng.lat, e.latlng.lng);
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
    li.id = "track-li-"+newTrack.id;
    li.classList.add("checkbox-item");
    li.innerHTML = `
       <label class="checkbox-item--label">
           <input data-id = "`+newTrack.id+`" type="checkbox" checked="checked">
           <span style ="background-color : `+newTrack.color+`; border-color :`+newTrack.color+`" class="checkmark">
                <i class="fas fa-check"></i>
            </span>
            <span class="trackName-`+newTrack.name+`">`+newTrack.name+`</span>
        </label>
        <button data-id = "`+newTrack.id+`" class="btn--track--edit">
            <i class="fas fa-pen"></i>
        </button>
        <button data-id = "`+newTrack.id+`" class="btn--track--settings">
            <i class="fas fa-cog"></i>
        </button>`;
    document.getElementById('editor--list').appendChild(li);
  //  console.log(li);
    // TRACK SELECTION LISTENER
    li.querySelectorAll('input').forEach(function(input){
        input.addEventListener('change', function () {
            if(input.checked){
                mapManager.showTrack(parseInt(input.dataset.id));
                li.querySelector('.btn--track--edit').style.display = "inline-block";
            }else{
                if(mapManager.currentEditID == input.dataset.id){
                    document.querySelectorAll('.track--edit').forEach(function (el) {
                        el.classList.remove('track--edit')
                    })
                    mapManager.switchMode(EditorMode.READING);
                }
                mapManager.hideTrack(parseInt(input.dataset.id))
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
           // console.log(btn);
            mapManager.currentEditID = parseInt(btn.dataset.id) ;
            mapManager.switchMode(EditorMode.TRACK_EDIT);
            this.parentElement.classList.toggle('track--edit');
        })
    });


    //TRACK SETTINGS COG
    li.querySelectorAll('.btn--track--settings').forEach(function (btn) {
       id = parseInt(btn.dataset.id);
       track =  mapManager.tracksMap.get(id);
        btn.addEventListener('click', function () {
           // console.log(document.querySelector('#TrackSettings_name'));
            document.querySelector('#TrackSettings_name').value  = track.name;
            document.querySelector('#TrackSettings_color').value = track.color;
            document.querySelector('#TrackSettings_id').value    = track.id;

            MicroModal.show('track-setting-popin');

        });
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
                  //  console.log(track);
                }
            } else {
                console.log("Status de la réponse: %d (%s)", xhr_object.status, xhr_object.statusText);
            }
        }
        mapManager.switchMode(EditorMode.READING);
    };

}

