;(function($, window, undefined) {
    'use strict';

    /* Check jquery */
    if(typeof($) === 'undefined') throw new Error('ZUI requires jQuery');

    // ZUI shared object
    if(!$.adminLTE_zui) $.adminLTE_zui = function(obj) {
        if($.isPlainObject(obj)) {
            $.extend($.adminLTE_zui, obj);
        }
    };

    var MOUSE_BUTTON_CODES = {
        all: -1,
        left: 0,
        middle: 1,
        right: 2
    };

    var lastUuidAmend = 0;
    $.adminLTE_zui({
        uuid: function() {
            return(new Date()).getTime() * 1000 + (lastUuidAmend++) % 1000;
        },

        callEvent: function(func, event, proxy) {
            if($.isFunction(func)) {
                if(proxy !== undefined) {
                    func = $.proxy(func, proxy);
                }
                var result = func(event);
                if(event) event.result = result;
                return !(result !== undefined && (!result));
            }
            return 1;
        },

        clientLang: function() {
            var lang;
            var config = window.config;
            if(typeof(config) != 'undefined' && config.clientLang) {
                lang = config.clientLang;
            }
            if(!lang) {
                var hl = $('html').attr('lang');
                lang = hl ? hl : (navigator.userLanguage || navigator.userLanguage || 'zh_cn');
            }
            return lang.replace('-', '_').toLowerCase();
        },

        strCode: function(str) {
            var code = 0;
            if(str && str.length) {
                for(var i = 0; i < str.length; ++i) {
                    code += i * str.charCodeAt(i);
                }
            }
            return code;
        },

        getMouseButtonCode: function(mouseButton) {
            if(typeof mouseButton !== 'number') {
                mouseButton = MOUSE_BUTTON_CODES[mouseButton];
            }
            if(mouseButton === undefined || mouseButton === null) mouseButton = -1;
            return mouseButton;
        }
    });

    $.fn.callEvent = function(name, event, model) {
        var $this = $(this);
        var dotIndex = name.indexOf('.adminLTE_zui.');
        var shortName = dotIndex < 0 ? name : name.substring(0, dotIndex);
        var e = $.Event(shortName, event);

        if((model === undefined) && dotIndex > 0) {
            model = $this.data(name.substring(dotIndex + 1));
        }

        if(model && model.options) {
            var func = model.options[shortName];
            if($.isFunction(func)) {
                e.result = $.adminLTE_zui.callEvent(func, e, model);
            }
        }
        $this.trigger(e);
        return e;
    };

    $.fn.callComEvent = function(component, eventName, params) {
        if (params !== undefined && !$.isArray(params)) {
            params = [params];
        }
        var $this = this;
        var result = $this.triggerHandler(eventName, params);

        var eventCallback = component.options[eventName];
        if (eventCallback) {
            result = eventCallback.apply(component, params);
        }
        return result;
    };
}(jQuery, window, undefined));

/* ========================================================================
 * ZUI: string.js
 * String Polyfill.
 * http://adminLTE_zui.sexy
 * ========================================================================
 * Copyright (c) 2014-2016 cnezsoft.com; Licensed MIT
 * ======================================================================== */


