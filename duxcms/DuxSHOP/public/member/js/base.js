/**
 * 页面框架
 */
(function ($, owner) {
    owner.frame = function () {
        dux.init();
    };
}(jQuery, window.base = {}));



(function ($, owner) {
    /**
     * 商品数量
     */
    owner.num = function ($el) {
        var $layout = $($el), $down = $layout.find('.down'), $up = $layout.find('.up'), $input = $layout.find('input'),
            maxCount = parseInt($layout.data('count')), callback = $layout.data('callback'),
            info = $layout.data('info');

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
     * 统计购物车
     * @param $el
     */
    owner.count = function ($el) {
        var $layout = $($el), $list = $($el).find('[data-list]');
        window.count = function (num, info, $input) {
            app.ajax({
                url: $layout.data('urlNum'),
                data: $.extend({}, info, {qty: num}),
                type: 'post',
                loading : true,
                success: function (data) {
                    countCart(data.info);
                    $input.parents('[data-item]').find('[data-item-price]').html(data.row.total);
                    $input.parents('[data-item]').find('[data-item-num]').html(data.row.qty);
                    $input.val(num);
                },
                error: function (msg) {
                    app.error(msg);
                }
            });

        };

        $layout.on('click', '[data-item-del]', function () {
            var $li = $(this).parents('[data-item]');
            app.ajax({
                url: $layout.data('urlDel'),
                data: {
                    rowid: $(this).data('itemDel')
                },
                loading : true,
                type: 'post',
                success: function (data) {
                    $li.remove();
                    countCart(data.info);
                },
                error: function (msg) {
                    app.error(msg, url);
                }
            });
        });

        $layout.on('click', '[data-del]', function () {
            var cartCount = $list.find('input[type=checkbox]:checked').length;
            if (cartCount <= 0) {
                app.error('请选择删除商品');
                return;
            }
            var keys = [];
            var ids = [];
            $list.find('input[type=checkbox]:checked').each(function () {
                if ($(this).data('storeCheckbox')) {
                    return true;
                }
                keys.push($(this).val());
                ids.push('#' + $(this).val());
            });
            app.ajax({
                url: $layout.data('urlDel'),
                type: 'post',
                loading : true,
                data: {
                    rowid: keys.join(',')
                },
                success: function (data, url) {
                    $(ids.join(',')).remove();
					$list.find('.dux-pro-list').each(function () {
						if(!$(this).find('.dux-pro-item').length){
							$(this).remove();
						}
					});
                    countCart(data.info);
                },
                error: function (msg, url) {
                    app.error(msg, url);
                }
            });
        });


        $layout.on('click', '[data-select-all]', function () {
            if ($(this).prop('checked')) {
                $list.find('input[type=checkbox]').prop("checked", true);
            } else {
                $list.find('input[type=checkbox]').prop("checked", false);
            }
            numCart();
        });

        $('[data-header-nav]').on('click', function () {
			str = $(this).find('a').text().replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'');
			$(this).find('a').text(str=='编辑' ? '完成' : '编辑');
            $layout.find('[data-show]').toggleClass('uk-hidden');
            $layout.find('[data-edit]').toggleClass('uk-hidden');
        });

        $list.on('click', 'input[type=checkbox]', function () {
            numCart();
        });

        $list.on('click', '[data-store-checkbox]', function () {
            if ($(this).prop('checked')) {
                $(this).parents('[data-store]').find('input[type=checkbox]').prop("checked", true);
            } else {
                $(this).parents('[data-store]').find('input[type=checkbox]').prop("checked", false);
            }
            numCart();
        });


        var numCart = function () {
            var checkedCount = 0;
            var uncheck = [];
            var checked = [];
            var num = 0;
            $list.find('input[type=checkbox]').each(function () {
                if ($(this).data('storeCheckbox')) {
                    return true;
                }
                num += 1;
                if ($(this).prop('checked')) {
                    checked.push($(this).val());
                    checkedCount += 1;
                } else {
                    uncheck.push($(this).val());
                }
            });
            if (num && num == checkedCount) {
                $layout.find('[data-select-all]').prop("checked", true);
            } else {
                $layout.find('[data-select-all]').prop("checked", false);
            }
            app.ajax({
                loading : true,
                url: $layout.data('urlChecked'),
                data: {
                    'checked': checked.join(','),
                    'uncheck': uncheck.join(',')
                },
                type: 'post',
                success: function (data) {
                    countCart(data.info);
                },
                error: function (msg) {
                    app.error(msg);
                }
            });
        };

        var countCart = function (info) {
            $layout.find('[data-decimal]').text(info.total);
            $layout.find('[data-total]').text(info.items);
            $layout.find('[data-decimal-checked]').text(info.checked_total);
            $layout.find('[data-total-checked]').text(info.checked_items);
            if (info.items == 0) {
                window.location.reload();
            }
        };

        var $popup = $('[data-popup]');
        var rowId = 0;
        var productJson = [];
        $layout.on('click', '[data-spec]', function () {
            var url = $(this).data('spec');
            var rowid = $(this).data('rowid');
            Do('tpl', function () {
                app.ajax({
                    url: url,
                    type: 'post',
                    loading : true,
                    success: function (data) {
                        productJson = data.skuList;
                        rowId = rowid;
                        laytpl($('[data-spec-tpl]').html()).render(data, function (html) {
                            $('body').addClass('uk-dimmer-active');
                            $popup.html(html);
                        });
                    },
                    error: function (msg) {
                        app.error(msg);
                    }
                });
            });
        });

        var popupClose = function () {
            $popup.html('');
            $('body').removeClass('uk-dimmer-active');
        };

        $popup.on('click', '.popup-close', function () {
            popupClose();
        });
        $popup.on('click', '[data-spec-list] a', function () {
            $(this).parent().find('a').removeClass('active');
            $(this).addClass('active');
        });
        $popup.on('click', '[data-spec-submit]', function () {
            var sku = [];
            var name = [];
            $popup.find('a.active').each(function () {
                var data = $(this).data();
                name.push(data.value);
                sku.push(data.id + ':' + data.value);
            });
            var key = sku.join(',');

            var id = productJson[key]['products_id'];

            if (!id) {
                app.error('请选择商品规格');
                return false;
            }
            var appName = $(this).data('app');
            app.ajax({
                url: (rootUrl ? rootUrl + '/' : '') + (roleName ? roleName : '') + '/' + appName + '/' + appName + '/addCart',
                type: 'post',
                data: {pro_id: id, row_id: rowId},
                loading : true,
                success: function (data) {
                    window.location.reload();
                },
                error: function (msg) {
                        dialog.msg(msg);
                }
            });

            $popup.html('');

        });
    };
}(jQuery, window.cart = {}));


