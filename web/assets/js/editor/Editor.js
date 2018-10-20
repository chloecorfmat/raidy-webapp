var UID = {
    _current: 0,
    getNew: function(){
        this._current++;
        return this._current;
    }
};

HTMLElement.prototype.pseudoStyle = function(element,prop,value){
    var _this = this;
    var _sheetId = "pseudoStyles";
    var _head = document.head || document.getElementsByTagName('head')[0];
    var _sheet = document.getElementById(_sheetId) || document.createElement('style');
    _sheet.id = _sheetId;
    var className = "pseudoStyle" + UID.getNew();

    _this.className +=  " "+className;

    _sheet.innerHTML += " ."+className+":"+element+"{"+prop+":"+value+"}";
    _head.appendChild(_sheet);
    return this;
};

var editor =  {activeTab :"tracks-pan"};

function openTab(evt, tabPan) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabPan).style.display = "block";
    evt.currentTarget.className += " active";

    if(tabPan == "pois-pan") mapManager.switchMode(EditorMode.POI_EDIT);
    else mapManager.switchMode(EditorMode.READING);

    editor.activeTab = tabPan;
}

document.getElementById('btn--laterale-bar').addEventListener('click', function() {
    var tab = this.parentElement.parentElement
    tab.classList.toggle('bar--invisible')
    mapManager.map.invalidateSize()
});

window.addEventListener('load', function() {
    openTab(event, 'tracks-pan');
    document.querySelector('.tab .tablinks').classList.add('active');
});

document.getElementById('addPoiButton').addEventListener('click', function () {
    this.classList.toggle("add--poi");
    if (this.classList.contains("add--poi")){
        mapManager.switchMode(EditorMode.ADD_POI);
    }else{
        mapManager.switchMode(mapManager.lastMode);
    }
});

document.getElementById('addTrackButton').addEventListener('click', function () {
    MicroModal.show('add-track-popin')
});

document.getElementById('addTrack_submit').addEventListener('click', function () {
    var trName = document.getElementById('addTrack_name').value;
    var trColor = document.getElementById('addTrack_color').value;
    mapManager.requestNewTrack(trName, trColor);
    MicroModal.close('add-track-popin');


    document.getElementById('addTrack_name').value = "";
    document.getElementById('addTrack_color').value = "#000000";
});

document.getElementById('editTrack_submit').addEventListener('click', function () {
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
    var trId = document.getElementById('editTrack_id').value;

    var track = mapManager.tracksMap.get(parseInt(trId));

    track.remove();
    MicroModal.close('edit-track-popin');

});

// ADD POI SUBMIT
document.getElementById('addPoi_submit').addEventListener('click', function () {
    var poiName = document.getElementById('addPoi_name').value;
    var poiType = document.getElementById('addPoi_type').value;
    var poiHelpersCount = document.getElementById('addPoi_nbhelper').value;

    MicroModal.close('add-poi-popin');
    mapManager.requestNewPoi(poiName, poiType, poiHelpersCount);

    document.getElementById('addPoi_name').value = "";
    document.getElementById('addPoi_type').value = "";
    document.getElementById('addPoi_nbhelper').value = "";

});

// EDIT POI SUBMIT
document.getElementById('editPoi_submit').addEventListener('click', function () {
    var poiId = document.getElementById('editPoi_id').value;
    var poi = mapManager.poiMap.get(parseInt(poiId));

    poi.name = document.getElementById('editPoi_name').value;
    poi.poiType = mapManager.poiTypesMap.get(parseInt(document.querySelector('#editPoi_type').value));
    poi.requiredHelpers = parseInt(document.getElementById('editPoi_nbhelper').value);
    poi.push();

    poi.buildUI();
    MicroModal.close('edit-poi-popin');

    document.getElementById('editPoi_name').value = "";
    document.getElementById('editPoi_type').value = "";
    document.getElementById('editPoi_nbhelper').value = "";


});

// EDIT POI DELETE
document.getElementById('editPoi_delete').addEventListener('click', function () {
   var poiId = document.getElementById('editPoi_id').value;

   var poi = mapManager.poiMap.get(parseInt(poiId));

    poi.remove();
    MicroModal.close('edit-poi-popin');

});