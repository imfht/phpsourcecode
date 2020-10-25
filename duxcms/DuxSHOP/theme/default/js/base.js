(function ($, owner) {
    owner.frame = function () {
        dux.init();
    };
}(jQuery, window.base = {}));

(function ($, owner) {
    /**
     * 用户信息
     * @param $el
     * @param config
     */
    owner.userInfo = function ($el, config) {
        app.ajax({
            url: config.url,
            type: 'post',
            data : {
               action : window.location.href
            },
            success: function (html) {
                $($el).html(html);
            },
            error: function (html) {
                $($el).html(html);
            }
        });
    };

    /**
     * 购物车
     */
    owner.cart = function ($el, config) {
        $($el).on('mouseover mouseleave', function (event) {
            if (event.type == "mouseover") {
                $(this).addClass('open');
            } else if (event.type == "mouseleave") {
                $(this).removeClass('open');
            }
        });
        $($el).on('click', '[data-del]', function () {
            app.ajax({
                url: $($el).data('urlDel'),
                data: {
                    rowid: $(this).data('del')
                },
                type: 'post',
                success: function () {
                    owner.getCart($el);
                },
                error: function (msg) {
                    app.error(msg);
                }
            });
        });
        owner.getCart($el, true);
    };
    /**
     * 刷新购物车
     * @param $el
     */
    owner.getCart = function ($el, open) {
        var config = $($el).data();
        app.ajax({
            url: config.url,
            type: 'get',
            data: {},
            login: function (msg) {
                $($el).find('[data-list]').html('<div class="tip">' + msg + '</div>');
                $($el).find('[data-num]').text(0);
                $($el).find('[data-price]').text('0.00');
            },
            success: function (data) {
                var html = '';
                if(!data.list) {
                    return false;
                }
                $.each(data.list, function (k, v) {
                    html += '<li class="uk-clearfix">' +
                        '<div class="img">' +
                        '<a href="' + v.url + '" target="_blank"><img src="' + v.image + '"></a> ' +
                        '</div> ' +
                        '<div class="title"> ' +
                        '<a href="' + v.url + '" target="_blank">' + v.name + '</a> ' +
                        '</div> ' +
                        '<div class="info"> ' +
                        '<div class="price">￥' + v.price + '×' + v.qty + '</div> ' +
                        '<div class="opt"> ' +
                        '<a href="javascript:;" data-del="' + k + '">删除</a> ' +
                        '</div> ' +
                        '</div> ' +
                        '</li>';
                });
                $($el).find('[data-num]').text(data.items);
                $($el).find('[data-price]').text(data.total);
                $($el).find('[data-list]').html(html);
                if(!open) {
                    UIkit.dropdown($($el).find('.uk-dropdown')).show();
                }
            },
            error: function (msg) {
                $($el).find('[data-list]').html('<div class="tip">' + msg + '</div>');
                $($el).find('[data-num]').text(0);
                $($el).find('[data-price]').text('0.00');
            }

        });
    };

    /**
     * 树形分类
     */
    owner.tree = function ($el) {
        var $active = $($el).find('.active');
        $active.parents('li').addClass('open');
        $($el).on('click', '.title', function () {
            if ($(this).parent('li').hasClass('open')) {
                $(this).parent('li').removeClass('open');
            } else {
                $(this).parent('li').addClass('open');
            }
        });
    }

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
        var $layout = $($el), $down = $layout.find('.down'), $up = $layout.find('.up'), $input = $layout.find('input'), maxCount = parseInt($layout.data('count')), callback = $layout.data('callback'), info = $layout.data('info');

        $down.click(function () {
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
                        common.getCart('.cart-body');
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
    /**
     * 图片展示
     */
    owner.show = function ($el) {

        var $items = $($el).find('.items'),$preview = $($el).find('.preview');

        $items.on('mousemove', 'li', function () {
            $items.find('li').removeClass('active');
            $(this).addClass('active');
            $preview.find('img').attr('src', $(this).find('img').attr('src'));


        });

    }
}(jQuery, window.shop = {}));
