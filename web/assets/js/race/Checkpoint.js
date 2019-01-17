var Checkpoint = function () {
    this.id = null;
    this.order = null;
    this.poi = null;
};

Checkpoint.prototype.toJSON = function () {
    let obj        = {};
    obj.id         = this.id;
    obj.order      = this.order;
    obj.poi        = this.poi.id;
    return JSON.stringify(obj);
};

Checkpoint.prototype.toObj = function () {
    let obj        = {};
    obj.id         = this.id;
    obj.order      = this.order;
    obj.poi        = this.poi.id;
    return obj;
};

Checkpoint.prototype.fromObj = function (obj) {
    this.id = obj['id'];
    this.order = obj['order'];
    this.poi = obj['poi'];
};