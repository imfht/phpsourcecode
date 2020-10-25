var mmg1,mmg2,mmg3,mmg4,mmg5,mmg6,mmg7,mmg8,mmg9,mmg10,mmg,h;
function initTab(p){
	var element = layui.element;
	var isInit1 = isInit2 = isInit3 = isInit4 = isInit5 = isInit6 = isInit7 = isInit8 = isInit9 = isInit10 =  false;
	element.on('tab(msgTab)', function(data){
        var tabId = $(this).attr("id");
        console.log(tabId);
        if(tabId=="users"){//用户资金
            if(!isInit1){
               isInit1 = true;
               userGridInit(p);
            }else{
               loadUserGrid(p);
            }
        }else if(tabId=="shops"){//商家资金
            if(!isInit2){
               isInit2 = true;
               shopGridInit(p);
            }else{
               loadShopGrid(p);
            }
        }else if(tabId=="suppliers"){//供货商资金
            if(!isInit3){
                isInit3 = true;
                supplierGridInit(p);
            }else{
                loadSupplierGrid(p);
            }
        }else if(tabId=="rechangeMoney"){//充值记录
            if(!isInit4){
                isInit4 = true;
                rechangeGridInit(p);
            }else{
                loadRechangeGrid(p);
            }
        }else if(tabId=="renewMoney"){//年费记录
            if(!isInit5){
                isInit5 = true;
                renewGridInit(p);
            }else{
                loadRenewGrid(p);
            }
        }else if(tabId=="refundMoney"){//退款记录
            if(!isInit6){
                isInit6 = true;
                refundGridInit(p);
            }else{
                loadRefundGrid(p);
            }
        }else if(tabId=="cashDraw"){//提现记录
            if(!isInit7){
                isInit7 = true;
                cashDrawGridInit(p);
            }else{
                loadCashDrawGrid(p);
            }
        }else if(tabId=="moneyList"){//资金流水
            if(!isInit8){
                isInit8 = true;
                moneyGridInit(p);
            }else{
                loadMoneyGrid(p);
            }
        }else if(tabId=="scoreList"){//积分流水
            if(!isInit9){
                isInit9 = true;
                scoreGridInit(p);
            }else{
                loadScoreGrid(p);
            }
        }else if(tabId=="commissionList"){//订单佣金
            if(!isInit10){
                isInit10 = true;
                commissionGridInit(p);
            }else{
                loadCommissionGrid(p);
            }
        }
        
	});
}
function phaseSummary(type,flag){
    if(flag==1)var loading = WST.msg('正在处理数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/logmoneys/phaseSummary'),{type:type},function(data,textStatus){
        if(flag==1)layer.close(loading);
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
            $("#s_rechangeMoney").html(json.data.rechangeMoney);
            $("#s_giveMoney").html(json.data.giveMoney);
            $("#s_renewMoney").html(json.data.renewMoney);
            $("#s_cashDraw").html(json.data.cashDraw);
            $("#s_refundMoney").html(json.data.refundMoney);
            $("#s_giveScore").html(json.data.giveScore);
            $("#s_exchangeScore").html(json.data.exchangeScore);
            $("#s_commission").html(json.data.commission);
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}
//用户资金
function userGridInit(p){
	h = WST.pageHeight();
    var cols = [
            {title:'账号', name:'loginName', width: 50,sortable: true},
            {title:'名称', name:'userName' ,width:80,sortable: true},
            {title:'可用金额', name:'userMoney' ,width:200,sortable: true,renderer: function (rowdata, rowindex, value){
	        	return '￥'+rowindex['userMoney'];
	        }},
            {title:'冻结金额', name:'lockMoney' ,width:70,sortable: true,renderer: function (rowdata, rowindex, value){
	        	return '￥'+rowindex['lockMoney'];
	        }},
            {title:'充值送', name:'rechargeMoney' ,width:70,sortable: true,renderer: function (rowdata, rowindex, value){
                return '￥'+rowindex['rechargeMoney'];
            }}
            ];
 
    mmg1 = $('.mmg1').mmGrid({
        height: h-402,
        indexCol: true,
        indexColWidth:50, 
        cols: cols,
        method:'POST',
        url: WST.U('admin/logmoneys/pageQueryByUser'), 
        fullWidthRows:true, 
        autoLoad: false,
        remoteSort:true ,
        sortName: 'userMoney',
        sortStatus: 'desc',
        plugins: [
            $('#pg1').mmPaginator({})
        ]
    });
    loadUserGrid(p);
}
function loadUserGrid(p){
    p=(p<=1)?1:p;
    mmg1.load({page:p,key:$('#key1').val()});
}
//商家资金
function shopGridInit(p){
	h = WST.pageHeight();
    var cols = [
            {title:'账号', name:'loginName', width: 50},
            {title:'商家', name:'shopName' ,width:80},
            {title:'可用金额', name:'shopMoney' ,width:200,renderer: function (rowdata, rowindex, value){
	        	return '￥'+rowindex['shopMoney'];
	        }},
            {title:'冻结金额', name:'lockMoney' ,width:70,renderer: function (rowdata, rowindex, value){
	        	return '￥'+rowindex['lockMoney'];
	        }},
            {title:'充值送', name:'rechargeMoney' ,width:70,sortable: true,renderer: function (rowdata, rowindex, value){
                return '￥'+rowindex['rechargeMoney'];
            }}
            ];
 
    mmg2 = $('.mmg2').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQueryByShop'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg2').mmPaginator({})
        ]
    });
    loadShopGrid(p);
}
function loadShopGrid(p){
    p=(p<=1)?1:p;
    mmg2.load({page:p,key:$('#key2').val()});
}

