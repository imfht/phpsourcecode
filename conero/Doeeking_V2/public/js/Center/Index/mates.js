$(function(){
    // 数据表格
    var formGrid = Cro.formListEvent({
        table:'#mate_data_acter ',
        pk:  'mate_no',
        url:'/conero/center/index/save/mates.html'
    },{
        afterAddRow: function(tr){
            var Mcode = tr.find('input[name="mate_code"]');
            var Td = Mcode.parents('td');
            var Cbox = Td.find('[type="checkbox"]');
            Mcode.attr("disabled",true);
            Cbox.attr("checked",false);
            Cbox.removeAttr("disabled");            
            Td.find('[name="command"]').removeAttr("disabled");            
        }
    });

    // 数据修改
    $('.edit_link').click(function(){
        var tr = $(this).parents("tr");
        var dataid = tr.attr('dataid');
        if(formGrid.havePkey(dataid)){
            Cro.modal_alert('该项数据列已经加入到维护列表了！');return;
        }
        if(dataid){
            $.post('/conero/center/index/ajax/mates.html',{item:'getData',dataid:dataid},function(data){
                formGrid.addRowByRecord(data,
                    function(record){
                        $('#collapseOne').collapse('show')
                    },
                    function(last){
                        var Pswd = last.find('[name="command"]');
                        var Td = Pswd.parents('td');
                        Td.find('[type="checkbox"]').attr('disabled',true);
                        Pswd.attr('disabled',true);
                        Cro.log(last);
                    }
                );
            });
        }
    });
    // 数据删除
    $('.del_link').click(function(){
        var dom = $(this);
        var text = '您确定要删除数据吗，此操作可能导致数无法恢复?';
        Cro.confirm(text,function(){
            var tr = dom.parents("tr");
            var dataid = tr.attr('dataid');
            var uid = Cro.getJsVar('uid');
            var url = '/conero/center/index/save/mates.html?dataid='+dataid+'&uid='+uid+'&mode='+Base64.encode('delete_'+Math.random());
            location.href = url;
        });
    });    
});
// var Cro = new Conero();
Cro.__APP = function(){
    var __APP__ =  function(th){
        // 新增时为用户注册
        this.register = function(dom){
            dom = $(dom);         
            var td = dom.parents("td");            
            if(dom.is(':checked')){
                var tr = td.parents("tr");
                var name = tr.find('[name="name"]').val();
                if(th.empty(name)){
                    dom.attr("checked",false);
                    th.modal_alert("请先选择名称");return;
                }
                td.find('[name="mate_code"]').removeAttr("disabled");

                var content = '<div class="input-group">'
                            +   '<span class="input-group-addon">账号</span>'
                            +   '<input type="text" class="form-control" name="code" placeholder="登录代码...">'
                            + '</div>'
                            + '<div class="input-group">'
                            +   '<span class="input-group-addon">登陆密码</span>'
                            +   '<input type="password" class="form-control" name="password" placeholder="用户密码...">'
                            + '</div>'
                            + '<div class="helper"></div>'
                    ;

                var md = th.modal({
                    title:"【"+name+"】账号设置",
                    content:content,
                    save:function(){
                        var body = $(this).parents('div.modal-content').find('div.modal-body');
                        var helper = body.find('div.helper');
                        var code = body.find('input[name="code"]').val();
                        if(th.empty(code)){
                            th.alert(helper,"【账号】不可为空！！");
                            return;
                        }
                        var reg = /^[a-zA-Z\d_-]{1,35}$/;
                        if(!reg.test(code)){
                            th.alert(helper,"【账号】非法-自能时英文字母或数值，且长度不能大于35");
                            return;
                        }                                                
                        var pwsd = body.find('input[name="password"]').val();
                        if(th.empty(pwsd)){
                            th.alert(helper,"【登录密码】名不可为空！！");
                            return;
                        }
                        th.dataInDb('net_user',{user_nick:code},function(data){
                            if(data == 'Y'){
                                th.alert(helper,"【"+code+"】已经存存，请更换其他登录名！");
                                return;
                            }
                            td.find('[name="mate_code"]').val(code);
                            td.find('[name="command"]').val(pwsd);     
                            md.modal('hide');
                        });                        
                    }
                });
            }
            else{
                td.find('[name="mate_code"]').attr("disabled",true);
                td.find('[name="command"]').val('');
            }
        }
    }
    return new __APP__(this);
}
var app = Cro.__APP();