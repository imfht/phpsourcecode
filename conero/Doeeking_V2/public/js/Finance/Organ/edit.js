$(function(){app.pageInit();});
var Cro = new Conero();
var app = Cro.extends(function(th){
    // 事件绑定
    var form = $('.form-horizontal');
    var mode = form.find('input[name="mode"]').val();
    this.pageInit = function(){
        form = $('.form-horizontal');
        mode = form.find('input[name="mode"]').val();
        if(mode == 'A'){
            $('#page_helper').hide();
        }
        else if(mode == 'M'){
            // 类型赋值
            var type = $('#type_ipter').attr("dataseled");
            if(type) $('#type_ipter').find('option[value="'+type+'"]').attr("selected",true);    
            var id = th.getUrlBind('edit');
            if(id){
                $('#name_ipter').after('<input type="hidden" name="id" value="'+id+'">');
            }
        }
    }
});
