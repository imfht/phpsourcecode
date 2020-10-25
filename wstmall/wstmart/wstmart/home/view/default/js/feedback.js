function save(){
    /* 表单验证 */
    $('#feedbackForm').validator({
        fields: {
            feedbackContent: {
                rule:"required",
                msg:{required:"请输入反馈内容"},
                tip:"请输入反馈内容",
                target:'#feedbackContentMsg'
            },
            feedbackType: {
                rule:"checked;",
                msg:{checked:"请选择反馈问题类型"},
                tip:"请选择反馈问题类型",
            },
            contact: {
                rule:"required",
                msg:{required:"请输入联系方式"},
                tip:"请输入联系方式",
            }
        },
        valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('home/feedbacks/add'),params,function(data,textStatus){
                layer.close(loading);
                var json = WST.toJson(data);
                if(json.status=='1'){
                    WST.msg(json.msg, {icon: 6},function(){
                        location.href = WST.U('home/feedbacks/index');
                    });
                }else{
                    WST.msg(json.msg,{icon:2});
                }
            });
        }
    });
}