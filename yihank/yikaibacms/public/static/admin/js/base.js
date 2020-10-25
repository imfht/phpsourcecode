$.ajaxSetup({
    cache: false
});
(function ($) {
    //表单处理
    $.fn.duxForm = function (options) {
        var defaults = {
            postFun: {},
            returnFun: {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            //表单提交
            var form = this;
            Do.ready('form', 'dialog', function () {
                $(form).Validform({
                    ajaxPost: true,
                    postonce: true,
                    tiptype: function (msg, o, cssctl) {
                        if (!o.obj.is("form")) {
                            //设置提示信息
                            var objtip = o.obj.siblings(".input-note");
                            if (o.type == 2) {
                                //通过
                                var className = ' ';
                                $('#tips').html('');
                                objtip.next('.js-tip').remove();
                                objtip.show();
                            }
                            if (o.type == 3) {
                                //未通过
                                var html = '<div class="alert alert-yellow"><strong>注意：</strong>您填写的信息未通过验证，请检查后重新提交！</div>';
                                $('#tips').html(html);
                                var className = 'check-error';
                                if ( objtip.next('.js-tip').length == 0 ) {
                                    objtip.after('<div class="input-note js-tip">' + msg + '</div>');
                                    objtip.hide();
                                }
                            }
                            //设置样式
                            o.obj.parents('.form-group').removeClass('check-error');
                            o.obj.parents('.form-group').addClass(className);
                        }
                    },
                    callback: function (data) {
                        layer.load('表单正在处理中，请稍等 ...');
                        if (data.status == 1) {
                            //成功返回
                            if ($.isFunction(options.returnFun)) {
                                options.returnFun(data);
                            } else {
                                if (data.url == null || data.url == '') {
                                    //不带连接
                                    layer.alert(data.info, 1, function () {
                                        window.location.reload();
                                    });
                                } else {
                                    //带连接
                                    $.layer({
                                        shade: [0],
                                        area: ['auto', 'auto'],
                                        dialog: {
                                            msg: data.info,
                                            btns: 2,
                                            type: 1,
                                            btn: ['继续操作', '返回'],
                                            yes: function () {
                                                window.location.reload();
                                            },
                                            no: function () {
                                                window.location.href = data.url;
                                            }
                                        }
                                    });
                                }
                            }
                        } else {
                            //失败返回
                            layer.alert(data.info, 8);
                        }
                    }
                });
                //下拉赋值
                var assignObj = $(form).find('.js-assign');
                assignObj.each(function () {
                    var assignTarget = $(this).attr('target');
                    $(this).change(function () {
                        $(assignTarget).val($(this).val());
                    });
                });
            });
        });
    };
})(jQuery);