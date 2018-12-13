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
        //console.log("------------------");
//        action.track.setEditable(false);
        //console.log(line.getLatLngs());
        //console.log(action.lastPosition);

        line.setLatLngs(action.lastPosition);
       // console.log(line.getLatLngs());

        if(line.editor != undefined){
          line.editor.reset();
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
        case "MOVE_TRACK_MARKER" :
          let toRedo = [];
          let latLngArray = action.track.line.getLatLngs();
          for (let element in latLngArray) {
            toRedo.push({
              lat: latLngArray[element].lat,
              lng: latLngArray[element].lng
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

    }
  };

  MapHistory.prototype.redo = function () {
    let action = this.redoBuffer.pop();
    if (action != undefined) {
       console.log("redo");
      switch (action.type) {
        case "MOVE_TRACK_MARKER" :
          let toUndo = [];
          let latLngArray = action.track.line.getLatLngs();
          for (let element in latLngArray) {
            toUndo.push({
              lat: latLngArray[element].lat,
              lng: latLngArray[element].lng
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
  }
  };

  MapHistory.prototype.logModification = function (obj) {
    //console.log(obj);
    this.undoBuffer.push(obj);
  };
}
/*
 * type
 * target
 * newValue
 * lastValue
 */