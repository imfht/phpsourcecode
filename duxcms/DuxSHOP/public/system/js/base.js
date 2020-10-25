/**
 * 页面框架
 */
(function ($, owner) {
    owner.frame = function () {
        dux.init();
    };
    owner.menu = function ($el, config) {
        $($el).click(function () {
            $('body').toggleClass('dux-mobile-menu');
        });
    };
}(jQuery, window.base = {}));

/**
 * 表格组件
 */
(function ($, owner) {
    owner.bind = function ($el, config) {
        Do('dialog', function () {
            var defaultConfig = {}, config = $.extend(defaultConfig, config);
            var $table = $($el).find('[data-table]'), $del = $table.find('[data-del]');
            //更改状态
            $table.on('click', '[data-status]', function () {
                var data = $(this).data(), $obj = this;
                if (data.status == 1) {
                    var status = 0;
                    var css = 'uk-text-danger';
                } else {
                    var status = 1;
                    var css = 'uk-text-success';
                }
                app.ajax({
                    type: 'post',
                    url: data.url,
                    data: {
                        id: data.id,
                        name: data.name,
                        status: status
                    },
                    success: function (info) {
                        notify.success({
                            content: info
                        });
                        $($obj).removeClass('uk-text-success uk-text-danger').addClass(css).data('status', status);
                    }
                });

            });
            //全选
            $table.find('[data-all]').click(function () {
                if (!$(this).is(':checked')) {
                    $table.find('input[type=checkbox]').prop("checked", false);
                } else {
                    $table.find('input[type=checkbox]').prop("checked", true);
                }
            });
            //删除
            $del.click(function () {
                var data = $(this).data(), $tr = $(this).parents('tr, .tr');

                dialog.confirm({
                    title : '是否确认删除?',
                    btn : ['删除', '取消'],
                    callback : [function () {
                        app.ajax({
                            type: 'post',
                            url: data.url,
                            data: {id: data.id},
                            success: function (info) {
                                notify.success({
                                    content: info
                                });
                                $tr.remove();
                                dialog.close();
                            },
                            error: function (msg) {
                                dialog.msg(msg);
                            }
                        });
                    }]
                });

            });
            //批量操作
            var $batch = $($el).find('[data-batch]');
            $batch.submit(function () {
                event.stopPropagation();
                var data = {}, ids = [];
                $.each($batch.serializeArray(), function (index, vo) {
                    data[vo.name] = vo.value;
                });
                $table.find('input[type=checkbox]:checked').each(function () {
                    var id = $(this).val();
                    if(id) {
                        ids.push(id);
                    }
                });
                data['ids'] = ids.join(',');
                app.ajax({
                    url: $batch.attr('action'),
                    data: data,
                    type: 'post',
                    success: function (info) {
                        dialog.alert({
                            title : info,
                            callback : function () {
                                location.reload();
                            }
                        });
                    },
                    error: function (info) {
                        dialog.alert({
                            title : info,
                            callback : function () {
                                location.reload();
                            }
                        });
                    }
                });
                return false;
            });
            //分页跳转
            var $pages = $($el).find('[data-pages]');
            $pages.submit(function (event) {
                event.stopPropagation();
                var page = $pages.find('input[name="page"]').val();
                var href = location.href;
                if (/page=\d+/.test(href)) {
                    href = href.replace(/page=\d+/, "page=" + page);
                } else if (href.indexOf('?') == -1) {
                    href = href + "?page=" + page;
                } else {
                    href = href + "&page=" + page;
                }
                window.location.href = href;
                return false;
            });

        });
    };
}(jQuery, window.table = {}));