//供货商资金
function supplierGridInit(p){
    h = WST.pageHeight();
    var cols = [
            {title:'账号', name:'loginName', width: 50},
            {title:'供货商', name:'supplierName' ,width:80},
            {title:'可用金额', name:'supplierMoney' ,width:200,renderer: function (rowdata, rowindex, value){
                return '￥'+rowindex['supplierMoney'];
            }},
            {title:'冻结金额', name:'lockMoney' ,width:70,renderer: function (rowdata, rowindex, value){
                return '￥'+rowindex['lockMoney'];
            }},
            {title:'充值送', name:'rechargeMoney' ,width:70,sortable: true,renderer: function (rowdata, rowindex, value){
                return '￥'+rowindex['rechargeMoney'];
            }}
            ];
 
    mmg3 = $('.mmg3').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQueryBySupplier'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg3').mmPaginator({})
        ]
    });
    loadSupplierGrid(p);
}
function loadSupplierGrid(p){
    p=(p<=1)?1:p;
    mmg3.load({page:p,key:$('#key3').val()});
}

//充值记录
function rechangeGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate4'
    });
    laydate.render({
        elem: '#endDate4'
    });
    var cols = [
            {title:'来源', name:'dataSrc', width: 30},
            {title:'个人账号/店铺名', name:'loginName', width: 120},
            {title:'金额', name:'money' ,width:20,renderer: function (rowdata, rowindex, value){
                $("#totalRechangeMoney").html(rowindex['totalRechangeMoney']);
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
 
    mmg4 = $('.mmg4').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/rechangepageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg4').mmPaginator({})
        ]
    });
    loadRechangeGrid(p);
}

function loadRechangeGrid(p){
    p=(p<=1)?1:p;
    mmg4.load({page:p,key:$('#key4').val(),type:$('#type4').val(),startDate:$('#startDate4').val(),endDate:$('#endDate4').val()});
}


//年费记录
function renewGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate5'
    });
    laydate.render({
        elem: '#endDate5'
    });
    var cols = [
            {title:'店铺名称', name:'shopName' ,width:60,renderer:function(val,item,rowIndex){
                $("#totalRenewMoney").html(item.totalRenewMoney);
                return item['shopName'];
            }},
            {title:'缴费对象', name:'type', width: 130,sortable:true, renderer:function(val,item,rowIndex){
                return (val==3)?"供货商":"商家";
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
 
    mmg5 = $('.mmg5').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/shops/statRenewMoneyByPage'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg5').mmPaginator({})
        ]
    });
    loadRenewGrid(p);
}

function loadRenewGrid(p){
    p=(p<=1)?1:p;
    mmg5.load({page:p,type:$('#type5').val(),key:$('#key5').val(),startDate:$('#startDate5').val(),endDate:$('#endDate5').val()});
}


