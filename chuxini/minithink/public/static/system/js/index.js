/*
 * @Author: Paco
 * @Date:   2017-01-31
 * @lastModify 2017-03-19
 * +----------------------------------------------------------------------
 * | jqadmin [ jq酷打造的一款懒人后台模板 ]
 * | Copyright (c) 2017 http://jqadmin.jqcool.net All rights reserved.
 * | Licensed ( http://jqadmin.jqcool.net/licenses/ )
 * | Author: Paco <admin@jqcool.net>
 * +----------------------------------------------------------------------
 */

layui.define(['jquery', 'elem', 'jqmenu', 'layer'], function(exports) {
    var $ = layui.jquery,
        element = layui.elem(),
        menu = layui.jqmenu,
        layer = layui.layer,
        oneMenu = new menu();
    jqIndex = function() {};
    /**
     *@todo 初始化方法
     */
    jqIndex.prototype.init = function(options) {

        oneMenu.init('menu-tpl');
        this.showMenu();
        this.refresh();

        $('.my-tips').click(function() {
            var l = layer.open({
                type: 1,
                title: "",
                shade: false,
                shadeClose: false,
                area: ['auto', 'auto'],
                content: $('.my-tip')
            });
        })
    }

    /**
     *@todo 绑定刷新按钮单击事件
     */
    jqIndex.prototype.refresh = function() {
        $('.fresh-btn').bind("click", function() {
            $('.jqadmin-body .layui-show').children('iframe')[0].contentWindow.location.reload(true);
        })
    }

    /**
     *@todo 绑定左侧菜单显示隐藏按钮单击事件
     */
    jqIndex.prototype.showMenu = function() {
        $('.menu-type').bind("click", function() {
            if ($(window).width() < 450) {
                $('.jqadmin-main-menu .layui-nav').show();
            }
            var type = parseInt($(this).data('type'));
            oneMenu.menuShowType($(this).data('type'));
            if (type >= 3) type = 0;
            $(this).data('type', type + 1);

        })

        $('.menu-btn').click(function() {
            oneMenu.showLeftMenu($(this));
        })
    }

    var index = new jqIndex();
    index.init();
    exports('index', {});
});