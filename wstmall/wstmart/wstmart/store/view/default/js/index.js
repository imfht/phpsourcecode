/*! AdminLTE app.js
* ================
* Main JS application file for AdminLTE v2. This file
* should be included in all pages. It controls some layout
* options and implements exclusive AdminLTE plugins.
*
* @Author  Almsaeed Studio
* @Support <https://www.almsaeedstudio.com>
* @Email   <abdullah@almsaeedstudio.com>
* @version 2.4.0
* @repository git://github.com/almasaeed2010/AdminLTE.git
* @license MIT <http://opensource.org/licenses/MIT>
*/
if (typeof jQuery === "undefined") {
    throw new Error("AdminLTE requires jQuery")
} +
function(g) {
    var f = "lte.boxrefresh";
    var c = {
        source: "",
        params: {},
        trigger: ".refresh-btn",
        content: ".box-body",
        loadInContent: true,
        responseType: "",
        overlayTemplate: '<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>',
        onLoadStart: function() {},
        onLoadDone: function(h) {
            return h
        }
    };
    var b = {
        data: '[data-widget="box-refresh"]'
    };
    var d = function(i, h) {
        this.element = i;
        this.options = h;
        this.$overlay = g(h.overlay);
        if (h.source === "") {
            throw new Error("Source url was not defined. Please specify a url in your BoxRefresh source option.")
        }
        this._setUpListeners();
        this.load()
    };
    d.prototype.load = function() {
        this._addOverlay();
        this.options.onLoadStart.call(g(this));
        g.get(this.options.source, this.options.params,
        function(h) {
            if (this.options.loadInContent) {
                g(this.options.content).html(h)
            }
            this.options.onLoadDone.call(g(this), h);
            this._removeOverlay()
        }.bind(this), this.options.responseType !== "" && this.options.responseType)
    };
    d.prototype._setUpListeners = function() {
        g(this.element).on("click", b.trigger,
        function(h) {
            if (h) {
                h.preventDefault()
            }
            this.load()
        }.bind(this))
    };
    d.prototype._addOverlay = function() {
        g(this.element).append(this.$overlay)
    };
    d.prototype._removeOverlay = function() {
        g(this.element).remove(this.$overlay)
    };
    function e(h) {
        return this.each(function() {
            var k = g(this);
            var j = k.data(f);
            if (!j) {
                var i = g.extend({},
                c, k.data(), typeof h == "object" && h);
                k.data(f, (j = new d(k, i)))
            }
            if (typeof j == "string") {
                if (typeof j[h] == "undefined") {
                    throw new Error("No method named " + h)
                }
                j[h]()
            }
        })
    }
    var a = g.fn.boxRefresh;
    g.fn.boxRefresh = e;
    g.fn.boxRefresh.Constructor = d;
    g.fn.boxRefresh.noConflict = function() {
        g.fn.boxRefresh = a;
        return this
    };
    g(window).on("load",
    function() {
        g(b.data).each(function() {
            e.call(g(this))
        })
    })
} (jQuery) +
function(d) {
    var b = "lte.boxwidget";
    var f = {
        animationSpeed: 500,
        collapseTrigger: '[data-widget="collapse"]',
        removeTrigger: '[data-widget="remove"]',
        collapseIcon: "fa-minus",
        expandIcon: "fa-plus",
        removeIcon: "fa-times"
    };
    var a = {
        data: ".box",
        collapsed: ".collapsed-box",
        body: ".box-body",
        footer: ".box-footer",
        tools: ".box-tools"
    };
    var h = {
        collapsed: "collapsed-box"
    };
    var i = {
        collapsed: "collapsed.boxwidget",
        expanded: "expanded.boxwidget",
        removed: "removed.boxwidget"
    };
    var e = function(k, j) {
        this.element = k;
        this.options = j;
        this._setUpListeners()
    };
    e.prototype.toggle = function() {
        var j = !d(this.element).is(a.collapsed);
        if (j) {
            this.collapse()
        } else {
            this.expand()
        }
    };
    e.prototype.expand = function() {
        var l = d.Event(i.expanded);
        var k = this.options.collapseIcon;
        var j = this.options.expandIcon;
        d(this.element).removeClass(h.collapsed);
        d(this.element).find(a.tools).find("." + j).removeClass(j).addClass(k);
        d(this.element).find(a.body + ", " + a.footer).slideDown(this.options.animationSpeed,
        function() {
            d(this.element).trigger(l)
        }.bind(this))
    };
    e.prototype.collapse = function() {
        var k = d.Event(i.collapsed);
        var l = this.options.collapseIcon;
        var j = this.options.expandIcon;
        d(this.element).find(a.tools).find("." + l).removeClass(l).addClass(j);
        d(this.element).find(a.body + ", " + a.footer).slideUp(this.options.animationSpeed,
        function() {
            d(this.element).addClass(h.collapsed);
            d(this.element).trigger(k)
        }.bind(this))
    };
    e.prototype.remove = function() {
        var j = d.Event(i.removed);
        d(this.element).slideUp(this.options.animationSpeed,
        function() {
            d(this.element).trigger(j);
            d(this.element).remove()
        }.bind(this))
    };
    e.prototype._setUpListeners = function() {
        var j = this;
        d(this.element).on("click", this.options.collapseTrigger,
        function(k) {
            if (k) {
                k.preventDefault()
            }
            j.toggle()
        });
        d(this.element).on("click", this.options.removeTrigger,
        function(k) {
            if (k) {
                k.preventDefault()
            }
            j.remove()
        })
    };
    function g(j) {
        return this.each(function() {
            var m = d(this);
            var l = m.data(b);
            if (!l) {
                var k = d.extend({},
                f, m.data(), typeof j == "object" && j);
                m.data(b, (l = new e(m, k)))
            }
            if (typeof j == "string") {
                if (typeof l[j] == "undefined") {
                    throw new Error("No method named " + j)
                }
                l[j]()
            }
        })
    }
    var c = d.fn.boxWidget;
    d.fn.boxWidget = g;
    d.fn.boxWidget.Constructor = e;
    d.fn.boxWidget.noConflict = function() {
        d.fn.boxWidget = c;
        return this
    };
    d(window).on("load",
    function() {
        d(a.data).each(function() {
            g.call(d(this))
        })
    })
} (jQuery) +
function(d) {
    var b = "lte.controlsidebar";
    var e = {
        slide: true
    };
    var a = {
        sidebar: ".control-sidebar",
        data: '[data-toggle="control-sidebar"]',
        open: ".control-sidebar-open",
        bg: ".control-sidebar-bg",
        wrapper: ".wrapper",
        content: ".content-wrapper",
        boxed: ".layout-boxed"
    };
    var h = {
        open: "control-sidebar-open",
        fixed: "fixed"
    };
    var i = {
        collapsed: "collapsed.controlsidebar",
        expanded: "expanded.controlsidebar"
    };
    var g = function(k, j) {
        this.element = k;
        this.options = j;
        this.hasBindedResize = false;
        this.init()
    };
    g.prototype.init = function() {
        if (!d(this.element).is(a.data)) {
            d(this).on("click", this.toggle)
        }
        this.fix();
        d(window).resize(function() {
            this.fix()
        }.bind(this))
    };
    g.prototype.toggle = function(j) {
        if (j) {
            j.preventDefault()
        }
        this.fix();
        if (!d(a.sidebar).is(a.open) && !d("body").is(a.open)) {
            this.expand()
        } else {
            this.collapse()
        }
    };
    g.prototype.expand = function() {
        if (!this.options.slide) {
            d("body").addClass(h.open)
        } else {
            d(a.sidebar).addClass(h.open)
        }
        d(this.element).trigger(d.Event(i.expanded))
    };
    g.prototype.collapse = function() {
        d("body, " + a.sidebar).removeClass(h.open);
        d(this.element).trigger(d.Event(i.collapsed))
    };
    g.prototype.fix = function() {
        if (d("body").is(a.boxed)) {
            this._fixForBoxed(d(a.bg))
        }
    };
    g.prototype._fixForBoxed = function(j) {
        j.css({
            position: "absolute",
            height: d(a.wrapper).height()
        })
    };
    function f(j) {
        return this.each(function() {
            var m = d(this);
            var l = m.data(b);
            if (!l) {
                var k = d.extend({},
                e, m.data(), typeof j == "object" && j);
                m.data(b, (l = new g(m, k)))
            }
            if (typeof j == "string") {
                l.toggle()
            }
        })
    }
    var c = d.fn.controlSidebar;
    d.fn.controlSidebar = f;
    d.fn.controlSidebar.Constructor = g;
    d.fn.controlSidebar.noConflict = function() {
        d.fn.controlSidebar = c;
        return this
    };
    d(document).on("click", a.data,
    function(j) {
        if (j) {
            j.preventDefault()
        }
        f.call(d(this), "toggle")
    })
} (jQuery) +
function(g) {
    var f = "lte.directchat";
    var c = {
        data: '[data-widget="chat-pane-toggle"]',
        box: ".direct-chat"
    };
    var e = {
        open: "direct-chat-contacts-open"
    };
    var a = function(h) {
        this.element = h
    };
    a.prototype.toggle = function(h) {
        h.parents(c.box).first().toggleClass(e.open)
    };
    function d(h) {
        return this.each(function() {
            var j = g(this);
            var i = j.data(f);
            if (!i) {
                j.data(f, (i = new a(j)))
            }
            if (typeof h == "string") {
                i.toggle(j)
            }
        })
    }
    var b = g.fn.directChat;
    g.fn.directChat = d;
    g.fn.directChat.Constructor = a;
    g.fn.directChat.noConflict = function() {
        g.fn.directChat = b;
        return this
    };
    g(document).on("click", c.data,
    function(h) {
        if (h) {
            h.preventDefault()
        }
        d.call(g(this), "toggle")
    })
} (jQuery) +
function(h) {
    var g = "lte.layout";
    var c = {
        slimscroll: true,
        resetHeight: true
    };
    var b = {
        wrapper: ".wrapper",
        contentWrapper: ".content-wrapper",
        layoutBoxed: ".layout-boxed",
        mainFooter: ".main-footer",
        mainHeader: ".main-header",
        sidebar: ".sidebar",
        controlSidebar: ".control-sidebar",
        fixed: ".fixed",
        sidebarMenu: ".sidebar-menu",
        logo: ".main-header .logo"
    };
    var f = {
        fixed: "fixed",
        holdTransition: "hold-transition"
    };
    var e = function(i) {
        this.options = i;
        this.bindedResize = false;
        this.activate()
    };
    e.prototype.activate = function() {
        this.fix();
        this.fixSidebar();
        h("body").removeClass(f.holdTransition);
        if (this.options.resetHeight) {
            h("body, html, " + b.wrapper).css({
                "height": "auto",
                "min-height": "100%"
            })
        }
        if (!this.bindedResize) {
            h(window).resize(function() {
                this.fix();
                this.fixSidebar();
                h(b.logo + ", " + b.sidebar).one("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend",
                function() {
                    this.fix();
                    this.fixSidebar()
                }.bind(this))
            }.bind(this));
            this.bindedResize = true
        }
        h(b.sidebarMenu).on("expanded.tree",
        function() {
            this.fix();
            this.fixSidebar()
        }.bind(this));
        h(b.sidebarMenu).on("collapsed.tree",
        function() {
            this.fix();
            this.fixSidebar()
        }.bind(this))
    };
    e.prototype.fix = function() {
        h(b.layoutBoxed + " > " + b.wrapper).css("overflow", "hidden");
        var l = h(b.mainFooter).outerHeight() || 0;
        var n = h(b.mainHeader).outerHeight() + l;
        var m = h(window).height();
        var i = h(b.sidebar).height() || 0;
        if (h("body").hasClass(f.fixed)) {
            h(b.contentWrapper).css("min-height", m - l);
            h(".content-iframe").height(m - l)
        } else {
            var k;
            if (m >= i) {
                h(b.contentWrapper).css("min-height", m - n);
                k = m - n;
                h(".content-iframe").height(m - n - 40)
            } else {
                h(b.contentWrapper).css("min-height", i);
                k = i;
                h(".content-iframe").height(i - 40)
            }
            var j = h(b.controlSidebar);
            if (typeof j !== "undefined") {
                if (j.height() > k) {
                    h(b.contentWrapper).css("min-height", j.height())
                }
                h(".content-iframe").height(j.height())
            }
        }
    };
    e.prototype.fixSidebar = function() {
        if (!h("body").hasClass(f.fixed)) {
            if (typeof h.fn.slimScroll !== "undefined") {
                h(b.sidebar).slimScroll({
                    destroy: true
                }).height("auto")
            }
            return
        }
        if (this.options.slimscroll) {
            if (typeof h.fn.slimScroll !== "undefined") {
                h(b.sidebar).slimScroll({
                    height: (h(window).height() - h(b.mainHeader).height()) + "px",
                    color: "rgba(0,0,0,0.2)",
                    size: "3px"
                })
            }
        }
    };
    function d(i) {
        return this.each(function() {
            var l = h(this);
            var k = l.data(g);
            if (!k) {
                var j = h.extend({},
                c, l.data(), typeof i === "object" && i);
                l.data(g, (k = new e(j)))
            }
            if (typeof i === "string") {
                if (typeof k[i] === "undefined") {
                    throw new Error("No method named " + i)
                }
                k[i]()
            }
        })
    }
    var a = h.fn.layout;
    h.fn.layout = d;
    h.fn.layout.Constuctor = e;
    h.fn.layout.noConflict = function() {
        h.fn.layout = a;
        return this
    };
    h(window).on("load",
    function() {
        d.call(h("body"))
    })
} (jQuery) +
function(d) {
    var b = "lte.pushmenu";
    var e = {
        collapseScreenSize: 767,
        expandOnHover: false,
        expandTransitionDelay: 200
    };
    var a = {
        collapsed: ".sidebar-collapse",
        open: ".sidebar-open",
        mainSidebar: ".main-sidebar",
        contentWrapper: ".content-wrapper",
        searchInput: ".sidebar-form .form-control",
        button: '[data-toggle="push-menu"]',
        mini: ".sidebar-mini",
        expanded: ".sidebar-expanded-on-hover",
        layoutFixed: ".fixed"
    };
    var h = {
        collapsed: "sidebar-collapse",
        open: "sidebar-open",
        mini: "sidebar-mini",
        expanded: "sidebar-expanded-on-hover",
        expandFeature: "sidebar-mini-expand-feature",
        layoutFixed: "fixed"
    };
    var i = {
        expanded: "expanded.pushMenu",
        collapsed: "collapsed.pushMenu"
    };
    var g = function(j) {
        this.options = j;
        this.init()
    };
    g.prototype.init = function() {
        if (this.options.expandOnHover || (d("body").is(a.mini + a.layoutFixed))) {
            this.expandOnHover();
            d("body").addClass(h.expandFeature)
        }
        d(a.contentWrapper).click(function() {
            if (d(window).width() <= this.options.collapseScreenSize && d("body").hasClass(h.open)) {
                this.close()
            }
        }.bind(this));
        d(a.searchInput).click(function(j) {
            j.stopPropagation()
        })
    };
    g.prototype.toggle = function() {
        var k = d(window).width();
        var j = !d("body").hasClass(h.collapsed);
        if (k <= this.options.collapseScreenSize) {
            j = d("body").hasClass(h.open)
        }
        if (!j) {
            this.open()
        } else {
            this.close()
        }
    };
    g.prototype.open = function() {
        var j = d(window).width();
        if (j > this.options.collapseScreenSize) {
            d("body").removeClass(h.collapsed).trigger(d.Event(i.expanded))
        } else {
            d("body").addClass(h.open).trigger(d.Event(i.expanded))
        }
    };
    g.prototype.close = function() {
        var j = d(window).width();
        if (j > this.options.collapseScreenSize) {
            d("body").addClass(h.collapsed).trigger(d.Event(i.collapsed))
        } else {
            d("body").removeClass(h.open + " " + h.collapsed).trigger(d.Event(i.collapsed))
        }
    };
    g.prototype.expandOnHover = function() {
        d(a.mainSidebar).hover(function() {
            if (d("body").is(a.mini + a.collapsed) && d(window).width() > this.options.collapseScreenSize) {
                this.expand()
            }
        }.bind(this),
        function() {
            if (d("body").is(a.expanded)) {
                this.collapse()
            }
        }.bind(this))
    };
    g.prototype.expand = function() {
        setTimeout(function() {
            d("body").removeClass(h.collapsed).addClass(h.expanded)
        },
        this.options.expandTransitionDelay)
    };
    g.prototype.collapse = function() {
        setTimeout(function() {
            d("body").removeClass(h.expanded).addClass(h.collapsed)
        },
        this.options.expandTransitionDelay)
    };
    function f(j) {
        return this.each(function() {
            var m = d(this);
            var l = m.data(b);
            if (!l) {
                var k = d.extend({},
                e, m.data(), typeof j == "object" && j);
                m.data(b, (l = new g(k)))
            }
            if (j === "toggle") {
                l.toggle()
            }
        })
    }
    var c = d.fn.pushMenu;
    d.fn.pushMenu = f;
    d.fn.pushMenu.Constructor = g;
    d.fn.pushMenu.noConflict = function() {
        d.fn.pushMenu = c;
        return this
    };
    d(document).on("click", a.button,
    function(j) {
        j.preventDefault();
        f.call(d(this), "toggle")
    });
    d(window).on("load",
    function() {
        f.call(d(a.button))
    })
} (jQuery) +
function(h) {
    var g = "lte.todolist";
    var c = {
        onCheck: function(i) {
            return i
        },
        onUnCheck: function(i) {
            return i
        }
    };
    var b = {
        data: '[data-widget="todo-list"]'
    };
    var e = {
        done: "done"
    };
    var f = function(j, i) {
        this.element = j;
        this.options = i;
        this._setUpListeners()
    };
    f.prototype.toggle = function(i) {
        i.parents(b.li).first().toggleClass(e.done);
        if (!i.prop("checked")) {
            this.unCheck(i);
            return
        }
        this.check(i)
    };
    f.prototype.check = function(i) {
        this.options.onCheck.call(i)
    };
    f.prototype.unCheck = function(i) {
        this.options.onUnCheck.call(i)
    };
    f.prototype._setUpListeners = function() {
        var i = this;
        h(this.element).on("change ifChanged", "input:checkbox",
        function() {
            i.toggle(h(this))
        })
    };
    function d(i) {
        return this.each(function() {
            var l = h(this);
            var k = l.data(g);
            if (!k) {
                var j = h.extend({},
                c, l.data(), typeof i == "object" && i);
                l.data(g, (k = new f(l, j)))
            }
            if (typeof k == "string") {
                if (typeof k[i] == "undefined") {
                    throw new Error("No method named " + i)
                }
                k[i]()
            }
        })
    }
    var a = h.fn.todoList;
    h.fn.todoList = d;
    h.fn.todoList.Constructor = f;
    h.fn.todoList.noConflict = function() {
        h.fn.todoList = a;
        return this
    };
    h(window).on("load",
    function() {
        h(b.data).each(function() {
            d.call(h(this))
        })
    })
} (jQuery) +
function(e) {
    var b = "lte.tree";
    var f = {
        animationSpeed: 500,
        accordion: true,
        followLink: false,
        trigger: ".treeview a"
    };
    var a = {
        tree: ".tree",
        treeview: ".treeview",
        treeviewMenu: ".treeview-menu",
        open: ".menu-open, .active",
        li: "li",
        data: '[data-widget="tree"]',
        active: ".active"
    };
    var h = {
        open: "menu-open active",
        tree: "tree"
    };
    var i = {
        collapsed: "collapsed.tree",
        expanded: "expanded.tree"
    };
    var d = function(k, j) {
        this.element = k;
        this.options = j;
        e(this.element).addClass(h.tree);
        e(a.treeview + a.active, this.element).addClass(h.open);
        this._setUpListeners()
    };
    d.prototype.toggle = function(n, m) {
        var l = n.next(a.treeviewMenu);
        var j = n.parent();
        var k = j.hasClass(h.open);
        if (!j.is(a.treeview)) {
            return
        }
        if (!this.options.followLink || n.attr("href") === "#") {
            m.preventDefault()
        }
        if (k) {
            this.collapse(l, j)
        } else {
            this.expand(l, j)
        }
    };
    d.prototype.expand = function(k, l) {
        var n = e.Event(i.expanded);
        if (this.options.accordion) {
            var j = l.siblings(a.open);
            var m = j.children(a.treeviewMenu);
            this.collapse(m, j)
        }
        l.addClass(h.open);
        k.slideDown(this.options.animationSpeed,
        function() {
            e(this.element).trigger(n)
        }.bind(this))
    };
    d.prototype.collapse = function(k, j) {
        var l = e.Event(i.collapsed);
        k.find(a.open).removeClass(h.open);
        j.removeClass(h.open);
        k.slideUp(this.options.animationSpeed,
        function() {
            k.find(a.open + " > " + a.treeview).slideUp();
            e(this.element).trigger(l)
        }.bind(this))
    };
    d.prototype._setUpListeners = function() {
        var j = this;
        e(this.element).on("click", this.options.trigger,
        function(k) {
            j.toggle(e(this), k)
        })
    };
    function g(j) {
        return this.each(function() {
            var m = e(this);
            var l = m.data(b);
            if (!l) {
                var k = e.extend({},
                f, m.data(), typeof j == "object" && j);
                m.data(b, new d(m, k))
            }
        })
    }
    var c = e.fn.tree;
    e.fn.tree = g;
    e.fn.tree.Constructor = d;
    e.fn.tree.noConflict = function() {
        e.fn.tree = c;
        return this
    };
    e(window).on("load",
    function() {
        e(a.data).each(function() {
            g.call(e(this))
        })
    })
} (jQuery) +
function(a) {
    a.GZSTLayout = {
        requestFullscreen: function() {
            var b = document.documentElement;
            if (b.requestFullscreen) {
                b.requestFullscreen()
            } else {
                if (b.mozRequestFullScreen) {
                    b.mozRequestFullScreen()
                } else {
                    if (b.webkitRequestFullScreen) {
                        b.webkitRequestFullScreen()
                    }
                }
            }
        },
        exitFullscreen: function() {
            var b = document;
            if (b.exitFullscreen) {
                b.exitFullscreen()
            } else {
                if (b.mozCancelFullScreen) {
                    b.mozCancelFullScreen()
                } else {
                    if (b.webkitCancelFullScreen) {
                        b.webkitCancelFullScreen()
                    }
                }
            }
        },
        showSidebarMenu: function(d) {
            var c = false;
            a(".j-sysmenu" + d).each(function() {
                if (a(this).hasClass("menu-open")) {
                    c = true
                }
            });
            var b = 0;
            a(".j-menulevel0").each(function() {
                if (a(this).hasClass("j-sysmenu" + d)) {
                    a(this).show();
                    if (!c && b == 0) {
                        a(this).addClass("menu-open active")
                    }
                    b++
                } else {
                    a(this).hide()
                }
            })
        },
        positionWay: function(h) {
            var f = null,
            g = [];
            for (var e in menus) {
                if (menus[e]["list"] && menus[e]["list"].length) {
                    for (var d = 0; d < menus[e]["list"].length; d++) {
                        if (menus[e]["list"][d]["list"] && menus[e]["list"][d]["list"].length) {
                            for (var c = 0; c < menus[e]["list"][d]["list"].length; c++) {
                                if (menus[e]["list"][d]["list"][c]["list"] && menus[e]["list"][d]["list"][c]["list"].length) {
                                    for (var b = 0; b < menus[e]["list"][d]["list"][c]["list"].length; b++) {
                                        if (menus[e]["list"][d]["list"][c]["list"][b]["menuId"] == h) {
                                            g.push(menus[e]["list"][d]["list"][c]["list"][b]["menuName"]);
                                            f = menus[e]["list"][d]["list"][c]["list"][d];
                                            break
                                        }
                                    }
                                }
                                if (menus[e]["list"][d]["list"][c]["menuId"] == h) {
                                    g.push(menus[e]["list"][d]["list"][c]["menuName"]);
                                    f = menus[e]["list"][d];
                                    break
                                }
                            }
                            if (g.length > 0 && f["menuId"] == menus[e]["list"][d]["menuId"]) {
                                g.push(menus[e]["list"][d]["menuName"]);
                                f = menus[e];
                                break
                            }
                        }
                    }
                    if (g.length > 0 && f["menuId"] == menus[e]["menuId"]) {
                        g.push(menus[e]["menuName"]);
                        f = menus[e];
                        break
                    }
                }
            }
            g = g.reverse();
            for (var e = 0; e < g.length; e++) {
                if (e == 0) {
                    g[e] = "<li><a href='#' onclick='javascript:location.reload()'><i class='fa fa-map-marker'></i>" + g[e] + "</a></li>"
                } else {
                    g[e] = "<li>" + g[e] + "</li>"
                }
            }
            return g;
        },
        showPosition: function() {
            var h = a(this),
            g = h.parent();
            g.addClass("active").siblings().removeClass("active");
            a(".navbar-custom-menu>ul>li.open").removeClass("open");
            var f = a(this).attr("href");
            var e = a(this).attr("dataid");
            var c = a.trim(a(this).text());
            var b = true;
            if (f == undefined || a.trim(f).length == 0) {
                return false
            }
            var i = a.GZSTLayout.positionWay(e);
            a(".breadcrumb").html(i.join(""));

            a("#iframe").attr("src", f);
            return false;
        },
        redirectPosition:function(mid){
           var item = $('#menuItem'+mid);
           var f = item.attr("href");
           var e = item.attr("dataid");
           var c = $.trim(item.text());
           var b = true;
           if (f == undefined || a.trim(f).length == 0) {
                return false
           }
           var i = a.GZSTLayout.positionWay(e);
           a(".breadcrumb").html(i.join(""));
           a("#iframe").attr("src", f);
           return false;
        },
        initEvent: function() {
            a(".menuItem").on("click", a.GZSTLayout.showPosition);
            a(".top-menu").click(function() {
                a(this).parent().addClass("wst-focus").siblings().removeClass("wst-focus");
                a.GZSTLayout.showSidebarMenu(a(this).attr("dataid"))
            });
            a(".top-menu")[0].click();
            a(".j-edit-pass").on("click", editPassBox);
            a(".j-logout").on("click", logout);
            a(".fullscreen").on("click",
            function() {
                if (!a(this).attr("fullscreen")) {
                    a(this).attr("fullscreen", "true");
                    a.GZSTLayout.requestFullscreen()
                } else {
                    a(this).removeAttr("fullscreen");
                    a.GZSTLayout.exitFullscreen()
                }
            });
            if(WST.conf.MESSAGE_BOX!=''){
                var msg = WST.conf.MESSAGE_BOX.split('||');
                for(var i=0;i<msg.length;i++){
                    WST.open({type: 1,
                      title: '系统提示',
                      shade: false,
                      area: ['340px', '215px'],
                      offset: 'rb',
                      time: 20000,
                      anim: 2,
                      content: "<div class='j-messsage-box'>"+msg[i]+"</div>",
                    })
                }
            }
            WST.getSysMessages(WST.conf.MSG_SHOP_GRANT);
            if(WST.conf.TIME_TASK=='1'){
                    setInterval(function(){
                        WST.getSysMessages(WST.conf.MSG_SHOP_GRANT);
                },10000);
            }
            WST.dropDownLayer("#toMsg",".j-dorpdown-layer");
            WST.dropDownLayer("#toUser",".j-dorpdown-layer");
        }
    };
    a(function() {
        a.GZSTLayout.initEvent();
    })
} (jQuery);
function logout() {
    var a = WST.msg("正在退出系统...", {
        icon: 16,
        time: 60000,
        offset: "200px"
    });
    $.post(WST.U("store/index/logout"), {},
    function(c, d) {
        layer.close(a);
        var b = WST.toJson(c);
        if (b.status == "1") {
            WST.msg(b.msg, {
                icon: 1,
                offset: "200px"
            },
            function() {
                location.href = WST.U("store/index/login")
            })
        } else {
            WST.msg(b.msg, {
                icon: 2,
                offset: "200px"
            })
        }
    })
}
WST.redirect = function(mid){
    $.GZSTLayout.redirectPosition(mid);
}
function editPassBox() {
    var a = WST.open({
        type: 1,
        title: "修改密码",
        shade: [0.6, "#000"],
        border: [0],
        content: $("#editPassBox"),
        area: ["510px", "250px"],
        btn: ["确定", "取消"],
        yes: function(c, b) {
            $("#editPassFrom").isValid(function(d) {
                if (d) {
                    var f = WST.getParams(".ipt");
                    var public_key=$('#token').val();
                    var exponent="10001";
                    var res = '';
                    if(WST.conf.IS_CRYPT=='1'){
                        var rsa = new RSAKey();
                        rsa.setPublic(public_key, exponent);
                        f.oldPass = rsa.encrypt($.trim(f.oldPass));
                        f.newPass = rsa.encrypt($.trim(f.newPass));
                        f.newPass2 = rsa.encrypt($.trim(f.newPass2));
                    }
                    var e = WST.msg("正在提交数据，请稍后...");
                    $.post(WST.U("store/users/passedit"), f,
                    function(h) {
                        layer.close(e);
                        var g = WST.toJson(h);
                        if (g.status == 1) {
                            WST.msg(g.msg, {
                                icon: 1
                            });
                            $('#editPassFrom')[0].reset();
                            layer.close(a);
                        } else {
                            WST.msg(g.msg, {
                                icon: 2
                            })
                        }
                    })
                }
            })
        }
    })
};