if(typeof(document.getElementById("editorContainer")) !== "undefined" && document.getElementById("editorContainer") !== null) {

  let moreButtonBehaviour =  function (e){
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
      disableScroll()
    }

    let dpdwn = this.nextElementSibling;
    if ((e.view.screen.height - e.screenY ) < convertRem(12) ){
      dpdwn.style.top = 'calc(${e.pageY}px - 17.0rem)';
    } else {
      dpdwn.style.top = 'calc(${e.pageY}px - 7.0rem)';
    }

    dpdwn.classList.toggle("show");
  }

  function EditorUI () {
    this.trackElements = new Map();
    this.poiElements = new Map();
  }
  EditorUI.prototype.addPoi = function(poi){
    let li = document.createElement('li');
    li.classList.add('list--pois-items');
    this.poiElements.set(poi.id, li);
    this.updatePoi(poi);
  }
  EditorUI.prototype.updatePoi = function(poi){
    let keepThis = this;
    if(!this.poiElements.has(poi.id)) {
      this.addPoi(poi);
    }
    let li = this.poiElements.get(poi.id);

    li.innerHTML =
      '             <div class="track--text">' +
      '                <span>' + poi.name + '</span>' +
      '            </div>' +
      '         </label>' +
      '         <button id="moreButton" data-id = "' + poi.id + '" class="dropbtn btn--track--more btn--editor-ico">' +
      '             <i class="fas fa-ellipsis-v"></i>' +
      '         </button>' +
      '         <div id="myDropdown" class="dropdown-content">' +
      '            <a class="btn--poi--settings" data-id = "' + poi.id + '"> <i class="fas fa-cog"></i> Modifier les infos</a>' +
      '            <a class="btn--poi--delete" data-id = "' + poi.id +'"><i class="fas fa-trash"></i> Supprimer</a>' +
      '          </div>';

    li.querySelector("#moreButton").addEventListener("click", moreButtonBehaviour);

    let btnDelete = li.querySelector('.btn--poi--delete');
    btnDelete.addEventListener("click", function () {
      document.getElementById("btn--delete-poi").dataset.id = poi.id;
      MicroModal.show('delete-poi');
    })

    document.getElementById('list--pois').appendChild(li);
    li.pseudoStyle('before', 'background-color', poi.color);
    li.querySelector('.btn--poi--settings').addEventListener('click', function () {
      document.getElementById('editPoi_id').value = poi.id;
      document.getElementById('editPoi_name').value = poi.name;
      document.getElementById('editPoi_nbhelper').value = poi.requiredHelpers;
      (poi.poiType!= null ) && (document.querySelector("#editPoi_type option[value='" + poi.poiType.id + "']").selected = 'selected');
      MicroModal.show('edit-poi-popin');
    })
    let panel = document.getElementById("pois-pan");
    if (panel.style.maxHeight){
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  }
  EditorUI.prototype.removePoi = function(poi){
    let li = this.poiElements.get(poi.id);
    document.getElementById('list--pois').removeChild(li)
  }

  EditorUI.prototype.addTrack = function(track){
    let li = document.createElement('li');
    li.classList.add('checkbox-item');
    this.trackElements.set(track.id, li);
    document.getElementById('editor--list').appendChild(li);
    this.updateTrack(track);
  }

  EditorUI.prototype.updateTrack = function(track){
    if (!this.trackElements.has(track.id)){
      this.addTrack(track);
    } else {
      let newTrack = track;
      let li = this.trackElements.get(track.id);

      li.id = 'track-li-' + newTrack.id
      li.innerHTML = '<label class="checkbox-item--label">' +
        '             <input data-id = "' + newTrack.id + '" type="checkbox" checked="checked">' +
        '             ' +
        '             <span style ="background-color : ' + newTrack.color + '; border-color :' + newTrack.color + '" class="checkmark">' +
        '                  <i class="fas fa-check"></i>' +
        '             </span>' +
        '             <div class="track--text">' +
        '                <span>' + newTrack.name + '</span>' +
        '                <span style="font-size : 0.75rem;"></br>(150,0 km)</span>' +
        '            </div>' +
        '         </label>' +
        '         <button id="moreButton" data-id = "' + newTrack.id + '" class="dropbtn btn--track--more btn--editor-ico">' +
        '             <i class="fas fa-ellipsis-v"></i>' +
        '         </button>' +
        '         <div id="myDropdown" class="dropdown-content">' +
        '            <a class="btn--track--edit" data-id = "' + newTrack.id + '"> <i class="fas fa-pen"></i> Éditer le tracé</a>' +
        '            <a class="btn--track--settings" data-id = "' + newTrack.id + '"> <i class="fas fa-cog"></i> Modifier les infos</a>' +
        '            <a class="btn--track--delete" data-id = "' + newTrack.id +'"><i class="fas fa-trash"></i> Supprimer</a.btn--track--delete>' +
        '            <!-- a><i class="fas fa-clone"></i> Dupliquer</a-->' +
        '          </div>'

       
      /* When the user clicks on the button, toggle between hiding and showing the dropdown content */

      li.querySelector("#moreButton").addEventListener("click", moreButtonBehaviour);
      newTrack.calculDistance()
      li.querySelector('label > div >span:nth-child(2)').innerHTML = '(' + Math.round(10 * newTrack.distance / 1000) / 10 + ' Km)'

      // TRACK SELECTION LISTENER
      li.querySelectorAll('input').forEach(function (input) {
        input.addEventListener('change', function () {
          if (input.checked) {
            mapManager.showTrack(parseInt(input.dataset.id))
           // li.querySelector('.btn--track--edit').style.display = 'inline-block'
            li.querySelector('label > span.checkmark').style.backgroundColor = li.querySelector('label > span.checkmark').style.borderColor
          } else {
            li.querySelector('label > span.checkmark').style.backgroundColor = '#ffffff'
            if (mapManager.currentEditID == input.dataset.id) {
              document.querySelectorAll('.track--edit').forEach(function (el) {
                el.classList.remove('track--edit')
              })
              mapManager.switchMode(EditorMode.READING)
            }
            mapManager.hideTrack(parseInt(input.dataset.id))
          //  li.querySelector('.btn--track--edit').style.display = 'none'
          }
        })
      })

      let btnDelete = li.querySelector('.btn--track--delete');
      btnDelete.addEventListener("click", function () {
        document.getElementById("btn--delete-track").dataset.id = newTrack.id;
        MicroModal.show('delete-track')
      })

      // TRACK EDIT PENCIL
     let btn = li.querySelector('.btn--track--edit');
     btn.addEventListener('click', function () {
     if (!this.parentElement.classList.contains('track--edit')) {
       document.querySelectorAll('.track--edit').forEach(function (el) {
         el.classList.remove('track--edit')
       })
      }
      this.parentElement.classList.toggle('track--edit')
       if (this.parentElement.classList.contains('track--edit')) {
         // console.log(btn);
         mapManager.currentEditID = parseInt(btn.dataset.id)
         console.log(btn.dataset.id)
         mapManager.switchMode(EditorMode.TRACK_EDIT)
       } else {
         mapManager.switchMode(EditorMode.READING)
       }
     })


      // TRACK SETTINGS COG
      li.querySelectorAll('.btn--track--settings').forEach(function (btn) {
        let id = parseInt(btn.dataset.id)
        let track = mapManager.tracksMap.get(id)

        btn.addEventListener('click', function () {
          document.querySelector('#editTrack_name').value = track.name
          document.querySelector('#editTrack_color').value = track.color
          document.querySelector('#editTrack_id').value = track.id

          MicroModal.show('edit-track-popin')
        })
      })

      let panel = document.getElementById("tracks-pan");
      if (panel.style.maxHeight){
        panel.style.maxHeight = panel.scrollHeight + "px";
      }

      return li
    }

  }
  EditorUI.prototype.removeTrack = function(track) {
    let li = this.trackElements.get(track.id)//document.getElementById('track-li-' + this.id)
    document.getElementById('editor--list').removeChild(li)
  }
  console.log("Editor UI for editor loaded")

}else{
  var EditorUI = function () {}
  EditorUI.prototype.addPoi = function(poi){}
  EditorUI.prototype.updatePoi = function(id, poi){}
  EditorUI.prototype.removePoi = function(id){}
  EditorUI.prototype.addTrack = function(poi){}
  EditorUI.prototype.updateTrack = function(id, poi){}
  EditorUI.prototype.removeTrack = function(id){}

  console.log("Editor UI for display only loaded")

}
