/**
 * Track class
 * Manage all actions on Tracks on the map
 */
let Track;
if (typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  let patternParameters;
  Track = function (map) {
    this.map = map;

    this.startMarker = L.marker([0, 0]);
    this.endMarker = L.marker([0, 0]);

    this.id = '';
    this.name = '';
    this.color = '';
    this.sportType = 1;

    this.decorator = null;
    this.visible = true;
    this.isCalibration = false;
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
    });

    this.addDecorator();

    this.startMarker.setIcon(L.divIcon({
      className: 'my-custom-pin', iconAnchor: [0, 0], labelAnchor: [0, 0], popupAnchor: [0, 0], iconSize: [2, 2],
      html: '<span class="track-marker" style=" border:2px solid ' + this.color + '; background-color: #78e08f' + ';" />'
    }));
    this.endMarker.setIcon(L.divIcon({
      className: 'my-custom-pin', iconAnchor: [0, 0], labelAnchor: [0, 0], popupAnchor: [0, 0], iconSize: [5, 5],
      html: '<span class="track-marker" style=" border:2px solid ' + this.color + '; background-color: #f74a45' + ';" />'
    }));

  };
  Track.prototype.addDecorator = function () {
    if (this.decorator != undefined) {
      this.map.removeLayer(this.decorator);
    }
    patternParameters = {
      offset: 100,
      endOffset: 50,
      repeat: 100,
      symbol: L.Symbol.arrowHead({
        pixelSize: 12,
        pathOptions: {fillOpacity: 1, color: this.color, weight: 1}
      })
    };

    this.decorator = L.polylineDecorator(this.line, {
      patterns: [patternParameters]
    });

    this.decorator.addTo(this.map);
  }
  Track.prototype.setSportType = function (sportType) {
    this.sportType = sportType;
  };

  Track.prototype.setEditable = function (b) {
    if (b) {
      this.line.enableEdit();
      this.decorator.removeFrom(this.map);
    } else {
      this.line.disableEdit();
      if (this.visible) {
        this.decorator.addTo(this.map);
        if (!this.line.isEmpty()) {
          this.decorator.removeFrom(this.map);
          this.addDecorator();

          let latLngs = this.line.getLatLngs();
          this.startMarker.setLatLng(latLngs[0]);
          this.startMarker.addTo(this.map);
          if (latLngs.length > 1) {
            this.endMarker.setLatLng(latLngs[latLngs.length - 1]);
            this.endMarker.addTo(this.map);
          }
        }
      }
    }
  };
  Track.prototype.update = function () {

    if (!this.line.isEmpty()) {
      if(!this.map.hasLayer(this.startMarker)){
        this.map.addLayer(this.startMarker);
      }
      let latLngs = this.line.getLatLngs();
      this.startMarker.setLatLng(latLngs[0]);
      if (latLngs.length > 1) {
        if(!this.map.hasLayer(this.endMarker)){
          this.map.addLayer(this.endMarker);
        }
        this.endMarker.setLatLng(latLngs[latLngs.length - 1]);
      }else{
        this.map.removeLayer(this.endMarker);
      }
    }else{
      this.map.removeLayer(this.startMarker);
      this.map.removeLayer(this.endMarker);
    }
  };


  Track.prototype.calculElevation = function () {
    let points = this.line.getLatLngs();
    var dp = 0,
      dm = 0,
      ret = {};

    for (var i = 0; i < points.length - 1; i++) {
      var diff = parseFloat(points[i + 1].alt) - parseFloat(points[i].alt);

      if (diff < 0) {
        dm += diff;
      } else if (diff > 0) {
        dp += diff;
      }
    }

    var elevation = [];
    var sum = 0;

    for (var i = 0, len = points.length; i < len; i++) {
      var ele = parseFloat(points[i].alt);
      elevation.push(ele);
      sum += ele;
    }

    this.maxElev = Math.max.apply(null, elevation);
    this.minElev = Math.min.apply(null, elevation);
    this.posElev = Math.abs(dp);
    this.negElev = Math.abs(dm);
    this.avgElev = sum / elevation.length;

    return ret;
  };

  Track.prototype.calculDistance = function () {
    let points = this.line.getLatLngs();
    this.distance = 0;
    if (points.length > 1) {
      for (let i = 0; i < points.length - 1; i++) {
        this.distance += points[i].distanceTo(points[i + 1]);
      }
    }
  };
  Track.prototype.updateDecorator = function () {
    this.decorator.removeFrom(this.map);
    this.addDecorator();
  }
  Track.prototype.hide = function () {
    this.decorator.removeFrom(this.map);
    this.startMarker.removeFrom(this.map);
    this.endMarker.removeFrom(this.map);

    let points = this.waypoints;
    for (let point in points) {
      mapManager.group.removeLayer(points[point]);
    }
    mapManager.group.removeLayer(this.line);
    this.visible = false;
    this.name = htmlentities.decode(this.name);
    this.push();
  };

  Track.prototype.show = function () {
    this.hide();
    mapManager.group.addLayer(this.line);

    this.addDecorator();
    this.startMarker.addTo(this.map);

    this.endMarker.addTo(this.map);
    let points = this.waypoints;
    for (let point in points) {
      mapManager.group.addLayer(points[point]);
    }
    this.visible = true;
    this.name = htmlentities.decode(this.name);
    this.push();
    this.update();
  };

  Track.prototype.toJSON = function () {
    let latlong = [];
    let i = 0;
    for (let obj of this.line.getLatLngs()) {
      latlong.push({lat: obj.lat, lng: obj.lng, ele: obj.alt});
      i++;
    }
    let track =
      {
        id: this.id != null ? this.id : null,
        name: this.name,
        color: this.color,
        sportType: this.sportType,
        isVisible: this.visible,
        isCalibration: this.isCalibration,
        trackpoints: this.line != null ? JSON.stringify(latlong) : null
      };

    let json = JSON.stringify(track);
    return json;
  };

  Track.prototype.fromObj = function (track) {
    this.id = track.id;
    this.color = track.color;
    this.name = track.name;
    this.sportType = track.sportType;
    this.visible = track.isVisible;
    this.isCalibration = track.isCalibration;
    let waypoints = JSON.parse(track.trackpoints);

    this.line = L.polyline(waypoints, {weight: 3, color: this.color});

    let i = 0;
    for (let obj of this.line.getLatLngs()) {
      obj.alt = waypoints[i].ele;
      i++;
    }

    this.line.addTo(mapManager.group);

    this.startMarker.setIcon(L.divIcon({
      className: 'my-custom-pin', iconAnchor: [0, 0], labelAnchor: [0, 0], popupAnchor: [0, 0],
      html: '<span class="track-marker" style=" border:2px solid ' + this.color + '; background-color: #78e08f' + ';" />'
    }));
    this.endMarker.setIcon(L.divIcon({
      className: 'my-custom-pin', iconAnchor: [0, 0], labelAnchor: [0, 0], popupAnchor: [0, 0],
      html: '<span class="track-marker" style=" border:2px solid ' + this.color + '; background-color: #f74a45' + ';" />'
    }));

    this.line.bindPopup('' +
      '<header style="' +
      'background: ' + this.color + ' ;' +
      'color: #ffffff ;' +
      'padding: 0rem 3rem;">' +
      '<h3>' + this.name + '</h3>' +
      '</header>');

    this.startMarker.addTo(this.map);
    this.endMarker.addTo(this.map);

    this.addDecorator();
    this.update();

    if (!this.visible) {
      this.map.removeLayer(this.line);
      this.map.removeLayer(this.decorator);
      this.map.removeLayer(this.startMarker);
      this.map.removeLayer(this.endMarker);
    }

  };
  Track.prototype.fromJSON = function (json) {
    let track = JSON.parse(json);
    this.fromObj(track);
  };

  Track.prototype.push = function (feedback = false) {
    //  console.log(this.line.getLatLngs());
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('PATCH', base_url + 'editor/raid/' + raidID + '/track/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    if (feedback) {
      xhr_object.onreadystatechange = function () {
        if (xhr_object.readyState === 4 && xhr_object.status === 200) {
          iziToast.success({
            message: 'Le parcours a bien été sauvergardé.',
            position: 'bottomRight',
          });
        }else if (xhr_object.readyState === 4) {
          iziToast.error({
            message: 'Impossible d\'enregistrer le parcours. Vérifier votre connexion internet.',
            position: 'bottomRight',
          });
        }
      };

    }
    xhr_object.send(this.toJSON());


    //Encode html entities to display purpose only
    this.name = htmlentities.encode(this.name);

    let li = document.getElementById('track-li-' + this.id);
    this.calculDistance();
    this.calculElevation();
    li.querySelector('.track-distance').innerHTML = Math.round(10 * this.distance / 1000) / 10 + 'Km ';
    li.querySelector('.track-elev-gain').innerHTML = Math.round(this.posElev) + 'm';
    li.querySelector('.track-elev-lose').innerHTML = Math.round(this.negElev) + 'm';
    mapManager.editorUI.updateTrack(this);
    this.decorator.removeFrom(this.map);

  };
  Track.prototype.remove = function (feedback = false) {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('DELETE', base_url + 'editor/raid/' + raidID + '/track/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');

    if (feedback) {
      xhr_object.onreadystatechange = function () {
        if (xhr_object.readyState  === 4) {
          if (xhr_object.status === 200) {
            iziToast.success({
              message: 'Le parcours a bien été supprimé.',
              position: 'bottomRight',
            });
          } else {
            iziToast.error({
              message: 'Impossible de supprimer le parcours. Vérifier votre connexion internet.',
              position: 'bottomRight',
            });
          }
        }
        ;
      }
    }

    xhr_object.send(null);

    this.map.removeLayer(this.decorator);
    this.map.removeLayer(this.line);
    this.map.removeLayer(this.startMarker);
    this.map.removeLayer(this.endMarker);

    mapManager.editorUI.removeTrack(this);
    mapManager.tracksMap.delete(this.id);
  };
  Track.prototype.buildUI = function () {
    mapManager.editorUI.updateTrack(this)
  };

  console.log("Track JS loaded");
}