(function() {
    'use strict';

    /**
     * Format string with argument list or object
     * @param  {object | arguments} args
     * @return {String}
     */
    if(!String.prototype.format) {
        String.prototype.format = function(args) {
            var result = this;
            if(arguments.length > 0) {
                var reg;
                if(arguments.length <= 2 && typeof(args) == 'object') {
                    for(var key in args) {
                        if(args[key] !== undefined) {
                            reg = new RegExp('(' + (arguments[1] ? arguments[1].replace('0', key) : '{' + key + '}') + ')', 'g');
                            result = result.replace(reg, args[key]);
                        }
                    }
                } else {
                    for(var i = 0; i < arguments.length; i++) {
                        if(arguments[i] !== undefined) {
                            reg = new RegExp('({[' + i + ']})', 'g');
                            result = result.replace(reg, arguments[i]);
                        }
                    }
                }
            }
            return result;
        };
    }

    /**
     * Judge the string is a integer number
     *
     * @access public
     * @return bool
     */
    if(!String.prototype.isNum) {
        String.prototype.isNum = function(s) {
            if(s !== null) {
                var r, re;
                re = /\d*/i;
                r = s.match(re);
                return(r == s) ? true : false;
            }
            return false;
        };
    }

    if(!String.prototype.endsWith) {
        String.prototype.endsWith = function(searchString, position) {
            var subjectString = this.toString();
            if(position === undefined || position > subjectString.length) {
                position = subjectString.length;
            }
            position -= searchString.length;
            var lastIndex = subjectString.indexOf(searchString, position);
            return lastIndex !== -1 && lastIndex === position;
        };
    }

    if(!String.prototype.startsWith) {
        String.prototype.startsWith = function(searchString, position) {
            position = position || 0;
            return this.lastIndexOf(searchString, position) === position;
        };
    }

    if(!String.prototype.includes) {
        String.prototype.includes = function() {
            return String.prototype.indexOf.apply(this, arguments) !== -1;
        };
    }

})();

/* ========================================================================
 * ZUI: messager.js
 * http://zui.sexy
 * ========================================================================
 * Copyright (c) 2014-2016 cnezsoft.com; Licensed MIT
 * ======================================================================== */

