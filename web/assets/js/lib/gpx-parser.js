'use strict';

var gpxParser = function gpxParser() {
    this.xmlSource = "";
    this.metadata = {};
    this.waypoints = [];
    this.tracks = [];
    this.routes = [];
};

gpxParser.prototype.parse = function (string) {
    var keepThis = this;
    var domParser = new DOMParser();
    this.xmlSource = domParser.parseFromString(string, 'text/xml');

    metadata = this.xmlSource.querySelector('metadata');
    if (metadata != null) {
        this.metadata.name = this.getElementValue(metadata, "name");
        this.metadata.desc = this.getElementValue(metadata, "desc");
        this.metadata.time = this.getElementValue(metadata, "time");

        var author = {};
        var authorElem = metadata.querySelector('author');
        if (authorElem != null) {
            author.name = this.getElementValue(authorElem, "name");

            author.email = {};
            var emailElem = authorElem.querySelector('email');
            if (emailElem != null) {
                author.email.id = emailElem.getAttribute("id");
                author.email.domain = emailElem.getAttribute("domain");
            }

            var _link = {};
            var _linkElem = authorElem.querySelector('link');
            if (_linkElem != null) {
                _link.href = _linkElem.getAttribute('href');
                _link.text = this.getElementValue(_linkElem, "text");
                _link.type = this.getElementValue(_linkElem, "type");
            }
            author.link = _link;
        }
        this.metadata.author = author;

        var link = {};
        var linkElem = metadata.querySelector('link');
        if (linkElem != null) {
            link.href = linkElem.getAttribute('href');
            link.text = this.getElementValue(linkElem, "text");
            link.type = this.getElementValue(linkElem, "type");
            this.metadata.link = link;
        }
    }

    var wpts = this.xmlSource.querySelectorAll('wpt');
    var _iteratorNormalCompletion = true;
    var _didIteratorError = false;
    var _iteratorError = undefined;

    try {
        for (var _iterator = wpts[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var wpt = _step.value;

            var pt = {};
            pt.name = keepThis.getElementValue(wpt, "name");
            pt.lat = parseFloat(wpt.getAttribute("lat"));
            pt.lon = parseFloat(wpt.getAttribute("lon"));
            pt.ele = parseFloat(keepThis.getElementValue(wpt, "ele"));
            pt.cmt = keepThis.getElementValue(wpt, "cmt");
            pt.desc = keepThis.getElementValue(wpt, "desc");
            keepThis.waypoints.push(pt);
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally {
        try {
            if (!_iteratorNormalCompletion && _iterator.return) {
                _iterator.return();
            }
        } finally {
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }

    var rtes = this.xmlSource.querySelectorAll('rte');
    var _iteratorNormalCompletion2 = true;
    var _didIteratorError2 = false;
    var _iteratorError2 = undefined;

    try {
        for (var _iterator2 = rtes[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
            var rte = _step2.value;

            var route = {};

            route.name = keepThis.getElementValue(rte, "name");
            route.cmt = keepThis.getElementValue(rte, "cmt");
            route.desc = keepThis.getElementValue(rte, "desc");
            route.src = keepThis.getElementValue(rte, "src");
            route.number = keepThis.getElementValue(rte, "number");
            route.link = keepThis.getElementValue(rte, "link");
            route.type = keepThis.getElementValue(rte, "type");

            var routepoints = [];
            var rtepts = rte.querySelectorAll('rtept');
            var _iteratorNormalCompletion4 = true;
            var _didIteratorError4 = false;
            var _iteratorError4 = undefined;

            try {
                for (var _iterator4 = rtepts[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
                    var rtept = _step4.value;

                    var _pt = {};
                    _pt.lat = parseFloat(rtept.getAttribute("lat"));
                    _pt.lon = parseFloat(rtept.getAttribute("lon"));
                    _pt.ele = parseFloat(keepThis.getElementValue(rtept, "ele"));
                    routepoints.push(_pt);
                }
            } catch (err) {
                _didIteratorError4 = true;
                _iteratorError4 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion4 && _iterator4.return) {
                        _iterator4.return();
                    }
                } finally {
                    if (_didIteratorError4) {
                        throw _iteratorError4;
                    }
                }
            }

            route.distance = keepThis.calculDistance(routepoints);
            route.elevation = keepThis.calcElevation(routepoints);
            route.points = routepoints;
            keepThis.routes.push(route);
        }
    } catch (err) {
        _didIteratorError2 = true;
        _iteratorError2 = err;
    } finally {
        try {
            if (!_iteratorNormalCompletion2 && _iterator2.return) {
                _iterator2.return();
            }
        } finally {
            if (_didIteratorError2) {
                throw _iteratorError2;
            }
        }
    }

    var trks = this.xmlSource.querySelectorAll('trk');
    var _iteratorNormalCompletion3 = true;
    var _didIteratorError3 = false;
    var _iteratorError3 = undefined;

    try {
        for (var _iterator3 = trks[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
            var trk = _step3.value;

            var track = {};

            track.name = keepThis.getElementValue(trk, "name");
            track.cmt = keepThis.getElementValue(trk, "cmt");
            track.desc = keepThis.getElementValue(trk, "desc");
            track.src = keepThis.getElementValue(trk, "src");
            track.number = keepThis.getElementValue(trk, "number");
            track.link = keepThis.getElementValue(trk, "link");
            track.type = keepThis.getElementValue(trk, "type");

            var trackpoints = [];
            var trkpt = trk.querySelectorAll('trkpt');
            var _iteratorNormalCompletion5 = true;
            var _didIteratorError5 = false;
            var _iteratorError5 = undefined;

            try {
                for (var _iterator5 = trkpts[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
                    var trkpt = _step5.value;

                    var _pt2 = {};
                    _pt2.lat = parseFloat(trkpt.getAttribute("lat"));
                    _pt2.lon = parseFloat(trkpt.getAttribute("lon"));
                    _pt2.ele = parseFloat(keepThis.getElementValue(trkpt, "ele"));
                    trackpoints.push(_pt2);
                }
            } catch (err) {
                _didIteratorError5 = true;
                _iteratorError5 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion5 && _iterator5.return) {
                        _iterator5.return();
                    }
                } finally {
                    if (_didIteratorError5) {
                        throw _iteratorError5;
                    }
                }
            }

            track.distance = keepThis.calculDistance(trackpoints);
            track.elevation = keepThis.calcElevation(trackpoints);
            track.points = trackpoints;
            keepThis.tracks.push(track);
        }
    } catch (err) {
        _didIteratorError3 = true;
        _iteratorError3 = err;
    } finally {
        try {
            if (!_iteratorNormalCompletion3 && _iterator3.return) {
                _iterator3.return();
            }
        } finally {
            if (_didIteratorError3) {
                throw _iteratorError3;
            }
        }
    }
};

gpxParser.prototype.getElementValue = function (parent, needle) {
    var elem = parent.querySelector(" :scope > " + needle);
    if (elem != null) {
        return elem.innerHTML;
    }
    return elem;
};

gpxParser.prototype.calculDistance = function (points) {
    var distance = {};
    var totalDistance = 0;
    var cumulDistance = [];
    for (var i = 0; i < points.length - 1; i++) {
        totalDistance += this.calcDistanceBetween(points[i], points[i + 1]);
        cumulDistance[i] = totalDistance;
    }
    cumulDistance[points.length - 1] = totalDistance;

    distance.total = totalDistance;
    distance.cumul = cumulDistance;

    return distance;
};

gpxParser.prototype.calcDistanceBetween = function (wpt1, wpt2) {
    var latlng1 = {};
    latlng1.lat = wpt1.lat;
    latlng1.lon = wpt1.lon;
    var latlng2 = {};
    latlng2.lat = wpt2.lat;
    latlng2.lon = wpt2.lon;
    var rad = Math.PI / 180,
        lat1 = latlng1.lat * rad,
        lat2 = latlng2.lat * rad,
        sinDLat = Math.sin((latlng2.lat - latlng1.lat) * rad / 2),
        sinDLon = Math.sin((latlng2.lon - latlng1.lon) * rad / 2),
        a = sinDLat * sinDLat + Math.cos(lat1) * Math.cos(lat2) * sinDLon * sinDLon,
        c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return 6371000 * c;
};

gpxParser.prototype.calcElevation = function (points) {
    var dp = 0,
        dm = 0,
        ret = {};

    for (var i = 0; i < points.length - 1; i++) {
        var diff = parseFloat(points[i + 1].ele) - parseFloat(points[i].ele);

        if (diff < 0) {
            dm += diff;
        } else if (diff > 0) {
            dp += diff;
        }
    }

    var elevation = [];
    var sum = 0;

    for (var i = 0, len = points.length; i < len; i++) {
        var ele = parseFloat(points[i].ele);
        elevation.push(ele);
        sum += ele;
    }

    ret.max = Math.max.apply(null, elevation);
    ret.min = Math.min.apply(null, elevation);
    ret.pos = Math.abs(dp);
    ret.neg = Math.abs(dm);
    ret.avg = sum / elevation.length;

    return ret;
};

gpxParser.prototype.isEmpty = function (obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) return false;
    }
    return true;
};
