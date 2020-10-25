

function loadGrid(p){
    p=(p<=1)?1:p;
    mmg1.load({page:p,settlementNo:$('#settlementNo_0').val(),isFinish:$('#isFinish_0').val()});
}
function loadUnSettleGrid(p){
    p=(p<=1)?1:p;
    mmg2.load({page:p,orderNo:$('#orderNo_1').val()});
}
function loadSettleGrid(p){
    p=(p<=1)?1:p;
    mmg3.load({page:p,settlementNo:$('#settlementNo_2').val(),orderNo:$('#orderNo_2').val(),isFinish:$('#isFinish_2').val()});
}
function view(val){
    location.href=WST.U('shop/settlements/view','id='+val);
}
function getQueryPage(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'结算单号', name:'settlementNo', width: 100},
        {title:'类型', name:'', width: 30,renderer:function(val,item,rowIndex){
                if(item['settlementType']==1){
                    return "定时";
                }else{
                    return "手动";
                }
            }},
        {title:'结算金额', name:'settlementMoney', width: 60,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'结算佣金', name:'commissionFee', width: 60,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'返还金额', name:'backMoney', width: 60,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'创建时间', name:'createTime', width: 120},
        {title:'结算状态', name:'', width: 50,renderer:function(val,item,rowIndex){
                if(item['settlementStatus']==1){
                    return "已结算";
                }else{
                    return "未结算";
                }
            }},
        {title:'结算时间', name:'', width: 100,renderer:function(val,item,rowIndex){
                return WST.blank(item['settlementTime'],'-');
            }},
        {title:'备注', name:'', width: 200,renderer:function(val,item,rowIndex){
                return WST.blank(item['remarks'],'-');
            }},
    ];

    mmg1 = $('.mmg1').mmGrid({height: h-122,indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('shop/settlements/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg1').mmPaginator({})
        ]
    });
    loadGrid(p);
}

function getUnSettledOrderPage(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'订单号', name:'orderNo', width: 200},
        {title:'下单时间', name:'createTime', width: 120},
        {title:'支付方式', name:'payTypeNames', width: 50},
        {title:'商品总金额', name:'goodsMoney', width: 100,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'运费', name:'deliverMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'订单总金额', name:'totalMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'实付金额', name:'realTotalMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'已退还金额', name:'refundedPayMoney' ,width:40, renderer:function(val,item,rowIndex){
            return '￥'+(parseFloat(val)+parseFloat(item['refundedScoreMoney']));
        }},
        {title:'失效积分换算金额', name:'refundedGetScoreMoney' ,width:100, renderer:function(val,item,rowIndex){
            return '￥'+parseFloat(item['refundedGetScoreMoney']);
        }},
        {title:'应付佣金', name:'commissionFee', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'预计结算日期', name:'afterSaleEndTime', width: 50}
    ];

    mmg2 = $('.mmg2').mmGrid({height: h-121,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/settlements/pageUnSettledQuery'), fullWidthRows: true, autoLoad: false,multiSelect:true,
        plugins: [
            $('#pg2').mmPaginator({})
        ]
    });
    loadUnSettleGrid(p)
}
function getSettleOrderPage(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'订单号', name:'orderNo', width: 100},
        {title:'支付方式', name:'payTypeNames', width: 60},
        {title:'商品总金额', name:'goodsMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'运费', name:'deliverMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'订单总金额', name:'totalMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'实付金额', name:'realTotalMoney', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'已退还金额', name:'refundedPayMoney' ,width:40, renderer:function(val,item,rowIndex){
            return '￥'+(parseFloat(val)+parseFloat(item['refundedScoreMoney']));
        }},
        {title:'失效积分换算金额', name:'refundedGetScoreMoney' ,width:100, renderer:function(val,item,rowIndex){
            return '￥'+parseFloat(item['refundedGetScoreMoney']);
        }},
        {title:'应付佣金', name:'commissionFee', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'结算单号', name:'settlementNo', width: 100},
        {title:'结算时间', name:'', width: 120,renderer:function(val,item,rowIndex){
                return WST.blank(item['settlementTime'],'-');
            }},
    ];

    mmg3 = $('.mmg3').mmGrid({height: h-122,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/settlements/pageSettledQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg3').mmPaginator({})
        ]
    });
    loadSettleGrid(p)
}