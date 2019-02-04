/**
 * Track class
 * Manage all actions on Tracks on the map
 */
let MapHistory;
if(typeof(document.getElementById("map")) !== "undefined" && document.getElementById("map") !== null) {
  MapHistory = function (map) {
    this.map = map;
    this.undoBuffer = [];
    this.redoBuffer = [];
  };

  MapHistory.prototype.apply = function (action) {
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
  }


  MapHistory.prototype.undo = function () {
    let action = this.undoBuffer.pop();
    if(action != undefined) {

      console.log("undo");
      console.log(action);
      switch (action.type) {
        case "ADD_MARKER_TRACK" :
          let wasEnabled1 = action.track.line.editEnabled();
          if(wasEnabled1){ action.track.line.disableEdit(); }
          action.track.line.getLatLngs().pop();
          if(wasEnabled1){ action.track.line.enableEdit(); }
          this.redoBuffer.push(action);
          action.track.update();
          action.track.line.redraw();
          action.track.push();
          break;

        case "AUTO_TRACK" :
          let wasEnabled = action.track.line.editEnabled();
          if(wasEnabled){ action.track.line.disableEdit(); }
          action.track.line.setLatLngs(action.track.line.getLatLngs().splice(0,action.lastSize));
          if(wasEnabled){ action.track.line.enableEdit(); }
          this.redoBuffer.push(action);
          action.track.update();
          action.track.line.redraw();
          action.track.push();
          break;
        case "MOVE_TRACK_MARKER" :
          let toRedo = [];
          let latLngArray = action.track.line.getLatLngs();
          for (let element in latLngArray) {
            toRedo.push({
              lat: latLngArray[element].lat,
              lng: latLngArray[element].lng,
              alt: latLngArray[element].alt
            });
          }
          this.redoBuffer.push({
            type: "MOVE_TRACK_MARKER",
            track: action.track,
            lastPosition: toRedo
          });
          break;
      }
      this.apply(action);

    }else{
      console.log("Nothing to undo.");
    }
  };

  MapHistory.prototype.redo = function () {
    let action = this.redoBuffer.pop();
    if (action != undefined) {
       console.log("redo");
      switch (action.type) {

        case "ADD_MARKER_TRACK" :
          let wasEnabled1 = action.track.line.editEnabled();
          if(wasEnabled1){ action.track.line.disableEdit(); }
          action.track.line.addLatLng(action.latLng);
          if(wasEnabled1){ action.track.line.enableEdit(); }
          this.redoBuffer.push(action);
          action.track.update();
          action.track.line.redraw();
          action.track.push();
          break;

        case "AUTO_TRACK" :
          let wasEnabled = action.track.line.editEnabled();
          if(wasEnabled){ action.track.line.disableEdit(); }
          for(let latLng of action.latLngs){
            console.log(latLng);
            action.track.line.addLatLng(latLng);
          }
          if(wasEnabled){ action.track.line.enableEdit(); }
          this.undoBuffer.push(action);
          action.track.update();
          action.track.line.redraw();
          action.track.push();
          break;


        case "MOVE_TRACK_MARKER" :
          let toUndo = [];
          let latLngArray = action.track.line.getLatLngs();
          for (let element in latLngArray) {
            toUndo.push({
              lat: latLngArray[element].lat,
              lng: latLngArray[element].lng,
              alt: latLngArray[element].alt
            });
          }
          this.undoBuffer.push({
            type: "MOVE_TRACK_MARKER",
            track: action.track,
            lastPosition: toUndo
          });
          break;
      }

    this.apply(action);
  }else{
      console.log("Nothing to redo.");
    }
  };

  MapHistory.prototype.logModification = function (obj) {
    this.undoBuffer.push(obj);
    console.log(obj);
  };

  MapHistory.prototype.clearHistory = function () {
    this.undoBuffer = [];
    this.redoBuffer = [];
    console.log("History cleared")
  };
}
/*
 * type
 * target
 * newValue
 * lastValue
 */
