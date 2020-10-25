$(function(){
    app.pageInit();
    // 数据唯一性检测
    $('#procode_ipter').change(function(){
        var dom = $(this);
        var value = dom.val();
        var mode = Cro.getJsVar('mode');
        if(value && mode == 'A'){
            // 字符串合法性检测
            var reg = /^[a-z\d_-]+$/i;
            if(!reg.test(value)){
                dom.val('');
                Cro.modal_alert('【'+value+'】名称不合法，请更换其他名称！');
                return;
            }
            Cro.dataInDb('project_list',{'pro_code':value},function(data){
                if(data == 'Y'){
                    dom.val('');
                    Cro.modal_alert('【'+value+'】项目已经存在，请更换其他名称！');
                }
            })
        }
    })
    // 保存
    $('#save_form_btn').click(function(){        
        if(app.formCheck()) return false;
        return true;
    });        
});
var Cro = new Conero();
var app = Cro.extends(function(th){    
    this.pageInit = function(){        
        // 创建内容富文本编辑器
        tinymce.init({
            selector: '#content_ipter',
            height: 400
        });
        // 日期控件       
        $('.form_date').datetimepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
        var mode = Cro.getJsVar('mode');
        if(mode == 'M'){
            $('#procode_ipter').attr('readonly',true);
        }
    }
    // 表单检测
    this.formCheck = function(){
        var form = $('form.form-horizontal');
        //form.attr("action","javascript:void(0);");
        var checks = form.find('[required]');
        var value;
        for(var i=0; i < checks.length; i++){
            value = $(checks[i]).val();
            if(th.empty(value)) return false;
        }
        var content = tinymce.get('content_ipter').getContent();        
        if(th.empty(content)){
            th.modal_alert('[内容]必填！');
            return true;
        }
        return false;
    }
});