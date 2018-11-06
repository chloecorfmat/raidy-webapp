if (typeof(document.getElementById("editorContainer")) !== "undefined" && document.getElementById("editorContainer") !== null) {
  var UID = {
    _current: 0,
    getNew: function () {
      this._current++;
      return this._current;
    }
  };

  function convertRem(value) {
    return value * getRootElementFontSize();
  }
  function getRootElementFontSize() {
    // Returns a number
    return parseFloat(
      // of the computed font-size, so in px
      getComputedStyle(
        // for the root <html> element
        document.documentElement
      ).fontSize
    );
  }
  HTMLElement.prototype.pseudoStyle = function (element, prop, value) {
    var _this = this;
    var _sheetId = 'pseudoStyles';
    var _head = document.head || document.getElementsByTagName('head')[0];
    var _sheet = document.getElementById(_sheetId) || document.createElement('style');
    _sheet.id = _sheetId;
    var className = 'pseudoStyle' + UID.getNew();

    _this.className += ' ' + className;

    _sheet.innerHTML += ' .' + className + ':' + element + '{' + prop + ':' + value + '}';
    _head.appendChild(_sheet);
    return this
  };

  var editor = {activeTab: 'tracks-pan'};

/* SCROLL MANAGEMENT */
  function preventDefault(e) {
    e = e || window.event;
    if (e.preventDefault)
      e.preventDefault();
    e.returnValue = false;
  }

  function preventDefaultForScrollKeys(e) {
    // left: 37, up: 38, right: 39, down: 40,
    // spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
    if ({37: 1, 38: 1, 39: 1, 40: 1}[e.keyCode]) {
      preventDefault(e);
      return false;
    }
  }

  function disableScroll() {
    if (window.addEventListener) // older FF
      window.addEventListener('DOMMouseScroll', preventDefault, false);
    window.onwheel = preventDefault; // modern standard
    window.onmousewheel = document.onmousewheel = preventDefault; // older browsers, IE
    window.ontouchmove  = preventDefault; // mobile
    document.onkeydown  = preventDefaultForScrollKeys;
  }

  function enableScroll() {
    if (window.removeEventListener)
      window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.onmousewheel = document.onmousewheel = null;
    window.onwheel = null;
    window.ontouchmove = null;
    document.onkeydown = null;
  }
  function disableDropdown(event) {
    if (!event.target.matches('.dropbtn')) {
      enableScroll();
      var dropdowns = document.getElementsByClassName("dropdown-content");
      for (var i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
          openDropdown.classList.remove('show');
        }
      }
    }
  }
  // Close the dropdown menu if the user clicks outside of it
  window.onclick = disableDropdown;

  document.getElementById('btn--laterale-bar').addEventListener('click', function () {
    var tab = this.parentElement.parentElement;
    tab.classList.toggle('bar--invisible');
    mapManager.map.invalidateSize()
  });

  window.addEventListener('load', function () {
    // LOAD EDITOR SPECIFIC CONTROLLER ON MAP
    L.POIEditControl = L.Control.extend({
      options: {
        position: 'topright'
      },
      initialize: function (options) {
        L.Util.setOptions(this, options);
        // Continue initializing the control plugin here.
      },
      onAdd: function (map) {
        var controlElementTag = 'div';
        var controlElementClass = 'my-leaflet-control';
        var controlElement = L.DomUtil.create(controlElementTag, controlElementClass);
        controlElement.innerHTML =
          '<div class="map-controller-container" >' +
          '<span class="switch-label">Édition des points d\'intérêt</span>' +
          '<label class="switch">' +
          '<input type="checkbox">' +
          '<span class="slider round"></span>' +
          '</label>' +
          '</div>';
        // Continue implementing the control here.
        controlElement.querySelector("input[type='checkbox']").addEventListener('change',function(){
            mapManager.setPoiEditable(this.checked);
        });
        return controlElement;
      }

    });


    //LOAD EDITOR SPECIFIC CONTROLLER ON MAP
    L.TrackEditControl = L.Control.extend({
      options: {
        position: 'topright'
      },
      initialize: function (options) {
        L.Util.setOptions(this, options);
        // Continue initializing the control plugin here.
      },
      onAdd: function () {
        var controlElementTag = 'div';
        var controlElementClass = 'my-leaflet-control';
        var controlElement = L.DomUtil.create(controlElementTag, controlElementClass);
        controlElement.innerHTML =
          '<div class="map-controller-container" >' +
          '<span class="switch-label">Édition du parcours</span>' +
          '<button class="btn-leave-track-edit">' +
          'X'+
          '</button>' +
          '</div>';
        // Continue implementing the control here.
        controlElement.querySelector(".btn-leave-track-edit").addEventListener('click',function(e){
          e.preventDefault();
          e.stopImmediatePropagation();
          track = mapManager.tracksMap.get(mapManager.currentEditID);
          if (track.line.editor.drawing() ) {
            track.line.editor.pop();
          }
          mapManager.switchMode(EditorMode.READING);
          this.checked = true;
          mapManager.displayTrackButton(false);
        });
        return controlElement;
      }
    });

    mapManager.trackControl = new L.TrackEditControl();
    MapManager.prototype.displayTrackButton = function (b) {
      b ? mapManager.map.addControl(mapManager.trackControl) :  mapManager.map.removeControl(mapManager.trackControl);
    }
    mapManager.map.addControl(new L.POIEditControl());

    var acc = document.getElementsByClassName("accordion");

    for (var i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight){
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        }
      });
      acc[i].nextElementSibling.style.maxHeight = acc[i].nextElementSibling.scrollHeight +"px";
    }
   //document.getElementById("tracks-pan").style.maxHeight = "3rem";
  //  document.getElementById("pois-pan").style.maxHeight = 0;
  });

  document.getElementById('addPoiButton').addEventListener('click', function () {
    this.classList.toggle('add--poi');
    if (this.classList.contains('add--poi')) {
      mapManager.switchMode(EditorMode.ADD_POI);
    } else {
      if (mapManager.waitingPoi !=null ){
        mapManager.map.removeEventListener("mousemove");
        mapManager.map.removeLayer(mapManager.waitingPoi.marker);
      }
      mapManager.switchMode(mapManager.lastMode);
    }
  });

  document.getElementById('addTrackButton').addEventListener('click', function () {
    MicroModal.show('add-track-popin');
  });

  document.getElementById('addTrack_form').addEventListener('submit', function (e) {
    e.preventDefault();
    var trName = document.getElementById('addTrack_name').value;
    var trColor = document.getElementById('addTrack_color').value;
    mapManager.requestNewTrack(trName, trColor);
    MicroModal.close('add-track-popin');

    document.getElementById('addTrack_name').value = '';
    document.getElementById('addTrack_color').value = '#000000'
  });

  document.getElementById('editTrack_form').addEventListener('submit', function (e) {
    e.preventDefault();
    var trName = document.getElementById('editTrack_name').value;
    var trColor = document.getElementById('editTrack_color').value;
    var trId = document.getElementById('editTrack_id').value;

    var track = mapManager.tracksMap.get(parseInt(trId));

    track.setName(trName);
    track.setColor(trColor);

    track.push();
    MicroModal.close('edit-track-popin');
  });

  document.getElementById('editTrack_delete').addEventListener('click', function () {
    MicroModal.close('edit-track-popin');
  });

  document.getElementById('btn--delete-track').addEventListener('click', function () {
    var trId = parseInt(this.dataset.id);
    var track = mapManager.tracksMap.get(parseInt(trId));
    track.remove();
    MicroModal.close('delete-track');
  });

  document.getElementById('btn--delete-poi').addEventListener('click', function () {
    var poiId = this.dataset.id;
    var poi = mapManager.poiMap.get(parseInt(poiId));
    poi.remove();
    MicroModal.close('delete-poi');
  });

