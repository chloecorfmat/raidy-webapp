var Race = function () {
    this.id = null;
    this.name = "";
    this.raceTracks = [];
    this.startTime = null;
    this.raid = null;
};

Race.prototype.toJSON = function () {
  let obj        = {};
  obj.id         = this.id;
  obj.name       = this.name;

  obj.raceTracks = [];
  this.raceTracks.forEach(function (rt) {
    obj.raceTracks.push(rt.toObj());
  });

  obj.startTime  = this.startTime;
  obj.raid       = this.raid;
  return JSON.stringify(obj);
};

Race.prototype.fromObj = function(obj){
    this.id = obj['id'];
    this.name = obj['name'];

    this.raceTracks = [];

    for(let idx in obj['tracks']){
        if (obj['tracks'].hasOwnProperty(idx)) {

            let rtObj = obj['tracks'][idx];

            let rt = new RaceTrack();
            rt.fromObj(rtObj);
            this.raceTracks.push(rt);
        }
    }

    this.startTime =  obj['startTime'];
    this.raid = obj['raid'];
};

