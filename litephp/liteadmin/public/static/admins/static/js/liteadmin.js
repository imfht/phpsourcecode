layui.define(['jquery', 'form', 'layer', 'element','upload','browser-md5-file'], function(exports) {
    var $ = layui.jquery;


    /**
     * 消息类
     * @constructor
     */
    function Msg() {
        this.indexs = [];
        this.shade = [0.02, '#000'];
    }

    Msg.prototype = {
        constructor: Msg,
        /**
         * 关闭指定弹出层
         * @param index 弹出层索引
         */
        close: function (index) {
            var i;
            if ((i = this.indexs.indexOf(index)) !== -1) {
                this.indexs.splice(i, 1)
            }
            layer.close(index);
        },
        /**
         * 关闭全部或指定类型弹出层
         */
        closeAll: function (flag) {
            if (flag){
                console.log(flag);
                layer.closeAll(flag);
            }else{
                layer.closeAll();
            }
        },
        /**
         * 提示窗
         * @param msg 提示消息
         * @param callback 关闭后的回调
         */
        alert: function (msg, callback) {
            var index = layer.alert(msg, {end: callback, scrollbar: false});
            return this.indexs.push(index), index;
        },
        /**
         * 成功提示框
         * @param msg 提示消息
         * @param callback 关闭后的回调
         */
        success: function (msg, callback) {
            var index = layer.msg(msg, {icon: 1, shade: this.shade, end: callback, time: 2000, shadeClose: true});
            return this.indexs.push(index), index;
        },
        /**
         * 错误提示窗
         * @param msg 提示消息
         * @param callback 关闭后的回调
         */
        error: function (msg, callback) {
            var index = layer.msg(msg, {
                icon: 2,
                shade: this.shade,
                time: 3000,
                end: callback,
                shadeClose: true
            });
            return this.indexs.push(index), index;
        },
        /**
         * 问题贴士
         * @param msg 提示消息
         * @param callback 关闭后的回调
         */
        tips: function (msg, callback) {
            var index = layer.msg(msg, {time: 3000, shade: this.shade, end: callback, shadeClose: true});
            return this.indexs.push(index), index;
        },
        /**
         * 加载装
         * @param msg 加载时的消息提示
         * @param callback 关闭后的回调
         */
        loading: function (msg, callback) {
            var index = msg ? layer.msg(msg, {
                icon: 16,
                time: 0,
                end: callback
            }) : layer.load(2, {time: 0, shade :  [0.5, '#393D49'], end: callback});
            return this.indexs.push(index), index;
        },
        /**
         * 确认对话框
         * @param msg 提示消息
         * @param yes 关闭后的回调
         */
        confirm: function (msg, yes, ele) {
            var _this = this;
            var index = layer.confirm(msg, {
                title: '操作确认',
                btn: ['确认', '取消'],
                cancel:function () {
                    layui.page.btnen(ele);
                }
            }, function () {
                yes();
            }, function () {
                _this.close(index);
                layui.page.btnen(ele);
            });
        },
        /**
         * 自动处理ajax返回数据
         * @param data ajax返回数据
         * @returns {*}
         */
        auto: function (data) {
            var _this = this;
            if (parseInt(data.code) === 1){
                this.success(data.msg, function () {
                    !!data.url ? (window.location.href = data.url) : layui.page.reload();
                });
            }else{
                this.error(data.msg, function () {
                    !!data.url && (window.location.href = data.url);
                });
            }
        }
    };
    /**
     * 表单类
     * @constructor
     */
    function Page() {

    }
    Page.prototype = {
        constructor:Page,
        /**
         * 页面重新加载
         */
        reload:function () {
            location.reload();
        },
        /**
         * get请求url并使用回调处理返回数据
         * @param url   要请求的url
         * @param callback 回调函数
         */
        get: function (url, callback, ele) {
            $.get(url).fail(function (res) {
                var message = res.responseJSON.msg || " 异常，请稍后再试";
                layui.msg.error("E_Status "+res.status+message);
                layui.page.btnen(ele);
            }).done(function (res) {
                callback(res);
            }).then(function () {
                layui.page.btnen(ele);
            });
        },
        /**
         * post请求url并使用回调处理返回数据
         * @param url 要请求的url
         * @param data 要提交的数据
         * @param callback 回调函数
         */
        post: function (url, data, callback,ele) {
            $.post(url, data).fail(function (res) {
                var message;
                if (res.responseJSON === undefined){
                    message = " 异常，请稍后再试";
                }else{
                    message = res.responseJSON.msg;
                }
                layui.msg.error("E_Status "+res.status+message);
                layui.page.btnen(ele);
            }).done(function (res) {
                callback(res);
            }).then(function () {
                layui.page.btnen(ele);
            });
        },
        /**
         * 模态框中打开新地址
         * @param url 要打开的地址
         * @param title 打开后模态框的标题
         */
        modal: function (url, title, ele) {
            var glue = (url.indexOf('?') === -1) ? "?" : "&";
            var rand = "_v="+Math.random();
            var getUrl = url + glue + rand;
            var reload = $(ele).data('reload');
            var area = $(ele).data('area') || '100%,100%';
            area = area.split(',');

            $.get(getUrl).fail(function (res) {
                var message;
                if (res.responseJSON === undefined){
                    message = " 异常，请稍后再试";
                }else{
                    message = res.responseJSON.msg;
                }
                layui.msg.error("E_Status "+res.status+message);
                layui.page.btnen(ele);
            }).done(function (data) {
                if (data.code == 0){
                    layui.msg.error(data.msg)
                }else{
                    layer.open({
                        type: 1,
                        title: title,
                        content: data,
                        area: area,
                        scrollbar:true,
                        end:function () {
                            if (reload == true){
                                location.reload()
                            }
                        }
                    });
                }
            }).then(function () {
                layui.page.btnen(ele);
            });
        },
        /**
         * 弹出ifreme中打开新地址
         * @param url 要打开的地址
         * @param title 打开后模态框的标题
         */
        iframe: function (url, title, ele) {
            var glue = (url.indexOf('?') === -1) ? "?" : "&";
            var rand = "_v="+Math.random();
            var getUrl = url + glue + rand;
            var reload = $(ele).data('reload');

            layer.open({
                type: 2,
                title: title,
                content: getUrl,
                area: ['100%','100%'],
                scrollbar:true,
                end:function () {
                    if (reload == true){
                        location.reload()
                    }
                    layui.page.btnen(ele);
                }
            });
        },
        /**
         * 禁用按钮
         * @param ele
         */
        btndis:function (ele) {
            $(ele).attr('disabled','disabled').addClass('layui-btn-disabled');
        },
        /**
         * 启用按钮
         * @param ele
         */
        btnen:function (ele) {
            $(ele).removeAttr('disabled').removeClass('layui-btn-disabled');
        }
    };

    /**
     * 根对象
     * @type {{msg: Window.layui.msg}}
     */
    layui.msg = new Msg();
    layui.page = new Page();

    $(function () {
        var $body= $('body');

        /**
         * [data-get] 组件，get 请求指定地址
         * 依赖 data-get="目标地址"
         *     data-conform="提示消息"
         */
        $body.on('click','[data-get]',function (e) {
            var ele = this;
            layui.page.btndis(ele);
            var url = $(this).data('get');
            var confirm = $(this).data('confirm');
            if (confirm){
                return layui.msg.confirm(confirm, getres, ele);
            }
            getres();

            /**
             * get 请求地址并处理返回参数
             */
            function getres() {
                layui.page.get(url, function (data) {
                    layui.msg.auto(data);
                }, ele);
            }
        });
        /**
         * [data-post-batch] 组件，get 请求指定地址
         * 依赖 data-post="目标地址"
         *     data-key="参数名"
         *     data-conform="提示消息"
         */
        $body.on('click','[data-post-batch]',function (e) {
            var ele = this;
            layui.page.btndis(ele);
            var url = $(this).data('post-batch');
            var key = $(this).data('key') || 'ids';
            var param = {};
            param[key] = tableCheck.getData().join(',');
            var confirm = $(this).data('confirm');
            if (confirm){
                return layui.msg.confirm(confirm, getres, ele);
            }
            getres();

            /**
             * get 请求地址并处理返回参数
             */
            function getres() {
                layui.page.post(url,param, function (data) {
                    layui.msg.auto(data);
                }, ele);
            }
        });

        /**
         * [data-get] 组件，get 请求指定地址
         * 依赖 data-post="目标地址"
         *     data-param="post参数"
         *     data-conform="提示消息"
         */
        $body.on('click','[data-post]',function (e) {
            var ele = this;
            layui.page.btndis(ele);
            var url = $(this).data('post');
            var param = $(this).data('param');
            var confirm = $(this).data('confirm');
            if (confirm){
                return layui.msg.confirm(confirm, getres, ele);
            }
            getres();

            /**
             * get 请求地址并处理返回参数
             */
            function getres() {
                layui.page.post(url,param, function (data) {
                    layui.msg.auto(data);
                }, ele);
            }
        });

        /**
         * [data-modal] 组件，模态框打开指定地址
         * 依赖 data-modal="目标地址"
         *      data-title="弹窗标题"
         */
        $body.on('click', '[data-modal]', function (e) {
            var ele = this;
            layui.page.btndis(ele);
            var url = $(this).data('modal');
            var title = $(this).data('title')||"表单";
            layui.page.modal(url, title, ele);
        });
        /**
         * [data-iframe] 组件，模态框打开指定地址
         * 依赖 data-iframe="目标地址"
         *      data-title="弹窗标题"
         */
        $body.on('click', '[data-iframe]', function (e) {
            var ele = this;
            layui.page.btndis(ele);
            var url = $(this).data('iframe');
            var title = $(this).data('title')||"表单";
            layui.page.iframe(url, title, ele);
        });
        /**
         * data-form 表单提交 并处理相应数据
         * 依赖 data-form="目标地址"
         * 默认post提交
         */
        $body.on('submit', '[data-form]', function (e) {
            var ele = this;
            layui.page.btndis(ele);
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).data('form');
            layui.page.post(url, data, function (data) {
                layui.msg.auto(data);
            }, ele);
        })
        /**
         * data-upload 文件上传 并处理相应数据
         * 依赖 data-upload="目标地址"
         *      data-field="上传返回值的字段"
         *      data-type="上传类型"  images（图片）、file（所有文件）、video（视频）、audio（音频）
         * 默认post提交
         */
        $body.on('click', '[data-upload]', function (e) {
            var upload = layui.upload; //得到 upload 对象
            var _this = this;
            var fetch_btn = document.createElement('button');
            var start_btn = document.createElement('button');
            var url = $(this).data('upload');
            var field = $(this).data('field');
            var type = $(this).data('type')|'images';

            function success_callback(field,res){
                $('[name="'+field+'"]').val(res.data.src);
                $('[data-preview]').each(function (index,value) {
                    if ($(value).data('preview')===field){
                        $(value).css('max-width','130px').css('max-height','130px');
                        $(value).attr('src',res.data.src);
                    }
                });
            }
            //创建一个上传组件
            upload.render({
                elem: fetch_btn
                ,url: url
                ,choose:function (obj) {
                    layui.msg.loading();
                    obj.preview(function (index, file, result) {
                        browserMD5File(file, function (err, md5) {
                            $.get("/admin/upload/checkFile.html?hash="+md5).then(function (res) {
                                if (res.code === 0){
                                    success_callback(field,res);
                                    layui.msg.closeAll('loading')
                                }else{
                                    $(start_btn).click();
                                }
                            })
                        });
                    });
                }
                ,done: function(res, index, upload){ //上传后的回调
                    layui.msg.closeAll('loading');

                    if (res.code === 1){
                        layui.msg.error(res.msg);
                        return;
                    }
                    success_callback(field,res)
                }
                ,error: function(index, upload){ //上传后的回调

                }
                ,accept: type //允许上传的文件类型
                ,size: 1024*2 //最大允许上传的文件大小 KB
                ,acceptMime:type
                ,auto:false,
                bindAction:start_btn
            });
            $(fetch_btn).click();
        });
        /**
         * data-preview 文件预览 并 点击删除数据
         * 依赖 data-preview="目标字段"
         */
        $body.on('click', '[data-preview]', function (e) {
            var field = $(this).data('preview');
            $('[name="'+field+'"]').val('');
            $(this).attr('src','');
        });
        /**
         * img.data-tips-image 组件
         * 依赖 data-preview="弹窗宽度"
         * 本组件不依赖data-*属性绑定，而是通过data-tips-image类名绑定
         */
        $body.on('click', 'img.data-tips-image', function (e) {
            var src = e.target.src;
            var img = $(this);
            $("<img/>").attr("src", $(img).attr("src")).load(function() {
                var src = e.target.src;
                var minWidth = Math.min(this.width,480);
                var width = $(e.target).data('width') || minWidth;
                var content = '<img src="'+src+'" style="width: '+width+'px;height: auto;">';
                layer.open({
                    type: 1,
                    content: content,
                    area:[width+'px'],
                    title: false,
                    shadeClose:true,
                });
            });
        });
    });

    exports('liteadmin', {});
});