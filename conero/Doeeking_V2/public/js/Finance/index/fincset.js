$(function(){
    Cro.pageInit();
});
var Cro = new Conero();
Cro.__APP = {
    // -- 构建图表
    financeEchart: function(){
        var chart = echarts.init(document.getElementById('figure'));       
        chart.setOption(figure);
    },
    // 年份改变时触发事件
    yearChange: function(dom){
        dom = $(dom);
        var url = dom.find('option:selected').attr("dataid");
        var typeid = dom.attr("typeid");
        if(typeid.indexOf("month")>-1){
            var year = dom.find('option:selected').val();
            $.post('/conero/finance/index/ajax.html',{item:'fincset/getmonth',year:year,uid:dom.attr("uid")},function(data){                
                var div =dom.parents('div.subhead'); 
                if(div.find('select[class="month"]').length>0){
                    div.find('select[class="month"]').remove();
                }
                dom.after(data);
            });
            return;
        }
        if(Cro.empty(url)) return;
        location.href = url;
    }
    // 月份监听
    ,monthChange: function(dom){
        dom = $(dom);
        var url = dom.find('option:selected').attr("dataid");    
        if(Cro.empty(url)) return;
        location.href = url;
    }
    // 月份加载按钮
    ,addMonthBtn: function(dom){
        dom = $(dom);
        var div = dom.parents('div.subhead');
        var selectEl = div.find('select');
        var uid = selectEl.attr("uid");
        var year = selectEl.find('option:selected').val();
        if(!Cro.empty(year)){
            $.post('/conero/finance/index/ajax.html',{item:'fincset/getmonth',year:year,uid:uid},function(data){
                var div =dom.parents('div.subhead'); 
                if(div.find('select[class="month"]').length>0){
                    div.find('select[class="month"]').remove();
                }
                dom.before(data);
            });
        }
    }
};
var app;
Cro.pageInit = function(){
    app = this.__APP;
    app.financeEchart();
}