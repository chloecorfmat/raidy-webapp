var GpxFactory = function(waypoints, lineBounds){
    this.document = document.implementation.createDocument(null, null);

    var root = this.buildRoot();
    root.appendChild(this.buildBounds(lineBounds));
    root.appendChild(this.buildTrack(waypoints));

    document.getElementById('gpxOutput').style.display = "inline-block";
    document.getElementById('gpxOutput').innerHTML = new XMLSerializer().serializeToString(this.document);


};

GpxFactory.prototype.buildRoot = function() {
    var root = this.document.createElement('gpx');
    root.setAttribute("version","1.0");
    root.setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
    root.setAttribute("xmlns","http://www.topografix.com/GPX/1/0");
    root.setAttribute("xsi:schemaLocation","http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd");
    this.document.appendChild(root);

    return root;
};

GpxFactory.prototype.buildBounds = function(lineBounds){
    var bounds = this.document.createElement('bounds', lineBounds);
    bounds.setAttribute('minlat', lineBounds.getSouthWest().lat);
    bounds.setAttribute('minlon', lineBounds.getSouthWest().lng);
    bounds.setAttribute('maxlat', lineBounds.getNorthEast().lat);
    bounds.setAttribute('maxlon', lineBounds.getNorthEast().lng);

    return bounds;
};

GpxFactory.prototype.buildTrack = function(points) {
    var trk = this.document.createElement('trk');
    var trkseg = this.document.createElement('trkseg');

    for (var point in points) {
        var trkpt = this.document.createElement('trkpt');

        trkpt.setAttribute('lat',points[point].getLatLng().lat);
        trkpt.setAttribute('lon',points[point].getLatLng().lng);

        trkseg.appendChild(trkpt);
    }

    trk.appendChild(trkseg);
    return trk;
};
