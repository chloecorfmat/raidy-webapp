var Track = function (map) {
    this.map = map;
    this.line = null;
   //s this.line.addTo(this.map);

    this.id = "";
    this.name = "";
    this.color = "";
    this.sportType = 0;

    this.waypoints = [];

}
Track.prototype.addPoint = function (lat, lng) {
    var keepThis = this;

    const markerHtmlStyles = `
          background-color: `+this.color +`;
          width: 1rem;
          height: 1rem;
          display: block;
          position: relative;
          border-radius: 3rem ;
          transform: translateY(-0.5rem) translateX(-0.5rem);
          border: 0px solid #FFFFFF`;

    const icon = L.divIcon({
        className: "my-custom-pin",
        iconAnchor: [0, 0],
        labelAnchor: [-5, 0],
        popupAnchor: [-8, -46],
        html: `<span style="${markerHtmlStyles}" />`
    });

    var marker = L.marker([lat, lng], {draggable: 'true', icon: icon}).addTo(this.map);

    marker.on('drag', function () {
        keepThis.map.dragging.disable();
        keepThis.reDrawLine();
    });
    marker.on('dragend', function () {
        keepThis.map.dragging.enable();
        keepThis.push();
    });

    marker.on('dblclick', function () {
        keepThis.removeMarker(this);
    });

    marker.on('click', function () {
        event.stopPropagation();
    });
    this.line.addLatLng(marker.getLatLng());
    this.waypoints.push(marker);
    // this.calculDistance();
};


Track.prototype.reDrawLine = function () {
    this.line.setLatLngs([]);
    var points = this.waypoints;
    for (var point in points) {
        this.line.addLatLng(points[point].getLatLng());
    }

    // this.calculDistance();
};

Track.prototype.calculDistance = function () {
    var points = this.line.getLatLngs();
    this.distance = 0;
    if (points.length > 1) {
        for (i = 0; i < points.length - 1; i++) {

            this.distance += points[i].distanceTo(points[i + 1]);
            this.waypoints[i + 1].bindPopup(this.formatDistance(this.distance)).openPopup();
        }
    }
    this.updateData();
};


Track.prototype.removeMarker = function (marker) {
    var leafletId = marker._leaflet_id;
    var markers = this.waypoints;
    var marker = this.findMarkerById(markers, leafletId);

    this.waypoints.splice(marker['targetMarkerId'], 1);
    this.map.removeLayer(marker['targetMarker']);

    this.reDrawLine();
};
Track.prototype.hide = function(){
    var points = this.waypoints;
    for (var point in points) {
        this.map.removeLayer(points[point]);
    }
    this.map.removeLayer(this.line);
    this.visible = false;
}

Track.prototype.show = function(){
    var points = this.waypoints;
    for (var point in points) {
        this.map.addLayer(points[point]);
    }
    this.map.addLayer(this.line);
    this.visible = true;
}

Track.prototype.setEditable = function(b){
    var points = this.waypoints;
    for (var point in points) {
        b ? points[point].setOpacity(1) : points[point].setOpacity(0);
        b ? points[point].dragging.enable() : points[point].dragging.disable() ;
    }
}

Track.prototype.load = function(){
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("GET", "", false);
    xhr_object.send(null);

    if (xhr_object.readyState == 4) alert("Requête effectuée !");
}

Track.prototype.toJSON = function(){
    var track =
    {
        id : this.id !=null ? this.id : null,
        name : this.name,
        color : this.color,
        sportType : this.sportType,
        trackpoints :  this.line != null ? this.line.getLatLngs() : null,
        isVisible:  this.visible
    }
    var json = JSON.stringify(track)
    return json;
}

Track.prototype.fromObj = function(track){
   // console.log(this);
    console.log(track);
    this.id = track.id;
    this.color = track.color;
    this.name = track.name;
    this.sportType = track.sportType;
    this.isVisible = track.isVisible;
    this.line = L.polyline([], {color: this.color}).addTo(this.map);
    test = JSON.parse(track.trackpoints);
    for (point of test ) {
        console.log(point)
        newTrack.addPoint(point.lat, point.lng);
    }
}
Track.prototype.fromJSON = function(json){
   var track = JSON.parse(json);
   this.fromObj(track);
}

Track.prototype.push = function(){
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("PATCH", "/organizer/raid/"+raidID+"/track/"+this.id, false);
    xhr_object.send(this.toJSON());
    console.log("pushed: "+this.toJSON());
}
