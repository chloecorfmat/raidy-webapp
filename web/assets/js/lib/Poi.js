var Poi = function (map) {
    this.map = map;

    this.id = 0;
    this.name = "";
    this.poiType = 0;
    this.helperCount = 0;

    this.color = "#000000";

}

Poi.prototype.toJSON = function(){
    var poi =
        {
            id : this.id !=null ? this.id : null,
            name : this.name,
            lat : this.marker.getLatLng().lat,
            lng : this.marker.getLatLng().lng,
            HelperCount : this.helperCount,
            POIType:  this.poiType,
            Color : this.color,
        }
    var json = JSON.stringify(poi);
    return json;
}
Poi.prototype.fromObj = function(poi){

    this.id = poi.id;
    this.color = poi.Color;
    this.name = poi.name;
    this.poiType = poi.POIType;
    this.helperCount = poi.HelperCount;

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

    this.marker = L.marker([poi.lat, poi.lng], {draggable: 'true', icon: icon});
    this.marker.dragging;

    this.marker.addTo(mapManager.map)
        .bindPopup('' +
            '<header style="' +
            'background: ' + color + ' ;' +
            'color: #ffffff ;' +
            'padding: 0rem 3rem;">' +
            '<h3>' + name + '</h3>' +
            '</header>' +
            '<div> ' +
            '<h4>Bénévoles</h4>' +
            '<p>Besoin de 3 bénévoles</p>' +
            '</div>')
        .openPopup();

    var li = document.createElement('li');
    li.classList.add("list--pois-items");
    li.innerHTML = name
        +`<button data-id = "`+this.id+`" class="btn--poi--settings">
           <i class="fas fa-cog"></i>
       </button>`;
    document.getElementById("list--pois").appendChild(li);
    li.pseudoStyle("before","background-color",this.color);

}
Poi.prototype.fromJSON = function(json){
    var track = JSON.parse(json);
    this.fromObj(track);
}
Poi.prototype.setEditable = function (b) {
     b ? this.marker.dragging.enable() : this.marker.dragging.disable();
}

