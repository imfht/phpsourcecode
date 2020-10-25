//设置全局AJAX默认选项。
$.ajaxSetup({cache : false});
//jQuery常用函数封装
(function ($) {
    //删除
    $.fn.isDel = function (){
        return this.each(function() {
            $(this).click(function(){
                var url = $(this).attr("url");
                var css = this;
                parent.layer.confirm('确认要删除本资源?',{icon: 3,title:'友情提示'}, function(index){
                    $.getJSON(url,function(data) {
                        parent.layer.close(index);
                        if (data.code == 200) {
                            $(css).parents("tr").remove();
                            $(css).parent().remove();
                            $.isEmptyObject(data.url) ? '' : window.location.replace(data.url);
                        }else{
                            parent.layer.msg(data.msg,{icon:5,shade:0.5})
                        }
                    });
                });
            })
        });
    };
    //点击URL请求
    $.fn.actUrl = function (options) {
        $(this).click(function () {
            $(this).requestUrl(options);
        })
    };
    //表单改变值请求
    $.fn.changeUrl = function (options) {
        var defaults = {
            ispost: true,
            confirm: false,
        }
        var options = $.extend(defaults, options);
        $(this).change(function () {
            if ($.isEmptyObject(options.param)) {
                options.param =  {
                    id : $(this).attr('id'),
                    sort: $(this).val(),
                }
            }
            $(this).requestUrl(options);
        })
    };
    /**
     * 通过AJAX请求
     */
    $.fn.requestUrl = function (options) {
        var defaults = {
            confirm: true,
            parent:false,
            ispost: false,
            msg:'确认要操作当前资源?',
        }
        var options = $.extend(defaults, options);
        console.log('actUrl默认参数');
        console.log(options);
        return this.each(function () {
            if ((options.ispost && $.isEmptyObject(options.param)) || (options.ispost && $.isEmptyObject(options.url))) {
                console.log('%cJQuery的actUrl参数在{ispost:true}下必须设置param和url对象参数\n{\n    ispost:true,\n    param:{}\n    url:"http://***"\n}',"color:red");
                return;
            }
            var param = $.isEmptyObject(options.param) ? {} : options.param
            var url  = $.isEmptyObject(options.url) ? $(this).attr("url") : options.url;
            var load = 2
            var callfun = function (data) {
                parent.layer.close(load);
                if (data.code == 200) {
                    if (options.parent) {
                        $.isEmptyObject(data.url) ? window.parent.location.reload() : window.parent.location.replace(data.url);
                    } else {
                        $.isEmptyObject(data.url) ? window.location.reload() : window.location.replace(data.url);
                    }
                }else{
                    parent.layer.msg(data.msg,{icon:5,shade:0.5})
                }
            }
            if (options.confirm) {
                parent.layer.confirm(options.msg,{ icon: 3, title: '友情提示' },function (index) {
                    parent.layer.close(index);
                    load = parent.layer.load(0, { shade: [0.3, '#393D49'], time: 3000 }); 
                    if (options.ispost) {
                        $.post(url,param,callfun)
                    } else {
                        $.getJSON(url,callfun,"json");
                    }
                });   
            } else {
                if (options.ispost) {
                    $.post(url,param,callfun)
                } else {
                    $.getJSON(url,callfun,"json");
                }
            }
        });
    }
    //弹出窗口
    $.fn.win = function (options) {
        $(this).click(function () {
            $(this).popup(options);
        })
    };
    $.fn.popup = function (options){
        var defaults = {
            url: $(this).attr("url"),
            input: $(this).attr('data') ? $(this).attr('data') : $(this).attr('id'),
            area: ['60%', '70%'],
            title:'快捷窗口',
            reload: 0
        }, options = $.extend(defaults, options);
        console.log('popup默认参数');
        console.log(options);
        return this.each(function () {
            options.input != void 0 && (options.url = options.url + '?input=' + options.input);
            parent.layer.open({
                type: 2, title: options.title,area: options.area, content: options.url, success: function (layero,index) {
                    parent.layer.getChildFrame('body', index).addClass(window.name);
                }, end: function () {
                    1 == options.reload && parent.$("#" + window.name)[0].contentWindow.location.reload();
                }
            });
        });
    };
    //表单
    $.fn.isForm = function (options){
        var defaults = {types: 0,iframe:0,upload:''}
        var options = $.extend(defaults, options);
        console.log('isForm默认参数');
        console.log(options);
        return this.each(function (){
            $(this).validatorForm(options); //表单处理
            $(".ui-editor").length > 0 && $(this).find(".ui-editor").editor(options.upload);
            $(".ui-editor").length > 0 && $(this).find('.ui-editor').editor(options.upload)//编辑器
            $(".ui-mieditor").length > 0 && $(this).find('.ui-mieditor').minieditor(options.upload)//编辑器
            $(".ui-date").length > 0 && $(this).find('.ui-date').layday()   //时间选择器
            $(".ui-time").length > 0 && $(this).find('.ui-time').laytime()  //时间选择器
            $(".ui-color").length > 0 && $(this).find('.ui-color').color()   //颜色选择器
            $(".ui-upload").length > 0 && $(".ui-upload").click(function () {$(this).popup({url:options.upload})}) //上传附件
        });
    };
    //表单验证
    $.fn.validatorForm = function (options){
        return this.each(function () {
            var win = parent.layer.getFrameIndex(window.name); 
            if (options.types == 0) {
                $(this).Validform({
                    btnSubmit: ".submit",showAllError:false,tiptype:3,ajaxPost: true,postonce:true,ignoreHidden:true,beforeSubmit:function(){
                        parent.layer.load(0,{shade:[0.2,'#000'],time:1500}); 
                    }, callback: function (data) {
                        if (data.code == "200") {
                            parent.layer.alert(data.msg, {icon: 1, closeBtn: 0 },function(index){
                                if(typeof data.parent != "undefined"){
                                    $.isEmptyObject(data.url) ? window.parent.location.reload(): window.parent.location.replace(data.url);
                                }else{
                                    $.isEmptyObject(data.url) ? window.location.reload(): window.location.replace(data.url);
                                }
                                parent.layer.close(index);
                                parent.layer.close(win);
                            });
                        }else if(data.code == "302"){
                            if(typeof data.parent != "undefined"){
                                $.isEmptyObject(data.url) ? window.parent.location.reload(): window.parent.location.replace(data.url);
                            }else{
                                $.isEmptyObject(data.url) ? window.location.reload(): window.location.replace(data.url);
                            }
                            parent.layer.close(win);
                        } else {
                            parent.layer.alert(data.msg,{icon:5,closeBtn:0});
                        }
                    }
                })
            }else{
                $(this).Validform({btnSubmit: ".submit",showAllError:false,postonce:true,tiptype:function(msg,o){if(o.type ==3){layer.tips(msg,'.submit')}},ignoreHidden:true,beforeSubmit:function(){
                    parent.layer.load(0,{shade:[0.2,'#000'],time:1500}); 
                }})
            }
        });
    };
    //颜色
    $.fn.color = function (){
        return this.each(function (){
            $(this).soColorPacker();
        });
    };
    //时间插件不带时间
    $.fn.layday = function () {
        this.each(function () {
            laydate.render({elem:this});
        });
    };
    //时间插件带时间
    $.fn.laytime = function () {
        this.each(function () {
            laydate.render({elem:this,'type':'datetime'});
        });
    };
    //编辑器调用
    $.fn.editor = function (uploads){
        return this.each(function (){
            var editorConfig = {
                allowFileManager: false, uploadJson: uploads, urlType: "domain",width: '100%',height:'450px',themeType : 'simple',
                items : ['fontname','fontsize','|','forecolor','hilitecolor','bold', 'italic','underline','removeformat','|','justifyleft','justifycenter','justifyright','|', 'image', 'multiimage','media','table','|','link','unlink','|','clearhtml','|','source','fullscreen'],
                afterBlur: function () {this.sync()}
            };
            KindEditor.create(this,editorConfig);
        });
    };
    //编辑器调用
    $.fn.minieditor = function (uploads){
        return this.each(function (){
            var editorConfig = {
                allowFileManager : false,uploadJson:uploads,urlType: "domain",width: '100%',height:'250px',themeType : 'simple',
                items : ['fontname','fontsize','|','forecolor','hilitecolor','bold', 'italic','underline','removeformat','|', 'justifyleft','justifycenter','justifyright','|','image','link','unlink','|','source','fullscreen'],
                afterBlur: function () {this.sync()}
            };
            KindEditor.create(this,editorConfig);
        });
    };
})(jQuery);