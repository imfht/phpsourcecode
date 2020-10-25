$.ajaxSetup({
    cache: false
});
(function ($) {
    //表格处理
    $.fn.duxTable = function (options) {
        var defaults = {
            selectAll: '#selectAll',
            selectSubmit: '#selectSubmit',
            selectAction: '#selectAction',
            deleteUrl: '',
            actionUrl: '',
            actionParameter: function(){}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var table = this;
            var id = $(this).attr('id');
            //处理多选单选
            $(options.selectAll).click(function () {
                $(table).find("[name='id[]']").each(function () {
                    if ($(this).prop("checked")) {
                        $(this).prop("checked", false);
                    } else {
                        $(this).prop("checked", true);
                    }
                })
            });
            //处理批量提交
            $(options.selectSubmit).click(function () {
                Do.ready('tips', 'dialog', function () {
                    //记录获取
                    var ids = new Array();
                    $(table).find("[name='id[]']").each(function () {
                        if ($(this).prop("checked")) {
                            ids.push($(this).val());
                        }
                    })
                    toastr.options = {
                        "positionClass": "toast-bottom-right"
                    }
                    if (ids.length == 0) {
                        toastr.warning('请先选择操作记录');
                        return false;
                    }
                    //操作项目
                    var dialog = layer.confirm('你确认要进行本次批量操作！', function () {
                        var parameter = $.extend({
                                ids: ids,
                                type: $(options.selectAction).val()
                            },
                            options.actionParameter()
                        );
                        $.post(options.actionUrl, parameter, function (json) {
                            if (json.status) {
                                toastr.success(json.info);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                toastr.warning(json.info);
                            }
                        }, 'json');
                        layer.close(dialog);
                    });

                });
            });
            //处理删除
            $(table).find('.js-del').click(function () {
                var obj = this;
                var div = $(obj).parent().parent();
                var url = $(obj).attr('url');
                if (url == '' || url == null || url == 'undefined') {
                    url = options.deleteUrl;
                }
                operat(
                    obj,
                    url,
                    function () {
                        div.remove();
                    },
                    function () {});
            });
            //处理其他动作
            $(table).find('.js-action').click(function () {
                var obj = this;
                var div = $(obj).parent().parent();
                var url = $(obj).attr('url');
                operat(
                    obj,
                    url,
                    function () {
                        var success = $(obj).attr('success');
                        if (success) {
                            eval(success);
                        }
                    },
                    function () {
                        var failure = $(obj).attr('failure');
                        if (failure) {
                            eval(failure);
                        }
                    });
            });
            //处理动作
            function operat(obj, url, success, failure) {
                Do.ready('tips', 'dialog', function () {
                    var text = $(obj).attr('title');
                    var dialog = layer.confirm('你确认执行' + text + '操作？', function () {
                        var dload = layer.load('操作执行中，请稍候...');
                        $.post(url, {
                                data: $(obj).attr('data')
                            },
                            function (json) {
                                layer.close(dload);
                                layer.close(dialog);
                                if (json.status) {
                                    toastr.success(json.info);
                                    success();
                                } else {
                                    toastr.warning(json.info);
                                    failure();
                                }
                            }, 'json');
                    });
                });
            }
            //处理编辑
            $(table).find('.table_edit').blur(function () {
                var obj = this;
                var data = $(obj).attr('data');
                var url = $(obj).attr('url');
                if (url == '' || url == null || url == 'undefined') {
                    url = options.editUrl;
                }
                Do.ready('tips', function () {
                    $.post(url, {
                            data: $(obj).attr('data'),
                            name: $(obj).attr('name'),
                            val: $(obj).val(),
                        },
                        function (json) {
                            if (json.status) {
                                toastr.success(json.info);
                            } else {
                                toastr.warning(json.info);
                            }
                        }, 'json');
                });
            });
        });
    };
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

    //时间插件
    $.fn.duxTime = function (options) {
        var defaults = {
            lang: 'ch'
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var id = this;
            Do.ready('time', function () {
                $(id).datetimepicker(options);
            });
        });
    };

    //上传调用
    $.fn.duxFileUpload = function (options) {
        var defaults = {
            uploadUrl: duxConfig.uploadUrl,
            type: '',
            uploadParams: function () {},
            complete: function () {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var upButton = $(this);
            var urlVal = upButton.attr('data');
            urlVal = $('#' + urlVal);
            var buttonText = upButton.text();
            var preview = upButton.attr('preview');
            preview = $('#' + preview);
            /* 图片预览 */
            preview.click(function () {
                if (urlVal.val() == '') {
                    alert('没有发现已上传图片！');
                } else {
                    window.open(urlVal.val());
                }
                return;
            });
            /*创建上传*/
            Do.ready('webuploader', function () {
                var uploader = WebUploader.create({
                    swf: duxConfig.baseDir + 'webuploader/Uploader.swf',
                    server: options.uploadUrl,
                    pick: {
                        id: upButton,
                        multiple: false
                    },
                    resize: false,
                    auto: true,
                    accept: {
                        title: '指定格式文件',
                        extensions: options.type
                    }
                });
                //上传开始
                uploader.on('uploadStart', function (file) {
                    uploader.option('formData' , $.extend(options.uploadParams(), {'class_id':$('#class_id').val()}));
                    upButton.attr('disabled', true);
                    upButton.find('.webuploader-pick span').text(' 等待');
                });
                //上传完毕
                uploader.on('uploadSuccess', function (file, data) {
                    upButton.attr('disabled', false);
                    upButton.find('.webuploader-pick span').text(' 上传');
                    if (data.status) {
                        urlVal.val(data.data.url);
                        options.complete(data.data);
                    } else {
                        alert(data.info);
                    }
                });
                uploader.on('uploadError', function (file) {
                    alert('文件上传失败');
                });
            });
        });
    };

})(jQuery);