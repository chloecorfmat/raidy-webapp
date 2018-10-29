if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  function Poi(map) {
    this.map = map
    this.marker = L.marker([0, 0])
    this.id = ''
    this.name = ''
    this.poiType = null
    this.requiredHelpers = 0

    this.color = '#0f5e54'
    this.buildUI()
  }

  Poi.prototype.toJSON = function () {
    let poi =
      {
        id: this.id != null ? this.id : null,
        name: this.name,
        latitude: this.marker.getLatLng().lat,
        longitude: this.marker.getLatLng().lng,
        requiredHelpers: this.requiredHelpers,
        poiType: this.poiType.id
      }
    let json = JSON.stringify(poi)
    return json
  }
  Poi.prototype.fromObj = function (poi) {
    let keepThis = this

    // console.log(poi);
    this.id = poi.id
    this.name = poi.name
    this.poiType = mapManager.poiTypesMap.get(poi.poiType);
    (poiType != null) && (this.color = this.poiType.color)
    this.requiredHelpers = poi.requiredHelpers
   // console.log(this.requiredHelpers)
    this.marker = L.marker([poi.latitude, poi.longitude])

    this.marker.addTo(mapManager.group)

    this.marker.disableEdit()
    this.marker.on('dragend', function (e) {
      keepThis.push()
    })
    keepThis.buildUI()
  }
  Poi.prototype.fromJSON = function (json) {
    let poi = JSON.parse(json)
    this.fromObj(poi)
  }
  Poi.prototype.setEditable = function (b) {
    b ? this.marker.enableEdit() : this.marker.disableEdit()
  }

  Poi.prototype.push = function () {
    let xhr_object = new XMLHttpRequest()
    xhr_object.open('PATCH', '/organizer/raid/' + raidID + '/poi/' + this.id, true)
    xhr_object.setRequestHeader('Content-Type', 'application/json')
    xhr_object.send(this.toJSON())
  //  console.log(this.toJSON());
    this.buildUI()
  }

  Poi.prototype.buildUI = function () {

    let keepThis = this
    poiType != null || (this.color = this.poiType.color )
    const markerHtmlStyles = `
  background-color: ` + this.color + `;
  width: 2rem;
  height: 2rem;
  display: block;
  
  position: relative;
  border-radius: 3rem 3rem 0;
  transform: translateX(-1rem) translateY(-2rem) rotate(45deg);`

    this.marker.bindPopup('' +
      '<header style="' +
      'background: ' + this.color + ' ;' +
      'color: #ffffff ;' +
      'padding: 0rem 3rem;">' +
      '<h3>' + this.name + '</h3>' +
      '</header>' +
      '<div> ' +
      '<h4>Bénévoles</h4>' +
      '<p>' + this.requiredHelpers + ' Requis </p>' +
      '</div>')
    let icon = L.divIcon({
      className: 'my-custom-pin',
      iconAnchor: [0, 5],
      labelAnchor: [0, 0],
      popupAnchor: [0, -35],
      html: `<span style="${markerHtmlStyles}" />`
    })

    this.marker.setIcon(icon)
  }

  Poi.prototype.remove = function () {
    let xhr_object = new XMLHttpRequest()
    xhr_object.open('DELETE', '/organizer/raid/' + raidID + '/poi/' + this.id, true)
    xhr_object.setRequestHeader('Content-Type', 'application/json')
    xhr_object.send(null)

    this.map.removeLayer(this.marker)

    mapManager.editorUI.removePoi(this)
  }
}