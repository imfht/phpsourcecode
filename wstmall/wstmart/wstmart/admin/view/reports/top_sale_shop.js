var mmg;
function initSaleGrid(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate'
    });
    laydate.render({
        elem: '#endDate'
    });
    var h = WST.pageHeight();
    var cols = [
            {title:'&nbsp;', name:'shopImg', width: 30, renderer:function(val,item,rowIndex){
                return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['shopImg']
            	+"'><span class='imged' ><img  style='height:180px;width:180px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['shopImg']+"'></span></span>";
            }},
            {title:'店铺', name:'shopName', width: 130},
            {title:'销售额', name:'totalMoney', width: 130, renderer:function(val,item,rowIndex){return '￥'+val;}},
            {title:'在线支付总金额', name:'onLinePayMoney', width: 130, renderer:function(val,item,rowIndex){return '￥'+val;}},
            {title:'在线支付实际收入', name:'onLinePayTrueMoney', width: 130, renderer:function(val,item,rowIndex){return '￥'+val;}},
            {title:'货到付款实际收入', name:'offLinePayMoney', width: 130, renderer:function(val,item,rowIndex){return '￥'+val;}},
            {title:'订单数', name:'orderNum', width: 50}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-139),indexCol: true, indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/reports/topShopSalesByPage',WST.getParams('.ipt')), fullWidthRows: true, autoLoad: true,
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
        location.href=WST.U('admin/reports/toExportShopSales',params);
    }});
}