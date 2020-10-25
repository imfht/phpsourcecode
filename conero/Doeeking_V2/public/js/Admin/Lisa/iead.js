$(function(){app.pageInit();});
var Cro = new Conero();
var app = Cro.extends(function(th){    
    var ieadFormHidden = true;
    var fnameAdmin = ''; // 后台传入的文件
    // 生成表单
    this.mkFormItem = function(dom){        
        dom = $(dom);
        var tr = dom.parents('tr');
        var code = tr.find('[name="setting_name"]').val();
        var desc = tr.find('[name="setting_desc"]').val();  
        var notnull = tr.find('[name="setting_notnull"]:checked').val();
        notnull = notnull && 'Y' == notnull? 'Y':'N'; 
        if(th.empty(code) || th.empty(desc)){
            return th.modal_alert('名称或描述不可为空！！');            
        }      
        var id = code+'_creator';
        if($('#'+id).length == 0){           
            var html = 
                '<div class="form-group'+(notnull == 'Y'? ' has-success':'')+'">'+
                    '<label class="col-sm-2 control-label" for="'+id+'">'+desc+'</label>'+
                    '<div class="col-sm-10">'+
                    '<input class="form-control" name="'+code+'" id="'+id+'" type="text" placeholder="填写值..."'+(notnull == 'Y'? ' required':'')+'>'+           
                    '</div>'+             
               '</div>'
            ;
            $('#iead_form_data').prepend(html);
            this.ieadFormToggle();
        }
    }
    // idea生成器开关
    this.ieadFormToggle = function(close){
        var panel = $('#iead_form_data').parents('div.panel');
        close = close? true:false;
        // 显示idea生成菜单
        if(ieadFormHidden && !close == true){             
            var pClass = panel.attr("class");
            if(pClass.indexOf('hidden') > -1){
                panel.removeClass('hidden');
                ieadFormHidden = false;
            }
        }
        else if(close){ // 页面关闭
            panel.addClass('hidden');
            ieadFormHidden = true;
        }
    }
    // 销毁表单
    this.rmFormItem = function(dom){
        dom = $(dom);
        var tr = dom.parents('tr');
        var code = tr.find('[name="setting_name"]').val();
        var id = code+'_creator';
        if($('#'+id).length > 0){
            $('#'+id).parents('div.form-group').remove();
        }
    }
    this.pageInit = function(){
        // 外面文件存在时显示列
        var loaddata = th.getJsVar('loaddata');
        if('Y' == loaddata){
            this.ieadFormToggle();
            // sys_page_fname
            var ipt = $('#iead_form_data').find('[name="sys_page_fname"]');
            var formGroup = ipt.parents('div.form-group');
            if(formGroup.length>0){
                var lable = formGroup.find('[for="sys_page_fname_creator"]');
                lable.text('文件名称');
                lable.attr("for",'fname_creator');
                ipt.attr("id","fname_creator");
                fnameAdmin = ipt.val();
            }
        }
        // th.alter_test();
        th.formListEvent({
            'table':'#col_maker'
        });
        // 设置文件名称
        $('#set_fname_link').click(function(){
            var id = 'fname_creator';
            if($('#'+id).length == 0){
                // var fname = th.getQuery('fname');
                var fname = th.getSearch('fname');
                fname = fname? Base64.decode(fname):null;
                var html = 
                    '<div class="form-group has-success">'+
                        '<label class="col-sm-2 control-label" for="fname_creator">文件名称</label>'+
                        '<div class="col-sm-10">'+
                        '<input class="form-control" '+(fname? 'value="'+fname+'" ':'')+'name="sys_page_fname" value="'+fnameAdmin+'" id="fname_creator" type="text" placeholder="保存文件路径" required>'+           
                        '</div>'+             
                    '</div>'
                ;
                $('#iead_form_data').prepend(html);
                app.ieadFormToggle();
            }
        });
        // 文件保存切换
        $('#download_link').click(function(){
            var id = 'fname_creator';
            if($('#'+id).length >0){
                $('#fname_creator').parents('div.form-group').remove();
            }
        });
    }
});