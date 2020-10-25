/* ========================================================================
 * Bootstrap: popover.js v3.0.0
 * http://twbs.github.com/bootstrap/javascript.html#popovers
 * ========================================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */


+function ($) { "use strict";

    // POPOVER PUBLIC CLASS DEFINITION
    // ===============================
	/**
	 * 原生Bootstrap的Popover组件
	 * @class Popover
	 * @constructor
	 */
    var Popover = function (element, options) {
        this.init('popover', element, options);
    };

    if (!$.fn.tooltip) throw new Error('Popover requires tooltip.js');

    Popover.DEFAULTS = $.extend({} , $.fn.tooltip.Constructor.DEFAULTS, {
        /**
         * 弹出框位置
         * @type Boolean
         * @type string | function(top | bottom | left | right | auto)
         * @default right
         */
        placement: 'right'
        /**
         * 触发弹出框的事件
         * @type  string | function(click | hover | focus | manual)
         * @default click
         */
        , trigger: 'click'
        /**
         * 弹出框内容
         * @type  string
         * @default ''
         */
        , content: ''
        /**
         * 弹出框标题
         * @type  string
         * @default ''
         */
        , title: ''
        /**
         * 弹出框的动画时间
         * @type number | object(delay: { show: 500, hide: 100 })
         * @default 0
         */
        ,delay: 0
        /**
         * 弹出框的父容器
         * @type  string | false
         * @default false
         */
        ,container: false
        , template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });


    // NOTE: POPOVER EXTENDS tooltip.js
    // ================================

    Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype);

    Popover.prototype.constructor = Popover;

    Popover.prototype.getDefaults = function () {
        return Popover.DEFAULTS;
    };

    Popover.prototype.setContent = function () {
        var $tip    = this.tip();
        var title   = this.getTitle();
        var content = this.getContent();

        $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title);
        $tip.find('.popover-content')[this.options.html ? 'html' : 'text'](content);

        $tip.removeClass('fade top bottom left right in');

        // IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
        // this manually by checking the contents.
        if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide();
    };

    Popover.prototype.hasContent = function () {
        return this.getTitle() || this.getContent();
    };

    Popover.prototype.getContent = function () {
        var $e = this.$element;
        var o  = this.options;

        return $e.attr('data-content')
            || (typeof o.content == 'function' ?
                o.content.call($e[0]) :
                o.content);
    };

    Popover.prototype.arrow = function () {
        return this.$arrow = this.$arrow || this.tip().find('.arrow');
    };

    Popover.prototype.tip = function () {
        if (!this.$tip) this.$tip = $(this.options.template);
        return this.$tip;
    };


    // POPOVER PLUGIN DEFINITION
    // =========================

    var old = $.fn.popover;

    $.fn.popover = function (option) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('bs.popover');
            var options = typeof option == 'object' && option;

            if (!data) $this.data('bs.popover', (data = new Popover(this, options)));
            if (typeof option == 'string') data[option]();
        });
    };

    $.fn.popover.Constructor = Popover;


    // POPOVER NO CONFLICT
    // ===================

    $.fn.popover.noConflict = function () {
        $.fn.popover = old;
        return this;
    };

}(window.jQuery);