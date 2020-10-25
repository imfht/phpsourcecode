/*
 * @Author: Paco
 * @Date:   2017-02-07
 * @lastModify 2017-03-19
 * +----------------------------------------------------------------------
 * | jqadmin [ jq酷打造的一款懒人后台模板 ]
 * | Copyright (c) 2017 http://jqadmin.jqcool.net All rights reserved.
 * | Licensed ( http://jqadmin.jqcool.net/licenses/ )
 * | Author: Paco <admin@jqcool.net>
 * +----------------------------------------------------------------------
 */

layui.define(['jquery', 'form', 'layer', 'ajax', 'modal', 'jqmenu'], function(exports) {
    var $ = layui.jquery,
        layer = layui.layer,
        jqmenu = layui.jqmenu,
        ajax = layui.ajax,
        modal = layui.modal,
        menu = new jqmenu(),
        jqMain = function() {};

    /**
     *@todo 初始化方法
     */
    jqMain.prototype.init = function() {
        this.panelToggle();
        modal.init();
        menu.menuBind();
    }

    /**
     *@todo 绑定面板显示隐藏按钮单击事件
     */
    jqMain.prototype.panelToggle = function() {
        $('.panel-toggle').bind("click", function() {
            var obj = $(this).parent('.panel-heading').next('.panel-body');
            if (obj.css('display') == "none") {
                $(this).find('i').html('&#xe604;');
                obj.slideDown();
            } else {
                $(this).find('i').html('&#xe603;');
                obj.slideUp();
            }
        })
    }

    var main = new jqMain();
    main.init();
    exports('main', {});
});