/**
 * Editor UI is used to manage map information that are not display on the map
 * On the editor view it's the left tab
 */
let EditorUI = function () {
};
EditorUI.prototype.addPoi = function (poi) {
};
EditorUI.prototype.updatePoi = function (id, poi) {
};
EditorUI.prototype.removePoi = function (id) {
};
EditorUI.prototype.addTrack = function (track) {
};
EditorUI.prototype.updateTrack = function (id, track) {
};
EditorUI.prototype.removeTrack = function (id) {
};

if (typeof(document.getElementById("editorContainer")) !== "undefined" && document.getElementById("editorContainer") !== null) {

  let moreButtonBehaviour = function (e) {
    e.stopPropagation();
    let dpdwn = this.nextElementSibling;
    if (dpdwn.classList.contains("show")) {
      dpdwn.classList.remove("show");
    } else {
      disableScroll();
      let drop = document.querySelector(".dropdown-content.show");
      if (drop != null) {
        drop.classList.remove("show");
      }
      dpdwn.classList.add("show");
      let remaining = screen.height - e.screenY;
      let topshift;
      if (remaining < dpdwn.clientHeight) {
        topshift = e.pageY - dpdwn.clientHeight * 1.5;
        dpdwn.style.top = topshift + 'px';
      } else {
        topshift = e.pageY - dpdwn.clientHeight;
        dpdwn.style.top = topshift + 'px';
      }
    }
  };

  EditorUI = function () {
    this.trackElements = new Map();
    this.poiElements = new Map();
  };

  EditorUI.prototype.addPoi = function (poi) {
    let li = document.createElement('li');
    li.classList.add('list--pois-items');
    this.poiElements.set(poi.id, li);
    this.updatePoi(poi);
  };

  EditorUI.prototype.updatePoi = function (poi) {
    let keepThis = this;
    if (!this.poiElements.has(poi.id)) {
      this.addPoi(poi);
    }

    let li = this.poiElements.get(poi.id);

    li.innerHTML =
      '             <div data-id = "' + poi.id + '" class="track--text">' +
      '                <span>' + poi.name + '</span>' +
      '            </div>' +
      '            <span class="poi--isCheckpoint" title="Validation nécessaire à ce point d\'intérêt">' + (poi.isCheckpoint ? '<i class="fas fa-flag"></i></span>' : '') +
      '         <button id="moreButton" data-id = "' + poi.id + '" class="dropbtn btn--track--more btn--editor-ico">' +
      '             <i class="fas fa-ellipsis-v"></i>' +
      '         </button>' +
      '         <div id="myDropdown" class="dropdown-content">' +
      '            <a class="btn--poi--settings" data-id = "' + poi.id + '"> <i class="fas fa-cog"></i> Modifier les infos</a>' +
      '            <a class="btn--poi--delete" data-id = "' + poi.id + '"><i class="fas fa-trash"></i> Supprimer</a>' +
      '          </div>';

    li.querySelector("#moreButton").addEventListener("click", moreButtonBehaviour);

    li.querySelector('.track--text').addEventListener('click', function (e) {
      let poi = mapManager.poiMap.get(parseInt(this.dataset.id));
      if (!poi.marker.isPopupOpen()) {
        mapManager.map.panTo(poi.marker.getLatLng());
      }
      poi.marker.togglePopup();
    });

    let btnDelete = li.querySelector('.btn--poi--delete');
    btnDelete.addEventListener("click", function () {
      document.getElementById("btn--delete-poi").dataset.id = poi.id;
      MicroModal.show('delete-poi');
    });

    document.getElementById('list--pois').appendChild(li);
    li.pseudoStyle('before', 'background-color', poi.color);
    li.pseudoStyle('before', 'border-color', poi.color);

    li.querySelector('.btn--poi--settings').addEventListener('click', function () {
      poi.fillEditionPopin();
    });

    let panel = document.getElementById("pois-pan");
    if (panel.style.maxHeight) {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  };

  EditorUI.prototype.removePoi = function (poi) {
    let li = this.poiElements.get(poi.id);
    document.getElementById('list--pois').removeChild(li);
  };

  EditorUI.prototype.addTrack = function (track) {
    let li = document.createElement('li');
    li.classList.add('checkbox-item');
    this.trackElements.set(track.id, li);
    document.getElementById('editor--list').appendChild(li);
    this.updateTrack(track);
  };

  EditorUI.prototype.updateTrack = function (track) {
    if (!this.trackElements.has(track.id)) {
      this.addTrack(track);
    } else {
      let newTrack = track;
      let li = this.trackElements.get(track.id);

      li.id = 'track-li-' + newTrack.id;
      let checked = newTrack.isVisible ? ' checked = "checked"' : '';
      let checkboxAttribute = 'style =" background-color : ' + newTrack.color + '; border-color :' + newTrack.color + '" class="checkmark"';

      li.innerHTML = '<label class="checkbox-item--label">' +
        '             <input data-id = "' + newTrack.id + '" type="checkbox"' + checked + '>' +
        '             ' +
        '             <span ' + checkboxAttribute + '>' +
        '                  <i class="fas fa-check"></i>' +
        '             </span>' +
        '             <div class="track--text">' +
        '                <span class="track-name">' + newTrack.name + '</span>' +
        '                <ul class="track-info">' +
        '                   <i class="fas fa-route"></i>' +
        '                   <li class="track-distance">150,0 km</li>' +
        '                   <i  class="fas fa-long-arrow-alt-up"></i>' +
        '                   <li class="track-elev-gain">100,0 m</li>' +
        '                   <i  class="fas fa-long-arrow-alt-down"></i>' +
        '                   <li class="track-elev-lose">100,0 m</li>' +
        '                </ul>' +
        '            </div>' +
        '            <span class="track--is-calibration" title="Parcours issu d\'une calibration">' + (newTrack.isCalibration ? '<i class="fas fa-mobile-alt"></i></span>' : '') +
        '         </label>' +
        '         <button id="moreButton" data-id = "' + newTrack.id + '" class="dropbtn btn--track--more btn--editor-ico">' +
        '             <i class="fas fa-ellipsis-v"></i>' +
        '         </button>' +
        '         <div id="myDropdown" class="dropdown-content">' +
        '            <a class="btn--track--edit" data-id = "' + newTrack.id + '"> <i class="fas fa-pen"></i> Éditer le tracé</a>' +
        '            <a class="btn--track--settings" data-id = "' + newTrack.id + '"> <i class="fas fa-cog"></i> Modifier les infos</a>' +
        '            <a class="btn--track--graph" data-id = "' + newTrack.id + '"> <i class="fas fa-chart-line"></i> Voir l\'altimétrie</a>' +
        '            <a class="btn--track--delete" data-id = "' + newTrack.id + '"><i class="fas fa-trash"></i> Supprimer</a.btn--track--delete>' +
        '          </div>';

      let input = li.querySelector("input");
      input.checked = newTrack.visible;
      if (newTrack.visible) {
        li.querySelector('label > span.checkmark').style.backgroundColor = li.querySelector('label > span.checkmark').style.borderColor;
      } else {
        li.querySelector('label > span.checkmark').style.backgroundColor = '#ffffff';
      }
      /* When the user clicks on the button, toggle between hiding and showing the dropdown content */
      li.querySelector("#moreButton").addEventListener("click", moreButtonBehaviour);
      newTrack.calculDistance();
      newTrack.calculElevation();
      li.querySelector('.track-distance').innerHTML = Math.round(10 * newTrack.distance / 1000) / 10 + 'Km ';
      li.querySelector('.track-elev-gain').innerHTML = Math.round(newTrack.posElev) + 'm';
      li.querySelector('.track-elev-lose').innerHTML = Math.round(newTrack.negElev) + 'm';
      // TRACK SELECTION LISTENER
      input.addEventListener('change', function () {
        mapManager.toggleTrackVisibility(newTrack);
      });

      let btnDelete = li.querySelector('.btn--track--delete');
      btnDelete.addEventListener("click", function () {
        document.getElementById("btn--delete-track").dataset.id = newTrack.id;
        document.getElementById("track-name-delete").dataset.name = htmlentities.decode(newTrack.name);
        document.getElementById("span--track-name").innerText = htmlentities.decode(newTrack.name);
        MicroModal.show('delete-track');
      });

      // TRACK EDIT PENCIL
      let btnShowElev = li.querySelector('.btn--track--graph');
      btnShowElev.addEventListener('click', function () {
        mapManager.elevator.initChart(mapManager.tracksMap.get(parseInt(btnShowElev.dataset.id)));
        document.querySelector(".elevation-tools").style.display = "block";
      });

      // TRACK EDIT PENCIL
      let btnEdit = li.querySelector('.btn--track--edit');
      btnEdit.addEventListener('click', function () {
        if (!this.parentElement.classList.contains('track--edit')) {
          let trackEdits = document.querySelectorAll('.track--edit');
          for (let el of trackEdits) {
            el.classList.remove('track--edit');
          }
        }
        this.parentElement.classList.toggle('track--edit');
        if (this.parentElement.classList.contains('track--edit')) {
          mapManager.currentEditID = parseInt(btnEdit.dataset.id);
          mapManager.switchMode(EditorMode.TRACK_EDIT);
        } else {
          mapManager.switchMode(EditorMode.READING);
          iziToast.success({
            message: 'Le parcours a bien été sauvegardé.',
            position: 'bottomRight',
          });
        }
      });

      // TRACK SETTINGS COG
      let btnSettings = li.querySelector('.btn--track--settings');
      btnSettings.addEventListener('click', function () {
        let id = parseInt(btnSettings.dataset.id);
        let track = mapManager.tracksMap.get(id);
        document.querySelector('#editTrack_name').value = htmlentities.decode(track.name);
        document.querySelector('#editTrack_color').value = track.color;
        document.querySelector('#editTrack_id').value = track.id;
        document.querySelector('#editTrack_sportType').value = track.sportType;

        MicroModal.show('edit-track-popin');
      });

      let panel = document.getElementById("tracks-pan");
      if (panel.style.maxHeight) {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
      return li;
    }
  };

  EditorUI.prototype.removeTrack = function (track) {
    let li = this.trackElements.get(track.id);
    document.getElementById('editor--list').removeChild(li);
  };

  EditorUI.prototype.displayGPXMetadata = function (tracks, routes, waypoints) {

    var form = document.getElementById('import-gpx--form');

    let sportSelect = this.buildSportTypeSelect();
    let poiTypeSelect = this.buildPoiTypeSelect();

    for (let idx in tracks) {
      let track = tracks[idx];
      let name = (track.name != null && track.name !== '') ? track.name : ('track #' + (parseInt(idx) + 1));
      let markup = '<div>' +
        '<input type="checkbox" data-id="' + idx + '" id="track-' + idx + '" name="' + name + '" checked="checked">' +
        '<label for="track-' + idx + '">' + name + '</label>' +
        sportSelect +
        '</div>';

      let div = form.querySelector('#import-gpx--tracks .import-gpx--checkboxes');
      div.parentNode.style.display = "block";
      div.innerHTML += markup;
    }

    for (let idx in routes) {
      let route = routes[idx];
      let name = (route.name != null && route.name !== '') ? route.name : ('route #' + (parseInt(idx) + 1));
      let markup = '<div>' +
        '<input type="checkbox" data-id="' + idx + '" id="route-' + idx + '" name="' + name + '" checked="checked">' +
        '<label for="route-' + idx + '">' + name + '</label>' +
        sportSelect +
        '</div>';

      let div = form.querySelector('#import-gpx--routes .import-gpx--checkboxes');
      div.parentNode.style.display = "block";
      div.innerHTML += markup;
    }


    for (let idx in waypoints) {
      let waypoint = waypoints[idx];
      let name = (waypoint.name != null && waypoint.name !== '') ? waypoint.name : ('POI #' + (parseInt(idx) + 1));
      let markup = '<div>' +
        '<input type="checkbox" data-id="' + idx + '" id="poi-' + idx + '" name="' + name + '" checked="checked">' +
        '<label for="poi-' + idx + '">' + name + '</label>' +
        poiTypeSelect +
        '</div>';

      let div = form.querySelector('#import-gpx--waypoints .import-gpx--checkboxes');
      div.parentNode.style.display = "block";
      div.innerHTML += markup;
    }
  };

  EditorUI.prototype.cleanImportGPXPopin = function () {
    let form = document.getElementById('import-gpx--form');

    form.querySelector('input[type=file]').value = '';

    form.querySelector('#import-gpx--tracks').style.display = 'none';
    form.querySelector('#import-gpx--routes').style.display = 'none';
    form.querySelector('#import-gpx--waypoints').style.display = 'none';

    form.querySelector('#import-gpx--tracks .import-gpx--checkboxes').innerHTML = '';
    form.querySelector('#import-gpx--routes .import-gpx--checkboxes').innerHTML = '';
    form.querySelector('#import-gpx--waypoints .import-gpx--checkboxes').innerHTML = '';
  };

  EditorUI.prototype.buildSportTypeSelect = function () {
    let select = "<select>";
    mapManager.sportTypesMap.forEach(function (sportType) {
      select += '<option value="' + sportType.id + '">' + sportType.sport + '</option>';
    });
    return select + "</select>";
  };

  EditorUI.prototype.buildPoiTypeSelect = function () {
    let select = "<select>";
    mapManager.poiTypesMap.forEach(function (poiType) {
      select += '<option value="' + poiType.id + '">' + poiType.type + '</option>';
    });
    return select + "</select>";
  };

  EditorUI.prototype.buildExportGPXPopin = function () {
    var tracks = mapManager.tracksMap;
    for (var track of tracks) {
      let markup = '<div>' +
        '<input type="checkbox" data-id="' + idx + '" id="track-' + idx + '" name="' + name + '" checked="checked">' +
        '<label for="route-' + idx + '">' + name + '</label>' +
        sportSelect +
        '</div>';
    }
  };
} else {
  if (document.querySelector('#map') != undefined) {
    let EditorUI = function () {
    }
    EditorUI.prototype.addPoi = function (poi) {
    };
    EditorUI.prototype.updatePoi = function (id, poi) {
    };
    EditorUI.prototype.removePoi = function (id) {
    };
    EditorUI.prototype.addTrack = function (poi) {
    };
    EditorUI.prototype.updateTrack = function (id, poi) {
    };
    EditorUI.prototype.removeTrack = function (id) {
    };
  }
}
