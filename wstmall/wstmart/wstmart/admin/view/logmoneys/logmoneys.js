var mmg1,mmg2,mmg3,mmg4,mmg,h;
function initTab(p){
	var element = layui.element;
	var isInit = isInit2 = isInit3 =  false;
	element.on('tab(msgTab)', function(data){
        var len = $(".layui-tab-title li").length;
	     if(data.index==1){
	        if(!isInit){
	           isInit = true;
	           shopGridInit(p);
	        }else{
	           loadShopGrid(p);
	        }
	     }
	     if(data.index==2){
            if(len==3){
                if(!isInit2){
                    isInit2 = true;
                    flowGridInit(p);
                }else{
                    loadFlowGrid(p);
                }
            }else if(len==4){
                if(!isInit3){
                    isInit3 = true;
                    supplierGridInit(p);
                }else{
                    loadSupplierGrid(p);
                }
            }
	     }
         if(data.index==3){
            if(!isInit2){
                isInit2 = true;
                flowGridInit(p);
            }else{
                loadFlowGrid(p);
            }
         }
	});
	userGridInit(p);
}
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
            }},
            {title:'操作', name:'op' ,width:20,renderer: function (val,item,rowIndex){
	        	return '<a class="btn btn-blue" href="javascript:tologmoneys(0,'+item['userId']+')"><i class="fa fa-search"></i>查看</a>';
	        }}
            ];
 
    mmg1 = $('.mmg1').mmGrid({
        height: h-125,
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
            }},
            {title:'操作', name:'op' ,width:20,renderer: function (val,item,rowIndex){
	        	return '<a class="btn btn-blue" href="javascript:tologmoneys(1,'+item['shopId']+')"><i class="fa fa-search"></i>查看</a>';
	        }}
            ];
 
    mmg2 = $('.mmg2').mmGrid({height: h-125,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQueryByShop'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg2').mmPaginator({})
        ]
    });
    loadShopGrid(p);
}
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
 
    mmg3 = $('.mmg3').mmGrid({height: h-126,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg3').mmPaginator({})
        ]
    });
    loadFlowGrid(p);
}
function loadUserGrid(p){
    p=(p<=1)?1:p;
	mmg1.load({page:p,key:$('#key1').val()});
}
function loadShopGrid(p){
    p=(p<=1)?1:p;
	mmg2.load({page:p,key:$('#key2').val()});
}
function loadFlowGrid(p){
    p=(p<=1)?1:p;
	mmg3.load({page:p,key:$('#key3').val(),type:$('#type').val(),startDate:$('#startDate').val(),endDate:$('#endDate').val()});
}
function tologmoneys(t,id){
	location.href= WST.U('admin/logmoneys/tologmoneys','id='+id+"&src=logmoneys&type="+t+"&startDate="+$('#startDate').val()+"&endDate="+'&endDate='+$('#endDate').val()+'&p='+WST_CURR_PAGE);
}

function moneyGridInit(type,id){
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
 
    mmg = $('.mmg').mmGrid({height: h-125,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQuery','type='+type+'&id='+id), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
}

function loadMoneyGrid(t,id){
	mmg.load({page:1,id:id,type:t,startDate:$('#startDate').val(),endDate:$('#endDate').val()});
}

var w;
function toAdd(id,type){
    var ll = WST.msg('正在加载信息，请稍候...');
    $.post(WST.U('admin/logmoneys/toAdd',{id:id,type:type}),{},function(data){
        layer.close(ll);
        w =WST.open({type: 1,title:"调节会员资金",shade: [0.6, '#000'],offset:'50px',border: [0],content:data,area: ['550px', '380px'],success:function(){
            layui.form.render();
        }});
    });
}
function editMoney(id,type){
    var params = WST.getParams('.ipt');
    params.targetId = id;
    params.targetType = type;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/logmoneys/add'),params,function(data,textStatus){
        layer.close(loading);
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
            WST.msg("操作成功",{icon:1});
            closeFrom();
            mmg.load();
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}
function closeFrom(){
    layer.close(w);
}

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
            }},
            {title:'操作', name:'op' ,width:20,renderer: function (val,item,rowIndex){
                return '<a class="btn btn-blue" href="javascript:tologmoneys(3,'+item['supplierId']+')"><i class="fa fa-search"></i>查看</a>';
            }}
            ];
 
    mmg2 = $('.mmg4').mmGrid({height: h-125,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/logmoneys/pageQueryBySupplier'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg4').mmPaginator({})
        ]
    });
    loadSupplierGrid(p);
}
function loadSupplierGrid(p){
    p=(p<=1)?1:p;
    mmg2.load({page:p,key:$('#key4').val()});
}