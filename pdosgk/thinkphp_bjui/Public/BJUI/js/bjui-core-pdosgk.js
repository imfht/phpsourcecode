/*!
 * B-JUI  v1.2 (http://b-jui.com)
 * Git@OSC (http://git.oschina.net/xknaan/B-JUI)
 * Copyright 2014 K'naan (xknaan@163.com).
 * Licensed under Apache (http://www.apache.org/licenses/LICENSE-2.0)
 */

/* ========================================================================
 * B-JUI: bjui-plugins.js  v1.2
 * @author K'naan (xknaan@163.com)
 * http://git.oschina.net/xknaan/B-JUI/blob/master/BJUI/js/bjui-plugins.js
 * ========================================================================
 * Copyright 2014 K'naan.
 * Licensed under Apache (http://www.apache.org/licenses/LICENSE-2.0)
 * ======================================================================== */

+function ($) {
    'use strict';
    var PDOSGK = {
        admin        : true,  //是否是管理员
        pri          : '',   //传入当前用户的权限数组
        init: function(options) {
            var op = $.extend({}, options)
            this.admin = op.admin || false
            if (op.pri) this.pri = op.pri
        },
    }
    window.PDOSGK = PDOSGK
}(jQuery);