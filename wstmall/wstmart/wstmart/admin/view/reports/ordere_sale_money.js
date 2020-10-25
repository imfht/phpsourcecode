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
            {title:'订单号', name:'orderNo' ,width:80},
            {title:'下单用户', name:'userName' ,width:80,renderer:function(val,item,rowIndex){
                $("#totalScoreMoney").html(item.totalScoreMoney);
                $("#totalRealTotalMoney").html(item.totalRealTotalMoney);
                return val;
            }},
            {title:'支付方式', name:'payType' ,width:80},
            {title:'订单状态', name:'status' ,width:80},
            {title:'订单总金额', name:'totalMoney' ,width:80},
            {title:'积分抵扣金额', name:'scoreMoney' ,width:80},
            {title:'订单实付金额', name:'realTotalMoney' ,width:80},
            {title:'下单时间', name:'createTime',width:80}
        ];
 
    mmg = $('.mmg').mmGrid({height: (h-139),indexCol: true,indexColWidth:50,  cols: cols,method:'POST',
        url: WST.U('admin/reports/ordereSaleMoneyByPage',WST.getParams('.ipt')), fullWidthRows: true, autoLoad: true,
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
        location.href=WST.U('admin/reports/toExportOrdereSaleMoney',params);
    }});
}