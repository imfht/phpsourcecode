$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        th.panelToggle('.panel-toggle');
        this.rateRowPanel();
    }
    // 利率表处理
    this.rateRowPanel = function(){
        var formAction = th.formListEvent({
            table   :  '#rate_row_dance',
            addBtn  :  '#rate_row_plus',
            delBtn  :  '#rate_row_minus'
        },{
            afterAddRow: function(curRow){
                var lastRow = curRow.prev('tr');
                var ltr = formAction.rowObj(lastRow);   
                var ctr = formAction.rowObj(curRow);
                ctr.val("rate",ltr.val("rate"));
            }
        });
    }
    // 利率计算函数
    this.rateCalculat = function(dom){
        dom = $(dom);
        var row = dom.parents('tr');
        // 返回当前列的值 - name , onlyRow 仅仅返回列名
        function getVal(name,value){
            var td = row.find('input[name="'+name+'"]');
            if(value){
                td.val(value);
                return;
            }
            return td.length > 0 ? parseFloat(td.val()) : 0 ;
        }
        var capital = getVal('capital');
        var rate = getVal('rate');
        var times = getVal('times');
        accrual = capital * rate * times;
        if(accrual == 0) accrual = "0";
        getVal('accrual',accrual);
    }
});