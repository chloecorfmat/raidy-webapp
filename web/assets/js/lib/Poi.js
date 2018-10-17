var Poi = function (id, name, loc, color, map) {
    this.map = map;

    this.name = name;
    this.id = id;
    this.color = color;
    const markerHtmlStyles = `
  background-color: ${color};
  width: 2rem;
  height: 2rem;
  display: block;
  
  position: relative;
  border-radius: 3rem 3rem 0;
  transform: translateX(-1rem) translateY(-2rem) rotate(45deg);
  border: 0px solid #FFFFFF`;

    const icon = L.divIcon({
        className: "my-custom-pin",
        iconAnchor: [0, 5],
        labelAnchor: [0, 0],
        popupAnchor: [0, -35],
        html: `<span style="${markerHtmlStyles}" />`
    });

    this.marker = L.marker(loc, {draggable: 'true', icon: icon});
    this.marker.dragging;

    this.marker.addTo(mapManager.map)
        .bindPopup('' +
            '<header style="' +
            'background: ' + color + ' ;' +
            'color: #ffffff ;' +
            'padding: 0rem 3rem;">' +
            '<h2>' + name + '</h2>' +
            '</header>' +
            '<div>Besoin de 3 bénévoles</div>')
        .openPopup();

    var li = document.createElement('li');
    li.classList.add("list--pois-items");
    li.innerHTML = name;
    document.getElementById("list--pois").appendChild(li);

}


Poi.prototype.setEditable = function (b) {
     b ? this.marker.dragging.enable() : this.marker.dragging.disable();
}

