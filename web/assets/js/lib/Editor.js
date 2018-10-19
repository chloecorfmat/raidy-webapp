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
    document.getElementById('map').style.cursor = "crosshair";
    mapManager.switchMode(EditorMode.ADD_POI);
});

document.getElementById('addTrackButton').addEventListener('click', function () {
    MicroModal.show('add-track-popin')
});

document.getElementById('addTrack_submit').addEventListener('click', function () {
    trName = document.getElementById('addTrack_name').value;
    trColor = document.getElementById('addTrack_color').value;
    mapManager.requestNewTrack(trName, trColor);
    MicroModal.close('add-track-popin');
});

document.getElementById('TrackSettings_submit').addEventListener('click', function () {
    trName = document.getElementById('TrackSettings_name').value;
    trColor = document.getElementById('TrackSettings_color').value;
    trId = document.getElementById('TrackSettings_id').value;

    track = mapManager.tracksMap.get(parseInt(trId));

    track.setName(trName);
    track.setColor(trColor);

    track.push();
    MicroModal.close('track-settings-popin');

});

document.getElementById('addPoi_submit').addEventListener('click', function () {
    console.log("nbfjklxwi:kvh;")
    poiName = document.getElementById('addPoi_name').value;
    poiType = document.getElementById('addPoi_type').value;
    poiHelpersCount = document.getElementById('addPoi_nbhelper').value;

    MicroModal.close('add-poi-popin');
    mapManager.requestNewPoi(poiName, poiType, poiHelpersCount);

});

document.getElementById('TrackSettings_delete').addEventListener('click', function () {
    trId = document.getElementById('TrackSettings_id').value;

    track = mapManager.tracksMap.get(parseInt(trId));

    track.remove();
    MicroModal.close('track-settings-popin');

});