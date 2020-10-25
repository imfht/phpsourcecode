/**

 @Name: Fly社区主入口

 */


layui.define(['layer', 'laytpl', 'form', 'element', 'upload', 'util', 'carousel'], function (exports) {

    var $ = layui.jquery
        , layer = layui.layer
        , laytpl = layui.laytpl
        , form = layui.form
        , element = layui.element
        , upload = layui.upload
        , util = layui.util
        , device = layui.device()
        , carousel = layui.carousel
        , DISABLED = 'layui-btn-disabled';

    //阻止IE7以下访问
    if (device.ie && device.ie < 8) {
        layer.alert('如果您非得使用 IE 浏览器访问Fly社区，那么请使用 IE8+');
    }

    //轮播
    carousel.render({
        elem: '#carousel'
        , width: '100%' //设置容器宽度
        , arrow: 'hover' //悬停显示箭头
        , height: '460px'
        , anim: 'fade' //切换动画方式
        ,autoplay:true
        //, indicator: 'none' //指示器不显示

    });

    layui.focusInsert = function (obj, str) {
        var result, val = obj.value;
        obj.focus();
        if (document.selection) { //ie
            result = document.selection.createRange();
            document.selection.empty();
            result.text = str;
        } else {
            result = [val.substring(0, obj.selectionStart), str, val.substr(obj.selectionEnd)];
            obj.focus();
            obj.value = result.join('');
        }
    };


    //数字前置补零
    layui.laytpl.digit = function (num, length, end) {
        var str = '';
        num = String(num);
        length = length || 2;
        for (var i = num.length; i < length; i++) {
            str += '0';
        }
        return num < Math.pow(10, length) ? str + (num | 0) : num;
    };

    var fly = {
        //Ajax
        json: function (url, data, success, options) {
            var that = this, type = typeof data === 'function';

            if (type) {
                options = success
                success = data;
                data = {};
            }
            options = options || {};
            return $.ajax({
                type: options.type || 'post',
                dataType: options.dataType || 'json',
                data: data,
                url: url,
                success: function (res) {
                    if (res.status === 0) {
                        success && success(res);
                    } else {
                        layer.msg(res.msg || res.code, {shift: 6});
                        options.error && options.error();
                    }
                }, error: function (e) {
                    layer.msg('请求异常，请重试', {shift: 6});
                    options.error && options.error(e);
                }
            });
        }

        //计算字符长度
        , charLen: function (val) {
            var arr = val.split(''), len = 0;
            for (var i = 0; i < val.length; i++) {
                arr[i].charCodeAt(0) < 299 ? len++ : len += 2;
            }
            return len;
        }

        , form: {}

        , escape: function (html) {
            return String(html || '').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
                .replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
        }

        //内容转义
        , content: function (content) {
            //支持的html标签
            var html = function (end) {
                return new RegExp('\\n*\\[' + (end || '') + '(pre|hr|div|span|p|table|thead|th|tbody|tr|td|ul|li|ol|li|dl|dt|dd|h2|h3|h4|h5)([\\s\\S]*?)\\]\\n*', 'g');
            };
            content = fly.escape(content || '') //XSS
                .replace(/img\[([^\s]+?)\]/g, function (img) {  //转义图片
                    return '<img src="' + img.replace(/(^img\[)|(\]$)/g, '') + '">';
                }).replace(/@(\S+)(\s+?|$)/g, '@<a href="javascript:;" class="fly-aite">$1</a>$2') //转义@
                .replace(/face\[([^\s\[\]]+?)\]/g, function (face) {  //转义表情
                    var alt = face.replace(/^face/g, '');
                    return '<img alt="' + alt + '" title="' + alt + '" src="' + fly.faces[alt] + '">';
                }).replace(/a\([\s\S]+?\)\[[\s\S]*?\]/g, function (str) { //转义链接
                    var href = (str.match(/a\(([\s\S]+?)\)\[/) || [])[1];
                    var text = (str.match(/\)\[([\s\S]*?)\]/) || [])[1];
                    if (!href) return str;
                    var rel = /^(http(s)*:\/\/)\b(?!(\w+\.)*(sentsin.com|layui.com))\b/.test(href.replace(/\s/g, ''));
                    return '<a href="' + href + '" target="_blank"' + (rel ? ' rel="nofollow"' : '') + '>' + (text || href) + '</a>';
                }).replace(html(), '\<$1 $2\>').replace(html('/'), '\</$1\>') //转移HTML代码
                .replace(/\n/g, '<br>') //转义换行
            return content;
        }

        //新消息通知
        , newmsg: function () {
            var elemUser = $('.fly-nav-user');
            if (layui.cache.user.uid !== -1 && elemUser[0]) {
                fly.json('/message/nums/', {
                    _: new Date().getTime()
                }, function (res) {
                    if (res.status === 0 && res.count > 0) {
                        var msg = $('<a class="fly-nav-msg" href="javascript:;">' + res.count + '</a>');
                        elemUser.append(msg);
                        msg.on('click', function () {
                            fly.json('/message/read', {}, function (res) {
                                if (res.status === 0) {
                                    location.href = '/user/message/';
                                }
                            });
                        });
                        layer.tips('你有 ' + res.count + ' 条未读消息', msg, {
                            tips: 3
                            , tipsMore: true
                            , fixed: true
                        });
                        msg.on('mouseenter', function () {
                            layer.closeAll('tips');
                        })
                    }
                });
            }
            return arguments.callee;
        }

    };


    $('body').on('click', '#LAY_signin', function () {
        var othis = $(this);
        if (othis.hasClass(DISABLED)) return;

        fly.json('/sign/in', {
            token: signRender.token || 1
        }, function (res) {
            signRender(res.data);
        }, {
            error: function () {
                othis.removeClass(DISABLED);
            }
        });

        othis.addClass(DISABLED);
    });




    //相册
    if ($(window).width() > 750) {
        layer.photos({
            photos: '.photos'
            , zIndex: 9999999999
            , anim: -1
        });
    } else {
        $('body').on('click', '.photos img', function () {
            window.open(this.src);
        });
    }


    //搜索
    $('.fly-search').on('click', function () {
        layer.open({
            type: 1
            , title: false
            , closeBtn: false
            //,shade: [0.1, '#fff']
            , shadeClose: true
            , maxWidth: 10000
            , skin: 'fly-layer-search'
            , content: ['<form action="http://cn.bing.com/search">'
                , '<input autocomplete="off" placeholder="搜索内容，回车跳转" type="text" name="q">'
                , '</form>'].join('')
            , success: function (layero) {
                var input = layero.find('input');
                input.focus();

                layero.find('form').submit(function () {
                    var val = input.val();
                    if (val.replace(/\s/g, '') === '') {
                        return false;
                    }
                    input.val('site:layui.com ' + input.val());
                });
            }
        })
    });

    //新消息通知
    fly.newmsg();

    //发送激活邮件
    fly.activate = function (email) {
        fly.json('/api/activate/', {}, function (res) {
            if (res.status === 0) {
                layer.alert('已成功将激活链接发送到了您的邮箱，接受可能会稍有延迟，请注意查收。', {
                    icon: 1
                });
            }
            ;
        });
    };
    $('#LAY-activate').on('click', function () {
        fly.activate($(this).attr('email'));
    });

    //点击@
    $('body').on('click', '.fly-aite', function () {
        var othis = $(this), text = othis.text();
        if (othis.attr('href') !== 'javascript:;') {
            return;
        }
        text = text.replace(/^@|（[\s\S]+?）/g, '');
        othis.attr({
            href: '/jump?username=' + text
            , target: '_blank'
        });
    });

    //表单提交
    form.on('submit(*)', function (data) {
        var action = $(data.form).attr('action');
        fly.json(action, data.field, function (res) {
            if (res.code > 0) {
                layer.msg(res.msg, {icon: 1, time: 1000}, function () {
                    if(res.url){
                        window.location.href = res.url;
                    }
                });
            } else {
                layer.msg(res.msg, {time: 1000, icon: 2});
            }
        });
        return false;
    });

    //加载特定模块
    if (layui.cache.page && layui.cache.page !== 'index') {
        var extend = {};
        extend[layui.cache.page] = layui.cache.page;
        layui.extend(extend);
        layui.use(layui.cache.page);
    }

    //手机设备的简单适配
    var treeMobile = $('.site-tree-mobile')
        , shadeMobile = $('.site-mobile-shade')

    treeMobile.on('click', function () {
        $('body').addClass('site-mobile');
    });

    shadeMobile.on('click', function () {
        $('body').removeClass('site-mobile');
    });

    //获取统计数据
    $('.fly-handles').each(function () {
        var othis = $(this);
        $.get('/api/handle?alias=' + othis.data('alias'), function (res) {
            othis.html('（下载量：' + res.number + '）');
        })
    });

    //固定Bar
    util.fixbar();

    exports('fly', fly);

});

