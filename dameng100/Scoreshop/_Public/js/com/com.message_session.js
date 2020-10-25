/* ========================================================================
 * MUUCMF: messager_session.js
 * http://muucmf.cn
 * ========================================================================
 * Copyright (c) 2017-2018 muucmf.cn; Licensed MIT
 * ======================================================================== */


(function($, window, undefined) {
    'use strict';

    /* Check jquery */
    if(typeof($) === 'undefined') throw new Error('MUU requires jQuery');
    // muu shared object
    if(!$.muu) $.muu = function(obj) {
        if($.isPlainObject(obj)) {
            $.extend($.muu, obj);
        }
    };
    /**
     * 绑定消息检查
     */
    var bindMessageChecker = function () {

        if (Config.GET_INFORMATION) {
            setInterval(function () {
                checkMessage();
            }, Config.GET_INFORMATION_INTERNAL);
        }
    }
    /**
     * 检查是否有新的消息
     */
     var checkMessage = function() {
        $.get(U('Ucenter/Public/getInformation'), {}, function (msg) {
            if (msg.messages) {
                paly_ios_sound();
                toast.info('有新消息~');
            }

            $('[data-role="now-message-num"]').html(msg.message_count);
            if(msg.message_count==0){
                $('[data-role="now-message-num"]').hide();
            }else{
                $('[data-role="now-message-num"]').show();
            }

        }, 'json');
    }

    /** 绑定发送私信事件**/
    var iMessage = function () {
        $("#iMessageAjaxPost").unbind('click');
        $("#iMessageAjaxPost").click(function () {

            var $this = $(this);
            $this.text("发送中...");

            var to_uid = $("input[name$='iMessageUid']").val();
            var content = $("#iMessageTxt").val();

            $.post(U('Ucenter/Message/postiMessage'), {iMessageUid: to_uid,iMessageTxt: content}, function (msg) {
                if (msg.status) {
                    toast.success(msg.info, '发送成功');
                    $this.text("发送完成");

                    //隐藏对话框
                } else {
                    toast.error(msg.info, '发送失败');
                    $this.text("发送");
                }
            }, 'json');
        })
    }

    function play_bubble_sound() {
        playsound('./Public/lib/toastr/message.wav');
    }
    function paly_ios_sound() {
        playsound('./Public/lib/toastr/tip.mp3');
    }

    var message_type='';
    var init_message=function(){
        $('[data-role="open-slider-box"]').unbind();
        $('[data-role="open-slider-box"]').click(function () {
            toast.showLoading();
            $.post(U('Ucenter/Message/messagetypelist'),{},function(html){
                $('#message-type-box').find('.message-type-list').html(html);
                $('[data-role="open-message-list"]').first().click();
            });
            toast.hideLoading();
        });
    };
    var list_message=function(){
        $('[data-role="open-message-list"]').unbind();
        $('[data-role="open-message-list"]').click(function(){
            message_type = $(this).attr("data-type");
             $.post(U('Ucenter/Message/messagelist'),{tab:message_type},function(html){
                $('.message-info-list').html(html);
            });
        });
    };
    
    var message_pageg = 1;
    var list_message_load_more=function(){

        var r = 10;
        $('.message-info-list').attr('data-page',1)
        $('.loadmore-type-messages').unbind();
        $('.loadmore-type-messages').click(function(){
            var _this = $(this);
            _this.html('加载中');
            message_pageg=message_pageg+1;
            $.post(U('Ucenter/Message/messagelist'),{tab:message_type,page:message_pageg,r:r},function(html){
                if(html.length){
                    $('.message-info-list').append(html);
                    _this.html('加载更多');
                }else{
                    _this.html('已经没有喽');
                }
            });
        });
    };
    
    $.muu({
        check_message: checkMessage,
        bind_message_checker:bindMessageChecker,
        init_message: init_message,
        list_message: list_message,
        list_message_load_more: list_message_load_more,
        send_imessage:iMessage,
    });
  
}(jQuery, window, undefined));

// Initialize
$(document).ready(function(e){
    if (is_login()) {
        $.muu.check_message();//检查一次消息
        $.muu.bind_message_checker();//绑定用户消息
        $.muu.send_imessage(); //绑定发送私信
    }
});