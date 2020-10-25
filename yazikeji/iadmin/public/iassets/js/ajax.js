/**
 * Created by carl on 2017/3/29.
 */
layui.define(['jquery', 'layer'], function (exports) {
    var $ = layui.jquery, layer = layui.layer

    var Ajax = function () {
        this.config = {
            form            : undefined,  //提交表单ID
            loading         : true,       //是否展示等待层
            confirmTitle    : undefined,  //询问确认文字内容
            icon            : 3,          //文内容的icon
            loadingMessage  : undefined,  //加载层的文字提示
            method          : undefined,  //提交方法
            dataType        : undefined,  //返回数据类型
            url             : undefined,  //提交地址
            data            : undefined   //提交数据
        };
    }

    Ajax.prototype.set = function (options) {
        var that = this;
        $.extend(true, that.config, options);
        return that;
    };

    Ajax.prototype.exec = function (callback) {
        var config = this.config;

        if (typeof config.form == 'undefined' && (typeof config.data == 'undefined' || typeof config.url == 'undefined') ) {
            throw new Error('Ajax Form error: The form must be an object');
            return
        }

        layer.confirm(
            ((typeof config.confirmTitle == 'undefined') ? '确定要添加此内容吗?' : config.confirmTitle),
            {icon: config.icon},
            function(index){
                layer.close(index)
                if (config.loading) {
                    var loading = layer.open({
                        id          : 'loading',
                        type        : 1,
                        title       : false,
                        closeBtn    : false,
                        area        : ['500px', '50px'],
                        resize      : false,
                        moveType    : 1,
                        offset      : '300px',
                        content     : '<div style="font-size:12px;line-height: 50px;background-color: #ffffff;color: rgb(117, 117, 117);border-radius: 15px;font-weight: 500;height: 50px;text-align: center;">'+((typeof config.loadingMessage == 'undefined') ? '信息正在提交......' : config.loadingMessage) +'</div>'
                    })
                }

                $.ajax({
                    headers : {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                    type    : (typeof config.method == 'undefined') ? 'POST' : config.method ,
                    url     : (typeof config.url != 'undefined') ? config.url : config.form.attr('action'),
                    data    : (typeof config.data != 'undefined') ? config.data : config.form.serialize(),
                    dataType: (typeof config.dataType == 'undefined') ? 'JSON' : config.dataType,
                    success : function (data) {
                        if (typeof callback == 'function') {
                            callback(data)
                        }
                    },
                    error   : function (error) {
                        if (error.status != 422) {
                            layer.msg('请求错误: ' + error.statusText)
                        } else {
                            message = ''
                            for (var i in error.responseJSON) {
                                message += error.responseJSON[i][0]+"<br />"
                            }
                            layer.open({
                                icon:2,
                                content : message
                            });
                        }

                    },
                    complete: function () {
                        layer.close(loading);
                    }
                })
            }
        );

    }

    var ajax = new Ajax();

    exports('ajax', function (options) {
        return ajax.set(options);
    });
});

