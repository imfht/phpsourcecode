$(function () {
	$('#tab').TabPanel({tab:0,callback:function(tab){
		switch(tab){
		   case 0:pageQuery(-1);break;
		   case 1:pageQuery(1);break;
		   case 2:pageQuery(0);break;
		}	
	}})
});
function pageQuery(type){
    var h = WST.pageHeight();
    var cols = [
        {title:'来源/用途', name:'dataSrc', width: 60},
        {title:'金额', name:'', width: 50,renderer:function(val,item,rowIndex){
                if(item['moneyType']==1){
                    return "+￥"+item['money'];
                }else{
                    return "-￥"+item['money'];
                }
            }},
        {title:'日期', name:'createTime', width: 120},
        {title:'外部流水号', name:'', width: 120,renderer:function(val,item,rowIndex){
                return WST.blank(item['tradeNo'],'-');
            }},
        {title:'备注', name:'remark', width: 300},
    ];

    mmg = $('.mmg').mmGrid({height: h-193,indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('shop/logmoneys/pageShopQuery'), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
}
function loadGrid(type){
    mmg.load({type:type,page:1});
}