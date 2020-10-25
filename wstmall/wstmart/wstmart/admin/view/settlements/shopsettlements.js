var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'结算单号', name:'settlementNo', width: 130,sortable:true},
            {title:'申请店铺', name:'shopName' ,width:100,sortable:true},
            {title:'结算金额', name:'settlementMoney' ,width:100,sortable:true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'结算佣金', name:'commissionFee' ,width:60,sortable:true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'返还金额', name:'backMoney' ,width:40,sortable:true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'申请时间', name:'createTime',sortable:true},
            {title:'状态', name:'settlementStatus' ,width:60,sortable:true, renderer:function(val,item,rowIndex){
                return (val==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 已结算&nbsp;</span>":"<span class='statu-yes'><i class='fa fa-check-circle'></i> 未结算&nbsp;</span>";
            }},
            {title:'操作', name:'' ,width:120, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            h += "<a class='btn btn-blue' href='javascript:toView(" + item['settlementId'] + ")'><i class='fa fa-search'></i>查看</a>&nbsp;&nbsp;";

	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-185,indexCol: true,indexColWidth:50,  cols: cols,method:'POST',
        url: WST.U('admin/settlements/pageQuery'), fullWidthRows: true, autoLoad: false,remoteSort: true,sortName:'createTime',sortStatus:'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         var diff = v?182:137;
         mmg.resize({height:h-diff})
    }});
    loadGrid(p);
}

function toView(id){
	location.href=WST.U('admin/settlements/toView','id='+id+'&p='+WST_CURR_PAGE);
}
function loadGrid(p){
    p=(p<=1)?1:p;
	mmg.load({page:p,settlementNo:$('#settlementNo').val(),settlementStatus:$('#settlementStatus').val(),shopName:$('#shopName').val(),startDate:$('#startDate').val(),endDate:$('#endDate').val()});
}

var flag = false;
function intView(id){
    var h = WST.pageHeight();
    var element = layui.element;
    var isInit = false;
    element.on('tab(msgTab)', function(data){
        if(data.index==1){
            if(!isInit){
               isInit = true;
               initGoodsGrid(id);
            }
        }
    });
}
function initGoodsGrid(id){
    var h = WST.pageHeight();
    var cols = [
            {title:'订单号', name:'orderNo', width: 60},
            {title:'商品名称', name:'goodsName' ,width:200},
            {title:'商品规格', name:'goodsSpecNames',width:200, renderer:function(val,item,rowIndex){
                if(WST.blank(val)!=''){
	            	val = val.split('@@_@@');
	                return val.join('，');
	            }
            }},
            {title:'商品价格', name:'goodsPrice' ,width:30, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'购买数量', name:'goodsNum' ,width:20},
            {title:'佣金比率', name:'commissionRate',width:20}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/settlements/pageGoodsQuery','id='+id), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
}
function toExport(){
    var params = {};
    params = WST.getParams('.j-ipt');
    var box = WST.confirm({content:"您确定要导出结算记录吗?",yes:function(){
        layer.close(box);
        location.href=WST.U('admin/settlements/toExport',params);
    }});
}