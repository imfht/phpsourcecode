var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'店铺编号', name:'shopSn', width: 130},
            {title:'店铺名称', name:'shopName' ,width:100},
            {title:'店主姓名', name:'shopkeeper', width: 130},
            {title:'店主联系电话', name:'telephone' ,width:100},
            {title:'待结算订单数', name:'noSettledOrderNum' ,width:60},
            {title:'待结算金额', name:'waitSettlMoney' ,width:40, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'待结算佣金', name:'noSettledOrderFee' ,width:40, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'操作', name:'' ,width:120, align:'center', renderer: function(val,item,rowIndex){
                var h = "<span id='s_"+item['shopId']+"' dataval='"+item['shopName']+"'></span><a class='btn btn-blue' href='javascript:toView(" + item['shopId'] + ")'><i class='fa fa-search'></i>订单列表</a>&nbsp;&nbsp;";
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-163,indexCol: true,indexColWidth:50,  indexColWidth:50,cols: cols,method:'POST',multiSelect:true,
        url: WST.U('admin/settlements/pageShopQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         var diff = v?163:135;
         mmg.resize({height:h-diff})
    }});
    loadShopGrid(p);
}
function toView(id){
   location.href=WST.U('admin/settlements/toOrders','id='+id);
}
function initOrderGrid(id,p){
    var h = WST.pageHeight();
    var cols = [
            {title:'订单号', name:'orderNo', width: 130},
            {title:'支付方式', name:'payTypeName' ,width:100},
            {title:'商品金额', name:'goodsMoney', width: 130},
            {title:'运费', name:'deliverMoney' ,width:100, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'待结算金额', name:'waitSettlMoney' ,width:40, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'订单总金额', name:'totalMoney' ,width:60, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'实付金额', name:'realTotalMoney' ,width:40, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'已退还金额', name:'refundedPayMoney' ,width:40, renderer:function(val,item,rowIndex){
                return '￥'+(parseFloat(val)+parseFloat(item['refundedScoreMoney']));
            }},
            {title:'失效积分换算金额', name:'refundedGetScoreMoney' ,width:100, renderer:function(val,item,rowIndex){
                return '￥'+parseFloat(item['refundedGetScoreMoney']);
            }},
            {title:'佣金', name:'commissionFee' ,width:40, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'下单时间', name:'createTime' ,width:120, align:'center'}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-90,indexCol: true, indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/settlements/pageShopOrderQuery','id='+id), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadOrderGrid(p);
}
function loadShopGrid(p){
    p=(p<=1)?1:p;
	var areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	mmg.load({page:p,shopName:$('#shopName').val(),areaIdPath:areaIdPath});
}
function loadOrderGrid(p){
	var id = $('#id').val();
    p=(p<=1)?1:p;
	mmg.load({page:p,orderNo:$('#orderNo').val(),payType:$('#payType').val(),id:id});
}
