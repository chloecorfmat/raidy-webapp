/**
 * Point of Interest class
 * Manage all actions on POIs on the map
 */
let Poi;
if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  Poi = function(map) {
    this.map = map;
    this.marker = L.marker([0, 0]);
    this.id = '';
    this.name = '';
    this.poiType = null;
    this.isCheckpoint = false;
    this.requiredHelpers = 0;
    this.color = '#0f5e54';
    this.description = '';
    this.image = '';
    this.helpers = [];
    this.buildUI();

    this.marker.unbindPopup();
  }

  Poi.prototype.toJSON = function () {
    let poi =
      {
        id: this.id != null ? this.id : null,
        name: this.name,
        latitude: this.marker.getLatLng().lat,
        longitude: this.marker.getLatLng().lng,
        requiredHelpers: this.requiredHelpers,
        poiType: this.poiType.id,
        description: this.description,
        image: this.image,
        isCheckpoint: this.isCheckpoint ? true : false
      };
    let json = JSON.stringify(poi);
    return json;
  };

  Poi.prototype.fromObj = function (poi) {
    let keepThis = this;
    this.id = poi.id;
    this.name = poi.name;
    this.poiType = mapManager.poiTypesMap.get(poi.poiType);
    this.requiredHelpers = poi.requiredHelpers;
    if(this.helpers){
      this.helpers = poi.helpers;
    }else{
      this.helpers = [];
    }
    //console.log(this.helpers);
    this.description = poi.description;
    this.image = poi.image;
    this.isCheckpoint = false;
    this.isCheckpoint = poi.isCheckpoint;

    this.marker = L.marker([poi.latitude, poi.longitude]);
    this.marker.addTo(mapManager.group);
    this.marker.disableEdit();
    this.marker.on('dragend', function () {
      keepThis.name = htmlentities.decode(keepThis.name);
      keepThis.push();
    });
    keepThis.buildUI();
  };
  Poi.prototype.fromJSON = function (json) {
    let poi = JSON.parse(json);
    this.fromObj(poi);
  };
  Poi.prototype.setEditable = function (b) {
    b ? this.marker.enableEdit() : this.marker.disableEdit();

  };

  Poi.prototype.push = function () {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('PATCH', '/editor/raid/' + raidID + '/poi/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(this.toJSON());

      this.name = htmlentities.encode(this.name);

    mapManager.editorUI.updatePoi(this);
  };

  Poi.prototype.buildUI = function () {
    let keepThis = this;
    this.poiType != null && (this.color = this.poiType.color );
    let shortDesc = this.description;
    if(this.description.length >= 200){
      shortDesc = this.description.substring(0, 200)+'...';
    }
    let checkpointIcon = this.isCheckpoint ? ' <i class="fas fa-flag"></i>' : '';
    let checkpointClass = this.isCheckpoint ? ' poi-checkpoint' : '';


    let icon = L.divIcon({
      className: 'my-custom-pin',
      iconAnchor: [0, 5],
      labelAnchor: [0, 0],
      popupAnchor: [0, -35],
      html: '<span class="poi-marker"' + checkpointClass + ' style="background-color:' + this.color + ';" />'
    });


    if(mapManager.isEditor){
      this.marker.bindPopup('' +
        '<header style="' +
        'background: ' + this.color + ' ;' +
        'color: #ffffff ;' +
        'padding: 0rem 3rem;">' +
        '<h3>' + this.name + checkpointIcon +'</h3>' +
        '</header>' +
        '<div> ' +
        '<p style="padding: 0.5rem; text-align: left;">'+shortDesc+'</p>'+
        '<h4>' + this.helpers.length+'/'+this.requiredHelpers + ' bénévoles requis.</h4>'+
        '<button style=" background-color: '+this.color+';" class="poi-action-btn" id="poi-edit-button-'+keepThis.id+'"> <i class="fas fa-cog"></i> </button>' +
        '<button style=" background-color: '+this.color+';"class="poi-action-btn" id="poi-info-button-'+keepThis.id+'"> <i class="fas three-points">...</i> </button>' +
        '</div>'
      );
    }else{
      this.marker.bindPopup('' +
        '<header style="' +
        'background: ' + this.color + ' ;' +
        'color: #ffffff ;' +
        'padding: 0rem 3rem;">' +
        '<h3>' + this.name + checkpointIcon +'</h3>' +
        '</header>' +
        '<div> ' +
        '<p style="padding: 0.5rem; text-align: left;">'+shortDesc+'</p>'+
        '<h4>' + this.helpers.length+'/'+this.requiredHelpers + '  bénévoles requis.</h4>'+
        '<button style=" background-color: '+this.color+';" class="poi-action-btn" id="poi-info-button-'+keepThis.id+'"> <i class="fas three-points">...</i> </button>' +
        '</div>'
      );
    }

    this.marker.setIcon(icon);

    let buttonInfo = document.getElementById("poi-info-button-"+keepThis.id);
    let buttonEdit = document.getElementById("poi-edit-button-"+keepThis.id);

    if(buttonInfo != null) {
      buttonInfo.addEventListener("click", function() { keepThis.fillInfoPopin(); });
    }
    if(buttonEdit != null){
      buttonEdit.addEventListener("click", function() { keepThis.fillEditionPopin(); });
    }

  //  this.marker.openPopup();
    this.marker.on('popupopen', function () {
      let buttonInfo = document.getElementById("poi-info-button-"+keepThis.id);
      let buttonEdit = document.getElementById("poi-edit-button-"+keepThis.id);

      if(buttonInfo != null) {
        buttonInfo.addEventListener("click", function() { keepThis.fillInfoPopin(); });
      }
      if(buttonEdit != null){
        buttonEdit.addEventListener("click", function() { keepThis.fillEditionPopin(); });
      }
    });

   // this.marker.closePopup();
  };

  Poi.prototype.remove = function () {
    let xhr_object = new XMLHttpRequest();
    xhr_object.open('DELETE', '/editor/raid/' + raidID + '/poi/' + this.id, true);
    xhr_object.setRequestHeader('Content-Type', 'application/json');
    xhr_object.send(null);

    this.map.removeLayer(this.marker);

    mapManager.editorUI.removePoi(this);
    mapManager.poiMap.delete(this.id);
  };


  Poi.prototype.fillEditionPopin = function () {
    let preview = document.getElementById('editPoi_preview');
    document.getElementById('editPoi_id').value = this.id;
    document.getElementById('editPoi_name').value = htmlentities.decode(this.name);
    document.getElementById('editPoi_nbhelper').value = this.requiredHelpers;
    document.getElementById('editPoi_isCheckpoint').checked = this.isCheckpoint;
    preview.src = this.image;
    if (this.image !== '') {
      preview.className = 'form--item-file-preview';
    }
    (this.poiType!= null ) && (document.querySelector("#editPoi_type option[value='" + this.poiType.id + "']").selected = 'selected');
    document.getElementById('editPoi_description').value = this.description;

    MicroModal.show('edit-poi-popin');
  }


  Poi.prototype.fillInfoPopin = function () {
    let checkpointIcon = this.isCheckpoint ? ' <i class="fas fa-flag"></i>' : '';
    let preview = document.getElementById('editPoi_preview');
    document.getElementById('poi-info-close-btn').style.backgroundColor = this.color;
    document.getElementById('poi-info-header').style.backgroundColor = this.color;
    document.getElementById('poi-info-content').style.borderColor = this.color;
    document.getElementById('poi-info-title').innerText = htmlentities.decode(this.name);
    document.getElementById('poi-info-description').innerText= this.description;

    document.getElementById('poi-info-helpers').innerText=  this.helpers.length+'/'+this.requiredHelpers+" bénévoles requis";
    let helpersTable = document.getElementById('poi-info-helpers-table');

    while (helpersTable.firstChild) {
      helpersTable.removeChild(helpersTable.firstChild);
    }
    if(this.helpers.length > 0){
      let node = document.createElement("TR");
      node.innerHTML = '<th>Prénom</th><th>Nom</th><th>Tél</th>';
      helpersTable.appendChild(node);
      for(let helper of this.helpers){
        console.log(this.helpers);
        node = document.createElement("TR");
        node.innerHTML = '<td>'+helper.firstname+'</td> <td>'+helper.lastname+'</td> <td>'+helper.phone+'</td>';
        helpersTable.appendChild(node);
      }
    }

    var old_element = document.getElementById('poi-info-edit-btn');
    var new_element = old_element.cloneNode(true);
    old_element.parentNode.replaceChild(new_element, old_element);
    let keepThis = this;
    document.getElementById('poi-info-edit-btn').addEventListener("click", function(){
      MicroModal.close('poi-info');
      keepThis.fillEditionPopin();
    });
    //document.getElementById('editPoi_isCheckpoint').checked = this.isCheckpoint;
    document.getElementById('poi-info-img').src = this.image;
    MicroModal.show('poi-info');
  }

  console.log("Track POI loaded");
}