/**
 * Point of Interest class
 * Manage all actions on POIs on the map
 */
let Poi;
if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  Poi = function(map) {
    this.map = map;
    this.marker = L.marker([0, 0]);
    this.id = '';
    this.name = '';
    this.poiType = null;
    this.requiredHelpers = 0;
    this.color = '#0f5e54';
    this.description = '';
    this.image = '';
    this.buildUI();

    this.marker.unbindPopup();
  }

  Poi.prototype.toJSON = function () {
    let poi =
      {
        id: this.id != null ? this.id : null,
        name: this.name,
        latitude: this.marker.getLatLng().lat,
        longitude: this.marker.getLatLng().lng,
        requiredHelpers: this.requiredHelpers,
        poiType: this.poiType.id,
        description: this.description,
        image: this.image
      };
    let json = JSON.stringify(poi);
    return json;
  };

  Poi.prototype.fromObj = function (poi) {
    let keepThis = this;
    this.id = poi.id;
    this.name = poi.name;
    this.poiType = mapManager.poiTypesMap.get(poi.poiType);
    this.requiredHelpers = poi.requiredHelpers;
    this.description = poi.description;
    this.image = poi.image;
    this.marker = L.marker([poi.latitude, poi.longitude]);

    this.marker.addTo(mapManager.group);

    this.marker.disableEdit();
    this.marker.on('dragend', function () {
      keepThis.name = htmlentities.decode(keepThis.name);
      keepThis.push();
    });
    keepThis.buildUI();
  };
  Poi.prototype.fromJSON = function (json) {
    let poi = JSON.parse(json);
    this.fromObj(poi);
  };
  Poi.prototype.setEditable = function (b) {
    b ? this.marker.enableEdit() : this.marker.disableEdit();
  };

  Poi.prototype.push = function () {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('PATCH', '/editor/raid/' + raidID + '/poi/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(this.toJSON());

      this.name = htmlentities.encode(this.name);

    mapManager.editorUI.updatePoi(this);
  };

  Poi.prototype.buildUI = function () {
    let keepThis = this;
    this.poiType != null && (this.color = this.poiType.color );

    this.marker.bindPopup('' +
      '<header style="' +
          'background: ' + this.color + ' ;' +
          'color: #ffffff ;' +
          'padding: 0rem 3rem;">' +
        '<h3>' + this.name + '</h3>' +
      '</header>' +
      '<div> ' +
        '<h4>Bénévoles</h4>' +
        '<p>' + this.requiredHelpers + ' Requis </p>' +
      '</div>');

    let icon = L.divIcon({
      className: 'my-custom-pin',
      iconAnchor: [0, 5],
      labelAnchor: [0, 0],
      popupAnchor: [0, -35],
      html: '<span class="poi-marker" style="background-color:' + this.color + ';" />'
    });

    this.marker.setIcon(icon);
  };

  Poi.prototype.remove = function () {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('DELETE', '/editor/raid/' + raidID + '/poi/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(null);

    this.map.removeLayer(this.marker);

    mapManager.editorUI.removePoi(this);
    mapManager.poiMap.delete(this.id);
  };

  console.log("Track POI loaded");
}