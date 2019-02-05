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

  };

  // REMOVE REDO
  MapHistory.prototype.redoRemoveMarkerTrack = function (action) {

  };
  // AUTO UNDO
  MapHistory.prototype.redoAutoTrack = function (action) {
    action.track.line.setLatLngs(action.track.line.getLatLngs().splice(0, action.lastSize));
  };

  // AUTO REDO
  MapHistory.prototype.undoAutoTrack = function (action) {
    for (let latLng of action.latLngs) {
     // console.log(latLng);
      action.track.line.addLatLng(latLng);
    }
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
        action.track.line.editor.reset();
        action.track.update();
        action.track.buildUI();
        action.track.push();
      }
     // console.log("undo");

    } else {
    //  console.log("Nothing to undo.");
      iziToast.info({
        message: 'Rien à annuler',
        position: 'bottomLeft',
      });
    }
   // console.log(this.redoBuffer);
   // console.log(this.undoBuffer);
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
    //  console.log("redo");
    } else {
      iziToast.info({
        message: 'Rien à rétablir',
        position: 'bottomLeft',
      });
    }
   // console.log(this.redoBuffer);
 //   console.log(this.undoBuffer);

  };

  MapHistory.prototype.logModification = function (obj) {
    this.undoBuffer.push(obj);
    this.redoBuffer = [];
   // console.log(obj);
  };

  MapHistory.prototype.clearHistory = function () {
    this.undoBuffer = [];
    this.redoBuffer = [];
   // console.log("History cleared")
  };
}
/*
 * type
 * target
 * newValue
 * lastValue
 */


/*MapHistory.prototype.apply = function (action) {
    switch (action.type) {
      case "MOVE_TRACK_MARKER" :
        let line = action.track.line;

        line.setLatLngs(action.lastPosition);

        if(line.editor != undefined){
          if(line.editor.drawing()) {
            console.log(line.editor._drawing);
            if (line.editor._drawing > 0) {
             console.log("forward");
              line.editor.endDrawing();
              line.editor.continueForward();
            } else {
             console.log("backward");
              line.editor.endDrawing();
              line.editor.continueBackward();
            }
          }else{
            line.editor.reset();
          }

        }else{
          action.track.updateDecorator();
        }
        action.track.update();
        break;
    }
  }*/