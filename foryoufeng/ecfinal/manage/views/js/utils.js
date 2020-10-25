/**
 * 用来做ajax的增删改查
 * 以jquery扩展的形式给出
 * Created by root on 7/29/16.
 */
(function($){
    $.http = function(url,data,callback){
        if(arguments.length>3){
            type=arguments[arguments.length-1];
        }else{
            type='POST';
        }
        $.ajax({
            type: type,
            url:url,
            data:data,
            beforeSend:function(){
                alert("请求发送之前");
                var $load="<img src='/themes/new/css/imgs/loading.gif' class='loading-img' style='position: fixed;top: 30%;left: 50%;z-index: 100;margin-left: -150px;' alt=''/>";
                $("body").append($load);
            },
            success:function(data){
                try{
                    if(typeof callback=='function')
                        callback(data);

                }catch (e){
                    alert(e.message);
                }
            },
            error:function(){
                alert("请求出错了");
            }
        })
    };
    $.error=function(param){
        var msg=param.msg||'出错了';
        var time=param.time||3000;
        var flag=param.flag||'auto';
        var $errormsg="<div class='error-tishi' style='position: fixed;top: 100px;left: 50%;margin-left: -230px; width: 460px;padding:10px;text-align: center;font-size: 16px;opacity: 0; color: #FFF; background-color: #dd4b39;border-radius: 6px;z-index: 1000000;'><p>"+msg+"</p><span class='closebtn-err' style='position:absolute;top: 0px;right: 8px;cursor: pointer;'>X</span></div>";
        $("body").append($errormsg);
        $(".error-tishi").animate({
            top:"25%",
            opacity:1
        },1000);
        if(flag.indexOf("auto")!=-1){
            setTimeout(function(){
                $(".error-tishi").animate({
                    top:"100px",
                    opacity:0
                },1000,function(){
                    $(".error-tishi").remove();
                });

            },time)
        }
        $(".closebtn-err").click(function(){
            $(".error-tishi").animate({
                top:"100px",
                opacity:0
            },1000,function(){
                $(".error-tishi").remove();
            });

        })
    };
    $.success=function(param){
        var msg=param.msg||'出错了';
        var time=param.time||3000;
        var flag=param.flag||'auto';
        var $successmsg="<div class='success-tishi' style='position: fixed;top: -222px;left: 50%;margin-left: -180px; width: 360px;padding-top: 20px; height: 202px;text-align: center;font-size: 24px;background-color: #00A65A;border-radius: 6px;z-index: 1000000;'><p>"+msg+"</p><span class='closebtn-suc' style='position:absolute;top: 0px;right: 8px;cursor: pointer;'>X</span></div>";
        $("body").append($successmsg);
        $(".success-tishi").animate({
            top:"25%"
        },1000);
        if(flag.indexOf("auto")!=-1){
            setTimeout(function(){
                $(".success-tishi").animate({
                    top:"100%"
                },1000,function(){
                    $(".success-tishi").remove();
                });

            },time)
        }
        $(".closebtn-suc").click(function(){
            $(".success-tishi").animate({
                top:"100%"
            },1000,function(){
                $(".success-tishi").remove();
            });

        })
    };
    $.confirm=function(param){
        var msg=param.msg||'出错了';
        var time=param.time||3000;
        var flag=param.flag||'auto';
        //var callback_sure=param.callback_sure;
        var $confirmmsg="<div class='confirm-tishi' style='position: fixed;top: -222px;left: 50%;margin-left: -180px; width: 360px;padding-top: 20px; height: 202px;text-align: center;font-size: 24px;background-color: #eeeeee;border-radius: 6px;z-index: 1000000;'><p style='line-height: 60px'>"+msg+"</p><button class='surebtn-con' style='margin-right:15px;border: none;border-radius: 4px;background-color: #278FF0;margin-top: 20px;padding: 0px 30px;'>确定</button><button class='closebtn-con' style='border: none;border-radius: 4px;background-color: #278FF0;margin-top: 20px;padding: 0px 30px;'>取消</button></div>";
        $("body").append($confirmmsg);
        $(".confirm-tishi").stop().animate({
            top:"25%"
        },1000);
        if(flag.indexOf("auto")!=-1){
            setTimeout(function(){
                $(".confirm-tishi").stop().animate({
                    top:"100%"
                },1000,function(){
                    $(".confirm-tishi").remove();
                });

            },time)
        }
        $(".closebtn-con").click(function(){
            $(".confirm-tishi").stop().animate({
                top:"100%"
            },1000,function(){
                $(".confirm-tishi").remove();

            });

        });
        $(".surebtn-con").click(function(){
            //callback_sure();
            $(".confirm-tishi").stop().animate({
                top:"100%"
            },1000,function(){
                $(".confirm-tishi").remove();
            });
        });
        function callback_default(){
            alert("默认的点击确定")
        }
    };
    //进行ajax搜索
    $.fn.search = function(data){
            //
            $.post('?c='+data.c+'&a=query',data,function(rs) {

                if (rs.code > 0) {

                    try{
                       $("#data-list").html(rs.content);
                    }catch (e){
                        alert(e.message);
                    }
                }else{
                    alert("服务器错误");
                }
            }
           );
    }


    //进行ajax添加或者修改
    $.fn.edit = function(data,callback){
        //
        $.post('?c='+data.c+'&a=edit',data,function(rs) {

                if (rs.code > 0) {

                    try{
                        if(typeof callback=='function')
                            callback(res.content);
                    }catch (e){
                        alert(e.message);
                    }
                }else{
                    alert("服务器错误");
                }
            }
        );
    }
    /**
     * 验证元素是否为空
     * @param html
     * @returns {boolean}
     */
    $.fn.not_empty=function(html){
        var flag=true;
        $(this).each(function(){
            $(this).focus(function(){
                $(this).siblings('.tishi').remove();
            })
            $(this).siblings('.tishi').remove();
            var value=$(this).val();
            if(!value || value==''){
                flag=false;
                $(this).after("<div class='tishi' style='color:#ff000f;'></div>");
                $(this).siblings('.tishi').html(html);
            }
        });
        return flag;
    }
    /**
     * 根据验证规则检验参数是否合法
     * @param regmsg 验证规则
     * @param txt 失败的提示消息
     * @returns {*} 成功返回校验值，失败返回false
     */
    $.fn.regular=function(regmsg,txt){
        var flag=$(this).val();
        $(this).focus(function(){
            $(this).siblings('.tishi').remove();
        })
        if ($(this).siblings('.tishi').text().toString().length==0) {
            var reg = regmsg;
            $(this).siblings('.tishi').remove();

            if(!reg.test($.trim($(this).val())))
            {
                flag=false;
                $(this).after("<div class='tishi' style='color:#ff000f'></div>");
                $(this).siblings('.tishi').html("");
                $(this).siblings('.tishi').html(txt);
            }
        }
        return flag;

    }
    /**
     * 验证两个密码是否一致
     * @param password
     * @param password2
     * @returns {*} 成功返回密码，失败返回false
     */
    $.password=function(password,password2){
        var password_value = $(password).val();
        var password2_value = $(password2).val();
        var value=password_value;
        $(password).focus(function(){
            $(password).siblings('.tishi').remove();
        })
        $(password2).focus(function(){
            $(password2).siblings('.tishi').remove();
        })

        if(password_value.length <6)
        {
            $(password).siblings('.tishi').remove();
            value=false;
            $(password).after("<div class='tishi' style='color:#ff000f'></div>");
            $(password).siblings('.tishi').html("");
            $(password).siblings('.tishi').html('密码不能小于6位');
        }

        if(password2_value.length <6)
        {
            $(password2).siblings('.tishi').remove();
            value=false;
            $(password2).after("<div class='tishi' style='color:#ff000f'></div>");
            $(password2).siblings('.tishi').html("");
            $(password2).siblings('.tishi').html('密码不能小于6位');
        }

        if(password_value != password2_value ){
            $(password2).siblings('.tishi').remove();
            value=false;
            $(password2).siblings('.tishi').remove();
            $(password2).after("<div class='tishi' style='color:#ff000f'></div>");
            $(password2).siblings('.tishi').html('两次密码不同！');
        }

        return value;

    }

})(jQuery);
(function(window){
    var action={};
    action.param={};
    action.search=function(selected){
        action.param.c=$(':submit').attr('c');
        $(selected).search(action.param);
    }
    action.page_first=function(selected){
        action.param.page=1;
        action.search(selected);
    }
    action.page_last=function(selected){
        var page_count=parseInt($("#page_count").html());
        action.param.page=page_count;
        action.search(selected);
    }
    action.page_next=function(selected){
        var page=parseInt($("#page").html());
        var page_count=parseInt($("#page_count").html());
        if(page<page_count){
            action.param.page=page+1;
            action.search(selected);
        }
    }
    action.page_prev=function(selected){
        var page=parseInt($("#page").html());
        var page_count=parseInt($("#page_count").html());
        if(page>1){
            action.param.page=page-1;
            action.search(selected);
        }
    }
    action.confirm=function(id){
        $("#confirm").attr("action",id);
        $("#confirm").modal('show');
    }
    action.editinput=function(obj,act,id,c){
        var c= c;
        $(obj).nextAll().remove();
        var val = $(obj).text();
        $(obj).hide();
        $("<input type='text' name='demo'>").insertAfter($(obj));
        $("input[name=demo]").focus();
        var tdwidth = parseInt($(obj).css("width"))+36+"px";
        var _this=$(obj).next();//_this是input文本框
        _this.val(val);
        _this.width(tdwidth);
        _this.blur(function(){
            if(_this.val()==""){
                $(obj).text(val);
            }else{
                $(obj).text(_this.val());
            }
            $(obj).show();
            _this.hide();
            var data={};
            data.id=id;
            data.field_value=$(obj).text(); //要修改的值
            data.field=act; //要修改的字段
            $.get('?c='+c+'&a=edit_data',data,function(data) {
                if(data.code <0){
                    alert(data.msg);
                }
            });
        })

    }
    window.action=action;
})(window);
$(function(){
    /**时间选择控件**/
    if($('#start_time').size()>0){
        $('#start_time').datetimepicker({
            lang:'ch',
            format:'Y/m/d',
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$('#datetime2').val()?$('#datetime2').val():false
                })
            },
            timepicker:false
        });
    }
    if($('#end_time').size()>0) {
        $('#end_time').datetimepicker({
            lang: 'ch',
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $('#datetime').val() ? $('#datetime').val() : false
                })
            },
            timepicker: false
        });
    }
    /**时间选择控件结束**/

    if($('#activity_start_time').size()>0){
        $('#activity_start_time').datetimepicker({
            lang:'ch',
            format:'Y/m/d H:i:s',
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$('#datetime2').val()?$('#datetime2').val():false
                })
            },
            timepicker:false
        });
    }
    if($('#activity_end_time').size()>0) {
        $('#activity_end_time').datetimepicker({
            lang: 'ch',
            format: 'Y/m/d H:i:s',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $('#datetime').val() ? $('#datetime').val() : false
                })
            },
            timepicker: false
        });
    }
    /**时间选择控件结束**/

});
$(function(){
    /**时间选择控件**/
    if($('#confirm_start_time').size()>0){
        $('#confirm_start_time').datetimepicker({
            lang:'ch',
            format:'Y/m/d',
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$('#datetime2').val()?$('#datetime2').val():false
                })
            },
            timepicker:false
        });
    }
    if($('#confirm_end_time').size()>0) {
        $('#confirm_end_time').datetimepicker({
            lang: 'ch',
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $('#datetime').val() ? $('#datetime').val() : false
                })
            },
            timepicker: false
        });
    }
    /**时间选择控件结束**/

});

