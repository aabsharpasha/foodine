/*!
 * Date picker for pickadate.js v3.2.2
 * http://amsul.github.io/pickadate.js/date.htm
 */
!function(a) {
    "function" == typeof define && define.amd ? define(["picker"], a) : a(Picker)
}(function(a) {
    function b(a, b) {
        var c = this, d = a.$node[0].value, e = a.$node.data("value"), f = e || d, g = e ? b.formatSubmit : b.format;
        c.settings = b, c.queue = {min: "measure create", max: "measure create", now: "now create", select: "parse create validate", highlight: "navigate create validate", view: "create validate viewset", disable: "flipItem", enable: "flipItem"}, c.item = {}, c.item.disable = (b.disable || []).slice(0), c.item.enable = -function(a) {
            return a[0] === !0 ? a.shift() : -1
        }(c.item.disable), c.set("min", b.min).set("max", b.max).set("now").set("select", f || c.item.now, {format: g, data: function(a) {
                return f && (a.indexOf("mm") > -1 || a.indexOf("m") > -1)
            }(c.formats.toArray(g))}), c.key = {40: 7, 38: -7, 39: 1, 37: -1, go: function(a) {
                c.set("highlight", [c.item.highlight.year, c.item.highlight.month, c.item.highlight.date + a], {interval: a}), this.render()
            }}, a.on("render", function() {
            a.$root.find("." + b.klass.selectMonth).on("change", function() {
                a.set("highlight", [a.get("view").year, this.value, a.get("highlight").date]), a.$root.find("." + b.klass.selectMonth).focus()
            }), a.$root.find("." + b.klass.selectYear).on("change", function() {
                a.set("highlight", [this.value, a.get("view").month, a.get("highlight").date]), a.$root.find("." + b.klass.selectYear).focus()
            })
        }).on("open", function() {
            a.$root.find("button, select").attr("disabled", !1)
        }).on("close", function() {
            a.$root.find("button, select").attr("disabled", !0)
        })
    }
    var c = 7, d = 6;
    b.prototype.set = function(a, b, c) {
        var d = this;
        return d.item["enable" == a ? "disable" : "flip" == a ? "enable" : a] = d.queue[a].split(" ").map(function(e) {
            return b = d[e](a, b, c)
        }).pop(), "select" == a ? d.set("highlight", d.item.select, c) : "highlight" == a ? d.set("view", d.item.highlight, c) : ("flip" == a || "min" == a || "max" == a || "disable" == a || "enable" == a) && d.item.select && d.item.highlight && d.set("select", d.item.select, c).set("highlight", d.item.highlight, c), d
    }, b.prototype.get = function(a) {
        return this.item[a]
    }, b.prototype.create = function(b, c, d) {
        var e, f = this;
        return c = void 0 === c ? b : c, c == -1 / 0 || 1 / 0 == c ? e = c : a._.isObject(c) && a._.isInteger(c.pick) ? c = c.obj : $.isArray(c) ? (c = new Date(c[0], c[1], c[2]), c = a._.isDate(c) ? c : f.create().obj) : c = a._.isInteger(c) || a._.isDate(c) ? f.normalize(new Date(c), d) : f.now(b, c, d), {year: e || c.getFullYear(), month: e || c.getMonth(), date: e || c.getDate(), day: e || c.getDay(), obj: e || c, pick: e || c.getTime()}
    }, b.prototype.now = function(a, b, c) {
        return b = new Date, c && c.rel && b.setDate(b.getDate() + c.rel), this.normalize(b, c)
    }, b.prototype.navigate = function(b, c, d) {
        if (a._.isObject(c)) {
            for (var e = new Date(c.year, c.month + (d && d.nav ? d.nav : 0), 1), f = e.getFullYear(), g = e.getMonth(), h = c.date; a._.isDate(e) && new Date(f, g, h).getMonth() !== g; )
                h -= 1;
            c = [f, g, h]
        }
        return c
    }, b.prototype.normalize = function(a) {
        return a.setHours(0, 0, 0, 0), a
    }, b.prototype.measure = function(b, c) {
        var d = this;
        return c ? a._.isInteger(c) && (c = d.now(b, c, {rel: c})) : c = "min" == b ? -1 / 0 : 1 / 0, c
    }, b.prototype.viewset = function(a, b) {
        return this.create([b.year, b.month, 1])
    }, b.prototype.validate = function(b, c, d) {
        var e, f, g, h, i = this, j = c, k = d && d.interval ? d.interval : 1, l = -1 === i.item.enable, m = i.item.min, n = i.item.max, o = l && i.item.disable.filter(function(b) {
            if ($.isArray(b)) {
                var d = i.create(b).pick;
                d < c.pick ? e = !0 : d > c.pick && (f = !0)
            }
            return a._.isInteger(b)
        }).length;
        if (!d.nav && (!l && i.disabled(c) || l && i.disabled(c) && (o || e || f) || c.pick <= m.pick || c.pick >= n.pick))
            for (l && !o && (!f && k > 0 || !e && 0 > k) && (k *= - 1); i.disabled(c) && (Math.abs(k) > 1 && (c.month < j.month || c.month > j.month) && (c = j, k = Math.abs(k) / k), c.pick <= m.pick?(g = !0, k = 1):c.pick >= n.pick && (h = !0, k = - 1), !g || !h); )
                c = i.create([c.year, c.month, c.date + k]);
        return c
    }, b.prototype.disabled = function(b) {
        var c = this, d = c.item.disable.filter(function(d) {
            return a._.isInteger(d) ? b.day === (c.settings.firstDay ? d : d - 1) % 7 : $.isArray(d) ? b.pick === c.create(d).pick : void 0
        }).length;
        return b.pick < c.item.min.pick || b.pick > c.item.max.pick || -1 === c.item.enable ? !d : d
    }, b.prototype.parse = function(b, c, d) {
        var e = this, f = {};
        if (!c || a._.isInteger(c) || $.isArray(c) || a._.isDate(c) || a._.isObject(c) && a._.isInteger(c.pick))
            return c;
        if (!d || !d.format)
            throw"Need a formatting option to parse this..";
        return e.formats.toArray(d.format).map(function(b) {
            var d = e.formats[b], g = d ? a._.trigger(d, e, [c, f]) : b.replace(/^!/, "").length;
            d && (f[b] = c.substr(0, g)), c = c.substr(g)
        }), [f.yyyy || f.yy, +(f.mm || f.m) - (d.data ? 1 : 0), f.dd || f.d]
    }, b.prototype.formats = function() {
        function b(a, b, c) {
            var d = a.match(/\w+/)[0];
            return c.mm || c.m || (c.m = b.indexOf(d)), d.length
        }
        function c(a) {
            return a.match(/\w+/)[0].length
        }
        return{d: function(b, c) {
                return b ? a._.digits(b) : c.date
            }, dd: function(b, c) {
                return b ? 2 : a._.lead(c.date)
            }, ddd: function(a, b) {
                return a ? c(a) : this.settings.weekdaysShort[b.day]
            }, dddd: function(a, b) {
                return a ? c(a) : this.settings.weekdaysFull[b.day]
            }, m: function(b, c) {
                return b ? a._.digits(b) : c.month + 1
            }, mm: function(b, c) {
                return b ? 2 : a._.lead(c.month + 1)
            }, mmm: function(a, c) {
                var d = this.settings.monthsShort;
                return a ? b(a, d, c) : d[c.month]
            }, mmmm: function(a, c) {
                var d = this.settings.monthsFull;
                return a ? b(a, d, c) : d[c.month]
            }, yy: function(a, b) {
                return a ? 2 : ("" + b.year).slice(2)
            }, yyyy: function(a, b) {
                return a ? 4 : b.year
            }, toArray: function(a) {
                return a.split(/(d{1,4}|m{1,4}|y{4}|yy|!.)/g)
            }, toString: function(b, c) {
                var d = this;
                return d.formats.toArray(b).map(function(b) {
                    return a._.trigger(d.formats[b], d, [0, c]) || b.replace(/^!/, "")
                }).join("")
            }}
    }(), b.prototype.flipItem = function(a, b) {
        var c = this, d = c.item.disable, e = -1 === c.item.enable;
        return"flip" == b ? c.item.enable = e ? 1 : -1 : !e && "enable" == a || e && "disable" == a ? d = c.removeDisabled(d, b) : (!e && "disable" == a || e && "enable" == a) && (d = c.addDisabled(d, b)), d
    }, b.prototype.addDisabled = function(a, b) {
        var c = this;
        return b.map(function(b) {
            c.filterDisabled(a, b).length || a.push(b)
        }), a
    }, b.prototype.removeDisabled = function(a, b) {
        var c = this;
        return b.map(function(b) {
            a = c.filterDisabled(a, b, 1)
        }), a
    }, b.prototype.filterDisabled = function(a, b, c) {
        var d = $.isArray(b);
        return a.filter(function(a) {
            var e = !d && b === a || d && $.isArray(a) && b.toString() === a.toString();
            return c ? !e : e
        })
    }, b.prototype.nodes = function(b) {
        var e = this, f = e.settings, g = e.item.now, h = e.item.select, i = e.item.highlight, j = e.item.view, k = e.item.disable, l = e.item.min, m = e.item.max, n = function(b) {
            return f.firstDay && b.push(b.shift()), a._.node("thead", a._.group({min: 0, max: c - 1, i: 1, node: "th", item: function(a) {
                    return[b[a], f.klass.weekdays]
                }}))
        }((f.showWeekdaysFull ? f.weekdaysFull : f.weekdaysShort).slice(0)), o = function(b) {
            return a._.node("div", " ", f.klass["nav" + (b ? "Next" : "Prev")] + (b && j.year >= m.year && j.month >= m.month || !b && j.year <= l.year && j.month <= l.month ? " " + f.klass.navDisabled : ""), "data-nav=" + (b || -1))
        }, p = function(c) {
            return f.selectMonths ? a._.node("select", a._.group({min: 0, max: 11, i: 1, node: "option", item: function(a) {
                    return[c[a], 0, "value=" + a + (j.month == a ? " selected" : "") + (j.year == l.year && a < l.month || j.year == m.year && a > m.month ? " disabled" : "")]
                }}), f.klass.selectMonth, b ? "" : "disabled") : a._.node("div", c[j.month], f.klass.month)
        }, q = function() {
            var c = j.year, d = f.selectYears === !0 ? 5 : ~~(f.selectYears / 2);
            if (d) {
                var e = l.year, g = m.year, h = c - d, i = c + d;
                if (e > h && (i += e - h, h = e), i > g) {
                    var k = h - e, n = i - g;
                    h -= k > n ? n : k, i = g
                }
                return a._.node("select", a._.group({min: h, max: i, i: 1, node: "option", item: function(a) {
                        return[a, 0, "value=" + a + (c == a ? " selected" : "")]
                    }}), f.klass.selectYear, b ? "" : "disabled")
            }
            return a._.node("div", c, f.klass.year)
        };
        return a._.node("div", o() + o(1) + p(f.showMonthsShort ? f.monthsShort : f.monthsFull) + q(), f.klass.header) + a._.node("table", n + a._.node("tbody", a._.group({min: 0, max: d - 1, i: 1, node: "tr", item: function(b) {
                var d = f.firstDay && 0 === e.create([j.year, j.month, 1]).day ? -7 : 0;
                return[a._.group({min: c * b - j.day + d + 1, max: function() {
                            return this.min + c - 1
                        }, i: 1, node: "td", item: function(b) {
                            return b = e.create([j.year, j.month, b + (f.firstDay ? 1 : 0)]), [a._.node("div", b.date, function(a) {
                                    return a.push(j.month == b.month ? f.klass.infocus : f.klass.outfocus), g.pick == b.pick && a.push(f.klass.now), h && h.pick == b.pick && a.push(f.klass.selected), i && i.pick == b.pick && a.push(f.klass.highlighted), (k && e.disabled(b) || b.pick < l.pick || b.pick > m.pick) && a.push(f.klass.disabled), a.join(" ")
                                }([f.klass.day]), "data-pick=" + b.pick)]
                        }})]
            }})), f.klass.table) + a._.node("div", a._.node("button", f.today, f.klass.buttonToday, "data-pick=" + g.pick + (b ? "" : " disabled")) + a._.node("button", f.clear, f.klass.buttonClear, "data-clear=1" + (b ? "" : " disabled")), f.klass.footer)
    }, b.defaults = function(a) {
        return{monthsFull: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], weekdaysFull: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], weekdaysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], today: "Today", clear: "Clear", format: "d mmmm, yyyy", klass: {table: a + "table", header: a + "header", navPrev: a + "nav--prev", navNext: a + "nav--next", navDisabled: a + "nav--disabled", month: a + "month", year: a + "year", selectMonth: a + "select--month", selectYear: a + "select--year", weekdays: a + "weekday", day: a + "day", disabled: a + "day--disabled", selected: a + "day--selected", highlighted: a + "day--highlighted", now: a + "day--today", infocus: a + "day--infocus", outfocus: a + "day--outfocus", footer: a + "footer", buttonClear: a + "button--clear", buttonToday: a + "button--today"}}
    }(a.klasses().picker + "__"), a.extend("pickadate", b)
});