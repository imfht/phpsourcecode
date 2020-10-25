;(function($, window, undefined) {
    'use strict';

    /**
     * ajax-post
     * 将链接转换为ajax请求，并交给handleAjax处理
     * 参数：
     * data-confirm：如果存在，则点击后发出提示。
     * 示例：<a href="xxx" class="ajax-post">Test</a>
     */
    let ajax_post = function(){
        
        $(document).on('click', '.ajax-post', function (e) {
            //取消默认动作，防止跳转页面
            e.preventDefault();
            //操作表单
            var target_form = $(this).attr('target-form');
            if(target_form){
                var form = $('.' + target_form);
                var query = form.serialize();

                if(query ==''){
                    toast.error('请勾选操作对象!。','danger');
                    return false;
                }
            }

            //获取URL参数（属性）
            let url = $(this).attr('href');
            if ($(this).attr('url')) {
                url = $(this).attr('url');
            }
            if ($(this).attr('data-url')) {
                url = $(this).attr('data-url');
            }
            
            //确认字符串
            if($(this).attr('data-confirm') || $(this).hasClass('confirm')){
                var confirmText = $(this).attr('data-confirm');
                    confirmText = confirmText ? confirmText : "确认要执行该操作吗?";
            }
            
            //如果需要的话，发出确认提示信息
            if (confirmText) {
                modal_confirm(confirmText,function(){
                    execute();
                });
                return false;
            }else{
                execute();
            }
            
            function execute(){
                //发送AJAX请求
                $.post(url, query, function (a, b, c) {
                    handle_ajax(a);
                });
            }
        });
    }

    /**
     * ajax get提交
     */
    let ajax_get = function(){

            $(document).on('click', '.ajax-get', function (e) {
            //取消默认动作，防止跳转页面
            e.preventDefault();
            //获取参数（属性）
            //操作表单
            var target_form = $(this).attr('target-form');
            if(target_form){
                var form = $('.' + target_form);
                var query = form.serialize();

                if(query ==''){
                    toast.error('请勾选操作对象!。','danger');
                    return false;
                }
            }
            //获取URL参数（属性）
            var url = $(this).attr('href');
            if ($(this).attr('url')) {
                url = $(this).attr('url');
            }
            if ($(this).attr('data-url')) {
                url = $(this).attr('data-url');
            }

            if($(this).attr('data-confirm') || $(this).hasClass('confirm')){
                var confirmText = $(this).attr('data-confirm');
                    confirmText = confirmText ? confirmText : "确认要执行该操作吗?";
            }

            //如果需要的话，发出确认提示信息
            if (confirmText) {
                modal_confirm(confirmText,function(){
                    execute();
                });
                return false;
            }else{
                execute();
            }

            function execute(){
                //发送AJAX请求
                $.get(url, function (a) {
                    handle_ajax(a);
                });
            }
        });
    }

    /**
     * ajax-form
     * 通过ajax提交表单，通过oneplus提示消息
     * 示例：<form class="ajax-form" method="post" action="xxx">
     */
    let ajax_form = function(){

        
        $(document).on('submit', 'form.ajax-form', function (e) {

            //取消默认动作，防止表单两次提交
            e.preventDefault();
            let confirmText = $(this).attr('data-confirm');
            let form = $(this);

            if(form.serialize()==''){
                toast.hideLoading();
                toast.error('请勾选操作对象!。','danger');
                return false;
            }
            
            
            //如果需要的话，发出确认提示信息
            if (confirmText) {
                modal_confirm(confirmText,function(){
                    execute();
                });
                return false;
            }else{
                execute();
            }
            
            //执行ajax
            function execute(){
                //禁用提交按钮，防止重复提交

                //let form = $(this);
                $('[type=submit]', form).addClass('disabled');

                //获取提交地址，方式
                let action = form.attr('action');
                let method = form.attr('method');
                
                //检测提交地址
                if (!action) {
                    //获取当前url
                    action = window.location.href;
                }

                //默认提交方式为get
                if (!method) {
                    method = 'get';
                }

                //获取表单内容
                let formContent = form.serialize();

                //发送提交请求
                let callable;
                if (method == 'post') {
                    callable = $.post;
                } else {
                    callable = $.get;
                }
                callable(action, formContent, function (a) {
                    handle_ajax(a);
                    $('[type=submit]', form).removeClass('disabled');
                });
            }
            
            //返回
            return false;
        });
    }

    /**
     * 处理ajax返回结果
     */
    let handle_ajax = function(data) {

        //如果需要跳转的话，消息的末尾附上即将跳转字样
        if (data.url) {
            data.msg += '，页面即将跳转～';
        }

        //弹出提示消息
        if (data.code==1) {
            toast.success(data.msg, 'success');
        } else {
            toast.error(data.msg, 'danger');
        }

        //需要跳转的话就跳转
        var interval = 1*1000;
        if (data.url == "refresh") {
            setTimeout(function () {
                location.href = location.href;
            }, interval);
        } else if (data.url) {
            setTimeout(function () {
                location.href = data.url;
            }, interval);
        }
    }

    /**
     * 模态提示确认操作
     *
     * @return     {boolean}  { description_of_the_return_value }
     */
    let modal_confirm = function(confirmText,callback) {

        if(confirmText == ''){
            confirmText = '确认执行该操作？';
        }
        
        // 自定义模态框样式
        let custom = '<div class="text-center">'+
                        '<i class="fa fa-exclamation-circle"></i>'+
                        '<p class="title">系统提示</p>'+
                        '<p class="desc">'+ confirmText +'</p>'+
                        '<div class="text-center">'+
                            '<button class="btn btn-info confirm margin-right" type="button">确认</button>'+
                            '<button type="button" class="btn" data-dismiss="modal">取消</button>'+
                        '</div>'+
                    '</div>';

        let modal_html = '<div class="modal fade confirm-modal" id="tip_Modal">'+
                        '<div class="modal-dialog modal-tip">'+
                            '<div class="modal-content">'+
                                '<div class="modal-body">'+ custom +'</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';

        if ($('#tip_Modal').length == 0 ) { 
            $('body').append(modal_html);
        } 

        $('#tip_Modal').modal('show');

        $('#tip_Modal').off('click', '.confirm');
        $('#tip_Modal').on('click','.confirm',function(){
            if (typeof callback === "function") {
                callback();
            }
            //关闭模态框
            $('[data-dismiss="modal"]').click();
            //移除dom
            $('#tip_Modal').remove();
            $('.modal-backdrop').remove();
        });
        
        return false;
    }

    $(document).ready(function(){
        //执行
        ajax_post();
        ajax_get();
        ajax_form();
    });

    window.handleAjax = handle_ajax;//兼容写法
    window.handle_ajax = handle_ajax;
    window.modal_confirm = modal_confirm;

}(jQuery, window, undefined));

