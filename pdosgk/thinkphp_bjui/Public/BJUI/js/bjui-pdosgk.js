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
    $(document).on(BJUI.eventType.initUI, function(e) {
        var $box    = $(e.target)
        /* 自定义的权限显示 */
        var $priv = $box.find('[data-custom="priv"')

        $priv.each(function(i) {
            //如果是管理员, 则不处理
            if(PDOSGK.admin == true) return

            var $element = $(this),
                action    = $element.data('action')
            //判断当前action是否显示
            if($.inArray(action, PDOSGK.pri) ===  -1){
                //如果不显示, 则隐藏该标签
                $element.addClass('collapse');
            }

        });
    })
}(jQuery);