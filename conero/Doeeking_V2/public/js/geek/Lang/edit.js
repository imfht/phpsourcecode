$(function(){
    app.pageInit();
    // 名称唯一性
    $('#name_ipter').change(function(){
        var dom = $(this);
        var name = dom.val();
        if(name){
            Cro.dataInDb('gk_lang',{name:name},function(data){
                if(data == 'Y'){
                    dom.val('');
                    Cro.modal_alert('【'+name+'】已经存在，如果您有帮助的地方，请直接维护该数据！');
                }
            });
        }
    })
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        // tinymce
        tinymce.init({
            selector: '#content_ipter',
            height: 400
        });
        // 修改是-name 不可改变
        if(!th.empty($('#name_ipter').val())) $('#name_ipter').attr('readonly',true);
    }
});