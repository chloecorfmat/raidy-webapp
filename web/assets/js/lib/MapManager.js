var MapManager = function() {
    this.map       = L.map('map').setView([48.758872948417604, 1.9461679458618164], 15);
    this.line      = L.polyline([]).addTo(this.map);
    this.waypoints = [];
    this.distance  = 0;

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
       attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
   }).addTo(this.map);


};
MapManager.prototype.initialize = function() {

  /*  MapManager.greenIcon = L.icon({
        iconUrl: mapManager.markerIconUrl,
        //  shadowUrl: 'leaf-shadow.png',
        iconSize:     [38, 95], // size of the icon
        shadowSize:   [50, 64], // size of the shadow
        iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
        shadowAnchor: [4, 62],  // the same for the shadow
        popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
    });*/
}

MapManager.prototype.updateData = function() {
   // document.getElementById('distance').value     = this.formatDistance(this.distance);
   // document.getElementById('markersCount').value = this.waypoints.length;
};
MapManager.prototype.addPoi = function(name, loc, color) {

    const markerHtmlStyles = `
  background-color: ${color};
  width: 2rem;
  height: 2rem;
  display: block;
  left: -1.5rem;
  top: -1.5rem;
  position: relative;
  border-radius: 3rem 3rem 0;
  transform: rotate(45deg);
  border: 1px solid #FFFFFF`;

    const icon = L.divIcon({
        className: "my-custom-pin",
        iconAnchor: [0, 24],
        labelAnchor: [-6, 0],
        popupAnchor: [0, -36],
        html: `<span style="${markerHtmlStyles}" />`
    });

    L.marker(loc, {icon: icon}).addTo(mapManager.map)
        .bindPopup('<div style="bakcgroud-color: '+color+'"> <h1>'+name+'</h1></div>')
        .openPopup();
}

MapManager.prototype.addWaypoint = function(e) {
    var keepThis = this;
    var marker = L.marker([e.latlng.lat, e.latlng.lng], {draggable:'true'}).addTo(this.map);
    marker.on('drag', function(){
        keepThis.map.dragging.disable();
        keepThis.reDrawLine();
    });
    marker.on('dragend', function(){
        keepThis.map.dragging.enable();

    });

    marker.on('dblclick', function(){
        keepThis.removeMarker(this);
    });

    marker.on('click', function(){
        event.stopPropagation();
    });
    this.line.addLatLng(marker.getLatLng());
    this.waypoints.push(marker);
    this.calculDistance();
};

MapManager.prototype.removeMarker = function(marker) {
    var leafletId = marker._leaflet_id;
    var markers   = this.waypoints;
    var marker    = this.findMarkerById(markers, leafletId);

    this.waypoints.splice(marker['targetMarkerId'], 1);
    this.map.removeLayer(marker['targetMarker']);

    this.reDrawLine();
};

MapManager.prototype.findMarkerById = function(markers, leafletId) {
    var data = [];
    for (var marker in markers) {
        if(markers[marker]._leaflet_id == leafletId){
            data['targetMarker']   = markers[marker];
            data['targetMarkerId'] = marker;
        }
    }
    return data;
};

MapManager.prototype.reDrawLine = function() {
    this.line.setLatLngs([]);
    var points = this.waypoints;
    for (var point in points) {
        this.line.addLatLng(points[point].getLatLng());
    }

    this.calculDistance();
};

MapManager.prototype.calculDistance = function() {
    var points = this.line.getLatLngs();
    this.distance = 0;
    if(points.length > 1) {
        for (i=0;i<points.length-1;i++) {

            this.distance += points[i].distanceTo(points[i+1]);
            this.waypoints[i+1].bindPopup(this.formatDistance(this.distance)).openPopup();
        }
    }
    this.updateData();
};

MapManager.prototype.formatDistance = function(distance) {
    return Math.round(10*distance/1000)/10+" Km";
}

MapManager.prototype.clearAll = function() {
    var markers = this.waypoints;
    for(marker in markers) {
        this.map.removeLayer(markers[marker]);
    }

    this.waypoints = [];
    this.distance  = 0;
    this.map.removeLayer(this.line);
    this.line      = L.polyline([]).addTo(this.map);

    this.updateData();
}
