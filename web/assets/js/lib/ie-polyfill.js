if (typeof Object.assign != "function") {
    Object.defineProperty(Object, "assign", {
        value: function assign(target, varArgs) {
            "use strict"
            if (target == null) {
                throw new TypeError("Cannot convert undefined or null to object")
            }
            var to = Object(target)
            for (var index = 1; index < arguments.length; index++) {
                var nextSource = arguments[index]
                if (nextSource != null) {
                    for (var nextKey in nextSource) {
                        if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                            to[nextKey] = nextSource[nextKey]
                        }
                    }
                }
            }
            return to
        },
        writable: true,
        configurable: true
    })
}

if (!Array.from) {
    Array.from = (function() {
        var toStr = Object.prototype.toString
        var isCallable = function(fn) {
            return typeof fn === "function" || toStr.call(fn) === "[object Function]"
        }
        var toInteger = function(value) {
            var number = Number(value)
            if (isNaN(number)) {
                return 0
            }
            if (number === 0 || !isFinite(number)) {
                return number
            }
            return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number))
        }
        var maxSafeInteger = Math.pow(2, 53) - 1
        var toLength = function(value) {
            var len = toInteger(value)
            return Math.min(Math.max(len, 0), maxSafeInteger)
        }

        return function from(arrayLike) {
            var C = this
            var items = Object(arrayLike)
            if (arrayLike == null) {
                throw new TypeError(
                    "Array.from requires an array-like object - not null or undefined"
                )
            }
            var mapFn = arguments.length > 1 ? arguments[1] : void undefined
            var T
            if (typeof mapFn !== "undefined") {
                if (!isCallable(mapFn)) {
                    throw new TypeError(
                        "Array.from: when provided, the second argument must be a function"
                    )
                }
                if (arguments.length > 2) {
                    T = arguments[2]
                }
            }
            var len = toLength(items.length)
            var A = isCallable(C) ? Object(new C(len)) : new Array(len)
            var k = 0
            var kValue
            while (k < len) {
                kValue = items[k]
                if (mapFn) {
                    A[k] =
                        typeof T === "undefined"
                            ? mapFn(kValue, k)
                            : mapFn.call(T, kValue, k)
                } else {
                    A[k] = kValue
                }
                k += 1
            }
            A.length = len
            return A
        }
    })()
}

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector;
}

(function(doc, proto) {
  try { // check if browser supports :scope natively
    doc.querySelector(':scope body');
  } catch (err) { // polyfill native methods if it doesn't
    ['querySelector', 'querySelectorAll'].forEach(function(method) {
      var nativ = proto[method];
      proto[method] = function(selectors) {
        if (/(^|,)\s*:scope/.test(selectors)) { // only if selectors contains :scope
          var id = this.id; // remember current element id
          this.id = 'ID_' + Date.now(); // assign new unique id
          selectors = selectors.replace(/((^|,)\s*):scope/g, '$1#' + this.id); // replace :scope with #ID
          var result = doc[method](selectors);
          this.id = id; // restore previous id
          return result;
        } else {
          return nativ.call(this, selectors); // use native code for other selectors
        }
      }
    });
  }
})(window.document, Element.prototype);
