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
            {title:'店铺名称', name:'shopName' ,width:60,renderer:function(val,item,rowIndex){
                $("#totalRenewMoney").html(item.totalRenewMoney);
                return item['shopName'];
            }},
            {title:'缴纳年费描述', name:'remark' ,width:200,renderer: function (rowdata, item, value){
                    return (item['isRefund']==1)?item['remark']+'(已退款)':item['remark'];
            }},
            {title:'年费金额', name:'money' ,width:60,sortable: true,renderer: function (rowdata, item, value){
                    return '￥'+item['money'];
                }},
            {title:'外部流水号', name:'', width: 120,renderer:function(val,item,rowIndex){
                    return WST.blank(item['tradeNo'],'-');
            }},
            {title:'缴纳时间', name:'createTime',width:60},
            {title:'开始日期', name:'startDate',width:60},
            {title:'结束日期', name:'endDate',width:60}
        ];
 
    mmg = $('.mmg').mmGrid({height: (h-87),indexCol: true,indexColWidth:50,  cols: cols,method:'POST',
        url: WST.U('admin/shops/renewMoneyByPage',WST.getParams('.ipt')), fullWidthRows: true, autoLoad: true,
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
        location.href=WST.U('admin/shops/toExportRenewMoney',params);
    }});
}