$(function(){
    app.pageInit();
    $('#delete_link').click(function(){
        var dom = $(this);
        Cro.confirm('您确定要删除数据吗？',function(){
            location.href = dom.attr("dataid");
        });
    });
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        tinymce.init({
            selector: '#tpl_ipter'
         });
    }
});