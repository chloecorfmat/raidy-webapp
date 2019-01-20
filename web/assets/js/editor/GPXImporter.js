let GPXImporter = function (mapManager) {
  this.mapManager = mapManager;
  this.gpxParser = new gpxParser();
};

GPXImporter.prototype.openGPX = function (file) {
    let keepThis = this;

    let reader = new FileReader();
    reader.onload = function(event) {
        keepThis.gpxParser = new gpxParser();
        keepThis.gpxParser.parse(reader.result);
        console.log(keepThis.gpxParser);

        let tracks = keepThis.gpxParser['tracks'];
        let routes = keepThis.gpxParser['routes'];
        let waypoints = keepThis.gpxParser['waypoints'];

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

    let track = new Track(this.mapManager.map);
    track.setName(name);
    track.setSportType(parseInt(sportType));
    track.setColor("#000000");
    track.line.setLatLngs(latLngs);

    let xhr_object = new XMLHttpRequest();
    xhr_object.open('PUT', '/editor/raid/' + raidID + '/track', true);
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

GPXImporter.prototype.importWaypoint = function (id, poiType) {
    let gpxWaypoint = this.gpxParser.waypoints[id];
    let keepThis = this;
    let name = (gpxWaypoint.name != null && gpxWaypoint.name !== '') ? gpxWaypoint.name : ('POI #' + (parseInt(id)+1));

    let poi = new Poi();
    poi.name = name;
    poi.editable = false;
    poi.poiType = this.mapManager.poiTypesMap.get(parseInt(poiType));
    poi.marker = L.marker([gpxWaypoint.lat, gpxWaypoint.lon]);
    console.log(poi);

    let xhr_object = new XMLHttpRequest();
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

