var Poi = function (map) {
    this.map = map;
    this.marker = L.marker([0, 0]);
    this.id = "";
    this.name = "";
    this.poiType = null;
    this.requiredHelpers = 0;

    this.color = "#000000";

}

Poi.prototype.toJSON = function(){
    var poi =
        {
            id : this.id !=null ? this.id : null,
            name : this.name,
            latitude : this.marker.getLatLng().lat,
            longitude : this.marker.getLatLng().lng,
            requiredHelpers : this.helperCount,
            poiType:  this.poiType.id,
        }
    var json = JSON.stringify(poi);
    return json;
}
Poi.prototype.fromObj = function(poi){

    this.id = poi.id;
    this.name = poi.name;
    this.poiType = mapManager.poiTypesMap.get(poi.poiType);
    this.color = poiType.color;
    this.requiredHelpers = poi.requiredHelpers;

    const markerHtmlStyles = `
  background-color: `+this.color +`;
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

    console.log(poi);
    this.marker = L.marker([poi.longitude, poi.latitude], {icon: icon});
    this.marker.addTo(this.map)
        .bindPopup('' +
            '<header style="' +
            'background: ' + this.color + ' ;' +
            'color: #ffffff ;' +
            'padding: 0rem 3rem;">' +
            '<h3>' + this.name + '</h3>' +
            '</header>' +
            '<div> ' +
            '<h4>Bénévoles</h4>' +
            '<p>Besoin de 3 bénévoles</p>' +
            '</div>');


    this.marker.disableEdit();
    var li = document.createElement('li');
    li.classList.add("list--pois-items");
    li.innerHTML = this.name
        +`<button data-id = "`+this.id+`" class="btn--poi--settings">
           <i class="fas fa-cog"></i>
       </button>`;
    document.getElementById("list--pois").appendChild(li);
    li.pseudoStyle("before","background-color",this.color);

}
Poi.prototype.fromJSON = function(json){
    var poi = JSON.parse(json);
    this.fromObj(poi);
}
Poi.prototype.setEditable = function (b) {
    b ? this.marker.enableEdit() : this.marker.disableEdit();
}

