(function ($, owner) {
    owner.frame = function () {
        dux.init();
    };
}(jQuery, window.base = {}));

(function ($, owner) {
    /**
     * 分类处理
     */
    owner.category = function ($el) {
        var $layout = $($el);
        var switchClass = function (i) {
            $layout.children('.class-first').find('li').removeClass('active');
            $layout.children('.class-first').children('li:eq(' + i + ')').addClass('active');
            $layout.children('.class-son').find('.son-item').removeClass('active');
            $layout.children('.class-son').children('.son-item:eq(' + i + ')').addClass('active');
        };

        $layout.children('.class-first').on('click', 'li', function () {
            var i = $(this).index();
            switchClass(i);
        });
    };


}(jQuery, window.common = {}));


(function ($, owner) {
    /**
     * 商品属性选择
     */
    owner.sku = function ($el) {
        var $layout = $($el);
        $layout.on('click', 'a', function () {
            $(this).parent().find('a').removeClass('active');
            $(this).addClass('active');
            var sku = [];
            $layout.find('a.active').each(function () {
                var data = $(this).data();
                sku.push(data.id + ':' + data.value);
            });
            var key = sku.join(',');
            window.location = productJson[key]['url'];
        });
    };

    /**
     * 商品数量
     */
    owner.count = function ($el) {
        var $layout = $($el), $down = $layout.find('.down'), $up = $layout.find('.up'), $input = $layout.find('input'),
            maxCount = parseInt($layout.data('count')), callback = $layout.data('callback'),
            info = $layout.data('info');

        $down.click(function () {
            app.debug('xxx');
            var curCount = parseInt($input.val());
            var num = curCount - 1;
            if (num <= 1) {
                num = 1;
            }
            upData(num);
        });
        $up.click(function () {
            var curCount = parseInt($input.val());
            var num = curCount + 1;
            if (maxCount && num >= maxCount) {
                num = maxCount;
            }
            upData(num);

        });
        $input.blur(function () {
            var num = parseInt($(this).val());
            if (num <= 1 || num >= maxCount) {
                $input.val(1);
            }
            if (callback != undefined && callback != '') {
                window[callback](num, info, $input);
            }
        });

        function upData(num) {
            if (callback != undefined && callback != '') {
                window[callback](num, info, $input);
            } else {
                $input.val(num);
            }
        }
    };
    /**
     * 加入购物车
     */
    owner.addCart = function ($el) {
        var $layout = $($el), url = $layout.data('url'), params = $layout.data('params'), $count = $($layout.data('count')), callback = $layout.data('callback');
        Do('dialog', function () {
            $layout.click(function () {
                app.ajax({
                    url: url,
                    data: $.extend({}, params, {qty: $count.val()}),
                    type: 'post',
                    success: function (msg, url) {
                        if(url) {
                            window.location.href = url;
                            return true;
                        }
                        if (callback != undefined && callback != '') {
                            window[callback](msg, url);
                        }
                        app.success(msg);
                    },
                    error: function (msg) {
                        app.error(msg);
                    }
                });
            });
        });
    };
    /**
     * 收藏商品
     */
    owner.follow = function ($el) {
        var $layout = $($el), url = $layout.data('url'), params = $layout.data('params'), $icon = $layout.find('[data-icon]');
        Do('dialog', function () {
            $layout.click(function () {
                app.ajax({
                    url: url,
                    data: params,
                    type: 'post',
                    success: function (msg, url) {
                        changeIcon();
                        app.success(msg);
                    },
                    error: function (msg) {
                        app.error(msg);
                    }
                });
            });


            var changeIcon = function () {
                $icon.removeClass('fa-heart-o fa-heart');
                if($layout.data('status')) {
                    $icon.addClass('fa-heart-o');
                    $layout.data('status', 0);
                }else {
                    $icon.addClass('fa-heart');
                    $layout.data('status', 1);
                }
            }
        });

    };

}(jQuery, window.shop = {}));

