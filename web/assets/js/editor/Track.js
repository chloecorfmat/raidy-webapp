if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {

  var Track = function (map) {
    this.map = map;
    this.line = [];


    this.startMarker = L.marker([0, 0]);
    this.startMarker.setIcon(L.divIcon({className: 'my-custom-pin',iconAnchor: [0, 0],labelAnchor: [0, 0], popupAnchor: [0, 0], iconSize: [2, 2],
      html: '<span class="track-marker" style=" border:1px solid black; background-color: #78e08f'  + ';" />'
    }));
    this.endMarker = L.marker([0, 0]);
    this.endMarker.setIcon(L.divIcon({className: 'my-custom-pin',iconAnchor: [0, 0],labelAnchor: [0, 0], popupAnchor: [0, 0], iconSize: [5, 5],
      html: '<span class="track-marker" style=" border:1px solid black; background-color: #f74a45' +  ';" />'
    }));

    this.id = '';
    this.name = '';
    this.color = '';
    this.sportType = 1;

    this.decorator = null;
    this.visible = true;
    this.calibration = false;
    this.waypoints = [];

    this.line = L.polyline([]);
  };

  Track.prototype.setName = function (name) {
    this.name = name
  };

  Track.prototype.setColor = function (color) {
    this.color = color;
    this.line.setStyle({
      color: color,
    })
  };

    Track.prototype.setSportType = function (sportType) {
        this.sportType = sportType;
    };

  Track.prototype.setEditable = function (b) {
    if(b){
      this.line.enableEdit();
      this.decorator.removeFrom(this.map);
      this.startMarker.removeFrom(this.map);
      this.endMarker.removeFrom(this.map);
    }else{
      this.line.disableEdit();
      if(this.visible) {
        this.decorator.addTo(this.map);
        if (!this.line.isEmpty()) {
          let latLngs = this.line.getLatLngs();
          this.startMarker.setLatLng(latLngs[0]);
          this.startMarker.addTo(this.map);
          if (latLngs.length > 1) {
            console.log(latLngs);
            this.endMarker.setLatLng(latLngs[latLngs.length - 1]);
            this.endMarker.addTo(this.map);
          }
        }
      }
    }
  };

  Track.prototype.calculDistance = function () {
    var points = this.line.getLatLngs();
    this.distance = 0;
    if (points.length > 1) {
      for (i = 0; i < points.length - 1; i++) {
        this.distance += points[i].distanceTo(points[i + 1]);
      }
    }
  };

  Track.prototype.hide = function () {
    this.decorator.removeFrom(this.map);
    this.startMarker.removeFrom(this.map);
    this.endMarker.removeFrom(this.map);

    var points = this.waypoints;
    for (var point in points) {
      mapManager.group.removeLayer(points[point]);
    }
    mapManager.group.removeLayer(this.line);
    this.visible = false;
  };

  Track.prototype.show = function () {
    this.decorator.addTo(this.map);
    this.startMarker.addTo(this.map);
    this.endMarker.addTo(this.map);

    var points = this.waypoints;
    for (var point in points) {
      mapManager.group.addLayer(points[point]);
    }
    mapManager.group.addLayer(this.line);
    this.visible = true;
  };

  Track.prototype.toJSON = function () {
    latlong = [];
    for (obj of this.line.getLatLngs()) {
      latlong.push({lat: obj.lat, lng: obj.lng});
    }
    var track =
      {
        id: this.id != null ? this.id : null,
        name: this.name,
        color: this.color,
        sportType: this.sportType,
        isVisible: this.visible,
        isCalibration: this.calibration,
        trackpoints: this.line != null ? JSON.stringify(latlong) : null
      };

    var json = JSON.stringify(track);
    return json;
  };

  Track.prototype.fromObj = function (track) {
    this.id = track.id;
    this.color = track.color;
    this.name = track.name;
    this.sportType = track.sportType;
    this.isVisible = track.isVisible;
    this.isCalibration = track.isCalibration;
    test = JSON.parse(track.trackpoints);

    this.line = L.polyline(test, {color: this.color}).addTo(mapManager.group);

    this.startMarker.setIcon(L.divIcon({className: 'my-custom-pin',iconAnchor: [0, 0],labelAnchor: [0, 0], popupAnchor: [0, 0], iconSize: [2, 2],
      html: '<span class="track-marker" style=" border:0.1rem solid '+this.color+'; background-color: #78e08f'  + ';" />'
    }));
    this.endMarker = L.marker([0, 0]);
    this.endMarker.setIcon(L.divIcon({className: 'my-custom-pin',iconAnchor: [0, 0],labelAnchor: [0, 0], popupAnchor: [0, 0], iconSize: [5, 5],
      html: '<span class="track-marker" style=" border:0.1rem solid '+this.color+'; background-color: #f74a45' +  ';" />'
    }));

    this.line.bindPopup('' +
      '<header style="' +
      'background: ' + this.color + ' ;' +
      'color: #ffffff ;' +
      'padding: 0rem 3rem;">' +
      '<h3>' + this.name + '</h3>' +
      '</header>');
    this.line.enableEdit();

    this.decorator = L.polylineDecorator(this.line, {
      patterns: [
        {offset: 25, repeat: 100, symbol: L.Symbol.arrowHead({pixelSize: 15, pathOptions: {fillOpacity: 1, color: this.color, weight: 0}})}
      ]
    }).addTo(this.map);

  };
  Track.prototype.fromJSON = function (json) {
    var track = JSON.parse(json);
    this.fromObj(track);
  };

  Track.prototype.push = function () {
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('PATCH', '/organizer/raid/' + raidID + '/track/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(this.toJSON());

    //Encode html entities to display purpose only
    this.name = htmlentities.encode(this.name);

    li = document.getElementById('track-li-' + this.id);
    this.calculDistance();
    li.querySelector('label > div > span:nth-child(2)').innerHTML = '(' + Math.round(10 * this.distance / 1000) / 10 + ' Km)';
    mapManager.editorUI.updateTrack(this);

  };
  Track.prototype.remove = function () {
    var xhr_object = new XMLHttpRequest();
    xhr_object.open('DELETE', '/organizer/raid/' + raidID + '/track/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');

    xhr_object.send(null);

    this.map.removeLayer(this.line);
    this.map.removeLayer(this.decorator);
    this.map.removeLayer(this.startMarker);
    this.map.removeLayer(this.endMarker);

    mapManager.editorUI.removeTrack(this);
  };
  Track.prototype.buildUI = function () {
    mapManager.editorUI.updatePoi()
  };

  console.log("Track JS loaded");
}