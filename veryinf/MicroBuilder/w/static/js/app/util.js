define(['bootstrap'], function($){
    var module = {};

    module.preview = function(title) {
        require(['fileinput'], function($){
            $('input[type="file"]').each(function(){
                var cfg = {
                    showUpload: false,
                    allowedPreviewTypes: ['image'],
                    browseLabel: title ? title : '选择图片',
                    removeLabel: '清空',
                    layoutTemplates: {
                        main1: '<div class="input-group {class}">\n' +
                            '   {caption}\n' +
                            '   <div class="input-group-btn">\n' +
                            '       {remove}\n' +
                            '       {upload}\n' +
                            '       {browse}\n' +
                            '   </div>\n' +
                            '</div>\n{preview}',
                        main2: '{preview}\n{remove}\n{upload}\n{browse}\n',
                        preview: '<div class="file-preview {class}">\n' +
                            '   <div class="close fileinput-remove text-right">&times;</div>\n' +
                            '   <div class="file-preview-thumbnails"></div>\n' +
                            '   <div class="clearfix"></div>' +
                            '   <div class="file-preview-status text-center text-success"></div>\n' +
                            '</div>',
                        caption: '<div tabindex="-1" class="form-control file-caption {class}">\n' +
                            '   <span class="glyphicon glyphicon-file kv-caption-icon"></span><div class="file-caption-name"></div>\n' +
                            '</div>',
                        modal: '<div id="{id}" class="modal fade">\n' +
                            '  <div class="modal-dialog modal-lg">\n' +
                            '    <div class="modal-content">\n' +
                            '      <div class="modal-header">\n' +
                            '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\n' +
                            '        <h3 class="modal-title">Detailed Preview <small>{title}</small></h3>\n' +
                            '      </div>\n' +
                            '      <div class="modal-body">\n' +
                            '        <textarea class="form-control" style="font-family:Monaco,Consolas,monospace; height: {height}px;" readonly>{body}</textarea>\n' +
                            '      </div>\n' +
                            '    </div>\n' +
                            '  </div>\n' +
                            '</div>\n'
                    }
                };
                if($.trim($(this).attr('data-preview')) != '') {
                    cfg.initialPreview = ["<img src='" + $(this).attr('data-preview') + "' class='file-preview-image'>"];
                }
                if($.trim($(this).attr('data-caption')) != '') {
                    cfg.initialCaption = $(this).attr('data-caption');
                }
                $(this).fileinput(cfg);
            });
        });
    }

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

    module.clip = function(elm, str, callback) {
        if(elm.clip) {
            return;
        }
        require(['jquery.zclip'], function() {
            $(elm).zclip({
                path: window.__public__ + 'components/zclip/ZeroClipboard.swf',
                copy: str,
                afterCopy: function() {
                    if($.isFunction(callback)) {
                        callback();
                    } else {
                        var obj = $('<em> &nbsp; <span class="label label-success"><i class="fa fa-check-circle"></i> 复制成功</span></em>');
                        var enext = $(elm).next().text();
                        if (!enext) {
                            $(elm).after(obj);
                        }
                        setTimeout(function(){
                            obj.remove();
                        }, 2000);
                    }
                }
            });
            elm.clip = true;
        });
    };

    module.colorpicker = function(elm, callback) {
        require(['colorpicker'], function(){
            $(elm).spectrum({
                className : "colorpicker",
                showInput: true,
                showInitial: true,
                showPalette: true,
                maxPaletteSize: 10,
                preferredFormat: "hex",
                change: function(color) {
                    if($.isFunction(callback)) {
                        callback(color);
                    }
                },
                palette: [
                    ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", "rgb(153, 153, 153)","rgb(183, 183, 183)",
                        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(239, 239, 239)", "rgb(243, 243, 243)", "rgb(255, 255, 255)"],
                    ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
                        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
                    ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
                        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
                        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
                        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
                        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
                        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
                        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
                        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
                        "rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)",
                        "rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)",
                        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
                        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
                ]
            });
        });
    }

    module.editor = function(name){
        return {
            getHtml : function() {
                return $(':hidden[name=' + name + ']').val();
            },
            getText : function() {
                return $($(':hidden[name=' + name + ']').val()).text();
            }
        };
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
            $(document.body).append('<div id="' + options.containerName + '" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>');
            modalobj = $('#' + options.containerName);
        }
        html =
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
        var modalobj = module.dialog('系统提示', content, footer, {'containerName' : 'modal-message'});
        modalobj.find('.modal-content').addClass('alert alert-'+type);
        if(redirect) {
            var timer = '';
            timeout = 3;
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
        modalobj.modal('show');
        return modalobj;
    };

    module.map = function(val, callback){
        require(['map'], function(BMap){
            if(!val) {
                val = {};
            }
            if(!val.lng) {
                val.lng = 116.403851;
            }
            if(!val.lat) {
                val.lat = 39.915177;
            }
            var point = new BMap.Point(val.lng, val.lat);
            var geo = new BMap.Geocoder();

            var modalobj = $('#map-dialog');
            if(modalobj.length == 0) {
                var content =
                    '<div class="form-group">' +
                        '<div class="input-group">' +
                        '<input type="text" class="form-control" placeholder="请输入地址来直接查找相关位置">' +
                        '<div class="input-group-btn">' +
                        '<button class="btn btn-default"><i class="icon-search"></i> 搜索</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div id="map-container" style="height:400px;"></div>';
                var footer =
                    '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                        '<button type="button" class="btn btn-primary">确认</button>';
                modalobj = module.dialog('请选择地点', content, footer, {containerName : 'map-dialog'});
                modalobj.find('.modal-dialog').css('width', '80%');
                modalobj.modal({'keyboard': false});

                map = module.map.instance = new BMap.Map('map-container');
                map.centerAndZoom(point, 12);
                map.enableScrollWheelZoom();
                map.enableDragging();
                map.enableContinuousZoom();
                map.addControl(new BMap.NavigationControl());
                map.addControl(new BMap.OverviewMapControl());
                marker = module.map.marker = new BMap.Marker(point);
                marker.setLabel(new BMap.Label('请您移动此标记，选择您的坐标！', {'offset': new BMap.Size(10,-20)}));
                map.addOverlay(marker);
                marker.enableDragging();
                marker.addEventListener('dragend', function(e){
                    var point = marker.getPosition();
                    geo.getLocation(point, function(address){
                        modalobj.find('.input-group :text').val(address.address);
                    });
                });
                function searchAddress(address) {
                    geo.getPoint(address, function(point){
                        map.panTo(point);
                        marker.setPosition(point);
                        marker.setAnimation(BMAP_ANIMATION_BOUNCE);
                        setTimeout(function(){marker.setAnimation(null)}, 3600);
                    });
                }
                modalobj.find('.input-group :text').keydown(function(e){
                    if(e.keyCode == 13) {
                        var kw = $(this).val();
                        searchAddress(kw);
                    }
                });
                modalobj.find('.input-group button').click(function(){
                    var kw = $(this).parent().prev().val();
                    searchAddress(kw);
                });
            }
            modalobj.off('shown.bs.modal');
            modalobj.on('shown.bs.modal', function(){
                marker.setPosition(point);
                map.panTo(marker.getPosition());
            });

            modalobj.find('button.btn-primary').off('click');
            modalobj.find('button.btn-primary').on('click', function(){
                if($.isFunction(callback)) {
                    var point = module.map.marker.getPosition();
                    geo.getLocation(point, function(address){
                        var val = {lng: point.lng, lat: point.lat, label: address.address};
                        callback(val);
                    });
                }
                modalobj.modal('hide');
            });
            modalobj.modal('show');
        });
    }; // end of map

    module.iconBrowser = function(callback){
        var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
        var modalobj = module.dialog('请选择图标',['./index.php?c=utility&a=icon&callback=selectIconComplete'],footer,{containerName:'icon-container'});
        modalobj.modal({'keyboard': false});
        modalobj.find('.modal-dialog').css({'width':'70%'});
        modalobj.find('.modal-body').css({'height':'70%','overflow-y':'scroll'});
        modalobj.modal('show');

        window.selectIconComplete = function(ico){
            if($.isFunction(callback)){
                callback(ico);
                modalobj.modal('hide');
            }
        };
    }; // end of icon dialog

    module.linkBrowser = function(callback){
        var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
        var modalobj = module.dialog('请选择链接',['./index.php?c=utility&a=link&callback=selectLinkComplete'],footer,{containerName:'link-container'});
        modalobj.modal({'keyboard': false});
        modalobj.find('.modal-body').css({'height':'300px','overflow-y':'auto' });
        modalobj.modal('show');

        window.selectLinkComplete = function(link){
            if($.isFunction(callback)){
                callback(link);
                modalobj.modal('hide');
            }
        };
    }; // end of icon dialog

    module.image = function(val, callback, opts) {
        require(['underscore'], function(_) {
            _.templateSettings = {
                interpolate: /\{\{(.+?)\}\}/g
            };
            var content = '' +
                '<ul class = "nav nav-tabs" style="margin:auto -10px;padding:0 10px;">'+
                '   <li class="active"><a href="#image_browser" data-toggle="tab">网络图片</a></li>'+
                '   <li><a href="#image_upload" data-toggle="tab">上传图片</a></li>'+
                '</ul>'+
                '<div class = "tab-content form-horizontal" style="padding:20px 0;">'+
                '   <div class="tab-pane active" id="image_browser">'+
                '       <div class="form-group">' +
                '           <label class="col-xs-12 col-sm-2 control-label">图片地址</label>' +
                '           <div class="col-sm-10">' +
                '               <div class="input-group">' +
                '                   <input class="form-control" type="text" id="image_url" value="{{val}}" placeholder="请输入图片URL"/>' +
                '                   <span class="input-group-btn">' +
                '                       <button class="btn btn-default btn-browser" type="button">浏览图片空间</button>' +
                '                   </span>' +
                '               </div>' +
                '           </div>' +
                '       </div>' +
                '   </div>'+
                '   <div class="tab-pane" id="image_upload">'+
                '       <iframe width="0" height="0" name="__image_file_uploader" style="display:none;"></iframe>' +
                '       <form action="{{dock}}" enctype="multipart/form-data" method="post" target="__image_file_uploader">'+
                '           <div class="form-group">' +
                '               <label class="col-xs-12 col-sm-2 control-label">上传图片</label>' +
                '               <div class="col-sm-10">' +
                '                   <input type="file" name="file" value="" accept="image/*">'+
                '                   <input type="hidden" name="options">'+
                '               </div>' +
                '           </div>' +
                '       </form>' +
                '   </div>' +
                '</div>';
            content = _.template(content, {
                dock: window.__public__ + '../index.php?s=bench/utility/file&do=upload&type=image&callback=uploaderImageComplete',
                val: val
            });
            var footer =
                '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                '<button type="button" class="btn btn-primary">确认</button>';
            var modalobj = module.dialog('请选择图片', content, footer, {containerName: 'image-container'});
    
            modalobj.modal({'keyboard': false});
            modalobj.find('button.btn-browser').off('click');
            modalobj.find('button.btn-browser').on('click', function(){
                var dialog = module.dialog('浏览图片空间的图片', '正在加载', footer, {containerName: 'image-container'});
                dialog.find('.modal-dialog').css('width', '80%');
                dialog.find('.modal-dialog').css('min-height', 600);
                dialog.modal('show');
    
                window.imageBrowser = {
                    attachpath : '',
                    browser: function(path){
                        if(!path){
                            path = '';
                        }
                        var url = window.__public__ + '../index.php?s=bench/utility/file&do=browser&type=image&callback=imageBrowser&path=' + path + '&file=' + val;
                        dialog.find('.modal-body').load(url);
                    },
                    select: function(r) {
                        callback({filename: r.filename, url: r.url});
                        modalobj.modal('hide');
                    },
                    delete: function(file) {
                        var url = window.__public__ + '../index.php?s=bench/utility/file&do=delete&path=' + path;
                        $.get(url).success(function(dat){
                            if(dat == 'success') {
                                window.imageBrowser.browser(file);
                            } else {
                                var o = $.parseJSON(dat);
                                module.message(o.message, '', 'info');
                            }
                        });
                    }
                };
                var def = '';
                if((/^images\/.*/i).test(val)) {
                    def = val.replace(/\/[^\/]+?$/g, '').replace(/^images\//, '');
                }
                window.imageBrowser.browser(def);
            });
            modalobj.find(':hidden[name="options"]').val(opts);
            modalobj.find('button.btn-primary').off('click');
            modalobj.find('button.btn-primary').on('click', function(){
                if(modalobj.find('.nav.nav-tabs li').eq(0).hasClass('active')) {
                    var url = modalobj.find('#image_url').val();
                    var reg = /^images\/[\d]+\/[\d]+\/[\d]+\/[\S]+/i;
                    if(reg.test(url)){
                        callback({filename: url, url: module.tomedia(url)});
                        modalobj.modal('hide');
                        return;
                    }
                    reg = /^images\/global\/[\S]+/i;
                    if(reg.test(url)){
                        callback({filename: url, url: module.tomedia(url)});
                        modalobj.modal('hide');
                        return;
                    }
                    var httpreg = /^http:\/\/[^\S]*/i;
                    if(httpreg.test(url)){
                        callback({filename: module.tomedia(url), url: module.tomedia(url)});
                        modalobj.modal('hide');
                    }
                } else {
                    modalobj.find('form')[0].submit();
                }
            });
            module.preview('上传图片');
            window.uploaderImageComplete = function(r){
                if(r && r.filename && r.url) {
                    callback({filename: r.filename, url: r.url});
                    modalobj.modal('hide');
                } else {
                    module.message(r.message, '', 'error');
                }
            };
        });
    }; // end of image

    module.audio = function(val, callback, opts) {
        var content = 	'<ul class = "nav nav-tabs" style="margin:auto -10px;padding:0 10px;">'+
            '<li class="active"><a href="#audio_browser" data-toggle="tab">网络音乐</a></li>'+
            '<li><a href="#audio_upload" data-toggle="tab">上传音乐</a></li>'+
            '</ul>'+
            '<div class = "tab-content form-horizontal" style="padding:20px 0;">'+
            '<div class="tab-pane active" id="audio_browser">'+
            '<div class="form-group">' +
            '<label class="col-xs-12 col-sm-2 control-label">音乐地址</label>' +
            '<div class="col-sm-10">' +
            '<div class="input-group">' +
            '<input class="form-control" type="text" id="audio_url" value="' + val + '" placeholder="请输入音乐URL"/>' +
            '<span class="input-group-btn">' +
            '<button class="btn btn-default btn-browser" type="button">浏览音乐空间</button>' +
            '</span>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'+
            '<div class="tab-pane" id="audio_upload">'+
            '<iframe width="0" height="0" name="__audio_file_uploader" style="display:none;"></iframe>' +
            '<form action="./index.php?c=utility&a=file&do=upload&type=audio&callback=uploaderAudioComplete" enctype="multipart/form-data" method="post" target="__audio_file_uploader">'+
            '<div class="form-group">' +
            '<label class="col-xs-12 col-sm-2 control-label">上传音乐</label>' +
            '<div class="col-sm-10">' +
            '<input type="file" name="file">'+
            '<input type="hidden" name="options">'+
            '</div>' +
            '</div>' +
            '</form>' +
            '</div>' +
            '</div>';
        var footer =
            '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                '<button type="button" class="btn btn-primary">确认</button>';
        var modalobj = module.dialog('请选择音乐', content, footer, {containerName: 'audio-container'});

        modalobj.modal({'keyboard': false});
        modalobj.find('button.btn-browser').off('click');
        modalobj.find('button.btn-browser').on('click', function(){

            var dialog = module.dialog('浏览音乐空间的音乐', '正在加载', footer, {containerName: 'audio-container'});
            dialog.find('.modal-dialog').css('width', '80%');
            dialog.find('.modal-dialog').css('min-height', 600);
            dialog.modal('show');

            window.audioBrowser = {
                attachpath : '',
                direct: function(val){
                    var strs= val.split("/");
                    dialog.find('.modal-body').load('./index.php?c=utility&a=file&do=browser&type=audio&callback=audioBrowser&path=' + '/'+strs[2]+'/'+strs[3], function(){
                        var bread = dialog.find('.modal-body').find('.breadcrumb');
                        bread.empty();
                        bread.append('<li><a herf="javascript:;" onclick="audioBrowser.browser(\'/\');"><i class="fa fa-home">&nbsp;</i></a></li>');
                        var str = '/'+strs[2];
                        bread.append('<li><a herf="javascript:;" onclick="audioBrowser.browser(\''+str+'\');">'+strs[2]+'</a></li>');
                        str = str +'/'+strs[3];
                        bread.append('<li><a herf="javascript:;" onclick="audioBrowser.browser(\''+str+'\');">'+strs[3]+'</a></li>');
                        var thumb = dialog.find('div.thumbnail[title="'+strs[4]+'"]');
                        if(thumb.length>0){
                            thumb.addClass('active')
                        }
                    });
                },
                browser: function(path){
                    dialog.find('.modal-body').load('./index.php?c=utility&a=file&do=browser&type=audio&callback=audioBrowser&path=' + path, function(){
                        var bread = dialog.find('.modal-body').find('.breadcrumb');
                        bread.empty();
                        var strs = [];
                        if(path == '/'){
                            bread.append('<li><a herf="javascript:;" onclick="audioBrowser.browser(\'/\');"><i class="fa fa-home">&nbsp;</i></a></li>');
                        } else {
                            strs= path.split("/");
                            var str = '';
                            bread.append('<li><a herf="javascript:;" onclick="audioBrowser.browser(\'/\');"><i class="fa fa-home"></i></a></li>');
                            for(var i=1; i<strs.length; i++){
                                str = str + '/'+strs[i];
                                bread.append('<li><a herf="javascript:;" onclick="audioBrowser.browser(\''+str+'\');">'+strs[i]+'</a></li>');
                            }
                        }
                    });
                },
                select: function(r) {
                    callback({filename: r.filename, url: r.url});
                    modalobj.modal('hide');
                },
                delete: function(file, path){
                    $.get('./index.php?c=utility&a=file&do=delete&type=audio&file=' + file).success(function(dat){
                        if(dat == 'success') {
                            window.audioBrowser.browser(path);
                        } else {
                            var o = $.parseJSON(dat);
                            module.message(o.message);
                        }
                    });
                }
            };
            window.audioBrowser.attachurl = (function(){
                var url=window.document.location.href;
                var pathName = window.document.location.pathname;
                var pos = url.indexOf(pathName);
                var host = url.substring(0,pos);
                return host + '/attachment/';
            })();

            val = val.replace(window.audioBrowser.attachurl, '');
            val = val.replace(/(^\s*)|(\s*$)/g,"");
            var reg = /^audios\/[\d]+\/[\d]+\/[\d]+\/[\S]+/i;
            if(reg.test(val)){
                window.audioBrowser.direct(val);
            } else {
                window.audioBrowser.browser('/');
            }
        });
        modalobj.find('button.btn-primary').off('click');
        modalobj.find('button.btn-primary').on('click', function(){
            if(modalobj.find('.nav.nav-tabs li').eq(0).hasClass('active')) {
                var url = modalobj.find('#audio_url').val();
                callback({filename: url, url: url});
                modalobj.modal('hide');
            } else {
                modalobj.find(':hidden[name="options"]').val(opts);
                modalobj.find('form')[0].submit();
            }
        });
        require(['filestyle'], function($){
            modalobj.find(':file[name="file"]').filestyle({buttonText: '上传音乐'});
        });
        window.uploaderAudioComplete = function(r){
            if(r && r.filename && r.url) {
                callback({filename: r.filename, url: r.url});
                modalobj.modal('hide');
            } else {
                module.message(r.message,'','error');
            }
        };
    }; // end of audio
    
    return module;
});
