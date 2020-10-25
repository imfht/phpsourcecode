$(function(){
    $('#organ_list .ogran_link').click(function(){
        var text = $(this).text();
        app.organid($(this).parents('tr').attr("dataid"));
        var panel = $('#about');
        panel.find("div.panel-heading").text(text);

        $('#about').show();
        location.href = location.pathname +'#about';
    });
    // 删除
    $('#about .delete_btn').click(function(){
        var id = app.organid();        
        Cro.confirm('您确定要是该财务机构吗？',function(){
            if(Cro.empty(id)){
                $('#btsp_modal_confirm').modal('hide');
                Cro.modal_alert('数据删除失败？');
                return;
            }
            Cro.post('/conero/finance/organ/save.html',{map:Cro.bsjson({mode:'D',id:id})});
        });
    });
    // 修改
    $('#about .edit_btn').click(function(){
        var id = app.organid();    
        if(Cro.empty(id)){
            $('#btsp_modal_confirm').modal('hide');
            Cro.modal_alert('数据删除失败？');
            return;
        }    
        location.href = '/conero/finance/organ/edit/'+id+'.html';       
    });
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        $('#about').hide();
    }
    // 机构ID 辅助器
    var _organid = null;
    this.organid = function(id){
        if(th.empty(id)) return _organid? _organid:'';
        else _organid = id;
    }
});