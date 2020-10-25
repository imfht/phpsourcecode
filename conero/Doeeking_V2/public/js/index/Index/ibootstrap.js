$(function(){
    // 
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        this.menuCreateAuthLogin();
    }
    // 菜单增加自动登录帮助
    this.menuCreateAuthLogin = function(){   
        var db = th.storage('local').table('user_helper');     
        var key = Base64.encode(location.host);
        var nick = th.getJsVar('nick');
        var command = db.get(key);
        // 跳转至登录 API 中
        if(th.empty(nick) && !th.empty(command)){
            var url = '/conero/index/login/authapi.html';
            var post = {};
            post[key] = command;
            th.post(url,post);
        }
        // 生成设置“记住登录”                        
        else if(!th.empty(nick)){
            if(th.empty(command) && $('#sma_user_helper').length == 0){
                var xhtml = '<li id="sma_user_helper"><a href="javascript:void(0);">记住登录状态</a></li>';
                $('#setting_menu_action').append(xhtml);
                // 保存
                $('#sma_user_helper').on('click',function(){
                    th.confirm('您确定要记住登录状态吗，确定以后系统后自动登录。此项操作请在个人电脑上允许！',function(){                      
                        var data = {};
                        data[key] = nick;
                        db.add(data);
                        $('#btsp_modal_confirm').modal('hide');
                    });
                });
            }
            else if(!th.empty(command) && nick == command && $('#sma_user_helper').length == 0){
                // 
                var xhtml = '<li id="sma_user_helper"><a href="javascript:void(0);">注销登录状态</a></li>';
                $('#setting_menu_action').append(xhtml);
                // 保存
                $('#sma_user_helper').on('click',function(){
                    th.confirm('取消该功能后，系统将不再自动为你提供登入功能？',function(){                                          
                        if(db.delete()) $('#sma_user_helper').remove();
                        $('#btsp_modal_confirm').modal('hide');
                    });
                });
            }           
        }
        // 不再记住“登录状态”
        // else if(!th.empty(nick) &&)
    }
});