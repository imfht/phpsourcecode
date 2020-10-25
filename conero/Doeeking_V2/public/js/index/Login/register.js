$(function(){
    //alert();
    Cro.formAlert('close');
    // 密码确认
    $('#ipt_commandChck').keydown(function(){
        Cro.passwordChck();
    });
    $('#ipt_commandChck').blur(function(){
        Cro.passwordChck();
    });
    // 用户存在性检测
    $('#ipt_nick').blur(function(){
        var nick = $('#ipt_nick').val();
        if(nick){
            var reg = /^[a-z0-9_-]{3,30}$/i;
            if(!nick.match(reg)){
                var text = "昵称【"+nick+"】无效，昵称只能包含数字、字母、下划线、横杆、$、@、#、*等，且长度为范围3-30";
                Cro.formAlert(text);
                $('#ipt_nick').val('');
                return;
            }
            Cro.dataInDb('net/user',{'user_nick':nick},function(data){
                if(data == 'Y'){
                    Cro.formAlert('【'+nick+"】用户已经存在，请更换其他的名称");
                    $('#ipt_nick').val('');
                }else{
                    Cro.formAlert('【'+nick+"】用户有效:)-",true);
                }
            });
        }else{
            Cro.formAlert('close');
        }
    });
    // 保存提交
    $('#save').click(function(){
        var psw = $('#ipt_command').val();
        var pswCk = $('#ipt_commandChck').val();
        if(psw && pswCk && psw != pswCk){
            Cro.formAlert('密码前后不一致');
            return false;
        }
        return true;
    });
});
var Cro = new Conero();
// 表单提示
Cro.formAlert = function(text,type){
    if(text == 'close'){
        $('#form-alert').hide();
        return;
    }
    $('#form-alert').show();
    $('#form-alert').html('');
    type = this.empty(type)? 'danger':type;
    if('danger' != type) type = 'success';
    $('#form-alert').attr('class','alert alert-'+type);
    var tpl = {"danger":"警告","success":"正常"};
    var aletrHtml = '<strong>'+tpl[type]+'!</strong> '+text+'.';
    $('#form-alert').html(aletrHtml);
}
// 错误保存
Cro.formErrorSign = function(sel,text){
    var dom = $(sel);
    if(dom.length == 0) return '';
    if(text == 'remove'){
        dom.removeAttr('_error');
        dom.removeAttr('_error_sign');
        return;
    }
    dom.attr('_error',text);
    dom.attr('_error_sign','Y');
}
// 错误保存-检测
Cro.formErrorSignChck = function(){
    var dom = $('[_error_sign="Y"]');
    var len = dom.length;
    var v;
    if(len > 0){
        for(var i=0; i<len; i++){
            v = $(dom[i]);
            v.focus();
            Cro.formAlert(v.attr('_error'));
            return true;
        }
    }
    return false;
}
// 密码检测
Cro.passwordChck = function(){
    var psw = $('#ipt_command').val();
    var text = "";
    if(Cro.empty(psw)){
        text = "请先-设置密码";      
        Cro.formAlert(text);
        $('#ipt_command').focus();
        return;
    }
    var pswCk = $('#ipt_commandChck').val();
    if(pswCk != psw){
        text = "密码前后不一致";
        Cro.formAlert(text);
        Cro.formErrorSign('#ipt_commandChck',text);
    }else{
        Cro.formErrorSign('#ipt_commandChck','remove');
        text = "密码写入成功！";
        Cro.formAlert(text,true);
    }
}