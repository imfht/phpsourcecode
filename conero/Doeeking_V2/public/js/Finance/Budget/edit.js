$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        var formid = th.getJsVar('formid');
        if(formid == 'newrl'){
            this.pageInitNewrl();
        }
    }
    // newrl 标签
    this.pageInitNewrl = function(){
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
        // 名字唯一性
        $('#name_ipter').change(function(){
            var dom = $(this);
            var name = dom.val();
            if(name){
                th.dataInDb('finc_budget',{name:name,center_id:th.getJsVar('cid'),umark:'regular'},function(data){
                    if(data == 'Y'){
                        $('#name_ipter').val('');
                        th.alert('#form_tip_bar','例行财务【'+name+'】--例行财务计划已经存在');
                    }
                    else th.alert('#form_tip_bar','例行财务【'+name+'】--有效','(^_^)');
                });
            }
        });
    }
});