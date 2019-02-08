/**
 * Track class
 * Manage all actions on Tracks on the map
 */
let MapHistory;
if (typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  MapHistory = function (map) {
    this.map = map;
    this.undoBuffer = [];
    this.redoBuffer = [];
  };

  /* TRACK MODIFICATION */
  // MOVE UNDO
  MapHistory.prototype.undoMoveMarkerTrack = function (action) {

    action.track.line.getLatLngs()[action.vertexId].lat = action.beforeLat;
    action.track.line.getLatLngs()[action.vertexId].lng = action.beforeLng;
  };
  // MOVE REDO
  MapHistory.prototype.redoMoveMarkerTrack = function (action) {
    action.track.line.getLatLngs()[action.vertexId].lat = action.afterLat;
    action.track.line.getLatLngs()[action.vertexId].lng = action.afterLng;
  };

  // ADD UNDO
  MapHistory.prototype.undoAddMarkerTrack = function (action) {
    if(action.head == 1){
      action.track.line.getLatLngs().pop();
    }else{
      action.track.line.getLatLngs().shift();
    }
  };
  // ADD REDO
  MapHistory.prototype.redoAddMarkerTrack = function (action) {
    if(action.head == 1){
      action.track.line.addLatLng(action.latLng);
    }else{
      action.track.line.getLatLngs().unshift(action.latLng);
    }
  };

  // REMOVE UNDO
  MapHistory.prototype.undoRemoveMarkerTrack = function (action) {
    let array = action.track.line.getLatLngs();
    array.splice(action.vertexId, 0, L.latLng(action.vertexLat, action.vertexLng));

  };

  // REMOVE REDO
  MapHistory.prototype.redoRemoveMarkerTrack = function (action) {
    let array = action.track.line.getLatLngs();
    array.splice(action.vertexId, 1);
  };
  // AUTO REDO
  MapHistory.prototype.redoAutoTrack = function (action) {
    for (let latLng of action.latLngs) {
      action.track.line.addLatLng(latLng);
    }
  };

  // AUTO UNDO
  MapHistory.prototype.undoAutoTrack = function (action) {
    action.track.line.setLatLngs(action.track.line.getLatLngs().splice(0, action.lastSize));

  };

  MapHistory.prototype.undo = function () {
    let action = this.undoBuffer.pop();
    if (action != undefined) {
      if (action.track) {
        let wasEnabled = action.track.line.editEnabled();
        if (wasEnabled) {
          action.track.line.disableEdit();
        }
        switch (action.type) {
          case "MOVE_MARKER_TRACK" :
            this.undoMoveMarkerTrack(action);
            break;

          case "ADD_MARKER_TRACK" :
            this.undoAddMarkerTrack(action);
            break;

          case "REMOVE_MARKER_TRACK" :
            this.undoRemoveMarkerTrack(action);
            break;

          case "AUTO_TRACK" :
            this.undoAutoTrack(action);
            break;
        }

        if (wasEnabled) {
          action.track.line.enableEdit();
          action.track.line.editor.reset();
        }

        this.redoBuffer.push(action);

        action.track.line.redraw();
        action.track.update();
        action.track.buildUI();
        action.track.push();
      }

    } else {
      iziToast.info({
        message: 'Rien à annuler',
        position: 'bottomRight',
      });
    }
  };

  MapHistory.prototype.redo = function () {
    let action = this.redoBuffer.pop();
    if (action != undefined) {
      if (action.track) {
        let wasEnabled = action.track.line.editEnabled();
        if (wasEnabled) {
          action.track.line.disableEdit();
        }
        switch (action.type) {

          case "MOVE_MARKER_TRACK" :
            this.redoMoveMarkerTrack(action);
            break;

          case "ADD_MARKER_TRACK" :
            this.redoAddMarkerTrack(action);
            break;

          case "REMOVE_MARKER_TRACK" :
            this.redoRemoveMarkerTrack(action);
            break;

          case "AUTO_TRACK" :
            this.redoAutoTrack(action);
            break;
        }

        if (wasEnabled) {
          action.track.line.enableEdit();
          action.track.line.editor.reset();
        }

        this.undoBuffer.push(action);

        action.track.line.redraw();
        action.track.update();
        action.track.buildUI();

        action.track.push();
      }
    } else {
      iziToast.info({
        message: 'Rien à rétablir',
        position: 'bottomRight',
      });
    }
  };

  MapHistory.prototype.logModification = function (obj) {
    this.undoBuffer.push(obj);
    this.redoBuffer = [];
  };

  MapHistory.prototype.clearHistory = function () {
    this.undoBuffer = [];
    this.redoBuffer = [];
  };
}
