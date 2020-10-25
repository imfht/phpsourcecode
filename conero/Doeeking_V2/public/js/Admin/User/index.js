$(function(){
    // 修改密码
    $('.change_pswd').click(function(){
        var nick = $(this).text();
        var chagepwd = ''
            + '<input type="password" name="pswd" class="form-control" placeholder="输入新密码">'
            + '<input type="password" name="pswdchk" class="form-control" placeholder="确认新密码">'
            + '<input type="hidden" name="nick" value="'+nick+'">'
            + '<div class="modal_alert" style="margin-top:2px;"></div>'
            ;
        var footer = '<button type="button" class="btn btn-primary" Onclick="app.chagepwd(this)">保存</button>';
        var opt = {content:chagepwd,footer:footer};
        opt['title'] = '【'+nick+"】密码修改";
        Cro.modal(opt);
    });
});
var Cro = new Conero();
Cro.objApp = function(){
    var __app = function(th){
        this.chagepwd = function(dom){
            dom = $(dom);
            var modal = dom.parents('[role="dialog"]');
            var container = modal.find('div.modal_alert');
            var pwd = modal.find('[name="pswd"]');
            var vpwd = pwd.val();        
            if(th.empty(vpwd)){
                pwd.focus();
                Cro.alert(container,'密码不能为空！');
                return;
            }
            var pwdck = modal.find('[name="pswdchk"]');
            var vpwdck = pwdck.val(); 
            if(th.empty(vpwdck)){
                Cro.alert(container,'密码不能为空！');
                pwdck.focus();return;
            }
            if(vpwd != vpwdck){
                Cro.alert(container,'密码前后不一致！');
                pwdck.focus();
                return;
            }
            var nick = modal.find('[name="nick"]').val();
            $.post('/conero/admin/user/ajax.html',{item:'index/change_pswd',pswd:vpwd,nick:nick},function(data){
                if('Y' == data){
                    modal.modal('close');
                }else{
                    alert('密码修改失败！');
                }
            });
        }
    };
    return new __app(this);
}
var app = Cro.objApp();