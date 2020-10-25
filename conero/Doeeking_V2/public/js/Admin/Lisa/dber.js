$(function(){app.pageInit();});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        // 删除节点
        $('.del_link').click(function(){
            var dom = $(this);
            th.confirm('您确定要删除数据？',function(){
                // var dataid = dom.parents('tr').attr('dataid');
                location.href = dom.attr("dataid");
            });            
        });
        // 修改节点
        $('.edit_link').click(function(){
            var dataid = $(this).parents('tr').attr('dataid');
            $.post('/conero/index/common/record.html',{map:th.bsjson(['project_tree','no',dataid])},function(record){
                var content = '';
                record.mode = 'M';
                content = th.formGroup({
                    param:[
                        {name:'no',type:'hidden'},
                        {name:'mode',type:'hidden'},
                        {name:'node_code',require:true,label:'代码'},
                        {name:'node_name',require:true,label:'名称'},
                        {name:'node_desc',label:'说明'},
                        {name:'url',label:'请求地址'}
                    ],
                    record:record
                });
                content = '<form action="/conero/admin/lisa/save.html" method="post" id="edit_form">'+content+'</form>';
                th.modal({
                    title: record.node_name + ' | 修改节点',
                    large:true,
                    content:content,
                    save:function(){
                        $('#edit_form').submit();
                    }
                });
            });
        });
    }
});