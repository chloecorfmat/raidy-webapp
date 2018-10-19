var Track = function (map) {
    this.map = map;
    this.line = [];
   //s this.line.addTo(this.map);

    this.id = "";
    this.name = "";
    this.color = "";
    this.sportType = 1;

    this.visible = true;
    this.waypoints = [];

    this.line = L.polyline([]);


}

Track.prototype.setName = function(name){
    this.name = name;

    li = document.getElementById("track-li-"+this.id);
    li.querySelector("label > span:nth-child(3)").innerHTML = this.name;
}

Track.prototype.setColor = function(color){
    this.color = color;
    this.line.setStyle({
        color: color
    });

    li = document.getElementById("track-li-"+this.id);
    li.querySelector("label > span.checkmark").style.backgroundColor = this.color;
    li.querySelector("label > span.checkmark").style.borderColor = this.color;
}


Track.prototype.addPoint = function (lat, lng) {
    var keepThis = this;

    const markerHtmlStyles = `
          background-color: `+this.color +`;
          width: 0.75rem;
          height: 0.75rem;
          display: block;
          position: relative;
          border-radius: 3rem ;
          transform: translateY(-0.375rem) translateX(-0.375rem);
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
    this.push();
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

Track.prototype.findMarkerById = function (markers, leafletId) {
    var data = [];
    for (var marker in markers) {
        if (markers[marker]._leaflet_id == leafletId) {
            data['targetMarker'] = markers[marker];
            data['targetMarkerId'] = marker;
        }
    }
    return data;
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
    //var points = this.waypoints;
    /*for (var point in points) {
        b ? points[point].setOpacity(1) : points[point].setOpacity(0);
        b ? points[point].dragging.enable() : points[point].dragging.disable() ;
    }*/
    b ? this.line.enableEdit() : this.line.disableEdit();
}

Track.prototype.load = function(){
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("GET", "", false);
    xhr_object.send(null);

    if (xhr_object.readyState == 4) alert("Requête effectuée !");
}

Track.prototype.toJSON = function(){
    latlong =  [];
    for(obj of this.line.getLatLngs() ){
        latlong.push({lat : obj.lat, lng : obj.lng } );
    }
    var track =
    {
        id : this.id !=null ? this.id : null,
        name : this.name,
        color : this.color,
        sportType : this.sportType,
        isVisible:  this.visible,
        trackpoints :  this.line != null ? JSON.stringify(latlong) : null
    }
    var json = JSON.stringify(track)
    return json;
}

Track.prototype.fromObj = function(track){

    this.id = track.id;
    this.color = track.color;
    this.name = track.name;
    this.sportType = track.sportType;
    this.isVisible = track.isVisible;
    test = JSON.parse(track.trackpoints);


    this.line = L.polyline(test, {color: this.color}).addTo(this.map);


    this.line.enableEdit();
    /*for (point of test ) {
       // console.log(point)
        newTrack.addPoint(point.lat, point.lng);
    }*/
}
Track.prototype.fromJSON = function(json){
   var track = JSON.parse(json);
   this.fromObj(track);
}

Track.prototype.push = function(){
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("PATCH", "/organizer/raid/"+raidID+"/track/"+this.id, true);
    xhr_object.setRequestHeader("Content-Type","application/json");
    xhr_object.send(this.toJSON());
    //console.log("pushed: "+this.toJSON());
}

Track.prototype.remove = function(){
    var xhr_object = new XMLHttpRequest();
    xhr_object.open("DELETE", "/organizer/raid/"+raidID+"/track/"+this.id, true);
    xhr_object.setRequestHeader("Content-Type","application/json");
    xhr_object.send(null);

    this.map.removeLayer(this.line);

    li = document.getElementById("track-li-"+this.id);
    document.getElementById('editor--list').removeChild(li);
}

