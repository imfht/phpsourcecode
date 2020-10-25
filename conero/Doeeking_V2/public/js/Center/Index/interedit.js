$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _editMode = 'A';
    this.pageInit = function(){
        // 是否注册
        $('#isloginIpter').change(function(){
            app.islogin();
        });
        var sysno = th.getUrlBind('internet');
        if(!th.empty(sysno)){
            _editMode = 'M';
            $('#nameIpter').after('<input type="hidden" name="sys_no" value="'+sysno+'">');
        }
        this.islogin();
        this.updateRecord();
        if(_editMode == 'A'){
            // $('.show_when_edit').hide();
            $('.show_when_edit').remove();
        }
        else if(_editMode == 'M'){
            $('.show_when_crea').remove();
        }
        // 按钮现实
        var xhtml = '';        
        if(_editMode == 'M'){
            xhtml = '<p class="text-right"><a href="javascript:void(0);" class="btn btn-warning btn-sm" id="data_del_btn">删除</a> <a href="/conero/center/index/edit/internet.html" class="btn btn-success btn-sm" id="data_del_btn">新增</a></p>';
            $('div.panel-footer').html(xhtml);
            // 删除数据
            $('#data_del_btn').click(function(){
                th.confirm('您确定要删除数据吗？',function(){
                    th.post('/conero/center/index/save/internet.html',{map:th.bsjson({sys_no:sysno,mode:'D'})});
                });
            });
        }
        // 关联账号选择
        $('#emid_btn').click(function(){
            th.pupop({
                title:'关联账号选择器',
                field:{sys_no:'hidden',name:'名称',type:'类型'},
                post:{table:'sys_organs',order:'edittm asc',map:'user_code="'+th.getJsVar('code')+'"'},
                single:true
            },{
                selected:function(){
                    var rowno = $(this).parents('tr.datarow');
                    var value = rowno.find('td.hidden').text();
                    var name = rowno.find('td.name').text();
                    $('#emaildescIpter').val(name);
                    $('#emailidIpter').val(value);
                    $(this).parents('div.modal').modal('hide');
                }
            });
        });
        // 显示密码
        $('#spswd_btn').click(function(){
            // th.modal({})
            // prompt('请输入名臣','44');
            th.prompt('请输入您的登录密码！',function(value){
                if(value){
                    $.post('/conero/center/index/ajax/internet.html',{'__:':th.bsjson({item:'show_me_by_pswd',pswd:value,sysno:sysno}),'$rd':Math.random()},function(data){
                        data = th.is_object(data)? data : JSON.parse(data);
                        if(data && data.error == 1){
                            $('#passwIpter').attr("type","text");
                            $('#passwIpter').attr("readonly",true);
                            $('#passwIpter').val(Base64.decode(data['desc']));
                            $('#spswd_btn').attr("disabled",true);
                        }
                    });
                }
            });
        });
    }
    this.islogin = function(){        
        var islogin = $('#isloginIpter:checked').val();
        if('Y' == islogin) $('#ifIsloginedState').show();
        else $('#ifIsloginedState').hide();                
    }
    this.updateRecord = function(){
        if(_editMode == 'M'){
            var tpls = ['#typeIpter','#atypeIpter'];
            var el,vdata,option;
            for(var i=0; i<tpls.length; i++){
                el = $(tpls[i]);
                vdata = el.attr("vdata");
                if(el.is('select')){
                    option = el.find('option[value="'+vdata+'"]');      
                    if(option.length > 0) option.attr("selected",true);
                }               
            }
        }
    }
});