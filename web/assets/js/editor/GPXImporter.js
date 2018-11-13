var GPXImporter = function (mapManager) {
  this.mapManager = mapManager;
  this.gpxParser = new gpxParser();
};

GPXImporter.prototype.openGPX = function (file) {
    var keepThis = this;

    var reader = new FileReader();
    reader.onload = function(event) {
        keepThis.gpxParser = new gpxParser();
        keepThis.gpxParser.parse(reader.result);
        console.log(keepThis.gpxParser);

        var tracks = keepThis.gpxParser['tracks'];
        var routes = keepThis.gpxParser['routes'];
        var waypoints = keepThis.gpxParser['waypoints'];

        keepThis.mapManager.editorUI.cleanImportGPXPopin();
        keepThis.mapManager.editorUI.displayGPXMetadata(tracks, routes, waypoints);
    };

    reader.readAsText(file);
};


GPXImporter.prototype.importGPXTrack = function(id, sportType, gpxTrack){
    let keepThis = this;
    let name = (gpxTrack.name != null && gpxTrack.name !== '') ? gpxTrack.name : ('track #' + (parseInt(id)+1));

    let latLngs = [];
    for(let point of gpxTrack.points){
        latLngs.push([point.lat, point.lon]);
    }

    let track = new Track();
    track.setName(name);
    track.setSportType(sportType);
    track.setColor("#000000");
    track.line.setLatLngs(latLngs);

    var xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/organizer/raid/' + raidID + '/track', true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(track.toJSON());

    xhr_object.onreadystatechange = function (event) {
        // XMLHttpRequest.DONE === 4
        if (this.readyState === XMLHttpRequest.DONE) {
            if (xhr_object.status === 200) {
                track = JSON.parse(xhr_object.responseText);
                var tr = keepThis.mapManager.addTrack(track);
                tr.setEditable(false);
                tr.setEditable(false);
            }
        }
    }
}

GPXImporter.prototype.importTrack = function (id, sportType) {
    let gpxTrack = this.gpxParser.tracks[id];
    this.importGPXTrack(id, sportType, gpxTrack);
};

GPXImporter.prototype.importRoute = function (id, sportType) {
    let gpxTrack = this.gpxParser.routes[id];
    this.importGPXTrack(id, sportType, gpxTrack);
};

GPXImporter.prototype.importWaypoint = function (id, sportType) {
    console.log(this.gpxParser.waypoints[id]);
};

