;(function ($) {
    var topMenu = function (ele, opt) {
        var instance = this;
        instance.$element = ele;
        instance.defaults = {
            'menu_container': '.top-menu'
        };
        instance.options = $.extend({}, instance.defaults, opt);

        instance.init = function () {
            instance.requestMenuData();
        };

        instance.requestMenuData = function () {


            var target = $(instance.options.menu_container).attr('href') || $(instance.options.menu_container).attr('url');
            if (target !== undefined && target !== '' && target !== '#') {
                $.post(target, {}, function (data) {
                    if (data.status === 1) {
                        if (data.info.length < 1) {
                            $(instance.options.menu_container).html('');
                            return;
                        }

                        var ulContent = new Array();
                        instance.loadSubMenu(ulContent, data.info, 0);
                        $(instance.options.menu_container).html(ulContent.join(''));
                    } else {
                        cigoLayer.msg(data.info, {icon: 5});
                    }
                });
            }
        };

        instance.loadSubMenu = function (ulContent, pList, level) {
            ulContent.push('<ul ' + ((level == 0) ? 'class="menu-list-top" ' : ' ') + 'style="z-index: ' + (level + 1) + ';">');
            $.each(pList, function (key, dataItem) {
                ulContent.push(
                    '<li id="menu_' + dataItem['id'] + '" data-ids="' + dataItem['path'] + dataItem['id'] + '"' +
                    (('subList' in dataItem) ? '" class="has-sub"' : ' ') +
                    '>' +
                    '   <a title="' + dataItem['title'] + '" ' +
                    '       ' + (('' != dataItem['url']) ?
                    '   href="' + dataItem['url'] + '" target="' + dataItem['target'] + '"' :
                    '   href="#" onclick="return false;"') +
                    '   >' +
                    '       <i class="cigo-iconfont ' + dataItem['icon'] + '"></i>' +
                    '       <span>' + dataItem['title'] + '</span>' +
                    '       <span class="label pull-right ' + dataItem['label_class'] + '"></span>' +
                    '   </a>'
                );
                if ('subList' in dataItem)
                    instance.loadSubMenu(ulContent, dataItem['subList'], level + 1);
                ulContent.push(
                    '</li>'
                );
            });
            ulContent.push('</ul>');
        };
    };

//定义插件
    $.fn.topMenu = function (options) {
        var menuInstance = new topMenu(this, options);
        //进行初始化操作
        menuInstance.init();

        return menuInstance;
    };
})
(jQuery);
