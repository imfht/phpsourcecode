define(['bootstrap'], function($){
    var module = {};

    module.is_error = function(obj) {
        if(!obj || !obj.errno || obj.errno == '0') {
            return false;
        }
        return true;
    };

    module.attach = function(src){
        if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0) {
            return src;
        } else {
            return '../attachment/' + src;
        }
    };

    module.dialog = function(title, content, footer, options) {
        if(!options) {
            options = {};
        }
        if(!options.containerName) {
            options.containerName = 'modal-message';
        }
        var modalobj = $('#' + options.containerName);
        if(modalobj.length == 0) {
            $(document.body).append('<div id="' + options.containerName + '" class="modal animated" tabindex="-1" role="dialog" aria-hidden="true"></div>');
            modalobj = $('#' + options.containerName);
        }
        var html =
            '<div class="modal-dialog">'+
                '	<div class="modal-content">';
        if(title) {
            html +=
                '<div class="modal-header">'+
                    '	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
                    '	<h3>' + title + '</h3>'+
                    '</div>';
        }
        if(content) {
            if(!$.isArray(content)) {
                html += '<div class="modal-body">'+ content + '</div>';
            } else {
                html += '<div class="modal-body">正在加载中</div>';
            }
        }
        if(footer) {
            html +=
                '<div class="modal-footer">'+ footer + '</div>';
        }
        html += '	</div></div>';
        modalobj.html(html);
        if(content && $.isArray(content)) {
            var embed = function(c) {
                modalobj.find('.modal-body').html(c);
            };
            if(content.length == 2) {
                $.post(content[0], content[1]).success(embed);
            } else {
                $.get(content[0]).success(embed);
            }
        }
        modalobj.modal({keyboard: false, show: false});
        return modalobj;
    };

    module.message = function(msg, redirect, type){
        if(!redirect && !type){
            type = 'info';
        }
        if($.inArray(type, ['success', 'error', 'info', 'warning']) == -1) {
            type = '';
        }
        if(type == '') {
            type = redirect == '' ? 'error' : 'success';
        }

        var icons = {
            success : 'check-circle',
            error :'times-circle',
            info : 'info-circle',
            warning : 'exclamation-triangle'
        };
        var p = '';
        if(redirect && redirect.length > 0){
            if(redirect == 'back'){
                p = '<p>[<a href="javascript:;" onclick="history.go(-1)">返回上一页</a>] &nbsp; [<a href="./?refresh">回首页</a>]</p>';
            }else{
                p = '<p><a href="' + redirect + '" target="main" data-dismiss="modal" aria-hidden="true">如果你的浏览器在 <span id="timeout"></span> 秒后没有自动跳转，请点击此链接</a></p>';
            }
        }
        var content =
            '<div class="row"><i class="col-xs-3 col-sm-2 fa fa-4x fa-'+icons[type]+'"></i>'+
                '<div class="col-xs-9 col-sm-10"><p>'+ msg +'</p>' +
                p +
                '</div></div>';
        var footer =
            '<button type="button" class="btn btn-default" data-dismiss="modal">确认</button>';
        var modalobj = module.dialog('系统提示', content, footer);
        modalobj.find('.modal-content').addClass('alert alert-'+type);
        if(redirect) {
            var timer = 0;
            var timeout = 3;
            modalobj.find("#timeout").html(timeout);
            modalobj.on('show.bs.modal', function(){doredirect();});
            modalobj.on('hide.bs.modal', function(){timeout = 0;doredirect(); });
            modalobj.on('hidden.bs.modal', function(){modalobj.remove();});
            function doredirect() {
                timer = setTimeout(function(){
                    if (timeout <= 0) {
                        modalobj.modal('hide');
                        clearTimeout(timer);
                        window.location.href = redirect;
                        return;
                    } else {
                        timeout--;
                        modalobj.find("#timeout").html(timeout);
                        doredirect();
                    }
                }, 1000);
            }
        }
        modalobj.on('show.bs.modal', function(e){
            $(e.target).removeClass('bounceOut');
            $(e.target).addClass('bounceIn');
        })
        modalobj.on('hide.bs.modal', function(e){
            if(!e.target.animated) {
                $(e.target).removeClass('bounceIn');
                $(e.target).addClass('bounceOut');
                e.preventDefault();
                e.target.animated = true;
                setTimeout(function(){
                    $(e.target).modal('hide');
                    e.target.animated = false;
                }, 1000);
            }
        })
        modalobj.modal('show');
    };

    /**
     * 点击指定的元素, 发送验证码, 并显示倒计时, 并通知发送状态
     * @param elm 元素节点
     * @param no 要发送验证码的手机号
     * @param callback 通知回调, 这个函数接受两个参数
     * function(ret, state)
     * ret 通知结果, success 成功, failed 失败, downcount 倒计时
     * state 通知内容, success 时无数据, failed 时指明失败原因, downcount 时指明当前倒数
     */
    module.sendCode = function(elm, no, callback) {
        if(!no || !elm || !$(elm).attr('uniacid')) {
            if($.isFunction(callback)) {
                callback('failed', '给定的参数有错误');
            }
            return;
        }
        $(elm).attr("disabled", true);
        var downcount = 60;
        $(elm).html(downcount + "秒后重新获取");

        var timer = setInterval(function(){
            downcount--;
            if(downcount <= 0){
                clearInterval(timer);
                $(elm).html("重新获取验证码");
                $(elm).attr("disabled", false);
                downcount = 60;
            }else{
                if($.isFunction(callback)) {
                    callback('downcount', downcount);
                }
                $(elm).html(downcount + "秒后重新获取");
            }
        }, 1000);

        var params = {};
        params.receiver = no;
        params.uniacid = $(elm).attr('uniacid');
        $.post('../web/index.php?c=utility&a=verifycode', params).success(function(dat){
            if(dat == 'success') {
                if($.isFunction(callback)) {
                    callback('success', null);
                }
            } else {
                if($.isFunction(callback)) {
                    callback('failed', dat);
                }
            }
        });
    };

    var run = null;
    window.__map_init = function() {
        if(run) {
            run();
        }
    };
    module.map = function(val, callback) {
        if(!val) {
            val = {};
        }
        if(!val.lng) {
            val.lng = 116.403851;
        }
        if(!val.lat) {
            val.lat = 39.915177;
        }
        var modalobj = $('#map-dialog');
        require(['http://map.qq.com/api/js?v=2.exp&callback=__map_init'], function() {
            if(run) {
                run(val, callback);
                return;
            }
            run = function(v, cb) {
                if(modalobj.length == 0) {
                    var content =
                        '<div class="form-group">' +
                            '<div class="input-group">' +
                                '<input type="text" class="form-control" placeholder="请输入地址来直接查找相关位置">' +
                                '<div class="input-group-btn">' +
                                    '<button class="btn btn-default"><i class="icon-search"></i> 搜索</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="help-block">&nbsp;</div>' +
                        '</div>' +
                        '<div id="map-container" style="height:20em;"></div><span class="fa fa-map-marker fa-2x" style="color:red;position:relative;"></span>';
                    var footer =
                        '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                            '<button type="button" class="btn btn-primary">确认</button>';
                    modalobj = module.dialog('请选择地点', content, footer, {containerName : 'map-dialog'});
                    modalobj.find('.modal-dialog').css('width', 'auto');
                    modalobj.modal({'keyboard': false});
                    modalobj.find('.fa').css('left', (modalobj.find('#map-container').width() - modalobj.find('.fa').width())/2);
                    modalobj.find('.fa').css('top', 0 - modalobj.find('#map-container').height()/2 - modalobj.find('.fa').width());
                    
                    var searchAddress = function(ret) {
                        if(ret.type == qq.maps.ServiceResultType.GEO_INFO) {
                            map.panTo(ret.detail.location);
                            modalobj.find('.help-block').text(ret.detail.address);
                        }
                    };
                    var posAddress = function(ret) {
                        if(ret.type == qq.maps.ServiceResultType.GEO_INFO) {
                            if(ret.detail.nearPois && ret.detail.nearPois.length > 0) {
                                modalobj.find('.help-block').text(ret.detail.nearPois[0].address + ret.detail.nearPois[0].name + '附近');
                            } else {
                                modalobj.find('.help-block').text(ret.detail.address);
                            }
                        }
                    };
                    var map = null;
                    var point = new qq.maps.LatLng(val.lat, val.lng);
                    var geo = new qq.maps.Geocoder({
                        complete: searchAddress
                    });
                    var option = {
                        zoom: 13,
                        center: point,
                        scrollwheel: false,
                        disableDoubleClickZoom: false,
                        keyboardShortcuts: false,
                        mapTypeId: qq.maps.MapTypeId.ROADMAP,
                        mapTypeControl: false,
                        panControl: false,
                        
                        zoomControl: true,
                        zoomControlOptions: {
                            position: qq.maps.ControlPosition.TOP_LEFT
                        },
    
                        scaleControl: true,
                        scaleControlOptions: {
                            position: qq.maps.ControlPosition.BOTTOM_LEFT
                        }
                    }
                    module.map.instance = map = new qq.maps.Map($("#map-container")[0], option);
                    geo.setComplete(posAddress);
                    geo.getAddress(map.getCenter());
                    qq.maps.event.addListener(map, 'dragend', function() {
                        geo.setComplete(posAddress);
                        geo.getAddress(map.getCenter());
                    });
                    
                    modalobj.find('.input-group :text').keydown(function(e){
                        if(e.keyCode == 13) {
                            var kw = $(this).val();
                            geo.setComplete(searchAddress);
                            geo.getLocation(kw)
                        }
                    });
                    modalobj.find('.input-group button').click(function(){
                        var kw = $(this).parent().prev().val();
                        geo.setComplete(searchAddress);
                        geo.getLocation(kw)
                    });
                }

                if(v) {
                    val = v;
                }
                module.map.instance.panTo(new qq.maps.LatLng(val.lat, val.lng));
                modalobj.find('button.btn-primary').off('click');
                modalobj.find('button.btn-primary').on('click', function(){
                    if(cb) {
                        callback = cb;
                    }
                    if($.isFunction(callback)) {
                        var point = module.map.instance.getCenter();
                        var val = {lng: point.lng, lat: point.lat, label: modalobj.find('.help-block').text()};
                        callback(val);
                    }
                    modalobj.find('.input-group :text').val('');
                    modalobj.modal('hide');
                });
                modalobj.modal('show');
            }
        });
    };
    
    return module;
});

