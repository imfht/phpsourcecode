/**
 * 三叶草IT QQ-316262448
 * www.sycit.cn, hyzwd@outlook.com
 * Created by Peter on 2017/8/24.
 */
+ function ($) {
    "use strict";
    //弹出窗口模板
    var _template =
        '<div class="modal fade dialog" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">'+
        '<div class="modal-dialog" role="document">'+
        '<div class="modal-content">'+
        '<div class="modal-header">'+
        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
    '<h4 class="modal-title"></h4>'+
    '</div>'+
    '<div class="modal-body"><p>这里是内容</p><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div>'+
    '</div>'+
    '</div>'+
    '</div>';

    //默认参数
    var _defaults = {
        'backdrop' : 'static', //'static'：静态模式窗口，鼠标点击背景不关闭窗口
                               //false：不显示背景遮罩
                               //true，显示背景遮罩，但鼠标点击遮罩会关闭窗口
        'title' : '提示信息',
        'width' : 700,
        'height' : 400,
        //'animation' : false,   //动画效果，默认关闭
        'close' : true,//窗口标题栏的关闭按钮是否启用
        'dialogMaxButton' : true,//窗口标题栏的最大化按钮是否启用
        'closeButton' : false, //对话框底部的关闭窗口按钮
        'scroll' : true,       //是否显示滚动条，默认显示
        'drag' : true,         //是否允许窗口进行拖拽
        'href' : false,        // 加载新页面
        'target' : false,        // ID
        'fullWidth' : false,   //是否展示全宽度窗口
        'customClass' : undefined,//自定义样式，它会添加到弹出窗口的最外层DIV上
        'show' : false,        //初始化时即显示模态对话框
        'onShow' : $.noop,     //显示对话框前执行的回调
        'onShowed' : $.noop,   //显示完成对话框后执行的回调
        'onHide' : $.noop,     //关闭/隐藏对话框前执行的回调
        'onHidden' : $.noop,   //关闭/隐藏对话框后执行的回调
        'callback' : $.noop    //窗口回调函数，参数1：回调后返回的数据(callback(data))
    };

    //
    var Combine = {
        /**
         * 合并默认参数与用户传递参数
         * @param {object} param - 用户传递参数集
         *
         * @return {object} 合并后参数集
         *
         * @access private
         */
        setParam : function(param){
            return $.extend({},_defaults,param);
        },

        /**
         * 生成窗口对象
         * @param {object} p - 插件参数集
         * @param {object} obj - 页面对象的html元素或jquery对象，用于将内容直接放入窗口中，若传递空值则使用iframe模式
         *
         * @return {object} 生成完成的窗口对象
         *
         * @access private
         */
        buildDialog: function (p, obj) {
            var template = _template;
            var dialog = $(template);

            //设置标题
            if (p.title) {
                $(".modal-title", $(dialog)).html(p.title)
            } else {
                $('.modal-header', $(dialog)).hide();
            }
            // 标题栏关闭按钮设定
            if(!p.close)$('button.bDialogCloseButton',$(dialog)).hide();

            return dialog;
        },

        /**
         * 在页面上弹出窗口
         * @param {object} dialog - 生成后的窗口对象
         * @param {object} p - 插件参数集
         *
         * @return void
         *
         * @access private
         */
        openDialog : function(dialog,p){
            $('div.modal-dialog',$(dialog)).css({
                'width' : p.width,
                'height' : p.height
            });
            window.top.$(dialog).modal({
                backdrop : p.backdrop
            }).removeClass('hide');
        },

        /**
         * 最大化窗口
         *
         * @access private
         */
        maxWindow : function(dialog,p){
            var $top = window.top.$;
            if(!dialog.max){
                $top(dialog).addClass('maximize');
                dialog.max = true;
            }else{
                $top(dialog).removeClass('maximize');
                dialog.max = false;
            }
            Combine.rePosition(dialog,0);
        },

        /**
         * 重新定位窗口位置(主要是处理垂直高度居中)
         * @param {object} dialog - 生成后的窗口对象
         * @param {number} speed - 动画速度
         *
         * @return void
         * @access private
         */
        rePosition : function(dialog,speed) {
            var
                //获得可视区域的宽度和高度
                viewport_width = $(window.top).width(),
                viewport_height = $(window.top).height(),

                //获得对话框的宽度和高度
                dialog_width = $('div.modal-dialog',$(dialog)).width(),
                dialog_height = $('div.modal-dialog',$(dialog)).height(),

                //计算位置内容
                values = {
                    'left':     0,
                    'top':      0,
                    'right':    viewport_width - dialog_width,
                    'bottom':   viewport_height - dialog_height,
                    'center':   (viewport_width - dialog_width) / 2,
                    'middle':   (viewport_height - dialog_height) / 2
                };

            dialog.dialog_top = values['middle'];

            //停止正在执行的动画
            $('div.modal-dialog',$(dialog)).stop(true);
            $('div.modal-dialog',$(dialog)).css('visibility', 'visible').animate({
                'top':  dialog.dialog_top
            }, (undefined !== $.type(speed) && $.type(speed) == 'number') ? speed : 100);
        },

        /**
         * 为窗口对象绑定事件
         * @param {object} dialog - 生成后的窗口对象
         * @param {object} p - 插件参数集
         *
         * @return void
         *
         * @access private
         */
        bindEvent: function (dialog,p) {
            var $top = window.top.$;
            var topBody = window.top.document.body;

            if(p.onShow && $.isFunction(p.onShow)) $top(dialog).off('show.bs.modal').on('show.bs.modal',function(){
                p.onShow(this);
            });
            if(p.onShowed && $.isFunction(p.onShowed)) $top(dialog).off('shown.bs.modal').on('shown.bs.modal',function(){
                p.onShowed(this);
            });
            if(p.onHide && $.isFunction(p.onHide)) $top(dialog).off('hide.bs.modal').on('hide.bs.modal',function(){
                p.onHide(this);
            });
            // 窗口最大化
            if(p.dialogMaxButton){
                $top('button.bDialogMaxButton',dialog).off('click.bDialog').on('click.bDialog',function(e){
                    e.stopPropagation();
                    Combine.maxWindow(dialog,p);
                });
            }

            $top(dialog).off('hidden.bs.modal').on('hidden.bs.modal',function(e){
                // stop the timeout
                clearTimeout(dialog.timeout);
                if(p.onHidden && $.isFunction(p.onHidden)) p.onHidden(this);
                var data = dialog[0].returnData, callback = dialog.callback;
                if(callback && $.isFunction(callback)) callback(data);
                //在移除窗口之前，先把iframe移除，解决在IE下，窗口上的输入控件获得不了焦点的问题
                if($('iframe',$(this)).size() > 0) $('iframe',$(this)).remove();
                $(this).remove();
                if($('[role="dialog"]',$(topBody)).size() > 0) $('[role="dialog"]:last',$(topBody)).addClass('dialogInActive');
            });
            if(!p.fullWidth){
                $top('dialog:last',$top(topBody)).off('click.dialog').on('click.dialog',function(e){
                    var srcEl = e.target || e.srcElement;
                    if($(srcEl).is('div.sycDialog')){
                        var that = $top('div.bDialog:last',$top(topBody));
                        $top(that).removeClass('animated').removeClass('shake');
                        setTimeout(function () {
                            that.addClass('animated').addClass('shake');
                        }, 0);
                    }
                });
            }

            //浏览器窗口尺寸变化时，自动对窗口位置进行调整
            $top(window.top).bind('resize.dialog', function() {
                // clear a previously set timeout
                // this will ensure that the next piece of code will not be executed on every step of the resize event
                clearTimeout(dialog.timeout);
                // set a small timeout before doing anything
                dialog.timeout = setTimeout(function() {
                    // reposition the dialog box
                    Combine.rePosition(dialog);
                }, 100);
            })
        },

        /**
         * 获得当前获得焦点的窗口对象
         * @return {object} 窗口对象
         * @access private
         */
        getDialog : function(){
            var dlg = $('[role="dialog"]',$(window.top.document.body));
            return (dlg && $(dlg).size() == 1) ? dlg : null;
        }
    };

    //
    var sycDialog = {
        //打开对话框
        //p:参数集
        //obj:jquery对象，用于网页片断式的显示内容，若设置了URL方式打开窗口，则不需要设置该参数
        open: function (param,obj) {
            // 合并参数
            var p = Combine.setParam(param);
            var dialog = Combine.buildDialog(p,obj);

            Combine.openDialog(dialog, p);

            return dialog;
        },
        //关闭当前弹出窗口
        closeCurrent : function(data){
            var dlg = this.getDialog();
            if(dlg && $(dlg).size() == 1){
                //清除参数
                dlg.callback = null;
                dlg[0].selectorparams = null;
                dlg[0].returnData = data;
                $("button[data-dismiss=\"modal\"]",dlg).click();
            }else console.warn('当前被激活的模态窗口不存在或多于一个，请检查功能是否正常！');
        },
        //获得弹出窗口对象
        getDialog : function(){
            return Combine.getDialog();
        },
        // 获得选择器中的传递参数
        getDialogParams : function(){
            var dlg = Combine.getDialog();
            return dlg ? dlg[0].params : null;
        },
        // 获得选择器中的回调函数
        getDialogCallback : function(dlg){
            return dlg ? dlg[0].callback : null;
        }
    };
    if(!window.top.sycDialog) window.top.sycDialog = sycDialog;
    window.sycDialog = sycDialog;
}(jQuery);