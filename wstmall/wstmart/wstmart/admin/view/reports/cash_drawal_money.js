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
            {title:'提现单号', name:'cashNo', width: 90,sortable: true},
            {title:'会员名称', name:'loginName' ,width:100, renderer:function(val,item,rowIndex){
                $("#totalMoney").html(item.totalMoney);
                if(item['targetType']==1){
                    return WST.blank(item['userName'])+"("+item['loginName']+")";
                }else{
                    return WST.blank(item['userName'])+"("+item['loginName']+")";
                }
            }},
            {title:'会员类型', name:'targetTypeName' ,width:60,sortable: true},
            {title:'提现银行', name:'accTargetName' ,width:60,sortable: true},
            {title:'银行卡号', name:'accNo' ,width:40,sortable: true},
            {title:'持卡人', name:'accUser' ,width:40,sortable: true},
            {title:'提现金额', name:'money' ,width:40,sortable: true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'提现时间', name:'createTime',sortable: true ,width:60}
        ];
 
    mmg = $('.mmg').mmGrid({height: (h-139),indexCol: true,indexColWidth:50,  cols: cols,method:'POST',
        url: WST.U('admin/reports/cashDrawalMoneyByPage',WST.getParams('.ipt')), fullWidthRows: true, autoLoad: true,
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
        location.href=WST.U('admin/reports/toExportCashDrawalMoney',params);
    }});
}