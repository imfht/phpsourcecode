$(function(){
    // 数据表格
    var formGrid = Cro.formListEvent({
            table:'#set_const_form',
            pk:  'sys_id',
            url:'/conero/admin/sconst/save.html'
        },{
            AddCheck: function(lastRow){
                var code = $('#gover_name').val();
                if(Cro.empty(code)){
                    Cro.alert('#form_tip_text','【代码】不可为空！',2);
                    $('#gover_name').parents('div.input-group').addClass('has-error');
                    return true;
                }
                var name = $('#gover_value').val();
                if(Cro.empty(name)){
                     $('#gover_value').parents('div.input-group').addClass('has-error');
                    Cro.alert('#form_tip_text','【名称】不可为空！',2);
                    return true;
                }
            },
            beforeSaveData: function(data){
                /*
                Cro.log(data);
                var add = {data:'ddd',zhou:"8574"};
                data.push(JSON.stringify(add));
                */
                //return true; // 中断数据提交
                var code = $('#gover_name').val();
                if(Cro.empty(code)){
                    //Cro.modal_alert('【代码】不可为空！');
                    Cro.alert('#form_tip_text','【代码】不可为空！',2);
                    $('#gover_name').parents('div.input-group').addClass('has-error');
                    return true;
                }
                var name = $('#gover_value').val();
                if(Cro.empty(name)){
                    $('#gover_value').parents('div.input-group').addClass('has-error');
                    Cro.alert('#form_tip_text','【名称】不可为空！',2);
                    return true;
                }
                var saveData = {};
                var add = {'gover_name':code,'gover_value':name};
                var len = data.length;
                var tmpArray = [],jsonStr;
                for(i=0; i<len; i++){
                    jsonStr = '"'+i+'":'+data[i];
                    tmpArray.push(jsonStr);
                }
                var mode = Cro.getJsVar('mode');
                if(Cro.empty(mode)) mode = 'A';
                saveData['sumy'] = '{'+tmpArray.join(",")+'}';
                saveData['dtl'] = JSON.stringify(add);
                saveData['mode'] = mode;
                return saveData;
            },
            afterAddRow: function(lastRow){
                var mode = Cro.getJsVar('mode');
                if('M' == mode){
                    lastRow.find('[name="sys_id"]').remove();
                    lastRow.find('[name="plus_name"]').removeAttr('readonly');
                    lastRow.find('[name="plus_name"]').val('');
                    lastRow.find('[name="plus_value"]').val('');                    
                }
                
            }
    });
    // 代码
    $('#gover_name').blur(function(){
        var groupDom = $('#gover_name').parents('div.input-group');
        groupDom.removeClass('has-error');
        var dom = $(this);
        var name = dom.val();
        var mode = Cro.getJsVar('mode');
        if(name && mode == 'A'){
            Cro.dataInDb('sys_site',{'user_name':'CONST','gover_name':name},function(data){
                var having = groupDom.find('span.form-control-feedback').length > 0? true:false;
                if(data == 'N'){                                       
                    if(having) groupDom.find('span.form-control-feedback').attr("class","glyphicon glyphicon-ok form-control-feedback");
                    else groupDom.append('<span class="glyphicon glyphicon-ok form-control-feedback"></span>');                    
                }
                else{
                    dom.val(''); 
                    if(having) groupDom.find('span.form-control-feedback').attr("class","glyphicon glyphicon-remove form-control-feedback");
                    else groupDom.append('<span class="glyphicon glyphicon-remove form-control-feedback"></span>');  
                }
            });
        }
    });
    // 名称
    $('#gover_value').blur(function(){
        $('#gover_value').parents('div.input-group').removeClass('has-error');
    });
    // 设置代码名称 协助框
    $('#set_constname_btn').click(function(){
        // ;
        Cro.modal();
    });
    // 删除常量
    $('#checked_delbtn').click(function(){
        var name = $('#gover_name').val();
        if(name){
            location.href = '/conero/admin/sconst/save.html?uid='+Cro.bsjson({'mode':'D','gover_name':name});
        }
    });
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        var mode = Cro.getJsVar('mode');
        if(mode != 'A') $('#gover_name').attr('readonly',true);
    }   
});
