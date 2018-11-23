let GPXExporter = function (mapManager) {
    this.mapManager = mapManager;
};

GPXExporter.prototype.buildGPXBase = function(){

    let gpx = document.createElement("gpx");
    gpx.setAttribute("version", "1.1");
    gpx.setAttribute("creator", "Raidy");


    if(mapManager.tracksMap.size > 0){
        let min = mapManager.group.getBounds().getNorthWest();
        let max = mapManager.group.getBounds().getSouthEast();

        let bounds = document.createElement('bounds');
        bounds.setAttribute("minlat", min.lat);
        bounds.setAttribute("minlon", min.lng);
        bounds.setAttribute("maxlat", max.lat);
        bounds.setAttribute("maxlon", max.lng);

        gpx.appendChild(bounds);
    }

    mapManager.poiMap.forEach(function (point) {
        let wpt = document.createElement('wpt');
        let latLng = point.marker.getLatLng();

        let name = document.createElement('name');
        name.innerHTML = point.name;
        wpt.appendChild(name);

        let desc = document.createElement('desc');
        desc.innerHTML = point.poiType.type;
        wpt.appendChild(desc);

        wpt.setAttribute('lat', latLng.lat);
        wpt.setAttribute('lon', latLng.lng);
        gpx.appendChild(wpt);
    })

    return gpx;
};

GPXExporter.prototype.exportAsTracks = function () {
    let gpx = this.buildGPXBase();
    this.mapManager.tracksMap.forEach(function (track) {
       let trk = document.createElement('trk');
       let name = document.createElement('name');
       name.innerHTML = track.name;
       trk.appendChild(name);

       let trkseg = document.createElement('trkseg');
       track.line.getLatLngs().forEach(function (point) {
          let trkpt = document.createElement('trkpt');
          trkpt.setAttribute("lat", point.lat);
          trkpt.setAttribute("lon", point.lng);
          trkseg.appendChild(trkpt);
       });

       trk.appendChild(trkseg);
       gpx.appendChild(trk);
    });

    let blob = new Blob([gpx.outerHTML], {
        type: "text/plain;charset=utf-8"
    });

    this.download(gpx, "raid-" + raidID + "-tracks.gpx");
};

GPXExporter.prototype.exportAsRoutes = function () {
    let gpx = this.buildGPXBase();
    this.mapManager.tracksMap.forEach(function (track) {
        let rte = document.createElement('rte');
        let name = document.createElement('name');
        name.innerHTML = track.name;
        rte.appendChild(name);

        track.line.getLatLngs().forEach(function (point) {
            let rtept = document.createElement('rtept');
            rtept.setAttribute("lat", point.lat);
            rtept.setAttribute("lon", point.lng);
            rte.appendChild(rtept);
        });
        gpx.appendChild(rte);
    });

    this.download(gpx, "raid-" + raidID + "-routes.gpx");
};

GPXExporter.prototype.download = function (gpx, filename) {
    let xmlHeader = '<?xml version="1.0" encoding="UTF-8" ?>\n';
    let content = xmlHeader + gpx.outerHTML;
    let blob = new Blob([content], {
        type: "text/plain;charset=utf-8"
    });

    saveAs(blob, filename);
};