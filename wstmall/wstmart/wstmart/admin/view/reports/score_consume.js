var mmg;
function initGrid(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate'
    });
    laydate.render({
        elem: '#endDate'
    });
    var h = WST.pageHeight();
    var cols = [
            {title:'用户', name:'userName', width: 80, renderer: function(val,item,rowIndex){
                $("#totalScore").html(item['totalScore']);
                $("#totalScoreMoney").html(item['totalScoreMoney']);
                
                return val;
            }},
            {title:'描述', name:'dataRemarks', width: 150},
            {title:'积分数', name:'score', width: 80},
            {title:'抵扣金额', name:'scoreMoney', width: 50},
            {title:'时间', name:'createTime' , width: 50}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-87),indexCol: true,indexColWidth:50,  cols: cols,method:'POST',
        url: WST.U('admin/reports/scoreConsumeByPage',WST.getParams('.ipt')), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });  
}
function loadGrid(){
	var params = WST.getParams('.ipt');
    params.page = 1;
	mmg.load(params);
}
function toolTip(){
    WST.toolTip();
}
function toExport(){
    var params = WST.getParams('.ipt');
    var box = WST.confirm({content:"您确定要导出该统计数据吗?",yes:function(){
        layer.close(box);
        location.href=WST.U('admin/reports/toExportScoreConsume',params);
    }});
}