//提现记录
function cashDrawGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate7'
    });
    laydate.render({
        elem: '#endDate7'
    });
    var cols = [
            {title:'提现单号', name:'cashNo', width: 90,sortable: true},
            {title:'会员名称', name:'loginName' ,width:100, renderer:function(val,item,rowIndex){
                $("#totalCashDrawMoney").html(item.totalCashDrawMoney);
                $('#totalCashDrawCommission').html(item.totalCashDrawCommission);
                if(item['targetType']==1){
                    return WST.blank(item['userName'])+"("+item['loginName']+")";
                }else{
                    return WST.blank(item['userName'])+"("+item['loginName']+")";
                }
            }},
            {title:'会员类型', name:'targetTypeName' ,width:40,sortable: true},
            {title:'提现银行', name:'accTargetName' ,width:60,sortable: true},
            {title:'银行卡号', name:'accNo' ,width:90,sortable: true},
            {title:'持卡人', name:'accUser' ,width:40,sortable: true},
            {title:'提现金额', name:'money' ,width:30,sortable: true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'手续费', name:'commission' ,width:30,sortable: true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'费率', name:'commissionRate' ,width:30,sortable: true, renderer:function(val,item,rowIndex){
                return val+'%';
            }},
            {title:'提现时间', name:'createTime',sortable: true ,width:60}
        ];
 
    mmg7 = $('.mmg7').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/Cashdraws/statCashDrawal'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg7').mmPaginator({})
        ]
    });
    loadCashDrawGrid(p);
}

function loadCashDrawGrid(p){
    p=(p<=1)?1:p;
    mmg7.load({page:p,key:$('#key7').val(),type:$('#type7').val(),startDate:$('#startDate7').val(),endDate:$('#endDate7').val()});
}


//资金流水
function moneyGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate8'
    });
    laydate.render({
        elem: '#endDate8'
    });
    var cols = [
            {title:'来源', name:'dataSrc', width: 30},
            {title:'个人账号/店铺名', name:'loginName', width: 120},
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
 
    mmg8 = $('.mmg8').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg8').mmPaginator({})
        ]
    });
    loadMoneyGrid(p);
}

function loadMoneyGrid(p){
    p=(p<=1)?1:p;
    mmg8.load({page:p,key:$('#key8').val(),type:$('#type8').val(),startDate:$('#startDate8').val(),endDate:$('#endDate8').val()});
}

//积分流水
function scoreGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate9'
    });
    laydate.render({
        elem: '#endDate9'
    });
    var cols = [
            {title:'来源', name:'dataSrc', width: 60},
            {title:'积分变化', name:'dataSrc',width: 60,renderer: function (val,item,rowIndex){
                $("#totalInScore").html(item.totalInScore);
                $("#totalOutScore").html(item.totalOutScore);
                if(item['scoreType']==1){
                    return '<font color="red">+'+item['score']+'</font>';
                }else{
                    return '<font color="green">-'+item['score']+'</font>';
                }
            }},
            {title:'备注', name:'dataRemarks',width: 60},
            {title:'日期', name:'createTime',width: 40}
            ];
 
    mmg9 = $('.mmg9').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/Userscores/statPageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg9').mmPaginator({})
        ]
    });
    loadScoreGrid(p);
}

function loadScoreGrid(p){
    p=(p<=1)?1:p;
    mmg9.load({page:p,key:$('#key9').val(),startDate:$('#startDate9').val(),endDate:$('#endDate9').val()});
}


//订单佣金
function commissionGridInit(p){
    var h = WST.pageHeight();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate10'
    });
    laydate.render({
        elem: '#endDate10'
    });
    var cols = [
            {title:'结算单号', name:'settlementNo', width: 130,sortable:true},
            {title:'结算对象', name:'type', width: 130,sortable:true, renderer:function(val,item,rowIndex){
                return (val==3)?"供货商":"商家";
            }},
            {title:'申请店铺', name:'shopName' ,width:100,sortable:true},
            {title:'结算金额', name:'settlementMoney' ,width:100,sortable:true, renderer:function(val,item,rowIndex){
                $("#totalCommission").html(item.totalCommission);
                return '￥'+val;
            }},
            {title:'返还金额', name:'backMoney' ,width:40,sortable:true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'结算佣金', name:'commissionFee' ,width:60,sortable:true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'结算时间', name:'createTime',sortable:true}
        ];
 
    mmg10 = $('.mmg10').mmGrid({height: h-402,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/settlements/statPageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg10').mmPaginator({})
        ]
    });
    loadCommissionGrid(p);
}

function loadCommissionGrid(p){
    p=(p<=1)?1:p;
    mmg10.load({page:p,key:$('#key10').val(),type:$('#type10').val(),startDate:$('#startDate10').val(),endDate:$('#endDate10').val()});
}
