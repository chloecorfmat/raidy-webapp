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

  function MapManager() {

    this.map = L.map('map', {editable: true}).setView([48.742917, -3.459180], 15);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    this.group = new L.featureGroup();
    this.group.addTo(this.map);

    this.waitingPoi = null;

    this.poiTypesMap = new Map();
    this.tracksMap = new Map();
    this.poiMap = new Map();

    this.distance = 0;
    this.currentEditID = 0;

    this.mode = EditorMode.READING;
    this.editorUI = new EditorUI();
  }

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
          document.getElementById('addPoiButton').classList.remove('add--poi');

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
      track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.push();
    });
    this.map.on('editable:drawing:click', function () {
      track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.push();
    });

    this.map.on('editable:vertex:dragend', function () {
      track = keepThis.tracksMap.get(keepThis.currentEditID);
      track.push();
    });


    this.map.on('editable:vertex:mousedown ', function (e) {
      e.vertex.continue();
    });
    this.map.on('editable:drawing:end', function () {
      document.getElementById('map').style.cursor = 'grab';
    });
    this.map.on('editable:drawing:start', function () {
      document.getElementById('map').style.cursor = 'crosshair';
    })

    this.loadRessources()

  };
  MapManager.prototype.displayTrackButton = function (b) {
  }

    MapManager.prototype.loadRessources = function () {
    var keepThis = this;
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/organizer/poitype', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function () {
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          var poiTypes = JSON.parse(xhr_object.responseText);
          for (poiType of poiTypes) {
            keepThis.poiTypesMap.set(poiType.id, poiType);
          };
          keepThis.loadTracks(); // Load tracks
          keepThis.loadPois(); // Load PoiS
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
        document.getElementById('map').style.cursor = 'none';
        this.map.addLayer(this.waitingPoi.marker);
        this.map.on("mousemove", function (e) {
          keepThis.waitingPoi.marker.setLatLng(e.latlng);
        });
        break;
      case EditorMode.POI_EDIT :
        if (this.waitingPoi != null) this.map.removeLayer(this.waitingPoi.marker);
        this.map.removeEventListener("mousemove");
        document.getElementById('map').style.cursor = 'grab';
        document.getElementById('addPoiButton').classList.remove('add--poi');
        document.querySelectorAll('.track--edit').forEach(function (el) {
          el.classList.remove('track--edit');
        });
        this.setTracksEditable(false);
        break;
      case EditorMode.TRACK_EDIT :
        this.displayTrackButton(true);
        document.getElementById('map').style.cursor = 'crosshair';
        document.getElementById('addPoiButton').classList.remove('add--poi');
        this.setTracksEditable(false);
        var res = this.tracksMap.get(this.currentEditID);
        currentTrack = this.tracksMap.get(this.currentEditID);
        currentTrack.setEditable(true);
        break;
      case EditorMode.READING :
        document.getElementById('map').style.cursor = 'grab';
        this.setPoiEditable(false);
        this.setTracksEditable(false);
        break
    }
  };

  MapManager.prototype.addTrack = function (track) {
    newTrack = new Track(this.map);
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
    this.tracksMap.forEach(function (value) {
      value.setEditable(b);
    })
  };
  MapManager.prototype.requestNewPoi = function (name, type, requiredHelpers) {
    var poi = this.waitingPoi;
    poi.name = name;
    poi.poiType = mapManager.poiTypesMap.get(parseInt(type));
    poi.requiredHelpers = parseInt(requiredHelpers);

    var xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/organizer/raid/' + raidID + '/poi', true);
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

  MapManager.prototype.requestNewTrack = function (name, color) {
    var track = new Track();
    track.name = name;
    track.color = color;
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/organizer/raid/' + raidID + '/track', true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(track.toJSON());

    xhr_object.onreadystatechange = function (event) {
      // XMLHttpRequest.DONE === 4
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          track = JSON.parse(xhr_object.responseText);
          mapManager.addTrack(track);
          mapManager.currentEditID = track.id;
          mapManager.switchMode(EditorMode.TRACK_EDIT);
          currentTrack.line.editor.continueForward();
        }
      }
    }
  };

  MapManager.prototype.loadTracks = function () {
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/organizer/raid/' + raidID + '/track', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      // XMLHttpRequest.DONE === 4
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          var tracks = JSON.parse(xhr_object.responseText);
          for (track of tracks) {
            mapManager.addTrack(track);
          }
        }
      }
      mapManager.switchMode(EditorMode.READING);
    }
  };
  MapManager.prototype.loadPois = function () {
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('GET', '/organizer/raid/' + raidID + '/poi', true);
    xhr_object.send(null);
    xhr_object.onreadystatechange = function (event) {
      // XMLHttpRequest.DONE === 4
      if (this.readyState === XMLHttpRequest.DONE) {
        if (xhr_object.status === 200) {
          var pois = JSON.parse(xhr_object.responseText);
          for (poi of pois) {
            mapManager.addPoi(poi);
          }
          mapManager.map.fitBounds(mapManager.group.getBounds());
        }
      }
      mapManager.switchMode(EditorMode.READING);
    }
  };

  MapManager.prototype.addPoi = function (poi) {
    newPoi = new Poi(this.map);
    newPoi.fromObj(poi);
    this.poiMap.set(poi.id, newPoi);
    mapManager.editorUI.updatePoi(newPoi)
  };

  MapManager.prototype.setPoiEditable = function (b) {
    this.poiMap.forEach(function (value, key, map) {
      value.setEditable(b);
    })
  };

  //console.log("MapManager loaded");
}

