let MapManager;
let EditorMode = Object.freeze({
  'READING': 0,
  'ADD_POI': 1,
  'TRACK_EDIT': 2,
  'POI_EDIT': 3,
  properties: {
    0: {name: 'READING', value: 0},
    1: {name: 'ADD_POI', value: 1},
    2: {name: 'TRACK_EDIT', value: 2},
    3: {name: 'POI_EDIT', value: 3}
  }
});

if (typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {

  /**
   * MapManager is the data to map content manager
   */
  MapManager = function() {
    this.map = L.map('map', {editable: true}).setView([46.9659015,2.458187], 6);
    this.OSMTiles = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);
    console.log("event loaded");

    this.redoBuffer = [];
    this.group = new L.featureGroup();
    this.group.addTo(this.map);

    this.waitingPoi = null;

    this.poiTypesMap   = new Map();
    this.sportTypesMap = new Map();
    this.tracksMap     = new Map();
    this.poiMap        = new Map();

    this.distance = 0;
    this.currentEditID = 0;

    this.mode = EditorMode.READING;
    this.lastMode = EditorMode.READING;
    this.editorUI = new EditorUI();
    this.GPXImporter = new GPXImporter(this);
    this.GPXExporter = new GPXExporter(this);
  }

  MapManager.prototype.initialize = function () {
    this.elevator = new MapElevation();

    /* MAP LISTENERS */
    let keepThis = this;
    this.initializeKeyboardControl();

    this.map.addEventListener('click', function (e) {
      switch (keepThis.mode) {
        case EditorMode.READING :
          break;
        case EditorMode.ADD_POI:
          MicroModal.show('add-poi-popin');
          keepThis.waitingPoi.marker.setLatLng(e.latlng);
          keepThis.map.removeLayer(keepThis.waitingPoi.marker); // mapManager.addPoiFromClick(e);

          keepThis.switchMode(EditorMode.READING);

          let fab = document.getElementById('fabActionButton');
          if(fab != null){
            fab.classList.remove('add--poi');
          }
          keepThis.map.removeEventListener("mousemove");
          keepThis.map.on("mousemove", function (e) {
            keepThis.mousePosition = e.latlng;
          });
          break;
        case EditorMode.TRACK_EDIT :
          break;
        case EditorMode.POI_EDIT :
          break;
        default :
      }
    });


    this.map.on('editable:enable', function () {
      keepThis.currentTrack = keepThis.tracksMap.get(keepThis.currentEditID);
    });
    /* Save track when middle marker is mouved */
    this.map.on('editable:middlemarker:mousedown', function () {
      let track = keepThis.tracksMap.get(keepThis.currentEditID)
      track.push();
      track.update();

    });
    this.map.on('editable:drawing:click', function () {
      let track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.name = htmlentities.decode(track.name);
      track.push();
     // keepThis.mapHistory.logModification({type : "ADD_TRACK_MARKER", target : e.Marker, lastPostition : keepThis.lastPostition, newPosition : e.vertex.latlng})
      track.update();
    });

    this.map.on('editable:drawing:clicked', function () {
      let track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.name = htmlentities.decode(track.name);
      track.push();
      track.update();
    });
    this.map.on('editable:drawing:mouseup', function () {
      let track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.update();
    });
    this.map.on('editable:vertex:dragstart', function (e) {
      keepThis.currentTrack = keepThis.tracksMap.get(keepThis.currentEditID);
      keepThis.lastPostition  = [];
      let latLngArray =  keepThis.currentTrack.line.getLatLngs();
      for (let element in latLngArray){
        keepThis.lastPostition.push({
          lat : latLngArray[element].lat,
          lng : latLngArray[element].lng
        });
      }
    });
    this.map.on('editable:vertex:dragend', function (e) {
      let track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.name = htmlentities.decode(track.name);
      keepThis.elevator.getElevationAt(e.vertex.latlng,function(){track.push()});
      track.push();
      track.update();
      keepThis.mapHistory.logModification({
        type : "MOVE_TRACK_MARKER",
        track : track,
        lastPosition : keepThis.lastPostition, })
    });

    this.map.on('editable:vertex:drag', function () {
      keepThis.currentTrack.update();
      keepThis.editorUI.updateTrack(keepThis.currentTrack)
    });
    this.map.on('editable:drawing:mouseup', function () {
      let track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.update();
    });

    this.map.on('editable:vertex:rawclick', function (e) { // click on a point
      keepThis.tracksMap.get(keepThis.currentEditID).update();
      e.cancel();
      e.vertex.continue();
    });

    this.map.on('editable:vertex:remove', function (e) { //point on track is removed
      let track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.update();
      keepThis.mapHistory.logModification({
        type : "MOVE_TRACK_MARKER",
        track : track,
        lastPosition : keepThis.lastPostition
      });
      let latLngArray =  keepThis.currentTrack.line.getLatLngs();
      keepThis.lastPostition  = [];
      for (let element in latLngArray){
        keepThis.lastPostition.push({
          lat : latLngArray[element].lat,
          lng : latLngArray[element].lng
        });
      }
    });

    this.map.on(' editable:vertex:new', function (e) {
      keepThis.elevator.getElevationAt(e.vertex.latlng, function(){track.push()});

      keepThis.mapHistory.logModification({
        type : "MOVE_TRACK_MARKER",
        track : keepThis.tracksMap.get(keepThis.currentEditID),
        lastPosition : keepThis.lastPostition
      });
      let latLngArray =  keepThis.currentTrack.line.getLatLngs();
      keepThis.lastPostition  = [];
      
      for (let element in latLngArray){
        keepThis.lastPostition.push({
          lat : latLngArray[element].lat,
          lng : latLngArray[element].lng
        });
      }
    });
    this.map.on('editable:drawing:end', function () {
      document.getElementById('map').style.cursor = 'grab';
    });
    this.map.on('editable:drawing:start', function () {
      document.getElementById('map').style.cursor = 'crosshair';
    });


    this.loadRessources();
    this.switchMode(EditorMode.READING);

  };
  MapManager.prototype.displayTrackButton = function (b) {
  }

  MapManager.prototype.loadRessources = function () {
    let keepThis = this;
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/editor/raid/'+raidID+'/poitype', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function () {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          let poiTypes = JSON.parse(xhr_object.responseText);
          for (let poiType of poiTypes) {
            keepThis.poiTypesMap.set(poiType.id, poiType);
          };
          keepThis.loadTracks(); // Load tracks
          keepThis.loadPois(); // Load PoiS
          keepThis.loadSportTypes();
        }
      }
    }
  };

  MapManager.prototype.loadSportTypes = function(){
      let keepThis = this;
      let xhr_object = new XMLHttpRequest();
      xhr_object.open('GET', '/editor/sporttype', true);
      xhr_object.send(null);
      xhr_object.onreadystatechange = function () {
          if (this.readyState === XMLHttpRequest.DONE) {
              if (xhr_object.status === 200) {
                  let sportTypes = JSON.parse(xhr_object.responseText);
                  for (let sportType of sportTypes) {
                      keepThis.sportTypesMap.set(sportType.id, sportType);
                  };
              }
          }
      }
  };

  MapManager.prototype.switchMode = function (mode) {
    if (this.mode == mode) return;
    switch (this.mode){ //leaving mode
      case EditorMode.ADD_POI :
        if (this.waitingPoi !=null ){
          this.map.removeEventListener("mousemove");
          this.map.on("mousemove", function (e) {
            keepThis.mousePosition = e.latlng;
          });
          this.map.removeLayer(this.waitingPoi.marker);
          document.getElementById("fabActionButton").classList.remove('add--poi');
        }
        break;
    }
    this.lastMode = this.mode;
    let keepThis = this;
    this.mode = mode;
    //console.log(this.lastMode);
   // console.log(this.mode);
    switch (mode) { //entering mode
      case EditorMode.ADD_POI :
        console.log("ADD POI");
        document.getElementById("fabActionButton").classList.add('add--poi');
        this.setPoiEditable(false);
        this.waitingPoi = new Poi(this.map);
        this.map.addLayer(this.waitingPoi.marker);
        if(keepThis.mousePosition != undefined){
          keepThis.waitingPoi.marker.setLatLng(keepThis.mousePosition);
        }
        keepThis.map.removeEventListener("mousemove");
        this.map.on("mousemove", function (e) {
          keepThis.waitingPoi.marker.setLatLng(e.latlng);
        });
        break;
      case EditorMode.POI_EDIT :
        if (this.waitingPoi != null) this.map.removeLayer(this.waitingPoi.marker);
        keepThis.map.removeEventListener("mousemove");
        this.map.on("mousemove", function (e) {
          keepThis.mousePosition = e.latlng;
        });
        document.getElementById('map').style.cursor = 'grab';
        document.getElementById('fabActionButton').classList.remove('add--poi');
        this.setTracksEditable(false);
        break;
      case EditorMode.TRACK_EDIT :
       // this.displayTrackButton(true);
        document.getElementById('map').style.cursor = 'grab';
        document.getElementById('fabActionButton').classList.add('add--poi');
        this.setTracksEditable(false);
        let res = this.tracksMap.get(this.currentEditID);
        let currentTrack = this.tracksMap.get(this.currentEditID);
        currentTrack.setEditable(true);
        if(currentTrack.line.isEmpty()){
          currentTrack.line.editor.continueForward();
          document.getElementById('map').style.cursor = 'crosshair';
        }
        this.elevator.initChart(currentTrack);
        break;
      case EditorMode.READING :
        document.getElementById('map').style.cursor = 'grab';
        this.setPoiEditable(false);
        this.setTracksEditable(false);
        let fab = document.getElementById('fabActionButton');
        if(fab != null){
            fab.classList.remove('add--poi');
        }
        break
    }
  };

  MapManager.prototype.addTrack = function (track) {
    let newTrack = new Track(this.map);
    newTrack.fromObj(track);
    this.tracksMap.set(track.id, newTrack);

    mapManager.editorUI.addTrack(newTrack);
    return newTrack;
  };
  MapManager.prototype.showTrack = function (id) {
    this.tracksMap.get(id).show();
  };

  MapManager.prototype.hideTrack = function (id) {
    this.tracksMap.get(id).hide();
  };
  MapManager.prototype.setTracksEditable = function (b) {
    this.tracksMap.forEach(function (tr) {
      tr.setEditable(b);
    });
  };
  MapManager.prototype.requestNewPoi = function (name, type, requiredHelpers, description, image, poiIsCheckpoint) {
    let poi = this.waitingPoi;
    poi.poiType = mapManager.poiTypesMap.get(parseInt(type));
    poi.name = name != "" ? name : poi.poiType.type;
    poi.requiredHelpers = requiredHelpers != "" ? parseInt(requiredHelpers) : 0;
    poi.description = description != '' ? description : '';
    poi.image = image != '' ? image : '';
    poi.isCheckpoint = poiIsCheckpoint != "" ? poiIsCheckpoint : false;

    console.log(poiIsCheckpoint);
    console.log(poi);
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/editor/raid/' + raidID + '/poi', true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(poi.toJSON());

    xhr_object.onreadystatechange = function () {
      // XMLHttpRequest.DONE === 4
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          poi = JSON.parse(xhr_object.responseText);
          mapManager.addPoi(poi);
        }
      }
    }
  };

  MapManager.prototype.requestNewTrack = function (name, color, sportType) {
    let track = new Track();
    track.name = name;
    track.color = color;
    track.sportType = sportType;

    let xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/editor/raid/' + raidID + '/track', true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(track.toJSON());

    xhr_object.onreadystatechange = function (event) {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          track = JSON.parse(xhr_object.responseText);
          mapManager.addTrack(track);
          mapManager.currentEditID = track.id;
          mapManager.currentTrack = mapManager.tracksMap.get(mapManager.currentEditID);
          mapManager.switchMode(EditorMode.TRACK_EDIT);
          mapManager.currentTrack.line.editor.continueForward();
        }
      }
    }
  };

  MapManager.prototype.loadTracks = function () {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/editor/raid/' + raidID + '/track', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          let tracks = JSON.parse(xhr_object.responseText);
          for (let track of tracks) {
            mapManager.addTrack(track);
          }
          if(mapManager.group.getLayers().length > 0) {
              mapManager.map.fitBounds(mapManager.group.getBounds());
          }
        }
      }
      mapManager.switchMode(EditorMode.READING);
    }
  };

  MapManager.prototype.loadPois = function () {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/editor/raid/' + raidID + '/poi', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          let pois = JSON.parse(xhr_object.responseText);
          for (let poi of pois) {
            mapManager.addPoi(poi);
          }
          if (mapManager.group.getLayers().length > 0) {
            mapManager.map.fitBounds(mapManager.group.getBounds());
          }
        }
      }
    }
  };

  MapManager.prototype.addPoi = function (poi) {
    let newPoi = new Poi(this.map);
    newPoi.fromObj(poi);
    this.poiMap.set(poi.id, newPoi);
    mapManager.editorUI.updatePoi(newPoi);
  };

  MapManager.prototype.setPoiEditable = function (b) {
      this.poiMap.forEach(function (poi) {
          poi.setEditable(b);
      });
  };

  MapManager.prototype.toggleTrackVisibility = function (track) {
    if (!track.visible)
    {
      this.showTrack(track.id);
    } else {
      if (this.currentEditID == track.id) {
        this.switchMode(EditorMode.READING);
      }
      this.hideTrack(track.id);
    }
  };

  MapManager.prototype.initializeKeyboardControl = function(){

    this.map.on("mousemove", function (e) {
      keepThis.mousePosition = e.latlng;
    });

    this.mapHistory = new MapHistory();
    let keepThis = this;
    console.log("Load keyboard listeners");
    let onKeyDown = function (e) {
        if (e.ctrlKey && e.keyCode == 90) { //Z
          if (e.shiftKey) {
            keepThis.mapHistory.redo();
          } else {
            keepThis.mapHistory.undo();
          }
        }
        if (e.ctrlKey && e.keyCode == 89) { //Y
          keepThis.mapHistory.redo();
        }
      if (e.keyCode == 80) { //P
        keepThis.switchMode(EditorMode.ADD_POI);
      }
      if (e.keyCode == 84) { //T
        MicroModal.show('add-track-popin');
      }
        if(e.key === "Escape") {
          console.log("ECHAP");
          if(keepThis.mode == EditorMode.TRACK_EDIT){
            if(keepThis.currentTrack.line.editor.drawing()){
              keepThis.currentTrack.line.editor.endDrawing();
            }else{
              keepThis.switchMode(keepThis.lastMode);
            }
          }else{
            keepThis.switchMode(EditorMode.READING);
          }
        }
      };
    //L.DomEvent.addListener(document, 'keydown', onKeyDown, keepThis.map);
    document.getElementById("map").addEventListener("keydown", onKeyDown);

  }
}


