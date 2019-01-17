var RaceTrack = function () {
  this.id = null;
  this.order = null;
  this.track = null;
  this.checkpoints = [];
};

RaceTrack.prototype.toJSON = function () {
    let obj        = {};
    obj.id         = this.id;
    obj.order      = this.order;
    obj.track      = this.track.id;
    return JSON.stringify(obj);
};

RaceTrack.prototype.toObj = function () {
    let obj        = {};
    obj.id         = this.id;
    obj.order      = this.order;
    obj.track      = this.track.id;

    obj.checkpoints = [];
    this.checkpoints.forEach(function (cp) {
        obj.checkpoints.push(cp.toObj());
    });

    return obj;
};

RaceTrack.prototype.fromObj = function (obj) {
    this.id = obj['id'];
    this.order = obj['order'];

    this.track = [];
    this.track.name = obj['track'];

    this.name = obj['name'];
    this.checkpoints = [];

    for(let idx in obj['checkpoints']){
        if (obj['checkpoints'].hasOwnProperty(idx)) {

            let cpObj = obj['checkpoints'][idx];

            let cp = new Checkpoint();
            cp.fromObj(cpObj);
            this.checkpoints.push(cp);
        }
    }

};