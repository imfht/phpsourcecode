var mmg;
$(function(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate'
    });
    laydate.render({
        elem: '#endDate'
    });
})
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'投诉人', name:'userName', width: 30,sortable: true, renderer: function(val,item,rowIndex){
            	return WST.blank(item['userName'],item['loginName']);
            }},
            {title:'投诉订单号', name:'orderNo',sortable: true, renderer: function(val,item,rowIndex){
            	var h = "";
	            h += "<img class='order-source2' src='"+WST.conf.ROOT+"/wstmart/admin/view/img/order_source_"+item['orderSrc']+".png'>"; 
              h += "<a style='cursor:pointer' onclick='javascript:showDetail("+ item['complainId'] +");'>"+item['orderNo']+"</a>";
	            return h;
            }},
            {title:'订单来源',width: 30,name:'orderCodeTitle'},
            {title:'被投诉人',width: 30,sortable: true, name:'shopName'},
            {title:'投诉类型',width: 120,sortable: true, name:'complainName'},
            {title:'投诉时间',width: 80,sortable: true, name:'complainTime'},
            {title:'状态', name:'complainStatus', width: 60,renderer: function(val,item,rowIndex){
              var html='23123213';
	        	if(val==0)
	        		return '新投诉';
	        	else if(val==1)
	        		return '转给应诉人';
	        	else if(val==2)
	        		return '应诉人回应';
	        	else if(val==3)
	        		return '等待仲裁';
	        	else if(val==4)
	        		return '已仲裁';
            }},
            {title:'操作', name:'op' ,width:180, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
		            h += "<a class='btn btn-blue' href='javascript:toView(" + item['complainId'] + ")'><i class='fa fa-search'></i>查看</a> ";
		            if(item['complainStatus']!=4)
		            h += "<a class='btn btn-blue' href='javascript:toHandle(" + item['complainId'] + ")'><i class='fa fa-pencil'></i>处理</a> ";
		            return h;
	            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/orderComplains/pageQuery'), fullWidthRows: true, autoLoad: false,nowrap:true,
        remoteSort:true ,
        sortName: 'complainTime',
        sortStatus: 'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function toView(id){
	location.href=WST.U('admin/orderComplains/view','cid='+id+'&p='+WST_CURR_PAGE);
}
function toHandle(id){
	location.href=WST.U('admin/orderComplains/toHandle','cid='+id+'&p='+WST_CURR_PAGE);
}
function loadGrid(page){
	var p = WST.getParams('.j-ipt');
	page=(page<=1)?1:page;
	p.page = page;
	mmg.load(p);
}


function deliverNext(id){
     WST.confirm({content:'您确定要转交给应诉人应诉吗?',yes:function(){
       $.post(WST.U('Admin/Ordercomplains/deliverRespond'),{id:id},function(data,textStatus){
          var json = WST.toAdminJson(data);
          if(json.status=='1'){
        	  WST.msg('投诉已移交应诉人',{icon:1},function(){
        		  location.reload();
        	  });
          }else{
            WST.msg(json.msg,{icon:2});
          }
        });
     }});
}

function finalHandle(id){
   var params = {};
   params.cid = id;
   params.finalResult = $.trim($('#finalResult').val());
   if(params.finalResult==''){
     WST.msg('请输入仲裁结果!',{icon:2});
     return;
   }

   var c = WST.confirm({title:'信息提示',content:'您确定仲裁该订单投诉吗?',yes:function(){
     layer.close(c);
     $.post(WST.U('Admin/OrderComplains/finalHandle'),params,function(data,textStatus){
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
          WST.msg(json.msg,{icon:1});
          location.reload();
        }else{
          WST.msg(json.msg,{icon:2});
        }
      });
   }});
}

function showDetail(id){
    parent.showBox({title:'订单详情',type:2,content:WST.U('admin/orderComplains/view',{cid:id,from:1}),area: ['1020px', '500px'],btn:['关闭']});
}
  
