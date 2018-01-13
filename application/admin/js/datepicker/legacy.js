/*!
 * Legacy browser support
 */
[].map || (Array.prototype.map = function(a, b) {
    for (var c = this, d = c.length, e = new Array(d), f = 0; d > f; f++)
        f in c && (e[f] = a.call(b, c[f], f, c));
    return e
}), [].filter || (Array.prototype.filter = function(a) {
    if (null == this)
        throw new TypeError;
    var b = Object(this), c = b.length >>> 0;
    if ("function" != typeof a)
        throw new TypeError;
    for (var d = [], e = arguments[1], f = 0; c > f; f++)
        if (f in b) {
            var g = b[f];
            a.call(e, g, f, b) && d.push(g)
        }
    return d
}), [].indexOf || (Array.prototype.indexOf = function(a) {
    if (null == this)
        throw new TypeError;
    var b = Object(this), c = b.length >>> 0;
    if (0 === c)
        return-1;
    var d = 0;
    if (arguments.length > 1 && (d = Number(arguments[1]), d != d ? d = 0 : 0 !== d && 1 / 0 != d && d != -1 / 0 && (d = (d > 0 || -1) * Math.floor(Math.abs(d)))), d >= c)
        return-1;
    for (var e = d >= 0 ? d : Math.max(c - Math.abs(d), 0); c > e; e++)
        if (e in b && b[e] === a)
            return e;
    return-1
});/*!
 * Cross-Browser Split 1.1.1
 * Copyright 2007-2012 Steven Levithan <stevenlevithan.com>
 * Available under the MIT License
 * http://blog.stevenlevithan.com/archives/cross-browser-split
 */
var nativeSplit = String.prototype.split, compliantExecNpcg = void 0 === /()??/.exec("")[1];
String.prototype.split = function(a, b) {
    var c = this;
    if ("[object RegExp]" !== Object.prototype.toString.call(a))
        return nativeSplit.call(c, a, b);
    var d, e, f, g, h = [], i = (a.ignoreCase ? "i" : "") + (a.multiline ? "m" : "") + (a.extended ? "x" : "") + (a.sticky ? "y" : ""), j = 0;
    for (a = new RegExp(a.source, i + "g"), c += "", compliantExecNpcg || (d = new RegExp("^" + a.source + "$(?!\\s)", i)), b = void 0 === b? - 1 >>> 0:b >>> 0; (e = a.exec(c)) && (f = e.index + e[0].length, !(f > j && (h.push(c.slice(j, e.index)), !compliantExecNpcg && e.length > 1 && e[0].replace(d, function() {
        for (var a = 1; a < arguments.length - 2; a++)
            void 0 === arguments[a] && (e[a] = void 0)
    }), e.length > 1 && e.index < c.length && Array.prototype.push.apply(h, e.slice(1)), g = e[0].length, j = f, h.length >= b))); )
        a.lastIndex === e.index && a.lastIndex++;
    return j === c.length ? (g || !a.test("")) && h.push("") : h.push(c.slice(j)), h.length > b ? h.slice(0, b) : h
};