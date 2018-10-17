
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

document.querySelectorAll('.btn--track--edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
        if (!this.parentElement.classList.contains('track--edit')) {
            document.querySelectorAll('.track--edit').forEach(function (el) {
                el.classList.remove('track--edit')
            })
        }
        mapManager.currentEditID = parseInt(btn.id) ;
        mapManager.switchMode(EditorMode.TRACK_EDIT);
        this.parentElement.classList.toggle('track--edit');
    })
});
document.querySelectorAll('.checkbox-item input').forEach(function(input){
    input.addEventListener('change', function () {
       if(input.checked){
           mapManager.showTrack(parseInt(input.id))
       }else{
           if(mapManager.currentEditID == input.id){
               document.querySelectorAll('.track--edit').forEach(function (el) {
                   el.classList.remove('track--edit')
               })
               mapManager.switchMode(EditorMode.READING);
           }
           mapManager.hideTrack(parseInt(input.id))
       }
    });
});
document.getElementById('addPoiButton').addEventListener('click', function () {
    this.classList.toggle("add--poi");
    document.getElementById('map').style.cursor = "crosshair";
    mapManager.switchMode(EditorMode.ADD_POI);
});

document.getElementById('addTrackButton').addEventListener('click', function () {
    document.getElementById('map').style.cursor = "crosshair";
    mapManager.currentEditID = mapManager.requestNewTrack();
    mapManager.switchMode(EditorMode.TRACK_EDIT);
});
