var MapManager = {};

if (typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {

  /*
  * Map editor mode :
  * 0 = reading
  * 1 = add poi
  * 2 = track edition
  */

  var EditorMode = Object.freeze({
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

  MapManager = function() {
    this.map = L.map('map', {editable: true}).setView([46.9659015,2.458187], 6);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    L.DomEvent.addListener(document, 'keydown', onKeyDown, this.map);
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

  onKeyDown = function (e) {
    var latlng;
    console.log(mapManager.map.editTools);
   // console.log(mapManager.map.editTools);
   // console.log(mapManager.map.editTools._drawingEditor);
    if (e.keyCode == 90) {
      var currentTrack = mapManager.tracksMap.get(mapManager.currentEditID);
      console.log("Z");
      if (!currentTrack.line.editor) return;
      console.log(currentTrack.line.editor);
      currentTrack.line.editor.pop();
     /* if (e.shiftKey) {
        console.log("CTRL-SHIFT-Z");
        if (this.redoBuffer.length) currentTrack.line.editor.push(mapManager.redoBuffer.pop());
      } else {
        console.log("CTRL-Z");
        latlng = currentTrack.line.editor.pop();
        if (latlng) mapManager.redoBuffer.push(latlng);
      }*/
    }
  };

  MapManager.prototype.initialize = function () {
    /* MAP LISTENERS */
    var keepThis = this;

    this.map.addEventListener('click', function (e) {
      // console.log("Mode : "+EditorMode.properties[keepThis.mode].name);
      switch (keepThis.mode) {
        case EditorMode.READING :
          break;
        case EditorMode.ADD_POI:
          MicroModal.show('add-poi-popin');
          keepThis.waitingPoi.marker.setLatLng(e.latlng);
          keepThis.map.removeLayer(keepThis.waitingPoi.marker); // mapManager.addPoiFromClick(e);
          if (editor.activeTab = 'pois-pan') {
            keepThis.switchMode(EditorMode.POI_EDIT);
          } else {
            keepThis.switchMode(EditorMode.READING);
          }

          var fab = document.getElementById('fabActionButton');
          if(fab != null){
            fab.classList.remove('add--poi');
          }
          keepThis.map.removeEventListener("mousemove");
          break;
        case EditorMode.TRACK_EDIT :
          break;
        case EditorMode.POI_EDIT :
          break;
        default :
      }
    });

    this.map.on('editable:middlemarker:mousedown', function () {
      var track = keepThis.tracksMap.get(keepThis.currentEditID)
      track.push();
    });
    this.map.on('editable:drawing:click', function () {
      var track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.name = htmlentities.decode(track.name);
      track.push();
      track.update();
    });

    this.map.on('editable:drawing:clicked', function () {
      var track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.name = htmlentities.decode(track.name);
      track.push();
      track.update();
    });
    this.map.on('editable:drawing:mouseup', function () {
      var track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.update();
    });
    this.map.on('editable:vertex:dragstart', function () {
      this.currentTrack = keepThis.tracksMap.get(keepThis.currentEditID);
    });

    this.map.on('editable:vertex:drag', function () {
      this.currentTrack.update();
    });
    this.map.on('editable:drawing:mouseup', function () {
      track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.update();
    });

    this.map.on('editable:vertex:rawclick', function (e) {
      e.cancel();
      e.vertex.continue();
    });

    this.map.on('editable:drawing:end', function () {
      document.getElementById('map').style.cursor = 'grab';
    });
    this.map.on('editable:drawing:start', function () {
      document.getElementById('map').style.cursor = 'crosshair';
    });

    this.loadRessources();

  };
  MapManager.prototype.displayTrackButton = function (b) {
  }

  MapManager.prototype.loadRessources = function () {
    var keepThis = this;
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/editor/raid/'+raidID+'/poitype', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function () {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          var poiTypes = JSON.parse(xhr_object.responseText);
          for (var poiType of poiTypes) {
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
      var keepThis = this;
      var xhr_object = new XMLHttpRequest();
      xhr_object.open('GET', '/editor/sporttype', true);
      xhr_object.send(null);
      xhr_object.onreadystatechange = function () {
          if (this.readyState === XMLHttpRequest.DONE) {
              if (xhr_object.status === 200) {
                  var sportTypes = JSON.parse(xhr_object.responseText);
                  for (var sportType of sportTypes) {
                      keepThis.sportTypesMap.set(sportType.id, sportType);
                  };
              }
          }
      }
  };

  MapManager.prototype.switchMode = function (mode) {
    if (this.mode != mode) this.lastMode = this.mode;
    var keepThis = this;
    this.mode = mode;

    switch (mode) {
      case EditorMode.ADD_POI :
        this.setPoiEditable(false);
        this.waitingPoi = new Poi(this.map);
        //disable cursor none for IE
        //document.getElementById('map').style.cursor = 'none';
        this.map.addLayer(this.waitingPoi.marker);
        this.map.on("mousemove", function (e) {
          keepThis.waitingPoi.marker.setLatLng(e.latlng);
        });
        break;
      case EditorMode.POI_EDIT :
        if (this.waitingPoi != null) this.map.removeLayer(this.waitingPoi.marker);
        this.map.removeEventListener("mousemove");
        document.getElementById('map').style.cursor = 'grab';
        document.getElementById('fabActionButton').classList.remove('add--poi');
        var els = document.querySelectorAll('.track--edit');
        for(var el of els) {
          el.classList.remove('track--edit');
        }
        this.setTracksEditable(false);
        break;
      case EditorMode.TRACK_EDIT :
       // this.displayTrackButton(true);
        document.getElementById('map').style.cursor = 'grab';
        document.getElementById('fabActionButton').classList.add('add--poi');
        this.setTracksEditable(false);
        var res = this.tracksMap.get(this.currentEditID);
        var currentTrack = this.tracksMap.get(this.currentEditID);
        currentTrack.setEditable(true);
        if(currentTrack.line.isEmpty()){
          currentTrack.line.editor.continueForward();
          document.getElementById('map').style.cursor = 'crosshair';
        }
        break;
      case EditorMode.READING :
        document.getElementById('map').style.cursor = 'grab';
        this.setPoiEditable(false);
        this.setTracksEditable(false);
        var fab = document.getElementById('fabActionButton');
        if(fab != null){
            fab.classList.remove('add--poi');
        }
        break
    }
  };

  MapManager.prototype.addTrack = function (track) {
    var newTrack = new Track(this.map);
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
  MapManager.prototype.requestNewPoi = function (name, type, requiredHelpers) {
    var poi = this.waitingPoi;
    poi.poiType = mapManager.poiTypesMap.get(parseInt(type));
    poi.name = name != "" ? name : poi.poiType.type;
    poi.requiredHelpers = requiredHelpers != "" ? parseInt(requiredHelpers) : 0;

    var xhr_object = new XMLHttpRequest();
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
    var track = new Track();
    track.name = name;
    track.color = color;
    track.sportType = sportType;

    var xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/editor/raid/' + raidID + '/track', true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(track.toJSON());

    xhr_object.onreadystatechange = function (event) {
      // XMLHttpRequest.DONE === 4
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
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/editor/raid/' + raidID + '/track', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      // XMLHttpRequest.DONE === 4
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          var tracks = JSON.parse(xhr_object.responseText);
          for (var track of tracks) {
            mapManager.addTrack(track);
          }
        }
      }
      mapManager.switchMode(EditorMode.READING);
    }
  };
  MapManager.prototype.loadPois = function () {
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/editor/raid/' + raidID + '/poi', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      // XMLHttpRequest.DONE === 4
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          var pois = JSON.parse(xhr_object.responseText);
          for (var poi of pois) {
            mapManager.addPoi(poi);
          }
          if(mapManager.group.getLayers().length > 0) {
            mapManager.map.fitBounds(mapManager.group.getBounds());
          }
        }
      }
      mapManager.switchMode(EditorMode.READING);
    }
  };

  MapManager.prototype.addPoi = function (poi) {
    var newPoi = new Poi(this.map);
    newPoi.fromObj(poi);
    this.poiMap.set(poi.id, newPoi);
    mapManager.editorUI.updatePoi(newPoi)
  };

  MapManager.prototype.setPoiEditable = function (b) {
    var pois = this.poiMap.forEach(function (poi) {
      poi.setEditable(b);
    });
  };

  MapManager.prototype.toggleTrackVisibility = function (track)
  {
   // console.log("changed : " + newTrack.visible);
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
}


