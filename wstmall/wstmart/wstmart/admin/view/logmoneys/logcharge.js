var mmg1,mmg2,mmg3,mmg,h;
function flowGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate'
    });
    laydate.render({
        elem: '#endDate'
    });
    var cols = [
            {title:'来源', name:'dataSrc', width: 30},
            {title:'个人账号/店铺名', name:'loginName', width: 100},
            {title:'金额', name:'money' ,width:20,renderer: function (rowdata, rowindex, value){
	        	if(rowindex['moneyType']==1){
                    return '<font color="red">+￥'+rowindex['money']+'</font>';
	        	}else{
                    return '<font color="green">-￥'+rowindex['money']+'</font>';
	        	}
	        }},
            {title:'备注', name:'remark',width:370},
            {title:'外部流水', name:'tradeNo',width:120},
            {title:'日期', name:'createTime' ,width:60}
            ];
 
    mmg3 = $('.mmg3').mmGrid({height: h-90,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQueryByCharge'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg3').mmPaginator({})
        ]
    });
    loadFlowGrid(p);
}
function loadFlowGrid(p){
    p=(p<=1)?1:p;
	mmg3.load({page:p,dataSrc:4,key:$('#key3').val(),type:$('#type').val(),startDate:$('#startDate').val(),endDate:$('#endDate').val()});
}