// ADD POI SUBMIT
  document.getElementById('addPoi_form').addEventListener('submit', function (e) {
    e.preventDefault();
    var poiName = document.getElementById('addPoi_name').value;
    var poiType = document.getElementById('addPoi_type').value;
    var poiHelpersCount = document.getElementById('addPoi_nbhelper').value;

    MicroModal.close('add-poi-popin');
    mapManager.requestNewPoi(poiName, poiType, poiHelpersCount);

    document.getElementById('addPoi_name').value = '';
    document.getElementById('addPoi_type').value = '';
    document.getElementById('addPoi_nbhelper').value = '';
  });

// EDIT POI SUBMIT
  document.getElementById('editPoi_form').addEventListener('submit', function (e) {
    e.preventDefault();
    var poiId = document.getElementById('editPoi_id').value;
    var poi = mapManager.poiMap.get(parseInt(poiId));

    poi.name = document.getElementById('editPoi_name').value;
    poi.poiType = mapManager.poiTypesMap.get(parseInt(document.querySelector('#editPoi_type').value));
    poi.requiredHelpers = parseInt(document.getElementById('editPoi_nbhelper').value);
    poi.push();

    MicroModal.close('edit-poi-popin');

    document.getElementById('editPoi_name').value = '';
    document.getElementById('editPoi_type').value = '';
    document.getElementById('editPoi_nbhelper').value = '';
  });

// EDIT POI DELETE
  document.getElementById('editPoi_delete').addEventListener('click', function () {
    var poiId = document.getElementById('editPoi_id').value;
    var poi = mapManager.poiMap.get(parseInt(poiId));
    poi.remove();

    MicroModal.close('edit-poi-popin');
  });

  console.log("Editor JS loaded");

  MapManager.prototype.displayTrackButton = function () {
  }
}
