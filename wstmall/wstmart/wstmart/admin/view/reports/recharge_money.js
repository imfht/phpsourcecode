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
            {title:'会员名称', name:'loginName' ,width:100, renderer:function(val,item,rowIndex){
                if(item['targetType']==1){
                    return WST.blank(item['userName'])+"("+item['loginName']+")";
                }else{
                    return WST.blank(item['userName'])+"("+item['loginName']+")";
                }
            }},
            {title:'会员类型', name:'targetType' ,width:60,renderer:function(val,item,rowIndex){
                $("#totalRechargeMoney").html(item.totalRechargeMoney);
                $("#totalGiveMoney").html(item.totalGiveMoney);
                return (item['targetType']==1)?"【商家】":"【会员】";
            }},
            {title:'提现描述', name:'remark' ,width:200},
            {title:'充值金额', name:'money' ,width:60},
            {title:'赠送金额', name:'giveMoney' ,width:60},
            {title:'提现时间', name:'createTime',width:60}
        ];
 
    mmg = $('.mmg').mmGrid({height: (h-87),indexCol: true,indexColWidth:50,  cols: cols,method:'POST',
        url: WST.U('admin/reports/rechargeMoneyByPage',WST.getParams('.ipt')), fullWidthRows: true, autoLoad: true,
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
        location.href=WST.U('admin/reports/toExportRechargeMoney',params);
    }});
}