(function($, window, undefined) {
    'use strict';

    var id = 0;
    var template = '<div class="messager messager-{type} {placement}" style="display: none"><div class="messager-content"></div><div class="messager-actions"></div></div>';
    var defaultOptions = {
        type: 'default',
        placement: 'top',
        time: 4000,
        parent: 'body',
        // clear: false,
        icon: null,
        close: true,
        // actions: [{icon, name, action, title}],
        // contentClass: null,
        // cssClass: null,
        // onAction: function,
        fade: true,
        scale: true
    };
    var all = {};

    var Messager = function(message, options) {
        if($.isPlainObject(message)) {
            options = message;
            message = options.message;
        }

        var that = this;
        options = that.options = $.extend({}, defaultOptions, options);
        
        that.id = options.id || (id++);
        var oldMessager = all[that.id];
        if(oldMessager) oldMessager.destroy();
        all[that.id] = that;
        that.message = (options.icon ? '<i class="icon-' + options.icon + ' icon"></i> ' : '') + message;

        that.$ = $(template.format(options)).toggleClass('fade', options.fade).toggleClass('scale', options.scale).attr('id', 'messager-' + that.id);

        if(options.cssClass) that.$.addClass(options.cssClass);

        var hasCloseAction = false;
        var $actions = that.$.find('.messager-actions');
        var appendAction = function(action) {
            var $btn = $('<button type="button" class="action action-' + action.name + '"/>');
            if(action.name === 'close') $btn.addClass('close');
            if(action.html !== undefined) {
                $btn.html(action.html);
            }
            if(action.icon !== undefined) {
                $btn.append('<i class="action-icon icon-' + action.icon + '"/>');
            }
            if(action.text !== undefined) {
                $btn.append('<span class="action-text">' + action.text + '</span>');
            }
            if(action.tooltip !== undefined) {
                $btn.attr('title', action.tooltip).tooltip();
            }
            $btn.data('action', action);
            $actions.append($btn);
        };
        if(options.actions) {
            $.each(options.actions, function(idx, action) {
                if(action.name === undefined) action.name = idx;
                if(action.name == 'close') hasCloseAction = true;
                appendAction(action);
            });
        }
        if(!hasCloseAction && options.close) {
            appendAction({name: 'close', html: '&times;'});
        }

        that.$.on('click', '.action', function(e) {
            var action = $(this).data('action'), result;
            if(options.onAction) {
                result = options.onAction.call(this, action.name, action, that);
                if(result === false) return;
            }
            if($.isFunction(action.action)) {
                result = action.action.call(this, that);
                if(result === false) return;
            }
            that.hide();
            e.stopPropagation();
        });

        that.$.on('click', function(e) {
            if(options.onAction) {
                var result = options.onAction.call(this, 'content', null, that);
                if(result === true) that.hide();
            }
        });

        var $content = that.$.find('.messager-content').html(that.message);
        if(options.contentClass) $content.addClass(options.contentClass);

        that.$.data('zui.messager', that);

        if(options.show && that.message !== undefined) {
            that.show();
        }
    };

    Messager.prototype.update = function(message, newOptions) {
        var that = this;
        var options = that.options;
        that.$.removeClass('messager-' + options.type);
        if(newOptions) {
            options = $.extend(options, newOptions);
        }
        that.$.addClass('messager-' + options.type);
        if(message) {
            that.message = (options.icon ? '<i class="icon-' + options.icon + ' icon"></i> ' : '') + message;
            that.$.find('.messager-content').html(that.message);
        }
    };

    Messager.prototype.show = function(message, callback) {
        var that = this,
            options = this.options;

        if($.isFunction(message)) {
            var oldCallback = callback;
            callback = message;
            if(oldCallback !== undefined) {
                message = oldCallback;
            }
        }

        if(that.isShow) {
            that.hide(function() {
                that.show(message, callback);
            });
            return;
        }

        if(that.hiding) {
            clearTimeout(that.hiding);
            that.hiding = null;
        }

        that.update(message);

        var placement = options.placement;
        var $parent = $(options.parent);
        var $holder = $parent.children('.messagers-holder.' + placement);
        if(!$holder.length) {
            $holder = $('<div/>').attr('class', 'messagers-holder ' + placement).appendTo($parent);
        }
        $holder.append(that.$);
        if(placement === 'center') {
            var offset = $(window).height() - $holder.height();
            $holder.css('top', Math.max(-offset, offset/2));
        }

        that.$.show().addClass('in');

        if(options.time) {
            that.hiding = setTimeout(function() {
                that.hide();
            }, options.time);
        }

        that.isShow = true;
        callback && callback();
        return that;
    };

    Messager.prototype.hide = function(callback, immediately) {
        if(callback === true) {
            immediately = true;
            callback = null;
        }
        var that = this;
        if(that.$.hasClass('in')) {
            that.$.removeClass('in');
            var removeMessager = function() {
                var $parent = that.$.parent();
                that.$.detach();
                if(!$parent.children().length) $parent.remove();
                callback && callback(true);
            };
            if(immediately) removeMessager();
            else setTimeout(removeMessager, 200);
        } else {
            callback && callback(false);
        }

        that.isShow = false;
    };

    Messager.prototype.destroy = function() {
        var that = this;
        that.hide(function()
        {
            that.$.remove();
            that.$ = null;
        }, true);
        delete all[that.id];
    };

    Messager.all = all;

    var hideMessage = function() {
        $('.messager').each(function() {
            var msg = $(this).data('zui.messager');
            if(msg && msg.hide) msg.hide(true);
        });
    };

    var showMessage = function(message, options) {
        if(typeof options === 'string') {
            options = {
                type: options
            };
        }
        options = $.extend({}, options);
        if(options.id === undefined) hideMessage();
        var msg = all[options.id] || new Messager(message, options);
        msg.show();
        return msg;
    };

    var getOptions = function(options) {
        return(typeof options === 'string') ? {
            placement: options
        } : options;
    };

    var zuiMessager = {
        show: showMessage,
        hide: hideMessage
    };

    $.each({
        primary  : 0,
        success  : 'ok-sign',
        info     : 'info-sign',
        warning  : 'warning-sign',
        danger   : 'exclamation-sign',
        important: 0,
        special  : 0
    }, function(name, icon){
        zuiMessager[name] = function(message, options) {
            return showMessage(message, $.extend({
                type: name,
                icon: icon || null
            }, getOptions(options)));
        };
    });

    $.adminLTE_zui({
        Messager: Messager,
        showMessager: showMessage,
        messager: zuiMessager
    });
}(jQuery, window, undefined));