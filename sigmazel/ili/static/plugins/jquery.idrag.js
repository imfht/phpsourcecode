~function($, window, undefined) {
    var win = $(window),
        doc = $(document),
        ie = $.browser.msie,
        version = parseInt($.browser.version),
        ie6 = ie && version < 7,
        dialog,
        dialogZindex = 1e4;
    
    drag = function(target, options) {
        return new drag.fn.init(target, options);
    };

    drag.fn = drag.prototype = {
        init: function(options) {

            var that           = this,
            ie6fix             = '(document.documentElement || document.body)';
            this.target        = $(options.target);
            options            = options || {};
            this.root          = options.root ? $(options.root) : this.target;
            this.min           = options.min;
            this.max           = options.max;
            this.start         = options.start;
            this.move          = options.move;
            this.end           = options.end;
            this.fixed         = options.fixed;
            this.startPosition = {};
            this.movePosition  = {};
            
            var _down = function(e) {
                e = that.fixEvent(e);
                that.startPosition = {
                    x: e.layerX,
                    y: e.layerY
                };
                that.start && that.start(that.startPosition);
                doc.bind('mousemove', _move)
                    .bind('mouseup', _end);
                this.setCapture && this.setCapture(false); //ie 鼠标移出浏览器依然可以拖拽
                e.preventDefault(); //阻止默认行为，chrome的拖拽选择文字行为
                return false;
            },
            _move = function(e) {
                e = that.fixEvent(e);

                that.movePosition = {
                    x: e.clientX - that.startPosition.x,
                    y: e.clientY - that.startPosition.y
                };
                that.limit();
                if (that.fixed && ie6) { //IE6 fixed
                    that.root[0].style.setExpression('left', 'eval(' + ie6fix + '.scrollLeft + ' + (that.movePosition.x - win.scrollLeft()) + ') + "px"');
                    that.root[0].style.setExpression('top', 'eval(' + ie6fix + '.scrollTop + ' + (that.movePosition.y - win.scrollTop()) + ') + "px"');
                } else {
                    that.root.css({
                        left: that.movePosition.x,
                        top: that.movePosition.y
                    });
                }
                that.move && that.move(that.movePosition);
                return false;
            },
            _end = function() {
                doc.unbind('mousemove', _move)
                    .unbind('mouseup', _end);
                that.end && that.end(that.movePosition);
                return false;
            };

            this.target.bind('mousedown', _down).bind('mouseup', function() {
                this.releaseCapture && this.releaseCapture();
            });
        },
        fixEvent: function(e) {
            if (!e.pageX) {
                e.pageX = e.clientX + win.scrollTop();
                e.pageY = e.clientY + win.scrollLeft();
            }
            if (!e.layerX) {
                e.layerX = e.clientX - parseInt(this.root.css('left'));
                e.layerY = e.clientY - parseInt(this.root.css('top'));
            }
            return e;
        },
        /**
         * 限制
         */
        limit: function() {
            if (this.min !== undefined) {
                this.movePosition = {
                    x: Math.max(this.min.x, this.movePosition.x),
                    y: Math.max(this.min.y, this.movePosition.y)
                };
            }
            if (this.max !== undefined) {
                this.movePosition = {
                    x: Math.min(this.max.x, this.movePosition.x),
                    y: Math.min(this.max.y, this.movePosition.y)
                };
            }
        }
    };
    
    drag.fn.init.prototype = drag.fn;
    window.idrag = $.drag = drag;
}(jQuery, this);