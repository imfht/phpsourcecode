$(function(){ 
    // 子账户新增按钮
    $('#new_child_btn').click(function(){
        Cro.modal('#new_child_dance');
    });    
    // 子账户处理表单
        // * 存在性检测
    $('#new_child_dance').find('input[name="user_nick"]').blur(function(){
        //Cro.alertTest();
        app.userNickHaving('checked');});

        // * 保存
    $('#newchild_save_btn').click(function(){
        $('#new_child_info').html('');
        var body = $('#new_child_dance').find('div.modal-body');
        var Nick = body.find('[name="user_nick"]');
        var unick = Nick.val();
        //var preg = app.user_preg;
        var preg = /^[a-zA-Z0-9-_]+$/g;
        // if(unick && !preg.test(unick)){Cro.log(unick,preg.test(unick)); // 本句于 PHP之间存在差异 - 谨慎使用
        if(app.userCheck(unick)){
            Cro.alert('#new_child_info','【'+unick+'】无效，只能为英文字母或数字等组成，且长度不等超过50个字符!!');
            return false;
        }
        var pswd = body.find('[name="command"]').val();
        var pswdCk = body.find('[name="command_chk"]').val();
        if(pswd && pswdCk){
            if(pswd.length < 6){
                Cro.alert('#new_child_info','【登录密码】长度必须大于6位!');
                return false;
            }
            if(pswd != pswdCk){
                Cro.alert('#new_child_info','【登录密码】前后不一致!');
                return false;
            }
            app.userNickHaving(unick);
        }                   
    });
    // * 删除无效的子账户
    $('.delete_link').click(function(){
        var elLi = $(this).parents('li');
        Cro.confirm('您确定删除该子账户吗?',function(){
            var code = elLi.attr("dataid");
            var url = '/conero/center/index/save/admin.html?dataid='+code+'&uid='+app.uInfo('uid')+'&mode='+Base64.encode('detele_'+Math.random());
            location.href = url;
        });            
    }); 
});
// var Cro = new Conero();
Cro.__APP = function(){
    var __APP__ =  function(th){
        this.userNickHaving = function(nick,checked){
            $('#new_child_info').html('');
            if(nick == 'checked'){nick = null;checked = true;}
            if(th.empty(nick)){
                var elNick = $('#new_child_dance').find('input[name="user_nick"]');
                nick = elNick.val();
                if(checked){
                    if(this.userCheck(nick)){
                        th.alert('#new_child_info','【'+nick+'】无效，只能为英文字母或数字等组成，且长度不等超过50个字符!');
                        elNick.val('');
                        return;
                    }
                }
                else{
                    if(this.userCheck(nick))  nick = null;
                }
            }
            if(nick){
                th.dataInDb('net_user',{user_nick:nick},function(data){
                    if(data == 'Y'){
                        th.alert('#new_child_info','【'+nick+'】用户已经存在!');
                        elNick.val('');
                    }
                });
            }
        }
        this.userCheck = function(value){
            if(th.empty(value)) return false;
            var preg = /^[a-zA-Z0-9-_]+$/g;
            return !preg.test(value);
        }
        //this.user_preg = /^[a-zA-Z0-9-_]+$/g; // 数据可能出现问题
        var _uinfo_;
        this.uInfo = function(key){
            if(th.empty(_uinfo_)) _uinfo_ = th.getJsVar('uinfo');
            var data = _uinfo_;
            if(key){
                if(data && th.is_object(data) && data[key]) return data[key];
                return '';
            }
            return data;
        }
    }
    return new __APP__(this);
}
var app = Cro.__APP();